<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Web\Language;
use App\Structures\AdminMenu\Structure;
use App\Structures\Enums\SingletonEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class AdminController extends Controller
{
    /**
     * @var string Page title
     */
    private $title;

    /**
     * @var string Page description
     */
    private $description;

    /**
     * @var Language
     */
    private $language;

    /**
     * @var string
     */
    protected $activeMenuItem;


    /**
     * AdminController constructor.
     */
    public function __construct()
    {
        $this->middleware('admin');

        $this->middleware(function (Request $request, $next) {
            $this->createMenu();
            $this->setActiveMenuItem($this->activeMenuItem);
            return $next($request);
        });

        // All
        view()->composer('*', function ($view) {
            $view->currentLanguage = $this->getLanguage();
        });

        // Layout
        view()->composer(['admin.layouts.master', 'admin.layouts.blank'], function ($view) {
            $view->pageTitle = $this->title;
            $view->pageDescription = $this->description;

            // ping interval
            $minPingInterval = 300;
            $pingInterval = ini_get("session.gc_maxlifetime") - $minPingInterval;
            $view->pingInterval = ($pingInterval < $minPingInterval ? $minPingInterval : $pingInterval) * 1000;
        });

        // Navbar
        view()->composer('admin.vendor._main_navbar', function ($view) {
            $view->languages = SingletonEnum::languagesCollection();
        });
    }


    /**
     * Create menu for administration.
     */
    private function createMenu()
    {
        app(\Lavary\Menu\Menu::class)->make('MyNavBar', function ($menu) {
            /** @var \Lavary\Menu\Builder $menu */
            $structure = config('admin.menu_structure') ?? [];
            if (is_array($structure)) {
                $structure = new Structure($structure);
            }

            $structure->fillMenu($menu, $this->getLanguage());
        })->filter(function ($item): bool {
            /** @var \Lavary\Menu\Item $item */
            if (!$item->hasChildren() && $item->data('is_group')) {
                return false;
            }

            return true;
        });
    }


    /**
     * Return current language
     *
     * @return Language|null
     */
    public function getLanguage()
    {
        if (!$this->language) {

            $languageId = app('session')->get('language');

            if ($languageId) {
                $this->language = SingletonEnum::languagesCollection()->get($languageId);
            }

            if (!$languageId || !$this->language) {
                $this->language = SingletonEnum::languagesCollection()->getDefault();
            }
        }

        return $this->language;
    }


    /**
     * Refresh page
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function refresh()
    {
        if (request()->ajax() || request()->acceptsJson()) {
            return response()->json([
                'refresh' => true
            ]);
        }

        return redirect()->back();
    }


    /**
     * Set page title and description
     *
     * @param string $title
     * @param string $description
     */
    public function setTitleDescription($title, $description = "")
    {
        $this->title = $title;
        $this->description = $description;
    }


    /**
     * Request to switch language
     *
     * @param Language $language
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switchLanguage(Language $language)
    {
        if (!$language->exists || !$language->enabled) {
            flash(trans('admin/general.notifications.language_switch_disabled'), 'warning');
            return redirect()->back();
        }

        request()->session()->put('language', $language->id);
        flash(trans('admin/general.notifications.language_changed'), 'info');
        return redirect()->back();
    }


    /**
     * Redirect when model's language does not match of active language.
     * @param Model $model
     * @param string|array $route - redirect to.
     */
    public function redirectWhenLanguageNotMatch($model, $route)
    {
        if ($model->language_id && $model->language_id !== $this->getLanguage()->id) {
            header("Location: " . route($route));
        }
    }

    /**
     * Ping request.
     * @return \Illuminate\Http\JsonResponse
     */
    public function ping()
    {
        return response()->json([
            'ok' => true
        ]);
    }


    /**
     * Turn off maintenance mode.
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function turnOffMaintenanceMode()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if (!$user->isAdmin()) {
            abort(403);
        }

        Artisan::call('up');
        return $this->refresh();
    }


    /**
     * Set active menu item by its nickname.
     *
     * @param $nickname
     */
    public function setActiveMenuItem($nickname)
    {
        /** @var \Lavary\Menu\Item $item */
        $item = app(\Lavary\Menu\Menu::class)->get('MyNavBar')->{$nickname};

        if ($item) {
            $item->active();
        }
    }


    /**
     * Get redirect response depending on type of request.
     *
     * @param string $url
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function redirect(string $url)
    {
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'redirect' => $url
            ]);
        }

        return response()->redirectTo($url);
    }

    /**
     * Get current authorized user.
     *
     * @return \App\Models\User
     */
    protected function getUser(): User
    {
        return auth()->user();
    }
}
