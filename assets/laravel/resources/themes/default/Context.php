<?php

class Context extends \App\Models\ContextBase
{
    /**
     * Called when context is initialized.
     *
     * Set default values of view data.
     */
    public function startup()
    {
        // Set default meta tags.
        $this->getViewData()->setDefaults([
            'description' => "Výchozí popis stránky",
            'og_description' => "Výchozí šablona",
        ]);

        $this->addBreadcrumb(
            'Hlavní stránka',
            $this->urlFactory->getHomepageUrl()
        );
    }


    /**
     * Render page
     *
     * @param \App\Models\Page\Page $page
     * @return \Illuminate\View\View
     */
    public function renderPage(\App\Models\Page\Page $page)
    {
        $languageCode = $this->getLanguage()->language_code;
        $view = 'theme::pages.page';

        // If I want to have breadcrumbs in template, I will set it here.
        $this->setBreadcrumbs($page->getBreadcrumbs());

        if ($page->view && View::exists($page->view)) {
            $view = $page->view;
        } elseif ($page->id === intval($this->theme->get($languageCode . '_articles_page_id'))) {
            $view = 'theme::pages.articles';
        } elseif ($page->is_homepage) {
            $view = 'theme::homepage.index';
        }

        $language = \App\Structures\Enums\SingletonEnum::languagesCollection()->get($page->language_id);
        $page->setRelation('language', $language);

        $content = $page->getActiveContent()->getHtml([
            'language' => $language
        ]);

        return view($view, compact('page', 'content'));
    }


    /**
     * Custom article render method
     *
     * @param \App\Models\Article\Article $article
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function renderArticle(\App\Models\Article\Article $article)
    {
        $languageCode = $this->getLanguage()->language_code;

        $articlesPageUrl = $this->urlFactory->getFullModelUrl(
            \App\Models\Page\Page::class,
            $this->theme->get($languageCode . '_articles_page_id')
        );

        // If I want to have breadcrumbs in template, I will set it here.
        $this->setBreadcrumbs($article->getBreadcrumbs());

        return $this->view('articles.detail', compact('article', 'articlesPageUrl'));
    }


    /**
     * Render search page.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|mixed
     */
    public function renderSearch(\Illuminate\Http\Request $request)
    {
        $result = parent::renderSearch($request);

        if (!is_array($result)) {
            return $result;
        }

        ['results' => $results, 'totalResults' => $totalResults] = $result;

        // (Simple example) Can be extended for more results like:
        // $results['myModels'] = Modules\MyModule\Models\Configuration::search($request->input('q'));
        // $totalResults += $results['myModels']->count();

        return $this->view('vendor.search_results', compact('results', 'totalResults'));
    }


    /**
     * Render article flag.
     *
     * @param \App\Models\Article\Flag $flag
     *
     * @return \Illuminate\View\View
     */
    public function renderFlag(\App\Models\Article\Flag $flag)
    {
        // e.g. $flag->categories
    }


    /**
     * Primary menu view
     *
     * @param \Illuminate\View\View $view
     */
    public function viewMenusPrimary(\Illuminate\View\View $view)
    {
        $this->createMenu('menu_primary', 'MenuPrimary');
    }


    /**
     * Toolbar view
     *
     * @param \Illuminate\View\View $view
     */
    public function viewLayoutsMain(\Illuminate\View\View $view)
    {
        $view->languages = \App\Structures\Enums\SingletonEnum::languagesCollection();
    }


    /**
     * Actualities page
     *
     * @param \Illuminate\View\View $view
     */
    public function viewPagesArticles(\Illuminate\View\View $view)
    {
        if ($this->object instanceof \App\Models\Article\Category) {
            $articles = $this->object->articles();
            $view->title = $this->object->name;
        } else {
            $articles = with(new \App\Models\Article\Article);
            $view->title = $this->trans('articles.title');
        }

        $scopeArticles = $articles->whereLanguage($this->getLanguage())
            ->published()
            ->orderPublish()
            ->limit(30);

        if ($tag = request('tag')) {
            $articlesTable = \App\Models\Article\Article::getTableName();
            $tag = \App\Models\Article\Tag::where('name', $tag)->first();
            $articlesWithTag = [];

            if ($tag) {
                $articlesWithTag = $tag
                    ->articles()
                    ->select($articlesTable . '.id')
                    ->pluck('id');
            }

            $scopeArticles->whereIn($articlesTable . '.id', $articlesWithTag);
        }

        $view->articles = $scopeArticles->get();

    }


    /**
     * Breadcrumbs.
     *
     * @param \Illuminate\View\View $view
     */
    public function viewVendorBreadcrumbs(\Illuminate\View\View $view)
    {
        $view->breadcrumbs = $this->breadcrumbs;
    }


    /**
     * Render theme config in administration
     *
     * @param \Illuminate\View\View $view
     */
    public function viewConfig(\Illuminate\View\View $view)
    {
        $languageCode = $this->getLanguage()->language_code;
        $view->language_code = $languageCode;

        $view->pages = \App\Models\Page\Page::whereLanguage($this->getLanguage())
            ->pluck('name', 'id');
        $view->pages->put('', trans('theme::config.default_articles_page'));
        $view->articlesPageId = $this->theme->get($languageCode . '_articles_page_id');
    }
}
