<?php namespace App\Http\Controllers\Admin;

use App\Components\Forms\SettingsForm;
use App\Http\Requests\Admin\SettingsRequest;
use App\Models\ContextBase;
use App\Models\Web\Theme;
use App\Structures\Enums\SingletonEnum;
use Illuminate\View\View;

class SettingsController extends AdminController
{
    /**
     * @var ContextBase
     */
    private $themeContext;

    /**
     * ThemesController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->themeContext = SingletonEnum::theme()->getContextInstance();
        view()->composer('theme::config.form', function (View $view) {
            if (method_exists($this->themeContext, 'viewConfig')) {
                $view->theme = SingletonEnum::theme();
                $this->themeContext->viewConfig($view);
            }
        });
    }

    /**
     * Request: theme
     *
     * @return \App\Components\Forms\SettingsForm
     * @throws \Exception
     */
    public function index()
    {
        $this->setTitleDescription(trans('admin/settings.header_title'));
        return new SettingsForm;
    }


    /**
     * Switch template
     *
     * @param $name
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function switchTheme($name)
    {
        $theme = Theme::findNamed($name);

        if (!$theme) {
            flash(trans('admin/settings.notifications.theme_unavailable'), 'error');
            return $this->refresh();
        }

        $theme->install();

        flash(trans('admin/settings.notifications.theme_changed'), 'success');
        return $this->refresh();
    }


    /**
     * PUT: Update settings.
     *
     * @param \App\Http\Requests\Admin\SettingsRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(SettingsRequest $request)
    {
        SingletonEnum::settings()->set($request->getValues());

        return response()->json([
            'message' => trans('admin/settings.notifications.settings_updated')
        ]);
    }

}
