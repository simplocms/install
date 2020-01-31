<?php

namespace App\Helpers;

use App\Models\Page\Page;
use App\Models\Web\Language;
use App\Models\Web\Url;
use App\Structures\Enums\SingletonEnum;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class UrlFactory
{
    /** @var Collection|null */
    private $urls;

    /** @var array|null */
    private $mappedUrls;

    /** @var array|null */
    private $mappedModels;

    /** @var \App\Models\Interfaces\UrlInterface[] */
    private $cachedModels;

    /**
     * UrlFactory constructor.
     */
    public function __construct()
    {
        $this->urls = null;
        $this->cachedModels = [];
        $this->mappedUrls = null;
        $this->mappedModels = null;
    }


    /**
     * Get language.
     *
     * @return \App\Models\Web\Language
     */
    public function getLanguage(): Language
    {
        return SingletonEnum::languagesCollection()->getContentLanguage();
    }


    /**
     * Get full url of model - class and id.
     *
     * @param string $class
     * @param int $id
     * @param string $default
     * @return string
     * @throws \Exception
     */
    public function getFullModelUrl(string $class, $id, string $default = '#'): string
    {
        if (is_null($this->mappedModels)) {
            $this->mapModels();
        }

        if (isset($this->mappedModels[$class]) && isset($this->mappedModels[$class][$id])) {
            /** @var \App\Models\Web\Url $urlModel */
            $urlModel = $this->urls->get($this->mappedModels[$class][$id]);
            if ($urlModel) {
                $language = $this->getLanguageFromShortUrl($urlModel->url);
                return $this->getAbsoluteUrlFromShortUrl($urlModel->url, $language);
            }
        }

        return $default;
    }


    /**
     * Get model by url.
     *
     * @param string $uri
     * @return \Illuminate\Database\Eloquent\Model|null
     * @throws \Exception
     */
    public function getModel(string $uri): ?Model
    {
        if (isset($this->cachedModels[$uri])) {
            return $this->cachedModels[$uri] ?: null;
        }

        if (is_null($this->mappedUrls)) {
            $this->mapUrls();
        }

        if ($this->isUrlHomepage($uri)) {
            return $this->cachedModels[$uri] = Page::getHomepage($this->getLanguage());
        }

        $shortUrl = isset($this->mappedUrls[$uri]) ? $uri : $this->uriToShortUrl($uri);

        if (isset($this->mappedUrls[$shortUrl])) {
            /** @var \App\Models\Web\Url $urlModel */
            $urlModel = $this->urls->get($this->mappedUrls[$shortUrl]);
            $instance = $urlModel->getInstance();

            if (!$instance) {
                // Instance is soft-deleted or cannot be rendered.
                return null;
            }

            // Set active parent URL (e.g. current parent category for viewed article)
            if (!$instance->workWithSingleUrl()) {
                $parentUrl = substr($shortUrl, 0, strrpos($shortUrl, '/'));
                $parentUrlId = $this->mappedUrls[$parentUrl] ?? null;

                if ($parentUrlId && $this->urls->get($parentUrlId)) {
                    $instance->setActiveParentUrl($this->urls->get($parentUrlId));
                }
            }

            return $this->cachedModels[$uri] = $instance;
        }

        $this->cachedModels[$uri] = false;
        return null;
    }


    /**
     * Normalize given uri for matching.
     *
     * @param string $uri
     * @return string
     */
    public static function normalizeUri(string $uri): string
    {
        $uri = trim($uri, '/');

        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }

        return $uri;
    }


    /**
     * Resolve language from url.
     *
     * @param string $uri
     * @return \App\Models\Web\Language
     */
    public static function resolveLanguage(string $uri): ?Language
    {
        $uri = self::normalizeUri($uri);
        $languagesCollection = SingletonEnum::languagesCollection();

        switch (true) {
            case self::languageByDirectory():
                if ($uri === '') {
                    break;
                }

                $firstSlashPos = strpos($uri, '/');

                // Uri does not contain slash = could be language code
                if ($firstSlashPos === false) {
                    $language = $languagesCollection->findByCode($uri);

                    // prevent duplicated content for default language
                    if ($language) {
                        return !self::showDefaultLanguage() && $language->default ? null : $language;
                    }
                } else {
                    $languageCode = substr($uri, 0, $firstSlashPos);
                    $language = $languagesCollection->findByCode($languageCode);

                    if ($language) {
                        return $language;
                    }
                }

                break;
            case self::languageBySubDomain():
                $parsedUrl = parse_url($_SERVER['SERVER_NAME']);
                $explodedHost = explode('.', $parsedUrl['path']);

                if (count($explodedHost)) {
                    return $languagesCollection->findByCode($explodedHost[0]);
                }

                break;
            case self::languageByDomain():
                $parsedUrl = parse_url($_SERVER['SERVER_NAME']);
                return $languagesCollection->firstWhere('domain', $parsedUrl['path']);
        }

        // Find default if language code is not required
        return self::showDefaultLanguage() ? null : $languagesCollection->getDefault();
    }


    /**
     * Get language code from short URL.
     *
     * @param string $shortUrl
     * @return string
     */
    public function getLanguageCodeFromShortUrl(string $shortUrl): string
    {
        $firstSlashPos = strpos($shortUrl, '/');
        return $firstSlashPos === false ? $shortUrl : substr($shortUrl, 0, $firstSlashPos);
    }


    /**
     * Resolve urls.
     * @throws \Exception
     */
    private function resolveUrls(): void
    {
        // already resolved
        if (!is_null($this->urls)) {
            return;
        }

        $this->urls = Url::query()->get()->keyBy('id');
    }


    /**
     * Map urls by url address.
     *
     * @throws \Exception
     */
    private function mapUrls(): void
    {
        $this->resolveUrls();

        $this->mappedUrls = [];
        foreach ($this->urls as $urlModel) {
            $this->mappedUrls[$urlModel->url] = $urlModel->id;
        }
    }


    /**
     * Map urls by url address.
     *
     * @throws \Exception
     */
    private function mapModels(): void
    {
        $this->resolveUrls();
        $this->mappedModels = [];

        foreach ($this->urls as $urlModel) {
            if (!isset($this->mappedModels[$urlModel->model])) {
                $this->mappedModels[$urlModel->model] = [];
            }

            $this->mappedModels[$urlModel->model][$urlModel->model_id] = $urlModel->id;
        }
    }


    /**
     * Adjust uri for matching. Adds language code to url if needed.
     *
     * @param string $uri
     * @param \App\Models\Web\Language|null $language
     * @return string
     */
    public function uriToShortUrl(string $uri, ?Language $language = null): string
    {
        if (!$language) {
            $language = $this->getLanguage();
        }

        if (self::languageByDirectory() && !self::showDefaultLanguage() && $language->default) {
            return $language->language_code . '/' . $uri;
        }

        if (!self::languageByDirectory()) {
            return $language->language_code . '/' . $uri;
        }

        return $uri;
    }


    /**
     * Adjust short url for use in route. Removes language code to url if needed.
     *
     * @param string $shortUrl
     * @param \App\Models\Web\Language|null $language
     * @return string
     */
    public function shortUrlToUri(string $shortUrl, ?Language $language = null): string
    {
        if (!$language) {
            $language = $this->getLanguage();
        }

        $shortUrlChunks = explode('/', $shortUrl);
        $languageCode = array_shift($shortUrlChunks);
        $uri = join('/', $shortUrlChunks);

        if ($language->language_code !== $languageCode) {
            $language = SingletonEnum::languagesCollection()->findByCode($languageCode);
        }

        if (!$language) {
            return $shortUrl;
        }

        return $language ? $uri : $shortUrl;
    }


    /**
     * Get language from short url.
     *
     * @param string $shortUrl
     * @return \App\Models\Web\Language
     */
    public function getLanguageFromShortUrl(string $shortUrl): Language
    {
        $language = $this->getLanguage();
        $shortUrlChunks = explode('/', $shortUrl);
        $languageCode = array_shift($shortUrlChunks);

        if ($language->language_code !== $languageCode) {
            $codeLanguage = SingletonEnum::languagesCollection()->findByCode($languageCode);

            if ($codeLanguage) {
                return $codeLanguage;
            }
        }

        return $language;
    }


    /**
     * Get full public url.
     *
     * @param string $shortUrl
     * @param \App\Models\Web\Language|null $language
     * @return string
     */
    public function getAbsoluteUrlFromShortUrl(string $shortUrl, ?Language $language = null): string
    {
        $uri = $this->shortUrlToUri($shortUrl, $language);
        return $this->getAbsoluteUrl($uri, $language);
    }


    /**
     * Get absolute url by given uri and language. If no language is specified, default language is used.
     *
     * @param string $uri - uri without language code
     * @param \App\Models\Web\Language|null $language
     * @return string
     */
    public function getAbsoluteUrl(string $uri, ?Language $language = null): string
    {
        if (is_null($language)) {
            $language = $this->getLanguage();
        }

        if (strlen($uri) && $uri[0] === '/') {
            $uri = substr($uri, 1);
        }

        switch (true) {
            case self::languageByDirectory():
                if (!$language->default || self::showDefaultLanguage()) {
                    $uri = "{$language->language_code}/$uri";
                }

                return route('homepage', $uri);
            case self::languageBySubDomain():
                $parsedUrl = parse_url($_SERVER['SERVER_NAME']);
                $urlExploded = explode('.', $parsedUrl['host'] ?? $parsedUrl['path']);

                if (count($urlExploded) > 2) {
                    array_shift($urlExploded);
                }

                $protocol = $parsedUrl['scheme'] ?? 'http';
                $port = isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '';
                $domain = implode('.', $urlExploded) . $port;

                return $protocol . '://' . $language->language_code . '.' . $domain . '/' . $uri;
            case self::languageByDomain():
                $parsedUrl = parse_url($_SERVER['SERVER_NAME']);
                $protocol = $parsedUrl['scheme'] ?? 'http';
                $domain = $language->domain ?? $parsedUrl['host'] ?? $parsedUrl['path'];

                return "$protocol://$domain/$uri";
        }

        return $uri;
    }


    /**
     * Is homepage url?
     *
     * @param string $url
     * @return bool
     */
    public function isUrlHomepage(string $url): bool
    {
        if (self::languageByDirectory()) {
            if ($url === $this->getLanguage()->language_code) {
                return true;
            } else if (self::showDefaultLanguage()) {
                return false;
            }
        }

        return $url === '' || $url === '/';
    }


    /**
     * Get homepage of current / specified language.
     *
     * @param \App\Models\Web\Language|null $language - You can specify language
     * @return string
     */
    public function getHomepageUrl(?Language $language = null): string
    {
        return $this->getAbsoluteUrl('', $language);
    }


    /**
     * Get url of RSS feed for current / specified language.
     *
     * @param \App\Models\Web\Language|null $language
     * @return string
     */
    public function getRssFeedUrl(?Language $language = null): string
    {
        if (is_null($language)) {
            $language = $this->getLanguage();
        }

        return route('feed.rss', $language->language_code);
    }


    /**
     * Get search url of current / specified language.
     *
     * @param \App\Models\Web\Language|null $language
     * @return string
     */
    public function getSearchUrl(?Language $language = null): string
    {
        if (is_null($language)) {
            $language = $this->getLanguage();
        }

        return $this->getAbsoluteUrl($this->getSearchUri($language), $language);
    }


    /**
     * Is search url?
     *
     * @param string $uri
     * @return bool
     */
    public function isUriSearch(string $uri): bool
    {
        $uriParts = explode('/', $uri);
        if (self::languageByDirectory() && !self::showDefaultLanguage() && count($uriParts) === 1) {
            return $uriParts[0] === $this->getSearchUri();
        }

        return count($uriParts) === 2 && $uriParts[1] === $this->getSearchUri();
    }


    /**
     * Get search uri.
     *
     * @param \App\Models\Web\Language|null $language
     * @return string
     */
    private function getSearchUri(?Language $language = null): string
    {
        if (is_null($language)) {
            $language = $this->getLanguage();
        }

        return SingletonEnum::settings()->getDictionary(
            'search_uri', trans('general.settings.search_uri', [], $language->language_code), $language
        );
    }


    /**
     * Get language display setting.
     *
     * @return int
     */
    protected static function getLanguageDisplaySetting(): int
    {
        return SingletonEnum::settings()->getInt(
            'language_display', config('admin.language_url.directory')
        );
    }


    /**
     * Check if language is in url represented as directory.
     *
     * @return bool
     */
    protected static function languageByDirectory(): bool
    {
        return self::getLanguageDisplaySetting() === config('admin.language_url.directory');
    }


    /**
     * Check if language is in url represented as directory.
     *
     * @return bool
     */
    protected static function languageBySubDomain(): bool
    {
        return self::getLanguageDisplaySetting() === config('admin.language_url.subdomain');
    }


    /**
     * Check if language is in url represented as directory.
     *
     * @return bool
     */
    protected static function languageByDomain(): bool
    {
        return self::getLanguageDisplaySetting() === config('admin.language_url.domain');
    }


    /**
     * Check if default language should be shown in URL.
     *
     * @return bool
     */
    protected static function showDefaultLanguage(): bool
    {
        if (self::languageByDirectory()) {
            return !SingletonEnum::settings()->getBoolean('default_language_hidden', false);
        }

        return false;
    }
}
