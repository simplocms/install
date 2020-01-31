<?php

namespace App\Http\Controllers;

use App\Contracts\PublishableModelInterface;
use App\Models\Web\Theme;
use App\Structures\Enums\SingletonEnum;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class MainController extends BaseController
{
    /**
     * @var \App\Models\Web\Theme
     */
    protected $theme;

    /**
     * @var \App\Models\ContextBase
     */
    protected $context;

    /**
     * @var string
     */
    protected $url;

    /**
     * Render front website using template context.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
     * @throws \Exception
     */
    public function index(Request $request)
    {
        // Obtain route url parameter
        $this->url = request()->route('url') ?: '';
        $this->initializeContext();

        $urlFactory = SingletonEnum::urlFactory();

        // Search
        if ($urlFactory->isUriSearch($this->url)) {
            return $this->context->renderSearch($request);
        }

        $model = $urlFactory->getModel($this->url);

        if (!$model) {
            if ($urlFactory->isUrlHomepage($this->url)) {
                return \view('site.no_home');
            }

            abort(404);
        }

        // Check if model is published
        if ($model instanceof PublishableModelInterface && !$model->isPublic() && !auth()->check()) {
            abort(404);
        }

        // Try to find render function
        $nsParts = explode('\\', get_class($model));
        $modelName = end($nsParts);

        if (method_exists($model, $beforeRenderMethod = 'beforeRender')) {
            $model = $model->$beforeRenderMethod($request, $model);
        }

        $this->context->setViewedObject($model);

        if (method_exists($this->context, $renderMethod = "render{$modelName}")) {
            return $this->context->$renderMethod($model);
        }

        return abort(404);
    }


    /**
     * Initialized context.
     */
    protected function initializeContext()
    {
        $this->theme = Theme::getDefault();
        $this->context = $this->theme->getContextInstance();
        $this->context->startup();

        view()->composer('*', function (View $view) {
            $this->context->viewAny($view);

            $renderMethod = 'view' . implode('', array_map('ucfirst', explode('.', str_replace('theme::', '', $view->name()))));

            if (method_exists($this->context, $renderMethod)) {
                $this->context->{$renderMethod}($view);
            }
        });

        view()->composer('vendor._ga_tracking_code', function (View $view) {
            $view->enableTracking = SingletonEnum::settings()->getBoolean('ga_enable_tracking');

            if ($view->enableTracking) {
                $view->propertyId = SingletonEnum::settings()->get('ga_property_id');
                if (!$view->propertyId) {
                    $view->enableTracking = false;
                }
            }
        });
    }
}
