<?php

namespace App\Http\Controllers\Admin;

use App\Components\DataTables\CategoriesTable;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Article\Category;
use App\Models\Article\Flag;
use App\Components\Forms\CategoryForm;
use Illuminate\Http\Request;

class CategoryController extends AdminController
{
    /**
     * Current flag.
     *
     * @var Flag
     */
    protected $flag;

    /**
     * CategoryController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->middleware(function (Request $request, $next) {
            $flagSlug = request()->route('flag');
            $this->setActiveMenuItem($flagSlug . '-categories');

            $this->flag = Flag::whereLanguage($this->getLanguage())->where('url', $flagSlug)->first();

            if (!$this->flag) {
                $routeName = $request->route()->getAction('as');
                $flag = Flag::whereLanguage($this->getLanguage())->first();
                return redirect()->route($routeName, $flag->url);
            }

            return $next($request);
        });

        $this->middleware('permission:article-categories-show')->only('index');
        $this->middleware('permission:article-categories-create')->only([ 'create', 'store' ]);
        $this->middleware('permission:article-categories-edit')->only([ 'edit', 'update' ]);
        $this->middleware('permission:article-categories-delete')->only('delete');
    }


    /**
     * List of categories
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $flag = $this->flag;
        $this->setTitleDescription($flag->name, trans('admin/category/general.descriptions.index'));

        $table = new CategoriesTable($flag, $this->getLanguage(), $this->getUser());
        return $table->toResponse($request, 'admin.categories.index', compact('flag'));
    }


    /**
     * Create new category
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $this->setTitleDescription($this->flag->name, trans('admin/category/general.descriptions.create'));

        $form = new CategoryForm($this->flag, new Category([
            'show' => true,
            'seo_index' => true,
            'seo_follow' => true,
            'seo_sitemap' => true,
        ]));

        return $form->getView();
    }


    /**
     * Edit existing category
     *
     * @param string $flag
     * @param Category $category
     * @return \Illuminate\View\View
     */
    public function edit(string $flag, Category $category)
    {
        // redirect when category language does not match.
        $this->redirectWhenLanguageNotMatch($category, ['admin.categories.index', $this->flag->url]);
        $this->setTitleDescription($this->flag->name, trans('admin/category/general.descriptions.edit'));

        $form = new CategoryForm($this->flag, $category);
        return $form->getView();
    }


    /**
     * Store new category
     *
     * @param CategoryRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CategoryRequest $request)
    {
        $category = new Category($request->getValues());
        $category->flag_id = $this->flag->id;
        $category->language_id = $this->getLanguage()->id;
        $category->user_id = auth()->id();
        $category->save();

        if ($parentId = $request->input('parent_id')) {
            $category->makeChildOf(Category::findOrFail($parentId));
        }else{
            $category->makeRoot();
        }

        flash(trans('admin/category/general.notifications.created'), 'success');
        return $this->redirect(route('admin.categories.index', $this->flag->url));
    }


    /**
     * Update category
     *
     * @param CategoryRequest $request
     * @param string $flag
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CategoryRequest $request, string $flag, Category $category)
    {
        $category->update($request->getValues());

        if ($parentId = $request->input('parent_id')) {
            $category->makeChildOf(Category::findOrFail($parentId));
        }else{
            $category->makeRoot();
        }

        flash(trans('admin/category/general.notifications.updated'), 'success');
        return $this->redirect(route('admin.categories.index', $this->flag->url));
    }


    /**
     * Delete category.
     *
     * @param string $flag
     * @param Category $category
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function delete(string $flag, Category $category)
    {
        $category->delete();

        flash(trans('admin/category/general.notifications.deleted'), 'success');
        return $this->refresh();
    }
}
