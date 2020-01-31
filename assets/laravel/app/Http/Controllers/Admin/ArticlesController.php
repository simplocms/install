<?php

namespace App\Http\Controllers\Admin;

use App\Components\DataTables\ArticlesTable;
use App\Http\Requests\Admin\ArticleRequest;
use App\Models\Article\Article;
use App\Models\Article\Category;
use App\Models\Article\Flag;
use App\Components\Forms\ArticleForm;
use App\Structures\Enums\PublishingStateEnum;
use Illuminate\Http\Request;

class ArticlesController extends AdminController
{
    /**
     * Active menu item nickname.
     *
     * @var string
     */
    protected $activeMenuItem;

    /**
     * Current flag.
     *
     * @var \App\Models\Article\Flag
     */
    protected $flag;

    /**
     * ArticlesController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->middleware(function (Request $request, $next) {
            $flagSlug = request()->route('flag');
            $this->setActiveMenuItem($flagSlug . '-articles');

            $this->flag = Flag::whereLanguage($this->getLanguage())->where('url', $flagSlug)->first();

            if (!$this->flag) {
                $routeName = $request->route()->getAction('as');
                $flag = Flag::whereLanguage($this->getLanguage())->first();
                return redirect()->route($routeName, $flag->url);
            }

            return $next($request);
        });

        $this->middleware('permission:articles-show')
            ->only(['index', 'categoriesTree']);

        $this->middleware('permission:articles-create')
            ->only(['create', 'store']);

        $this->middleware('permission:articles-edit')
            ->only(['edit', 'update', 'duplicate']);

        $this->middleware('permission:articles-edit|articles-create')
            ->only(['updatePhoto', 'photoList', 'deletePhoto']);

        $this->middleware('permission:articles-delete')
            ->only('delete');
    }


    /**
     * GET: Table of articles.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $flag = $this->flag;
        $this->setTitleDescription($flag->name, trans('admin/article/general.descriptions.index'));

        $table = new ArticlesTable($flag, $this->getLanguage(), $this->getUser());
        return $table->toResponse($request, 'admin.articles.index', compact('flag'));
    }


    /**
     * GET: Show form for creating article.
     *
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function create()
    {
        $this->setTitleDescription($this->flag->name, trans('admin/article/general.descriptions.create'));

        $article = new Article();
        $article->forceFill([
            'publish_at' => \Carbon\Carbon::now(),
            'seo_index' => true,
            'seo_follow' => true,
            'seo_sitemap' => true,
            'state' => PublishingStateEnum::PUBLISHED,
            'user_id' => auth()->id()
        ]);

        $form = new ArticleForm($this->flag, $article);
        return $form->getView();
    }


    /**
     * GET: Show form for editing article.
     *
     * @param string $flag
     * @param \App\Models\Article\Article $article
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function edit(string $flag, Article $article)
    {
        // redirect when article language does not match.
        $this->redirectWhenLanguageNotMatch($article, 'admin.articles.index');
        $this->setTitleDescription($this->flag->name, trans('admin/article/general.descriptions.edit'));

        $form = new ArticleForm($this->flag, $article);
        return $form->getView();
    }


    /**
     * GET: tree of categories.
     * If article is specified, categories of this article are preselected.
     *
     * @param string $flag
     * @param \App\Models\Article\Article|NULL $article
     * @return mixed
     */
    public function categoriesTree(string $flag, Article $article = NULL)
    {
        return Category::getTree($this->flag->categories(), $article);
    }


    /**
     * POST: Store new article.
     *
     * @param string $flag
     * @param \App\Http\Requests\Admin\ArticleRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \App\Exceptions\GridEditorException
     */
    public function store(string $flag, ArticleRequest $request)
    {
        // Create article
        $article = new Article($request->getValues());

        $article->user_id = auth()->user()->isAdmin() ? $request->input('user_id') : auth()->id();
        $article->setLanguage($this->getLanguage());
        $article->setCategoriesToSave($request->getCategories());

        $this->flag->articles()->save($article);

        $article->savePhotogallery($request->getPhotogallery());

        // Create new content version.
        if ($this->flag->use_grid_editor) {
            $article->createNewVersionIfChanged($request->getGridEditorContent());
        }

        // Save tags
        if ($this->flag->use_tags) {
            $article->syncTags($request->getTags());
        }

        flash(trans('admin/article/general.notifications.created'), 'success');
        return $this->redirect(route('admin.articles.index', $flag));
    }


    /**
     * POST: Update article.
     *
     * @param \App\Http\Requests\Admin\ArticleRequest $request
     * @param string $flag
     * @param \App\Models\Article\Article $article
     * @return \Illuminate\Http\RedirectResponse
     * @throws \App\Exceptions\GridEditorException
     */
    public function update(ArticleRequest $request, string $flag, Article $article)
    {
        // Save values
        $article->fill($request->getValues());

        if (auth()->user()->isAdmin()) {
            $article->user_id = $request->input('user_id');
        }

        $article->setCategoriesToSave($request->getCategories());

        if ($this->flag->use_grid_editor) {
            $article->text = null;
        }

        $article->save();

        $article->syncTags($request->getTags());
        $article->savePhotogallery($request->getPhotogallery());

        // Create new content version if changed.
        if ($this->flag->use_grid_editor) {
            $article->createNewVersionIfChanged($request->getGridEditorContent());
        }

        // Update tags
        if ($this->flag->use_tags) {
            $article->syncTags($request->getTags());
        }

        flash(trans('admin/article/general.notifications.updated'), 'success');
        return $this->redirect(route('admin.articles.index', $flag));
    }


    /**
     * DELETE: delete article.
     *
     * @param string $flag
     * @param \App\Models\Article\Article $article
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function delete(string $flag, Article $article)
    {
        $article->delete();

        flash(trans('admin/article/general.notifications.deleted'), 'success');
        return $this->refresh();
    }


    /**
     * POST: Duplicate article.
     *
     * @param string $flag
     * @param \App\Models\Article\Article $article
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \App\Exceptions\GridEditorException
     */
    public function duplicate(string $flag, Article $article)
    {
        $article->replicateFull();

        flash(trans('admin/article/general.notifications.duplicated'), 'success');
        return $this->refresh();
    }


    /**
     * Get content of specified version.
     *
     * @param string $flag
     * @param \App\Models\Article\Article $article
     * @param int $contentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVersionContent(string $flag, Article $article, $contentId)
    {
        /** @var \App\Models\Article\Content $content */
        $content = $article->contents()->find($contentId);

        if (!$content) {
            abort(404);
        }

        return response()->json([
            'content' => $content->getRaw()
        ]);
    }
}
