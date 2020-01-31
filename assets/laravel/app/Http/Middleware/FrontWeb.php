<?php

namespace App\Http\Middleware;

use App\Helpers\CDNUrlHelper;
use App\Helpers\UrlFactory;
use App\Models\Web\Language;
use App\Models\Web\Redirect;
use App\Services\ResponseManager\Link;
use App\Structures\Enums\SingletonEnum;
use Closure;
use Symfony\Component\HttpFoundation\Response;

final class FrontWeb
{
    /**
     * Verify language code in url.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $requestUri = $request->getRequestUri();
        $uri = UrlFactory::normalizeUri($requestUri);

        // Custom redirects
        $redirect = $this->redirect($requestUri, $uri);
        if ($redirect !== null) {
            return $redirect;
        }

        $language = SingletonEnum::languagesCollection()->getContentLanguage();

        // Url language verification
        $redirect = $this->verifyUrlLanguage($uri, $language);
        if ($redirect !== null) {
            return $redirect;
        }

        $this->prepareLinkHeaders();

        $response = $next($request);

        $this->replaceCdnUrls($response);

        return $response;
    }

    private function prepareLinkHeaders()
    {
        $languages = SingletonEnum::languagesCollection();

        if ($languages->count() <= 1) {
            return;
        }

        /** @var \App\Models\Web\Language $language */
        foreach ($languages as $language) {
            if ($language->getKey() === SingletonEnum::languagesCollection()->getContentLanguage()->getKey()) {
                continue;
            }

            $url = \App\Structures\Enums\SingletonEnum::urlFactory()->getHomepageUrl($language);
            SingletonEnum::responseManager()->addLink(new Link($url, 'alternate', [
                'hreflang' => $language->language_code
            ]));
        }
    }


    /**
     * @param string $uri
     * @param \App\Models\Web\Language $language
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|null
     */
    private function verifyUrlLanguage(string $uri, Language $language)
    {
        if (SingletonEnum::languagesCollection()->isContentLanguageFallback()) {
            if (! strlen($uri)) {
                return redirect(SingletonEnum::urlFactory()->getHomepageUrl($language));
            }

            abort(404);
        }

        return null;
    }


    /**
     * @param string $requestUri
     * @param string $uri
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|null
     */
    private function redirect(string $requestUri, string $uri)
    {
        $language = SingletonEnum::languagesCollection()->getContentLanguage();
        $shortUrl = SingletonEnum::urlFactory()->uriToShortUrl($uri, $language);

        $searchUri = Redirect::normalizeUrl($uri);
        $searchShortUrl = Redirect::normalizeUrl($shortUrl);
        $searchRedirectFor = $searchShortUrl === $searchUri ? [$searchShortUrl] : [$searchShortUrl, $searchUri];

        $redirects = Redirect::findBySourceUrl($searchRedirectFor);
        if ($redirects->isEmpty()) {
            return null;
        }

        // Get redirect that matches current uri the most
        /** @var \App\Models\Web\Redirect $redirect */
        $redirect = $redirects->first(
            $redirects->count() === 1 ? null : function (Redirect $redirect) use ($searchUri): bool {
                return $redirect->from === $searchUri;
            }
        );

        if ($redirect->pointToUrl()) {
            $targetUrl = $redirect->to;
        } else {
            $targetLanguage = UrlFactory::resolveLanguage($uri);
            $targetUrl = SingletonEnum::urlFactory()->getAbsoluteUrlFromShortUrl($redirect->to, $targetLanguage);
        }

        if (($pos = strpos($requestUri, '?')) !== false) {
            $targetUrl .= substr($requestUri, $pos);
        }

        return redirect($targetUrl, $redirect->status_code);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    private function replaceCdnUrls(Response $response): void
    {
        $response->setContent(CDNUrlHelper::modifyContent($response->getContent()));
    }
}
