<?php

namespace App\Models;

use App\Helpers\CDNUrlHelper;
use App\Helpers\UrlFactory;
use App\Models\Article\Article;
use App\Models\Article\Category;
use App\Models\Menu\Menu;
use App\Models\Page\Page;
use App\Models\Photogallery\Photogallery;
use App\Models\Web\Theme;
use App\Models\Web\ViewData;
use App\Structures\Collections\BreadcrumbsCollection;
use App\Structures\DataTypes\Breadcrumb;
use App\Structures\Enums\SingletonEnum;
use App\Structures\Paginator;
use Illuminate\Http\Request;
use Lavary\Menu\Builder;

abstract class ContextBase
{
    /**
     * @var Theme
     */
    public $theme;

    /**
     * @var mixed
     */
    protected $object;

    /**
     * @var \App\Models\Web\ViewData
     */
    private $viewData;

    /**
     * Are view data set?
     * @var bool
     */
    private $areViewDataSet;

    /**
     * @var UrlFactory
     */
    protected $urlFactory;

    /**
     * @var \App\Structures\Collections\BreadcrumbsCollection
     */
    protected $breadcrumbs;


    /**
     * ContextBase constructor.
     * @param Theme $theme
     */
    public function __construct(Theme $theme)
    {
        $this->theme = $theme;
        $this->urlFactory = SingletonEnum::urlFactory();
        $this->breadcrumbs = new BreadcrumbsCollection();
        $this->areViewDataSet = false;
    }


    /**
     * Called after initializing context.
     */
    public function startup()
    {
        // override
    }


    /**
     * Get template directory.
     * @return string
     */
    protected function getDir()
    {
        $reflector = new \ReflectionClass(get_class($this));
        return basename(dirname($reflector->getFileName()));
    }


    /**
     * Render homepage.
     * @param Page $page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function renderHomepage(Page $page)
    {
        $gridContent = $page->getActiveContent()->getHtml([
            'language' => $this->getLanguage()
        ]);

        return $this->view('homepage.index', compact('gridContent'));
    }


    /**
     * Render page.
     * @param Page $page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function renderPage(Page $page)
    {
        $this->object = $page;

        // If I want to have breadcrumbs in template, I will set it here.
        $this->setBreadcrumbs($page->getBreadcrumbs());

        if ($page->is_homepage) {
            return $this->renderHomepage($page);
        }

        $gridContent = $page->getActiveContent()->getHtml([
            'language' => $this->getLanguage()
        ]);

        return view($page->view ?: 'theme::pages.default', compact('gridContent', 'page'));
    }


    /**
     * Render article.
     * @param Article $article
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function renderArticle(Article $article)
    {
        $this->object = $article;

        // If I want to have breadcrumbs in template, I will set it here.
        $this->setBreadcrumbs($article->getBreadcrumbs());

        return $this->view('pages.article', compact('article'));
    }


    /**
     * Render article category.
     * @param Category $category
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function renderCategory(Category $category)
    {
        $this->object = $category;

        // If I want to have breadcrumbs in template, I will set it here.
        $this->setBreadcrumbs($category->getBreadcrumbs());

        return $this->view('homepage.index');
    }


    /**
     * Render search page.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed[] - key "results" with results and "totalResults" with number of total results
     */
    public function renderSearch(Request $request)
    {
        // Get search term
        $term = $request->input('q') ?? '';
        if (!strlen($term)) {
            return redirect()->back();
        }

        $results = [];

        // Get results for the search term
        if (SingletonEnum::settings()->getBoolean('search_in_pages', true)) {
            $results['pages'] = Paginator::make(
                Page::search($term, $this->getLanguage()), 10, 'pagesPage'
            );
        }

        if (SingletonEnum::settings()->getBoolean('search_in_categories', true)) {
            /** @var \App\Models\Article\Category[]|\Illuminate\Database\Eloquent\Collection $results['categories'] */
            $results['categories'] = strlen($term) ?
                Category::published()->whereLanguage($this->getLanguage())->search($term)->get() :
                collect([]);
        }

        if (SingletonEnum::settings()->getBoolean('search_in_articles', true)) {
            $results['articles'] = $articles = Paginator::make(
                Article::search($term, $this->getLanguage()), 10, 'articlesPage'
            );
        }

        if (SingletonEnum::settings()->getBoolean('search_in_photogalleries', true)) {
            $results['photogalleries'] = Paginator::make(
                Photogallery::search($term, $this->getLanguage()), 10, 'photogalleriesPage'
            );
        }

        // Set up paginators to generate correct page links
        $searchUrl = SingletonEnum::urlFactory()->getSearchUrl($this->getLanguage());
        // Compute total results
        $totalResults = isset($results['categories']) ? $results['categories']->count() : 0;
        foreach (array_only($results, ['pages', 'articles', 'photogalleries']) as $paginator) {
            /** @var \App\Structures\Paginator $paginator */
            $paginator->withPath("$searchUrl?" . http_build_query($request->except($paginator->getPageName())));
            $totalResults += $paginator->total();
        }

        $this->getViewData()->fill([
            'title' => $this->trans('theme.search_page.title') .
                ' (' .
                $this->trans_choice('theme.search_page.results_count', $totalResults, ['count' => $totalResults])
                . ')'
        ]);

        return compact('results', 'totalResults');
    }


    /**
     * Render custom error exception.
     *
     * @param \Symfony\Component\HttpKernel\Exception\HttpException $exception
     * @return null
     */
    public function renderErrorException(\Symfony\Component\HttpKernel\Exception\HttpException $exception)
    {
        return null;
    }


    /**
     * Any view
     *
     * @param \Illuminate\View\View $view
     */
    public function viewAny(\Illuminate\View\View $view)
    {
        $view->data = $this->getViewData();
        $view->context = $this;
        $view->urlFactory = $this->urlFactory;
    }


    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string $view
     * @param  array $data
     * @param  array $mergeData
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function view($view = null, $data = [], $mergeData = [])
    {
        return view('theme::' . $view, $data, $mergeData);
    }


    /**
     * Get language
     *
     * @return \App\Models\Web\Language
     */
    public function getLanguage()
    {
        return SingletonEnum::languagesCollection()->getContentLanguage();
    }


    /**
     * Laravel mix.
     *
     * @param string $path
     * @return string
     * @throws \Exception
     */
    public function mix(string $path)
    {
        try {
            $url = mix($path, 'theme');
        } catch (\Exception $e) {
            $this->theme->checkAndFixPublicLink();
            $url = mix($path, 'theme');
        }

        return CDNUrlHelper::make($url);
    }


    /**
     * Media
     *
     * @param $path
     * @return string
     */
    public function media($path)
    {
        $this->theme->checkAndFixPublicLink();
        return CDNUrlHelper::make('theme/media/' . $path);
    }


    /**
     * Media
     *
     * @param $path
     * @return string
     */
    public function asset($path)
    {
        $this->theme->checkAndFixPublicLink();
        return CDNUrlHelper::make('theme/' . $path);
    }


    /**
     * Translate the given message.
     *
     * @param  string $id
     * @param  array $replace
     * @param  string $locale
     *
     * @return \Illuminate\Contracts\Translation\Translator|string|array|null
     */
    public function trans($id = null, $replace = [], $locale = null)
    {
        return trans('theme::' . $id, $replace, $locale ?: $this->getLanguage()->language_code);
    }


    /**
     * Translates the given message based on a count.
     *
     * @param  string $id
     * @param  int|array|\Countable $number
     * @param  array $parameters
     * @param  string $locale
     * @return string
     */
    public function trans_choice($id, $number, array $replace = [], $locale = null)
    {
        return trans_choice(
            'theme::' . $id, $number, $replace, $locale ?: $this->getLanguage()->language_code
        );
    }


    /**
     * Get url from config
     *
     * @param $key
     * @param $class
     * @return string
     */
    public function getConfigUrl($key, $class = Page::class)
    {
        $id = $this->theme->get($this->getLanguage()->language_code . '_' . $key);
        return \UrlFactory::getFullUrl($class, $id);
    }


    /**
     * Get module. If module is not installed, returns null.
     *
     * @param string $name
     * @return \Module|null
     */
    protected function getModule($name)
    {
        $installedModule = \App\Models\Module\InstalledModule::findNamed($name);
        return $installedModule ? $installedModule->module : null;
    }


    /**
     * Creates basic menu.
     *
     * @param string $key - Key to load menu.
     * @param string $name - Name of menu / variable.
     */
    protected function createMenu($key, $name)
    {
        /** @var \App\Models\Menu\Menu $menu */
        $menu = Menu::find($this->theme->get($key));

        \Menu::make($name, function (Builder $builder) use ($menu) {
            if (!$menu) {
                return;
            }

            $menu->fillMenu($builder, $this->getLanguage());
        });
    }


    /**
     * Add breadcrumb.
     *
     * @param string $text - Text of breadcrumb
     * @param string $url - Url of breadcrumb, default null
     * @return $this
     */
    protected function addBreadcrumb(string $text, string $url = null)
    {
        $this->breadcrumbs->push(new Breadcrumb($text, $url));
        return $this;
    }


    /**
     * Set breadcrumbs.
     *
     * @param \App\Structures\Collections\BreadcrumbsCollection $collection
     * @return $this
     */
    protected function setBreadcrumbs(BreadcrumbsCollection $collection)
    {
        $this->breadcrumbs = $collection;
        return $this;
    }


    /**
     * Get view data.
     *
     * @return \App\Models\Web\ViewData
     */
    protected function getViewData()
    {
        if (!$this->viewData) {
            $this->viewData = new ViewData([
                'theme' => $this->theme,
                'language' => $this->getLanguage()
            ]);
        }

        if (!$this->areViewDataSet && $this->object && method_exists($this->object, 'getViewData')) {
            $this->areViewDataSet = true;
            $this->viewData = $this->object->getViewData($this->viewData);
        }

        $this->viewData->setCurrentModel($this->object);
        return $this->viewData;
    }


    /**
     * Set object being viewed.
     *
     * @param mixed $object
     * @return $this
     */
    public function setViewedObject($object)
    {
        $this->object = $object;
        return $this;
    }
}
