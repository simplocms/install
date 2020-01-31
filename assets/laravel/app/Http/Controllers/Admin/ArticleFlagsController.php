<?php

namespace App\Http\Controllers\Admin;

use App\Components\DataTables\ArticleFlagsTable;
use App\Models\Article\Flag;
use App\Http\Requests\Admin\ArticleFlagRequest;
use App\Components\Forms\ArticleFlagForm;
use Illuminate\Http\Request;

class ArticleFlagsController extends AdminController
{
    /**
     * Set active menu item by its nickname.
     *
     * @var string
     */
    protected $activeMenuItem = 'article_flags';

    /**
     * ArticleFlagsController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:article-flags-show')->only('index');
        $this->middleware('permission:article-flags-create')->only([ 'create', 'store' ]);
        $this->middleware('permission:article-flags-edit')->only([ 'edit', 'update' ]);
        $this->middleware('permission:article-flags-delete')->only('delete');
    }


    /**
     * List of flags.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->setTitleDescription(
            trans('admin/article_flags/general.header_title'),
            trans('admin/article_flags/general.descriptions.index')
        );

        $table = new ArticleFlagsTable($this->getLanguage(), $this->getUser());
        return $table->toResponse($request, 'admin.article_flags.index');
    }


    /**
     * Create new flag form.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $this->setTitleDescription(
            trans('admin/article_flags/general.header_title'),
            trans('admin/article_flags/general.descriptions.create')
        );

        $flag = new Flag([
            'seo_index' => true,
            'seo_follow' => true,
            'seo_sitemap' => true,
            'should_bound_articles_to_category' => true,
        ]);

        return (new ArticleFlagForm($flag))->getView();
    }


    /**
     * Edit existing article flag.
     *
     * @param Flag $articleFlag
     * @return \Illuminate\View\View
     */
    public function edit(Flag $articleFlag)
    {
        $this->redirectWhenLanguageNotMatch($articleFlag, 'admin.article_flags.index');
        $this->setTitleDescription(
            trans('admin/article_flags/general.header_title'),
            trans('admin/article_flags/general.descriptions.edit')
        );

        return (new ArticleFlagForm($articleFlag))->getView();
    }


    /**
     * Store new article flag.
     *
     * @param ArticleFlagRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ArticleFlagRequest $request)
    {
        $flag = new Flag($request->getValues());
        $flag->language_id = $this->getLanguage()->id;
        $flag->author_user_id = auth()->id();
        $flag->save();

        flash(trans('admin/article_flags/general.notifications.created'), 'success');
        return $this->redirect(route('admin.article_flags.index'));
    }


    /**
     * Update article flag.
     *
     * @param ArticleFlagRequest $request
     * @param Flag $articleFlag
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(ArticleFlagRequest $request, Flag $articleFlag)
    {
        $this->redirectWhenLanguageNotMatch($articleFlag, 'admin.article_flags.index');
        $articleFlag->update($request->getValues());

        flash(trans('admin/article_flags/general.notifications.updated'), 'success');
        return $this->redirect(route('admin.article_flags.index'));
    }


    /**
     * Delete category
     *
     * @param \App\Models\Article\Flag $articleFlag
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function delete(Flag $articleFlag)
    {
        if (!$articleFlag || !$articleFlag->exists) {
            abort(404);
        }

        $articleFlag->delete();

        flash(trans('admin/article_flags/general.notifications.deleted'), 'success');
        return $this->refresh();
    }
}
