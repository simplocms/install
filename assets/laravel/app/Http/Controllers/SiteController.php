<?php

namespace App\Http\Controllers;

use App\Contracts\PublishableModelInterface;
use App\Models\Article\Article;
use App\Models\Media\File;
use App\Models\Page\Page;
use App\Models\Photogallery\Photogallery;
use App\Models\Web\Language;
use App\Models\Web\Url;
use App\Services\FaviconGenerator\FaviconGenerator;
use App\Services\MediaLibrary\BitmapImage;
use App\Services\MediaLibrary\ImageBuilder;
use App\Structures\Enums\SingletonEnum;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SiteController extends BaseController
{
    /**
     * Display robots.txt for environments
     *
     * @return Response
     */
    public function robots()
    {
        return response()->view('site.robots', [
            'isProduction' => \App::environment() === 'production'
        ])->header('Content-Type', 'text/plain');
    }


    /**
     * Create sitemap index
     * @return Response
     */
    public function sitemapIndex()
    {
        $languages = SingletonEnum::languagesCollection();
        $imagePagesCount = ceil(File::imagesOnly()->count() / 1000);

        return response()->view('site.sitemapindex', compact('languages', 'imagePagesCount'))
            ->header('Content-Type', 'text/xml');
    }


    /**
     * Sitemap for concrete language
     *
     * @param $languageCode
     * @return Response
     */
    public function sitemap($languageCode)
    {
        $language = SingletonEnum::languagesCollection()->findByCode($languageCode);
        if (!$language) {
            return abort(404);
        }

        $urls = Url::whereLanguage($language)->get();
        $urlSet = collect([]);
        $homepage = Page::getHomepage($language);

        $models = $urls->mapToGroups(function (Url $url) {
            return [$url->model => $url->model_id];
        })->map(function ($group, $modelClass) {
            /** @var \Illuminate\Database\Eloquent\Model $instance */
            $instance = new $modelClass;

            // Make sure we have array/Collection, so the collection will be returned
            if (!$group instanceof Arrayable) {
                $group = [$group];
            }

            return $instance->find($group)->keyBy('id');
        });

        /** @var Url $url */
        foreach ($urls as $url) {
            /** @var \App\Models\Interfaces\UrlInterface $model */
            $model = $models->get($url->model, collect([]))->get($url->model_id);

            if (!$model ||
                $model->getAttribute('seo_sitemap') === false ||
                $model->getAttribute('seo_index') === false ||
                $model instanceof PublishableModelInterface && !$model->isPublic()
            ) {
                continue;
            }

            $priority = 0.6;

            switch ($url->model) {
                case Page::class:
                    $priority = 0.8;
                    break;
                case Article::class:
                case Photogallery::class:
                    $priority = 0.7;
                    break;
            }

            /**
             * http://michalkubicek.cz/jak-na-prioritu-a-frekvenci-v-sitemap-xml/
             */
            $urlSet->push((object)[
                'loc' => SingletonEnum::urlFactory()->getAbsoluteUrlFromShortUrl($url->url, $language),
                'lastmod' => $model->updated_at ?? $homepage->updated_at,
                'changefreq' => 'weekly',
                'priority' => $priority
            ]);
        }

        $alternateLanguages = SingletonEnum::languagesCollection()->where('language_code', '!=', $languageCode);

        return response()->view('site.sitemap', [
            'urlSet' => $urlSet,
            'language' => $language,
            'alternateLanguages' => $alternateLanguages
        ])->header('Content-Type', 'text/xml');
    }


    /**
     * Generate sitemap with images.
     *
     * @param int $page
     * @return \Illuminate\Http\Response
     */
    public function imageSitemap($page)
    {
        $page = max(intval($page), 1);
        $images = File::imagesOnly()
            ->offset(1000 * ($page - 1))
            ->limit(1000)
            ->get();

        return response()->view('site.image_sitemap', [
            'images' => $images,
        ])->header('Content-Type', 'text/xml');
    }


    /**
     * Get media file.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $path
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function media(Request $request, string $path)
    {
        $mediaLibrary = SingletonEnum::mediaLibrary();
        $safePath = str_replace('../', '', $path);

        // Check if file exists.
        if (!$mediaLibrary->fileExists($safePath)) {
            abort(404);
        }

        $lastModified = $mediaLibrary->lastModified($safePath);
        $lastModified->setTimezone(new \DateTimeZone('UTC'));
        $lastModifiedFormatted = $lastModified->format(\DateTime::RFC7231);

        // Cache: check if content was not modified since condition time.
        if ($request->headers->get('if_modified_since', null) === $lastModifiedFormatted) {
            return response('', 304)->setPublic();
        }

        $response = null;
        $params = $request->all();

        // Response for processable file
        if ($params && $mediaLibrary->isFileProcessable($safePath)) {
            $builder = ImageBuilder::fromParameters($params);
            try {
                $file = $builder->getFile($safePath);

                // Hash key of cached image is filename of the resized image at the same time:
                $eTag = basename($file->getPath());
                $response = $file->getResponse();
            } catch (\Exception $e) {
                if (isset($params['format'])) {
                    $eTag = md5($safePath . implode('', $params));
                    $placeholderImage = new BitmapImage(public_path('media/images/media-placeholder.png'));
                    $response = $builder->applyOnFile($placeholderImage)->getResponse();
                }
                // when something fails, code continues to fallback
            }
        }

        // fallback response
        if (!$response) {
            $eTag = md5($safePath);
            $response = $mediaLibrary->toResponse($safePath);
        }

        // If etag matches, return status 304
        if (trim($request->headers->get('if_none_match', ''), '"') === $eTag) {
            return response('', 304)->setPublic();
        }

        return $response->setCache([
            'etag' => $eTag,
            'last_modified' => $lastModified,
            'max_age' => 60,
            'public' => true
        ]);
    }


    /**
     * Generate RSS feed for last articles.
     *
     * @param string $languageCode
     * @return \Illuminate\Http\Response
     */
    public function rssFeed(string $languageCode)
    {
        $language = SingletonEnum::languagesCollection()->findByCode($languageCode);
        if (!$language) {
            abort(404);
        }

        SingletonEnum::languagesCollection()->changeContentLanguage($language);

        /** @var \Illuminate\Support\Collection $articles */
        $articles = Article::query()
            ->whereLanguage($language)
            ->with('categories', 'flag', 'user', 'image')
            ->published()
            ->orderPublish()
            ->limit(20)
            ->get();

        $settings = SingletonEnum::settings()->collect()
            ->getLocalized(
                'site_name', $language, trans('general.settings.site_name', [], $language->language_code)
            )
            ->getLocalized('company_name', $language)
            ->getImage('logo')
            ->getLocalized('seo_description', $language)
            ->getAll();

        $homepageUrl = SingletonEnum::urlFactory()->getHomepageUrl($language);
        $view = view(
            'site.rss_feed',
            compact('articles', 'settings', 'homepageUrl', 'language')
        );
        return \response()->make($view)->header('Content-Type', 'text/xml');
    }


    /**
     * Generate "site.webmanifest" file.
     *
     * @param string $languageCode
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function webManifest(string $languageCode)
    {
        $language = SingletonEnum::languagesCollection()->findByCode($languageCode);
        if (!$language) {
            abort(404);
        }

        SingletonEnum::languagesCollection()->changeContentLanguage($language);

        $settings = SingletonEnum::settings();
        $siteName = $settings->get('site_name', trans('general.settings.site_name', [], $languageCode));

        $icons = FaviconGenerator::getWebManifestIcons();

        return \response([
            'lang' => $languageCode,
            'dir' => 'ltr',
            'name' => $siteName,
            'icons' => $icons,
            'theme_color' => $settings->get('theme_color', '#ffffff'),
            'background_color' => $settings->get('theme_color', '#ffffff'),
            'display' => 'standalone',
        ])->header('Content-Type', 'application/manifest+json');
    }


    /**
     * Generate "browserconfig.xml" file.
     *
     * @return \Illuminate\Http\Response
     */
    public function browserConfig()
    {
        $color = SingletonEnum::settings()->get('theme_color', '#ffffff');
        $icons = FaviconGenerator::getMsTileIcons();
        $view = view('site.browserconfig', compact('color', 'icons'));
        return \response()->make($view)->header('Content-Type', 'text/xml');
    }

}
