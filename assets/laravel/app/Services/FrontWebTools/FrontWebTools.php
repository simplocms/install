<?php
/**
 * FrontWebTools.php created by Patrik Václavek
 */

namespace App\Services\FrontWebTools;


use App\Contracts\ViewableModelInterface;
use App\Helpers\UrlFactory;
use App\Structures\Enums\SingletonEnum;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FrontWebTools
{
    const SCRIPT_NAME = 'cms-toolbar.frontweb.js';
    const STYLE_NAME = 'cms-toolbar.frontweb.css';

    /**
     * Modify the response and inject toolbar.
     *
     * @param  \Symfony\Component\HttpFoundation\Request $request
     * @param  \Symfony\Component\HttpFoundation\Response $response
     * @return void
     * @throws \Exception
     */
    public function modifyResponse(Request $request, Response $response)
    {
        if (!$this->shouldInject($request, $response)) {
            return;
        }

        $this->injectToolBar($request, $response);
    }


    /**
     * Injects the web toolbar into the given response.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\HttpFoundation\Response $response A Response instance
     * @throws \Exception
     */
    protected function injectToolBar(Request $request, Response $response)
    {
        $content = $response->getContent();

        $renderedContent = $this->renderHead();
        $pos = strripos($content, '</head>');
        if (false !== $pos) {
            $content = substr($content, 0, $pos) . $renderedContent . substr($content, $pos);
        } else {
            $content = $content . $renderedContent;
        }

        $pos = strripos($content, '</body>');
        $renderedContent = "<script>new CMSToolbar(" . json_encode($this->getToolbarData($request)) . ");</script>";
        if (false !== $pos) {
            $content = substr($content, 0, $pos) . $renderedContent . substr($content, $pos);
        } else {
            $content = $content . $renderedContent;
        }

        // Update the new content and reset the content length
        $response->setContent($content);
        $response->headers->remove('Content-Length');
    }


    /**
     * Get toolbar data.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array
     * @throws \Exception
     */
    protected function getToolbarData(Request $request): array
    {
        return array_merge($this->getToolbarOptions($request)->toArray(), [
            'urls' => [
                'admin' => route('admin'),
                'editAccount' => route('admin.account.edit'),
                'logout' => route('admin.auth.logout'),
                'turnOffMaintenance' => auth()->user()->isAdmin() ? route('admin.maintenance.off') : null,
            ],
            'csrfToken' => csrf_token(),
            'user' => [
                'username' => auth()->user()->username,
                'name' => auth()->user()->name,
                'avatar' => auth()->user()->image_url,
                'registration' => auth()->user()->created_at->format('d. m. Y')
            ],
            'localization' => trans('admin/layout.main_navbar'),
            'isMaintenance' => app()->isDownForMaintenance(),
        ]);
    }


    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \App\Services\FrontWebTools\ToolbarOptions
     * @throws \Exception
     */
    protected function getToolbarOptions(Request $request): ToolbarOptions
    {
        $options = new ToolbarOptions();
        $model = $this->getModel($request);

        if ($model) {
            $model->setFrontWebToolbarOptions($options);
        }

        return $options;
    }


    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \App\Contracts\ViewableModelInterface|null
     * @throws \Exception
     */
    protected function getModel(Request $request): ?ViewableModelInterface
    {
        $uri = UrlFactory::normalizeUri($request->getRequestUri());
        $model = SingletonEnum::urlFactory()->getModel($uri);

        if ($model && $model instanceof ViewableModelInterface) {
            return $model;
        }

        return null;
    }


    /**
     * Render HTML for head.
     *
     * @return string
     */
    protected function renderHead(): string
    {
        try {
            $scriptPath = mix('js/' . self::SCRIPT_NAME);
        } catch (\Exception $e) {
            return '';
        }

        $html = "<script type='text/javascript' src='{$scriptPath}'></script>";
        return $html;
    }


    /**
     * Check if tools should be injected for given request and response.
     *
     * @param  \Symfony\Component\HttpFoundation\Request $request
     * @param  \Symfony\Component\HttpFoundation\Response $response
     * @return bool
     */
    protected function shouldInject(Request $request, Response $response): bool
    {
        return auth()->check() &&
            !isset($response->exception) &&
            !$response->isRedirection() &&
            !$this->isJsonRequest($request) &&
            $this->isHtmlContent($request, $response);
    }


    /**
     * Check if is JSON request.
     *
     * @param  \Symfony\Component\HttpFoundation\Request $request
     * @return bool
     */
    protected function isJsonRequest(Request $request): bool
    {
        // If XmlHttpRequest, return true
        if ($request->isXmlHttpRequest()) {
            return true;
        }

        // Check if the request wants Json
        $acceptable = $request->getAcceptableContentTypes();
        return (isset($acceptable[0]) && $acceptable[0] === 'application/json');
    }


    /**
     * Is HTML content?
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @return bool
     */
    protected function isHtmlContent(Request $request, Response $response): bool
    {
        return $request->getRequestFormat() === 'html' &&
            $response->getContent() !== false &&
            (
                $response->headers->has('Content-Type') &&
                strpos($response->headers->get('Content-Type'), 'html') !== false
            );
    }
}
