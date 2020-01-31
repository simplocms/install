<?php

namespace App\Services\ResponseManager;

use App\Structures\Enums\ReferrerPolicyEnum;
use App\Structures\Enums\SingletonEnum;
use App\Structures\Enums\XSSProtectionEnum;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ResponseManager
 * @package App\Services\ResponseManager
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 */
class ResponseManager
{
    /**
     * Headers set at runtime.
     *
     * @var array
     */
    protected $runtimeHeaders;

    /**
     * @var bool
     */
    protected $noIndex;

    /**
     * @var bool
     */
    protected $noFollow;

    /**
     * @var string[]
     */
    protected $links;

    /**
     * @var \Illuminate\View\View[]
     */
    protected $injectViews;

    /**
     * Initialize response manager.
     */
    public function __construct()
    {
        $this->runtimeHeaders = [];
        $this->links = [];
        $this->injectViews = [];
        $this->noIndex = $this->noFollow = false;
    }


    /**
     * Handle response and append required headers.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Response $response): Response
    {
        $this->appendHeaders($response);
        $this->injectViews($response);
        return $response;
    }


    /**
     * Set header.
     *
     * @param string $name
     * @param string $value
     * @return \App\Services\ResponseManager\ResponseManager
     */
    public function set(string $name, string $value): ResponseManager
    {
        $this->runtimeHeaders[$name] = $value;
        return $this;
    }


    /**
     * Set X-Robots-Tag by specified parameters.
     *
     * @param bool $index
     * @param bool $follow
     * @return \App\Services\ResponseManager\ResponseManager
     */
    public function robots(bool $index, bool $follow): ResponseManager
    {
        $value[] = $index ? 'index' : 'noindex';
        $value[] = $follow ? 'follow' : 'nofollow';
        $this->runtimeHeaders['X-Robots-Tag'] = join(', ', $value);
        return $this;
    }


    /**
     * Add link header.
     *
     * @param \App\Services\ResponseManager\Link $link
     * @return \App\Services\ResponseManager\ResponseManager
     */
    public function addLink(Link $link): ResponseManager
    {
        $this->links[] = $link;
        $this->runtimeHeaders['Link'][] = $link->getHeaderString();
        return $this;
    }


    /**
     * @return \App\Services\ResponseManager\Link[]
     */
    public function getLinks(): array
    {
        return $this->links;
    }


    /**
     * Do not index page.
     *
     * @return \App\Services\ResponseManager\ResponseManager
     */
    public function noIndex(): ResponseManager
    {
        $this->noIndex = true;
        return $this;
    }


    /**
     * Do not follow page's links.
     *
     * @return \App\Services\ResponseManager\ResponseManager
     */
    public function noFollow(): ResponseManager
    {
        $this->noFollow = true;
        return $this;
    }


    /**
     * Check if page should be indexed.
     *
     * @return bool
     */
    public function shouldIndex(): bool
    {
        return !$this->noIndex;
    }


    /**
     * Check if page links should be followed.
     *
     * @return bool
     */
    public function shouldFollow(): bool
    {
        return !$this->noFollow;
    }


    /**
     * Check if runtime header is set.
     *
     * @param string $name
     * @return bool
     */
    private function isRuntimeHeaderSet(string $name): bool
    {
        return isset($this->runtimeHeaders[$name]);
    }


    /**
     * Append headers to given response.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @return void
     */
    private function appendHeaders(Response $response)
    {
        $settings = SingletonEnum::settings()->collect()
            ->get('x_frame_options', 'sameorigin')
            ->get('x_xss_protection', XSSProtectionEnum::BLOCK_ATTACKS)
            ->get('referrer_policy', ReferrerPolicyEnum::STRICT_ORIGIN_WHEN_CROSS_ORIGIN)
            ->getBool('x_content_type_options', true)
            ->getBool('hsts_enabled', true)
            ->getBool('hsts_include_subdomains', true)
            ->getInt('hsts_max_age', 31536000)
            ->getAll();

        // X-Robots-Tag //
        $this->runtimeHeaders['X-Robots-Tag'] = join(', ', [
            $this->noIndex ? 'noindex' : 'index',
            $this->noFollow ? 'nofollow' : 'follow'
        ]);

        // X-Frame-Options //
        if (strtolower($settings->get('x_frame_options')) !== 'allow' &&
            !$this->isRuntimeHeaderSet('X-Frame-Options')
        ) {
            $value = strtolower($settings->get('x_frame_options'));
            if ($value !== 'deny' && $value !== 'sameorigin') {
                $value = "allow-from $value";
            }

            $response->headers->set('X-Frame-Options', $value, true);
        }

        // X-Xss-Protection //
        if ($settings->get('x_xss_protection') !== XSSProtectionEnum::DO_NOT_USE &&
            !$this->isRuntimeHeaderSet('X-Xss-Protection')
        ) {
            $response->headers->set('X-Xss-Protection', $settings->get('x_xss_protection'), true);
        }

        // Referrer-Policy //
        if ($settings->get('referrer_policy') !== ReferrerPolicyEnum::DO_NOT_USE &&
            !$this->isRuntimeHeaderSet('Referrer-Policy')
        ) {
            $response->headers->set('Referrer-Policy', $settings->get('referrer_policy'), true);
        }

        // X-Content-Type-Options //
        if ($settings->get('x_content_type_options') &&
            !$this->isRuntimeHeaderSet('X-Content-Type-Options')
        ) {
            $response->headers->set('X-Content-Type-Options', 'nosniff', true);
        }

        // HTTP Strict Transport Security //
        if ($settings->get('hsts_enabled') &&
            !$this->isRuntimeHeaderSet('Strict-Transport-Security')
        ) {
            $response->headers->set(
                'Strict-Transport-Security',
                $this->getHSTSHeader(
                    $settings->get('hsts_max_age'), $settings->get('hsts_include_subdomains')
                ),
                true
            );
        }

        // Set runtime headers.
        if (!empty($this->runtimeHeaders)) {
            foreach ($this->runtimeHeaders as $header => $value) {
                $response->headers->set($header, $value, $header !== 'Link');
            }
        }
    }


    /**
     * Get HTTP Strict Transport Security header.
     *
     * @param int $maxAge
     * @param bool $includeSubDomains
     * @return string
     */
    private function getHSTSHeader(int $maxAge, bool $includeSubDomains): string
    {
        $value = "max-age={$maxAge};";
        if ($includeSubDomains) {
            $value .= ' includeSubDomains;';
        }
        return $value;
    }

    /**
     * @param \Illuminate\View\View|\Illuminate\View\Factory $view
     * @return \App\Services\ResponseManager\ResponseManager
     */
    public function injectView($view): ResponseManager
    {
        $this->injectViews[] = $view;
        return $this;
    }

    /**
     * @return \Illuminate\View\View[]
     */
    public function getViewsToInject(): array
    {
        return $this->injectViews;
    }

    /**
     * Inject views to response.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @throws \Throwable
     */
    private function injectViews(Response $response): void
    {
        $renderedContent = '';

        foreach ($this->getViewsToInject() as $view) {
            $renderedContent .= $view->render();
        }

        if (empty($renderedContent)) {
            return;
        }

        $content = $response->getContent();
        $pos = strripos($content, '</body>');
        if (false !== $pos) {
            $content = substr($content, 0, $pos) . $renderedContent . substr($content, $pos);
        } else {
            $content .= $renderedContent;
        }

        // Update the new content and reset the content length
        $response->setContent($content);
        $response->headers->remove('Content-Length');
    }
}
