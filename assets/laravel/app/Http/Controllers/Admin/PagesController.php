<?php

namespace App\Http\Controllers\Admin;

use App\Components\DataTables\PagesTable;
use App\Http\Requests\Admin\PageRequest;
use App\Models\Page\Page;
use App\Components\Forms\PageForm;
use App\Services\Pages\PageManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PagesController extends AdminController
{
    /**
     * Active menu item nickname.
     *
     * @var string
     */
    protected $activeMenuItem = 'pages';
    /**
     * @var \App\Services\Pages\PageManager
     */
    private $pageManager;

    /**
     * PagesController constructor.
     * @param \App\Services\Pages\PageManager $pageManager
     */
    public function __construct(PageManager $pageManager)
    {
        parent::__construct();
        $this->middleware('permission:pages-show')->only('index');
        $this->middleware('permission:pages-create')->only(['create', 'store']);
        $this->middleware('permission:pages-edit')->only(['edit', 'update', 'switchVersion']);
        $this->middleware('permission:pages-delete')->only('delete');
        $this->pageManager = $pageManager;
    }


    /**
     * Request: Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->setTitleDescription(
            trans('admin/pages/general.header_title'), trans('admin/pages/general.descriptions.index')
        );

        $table = new PagesTable($this->getLanguage(), $this->getUser());
        return $table->toResponse($request, 'admin.pages.index');
    }


    /**
     * Request: Show the form for creating a new page.
     *
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function create()
    {
        $this->setTitleDescription(
            trans('admin/pages/general.header_title'), trans('admin/pages/general.descriptions.create')
        );

        $form = new PageForm($this->getLanguage(), new Page([
            'is_homepage' => !Page::getHomepage($this->getLanguage()),
            'published' => true,
            'publish_at' => \Carbon\Carbon::now(),
            'seo_index' => true,
            'seo_follow' => true,
            'seo_sitemap' => true,
        ]));

        return $form->getView();
    }


    /**
     * POST: Store new page
     *
     * @param \App\Http\Requests\Admin\PageRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \App\Exceptions\GridEditorException
     */
    public function store(PageRequest $request)
    {
        $this->pageManager->create($this->getLanguage(), $request->getValues(), $request->getGridEditorContent());

        flash(trans('admin/pages/general.notifications.created'), 'success');
        return $this->redirect(route('admin.pages.index'));
    }


    /**
     * Request: Show the form for editing the specified page.
     *
     * @param \App\Models\Page\Page $page
     *
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function edit(Page $page)
    {
        // redirect when page language does not match.
        $this->redirectWhenLanguageNotMatch($page, 'admin.pages.index');
        $this->setTitleDescription(
            trans('admin/pages/general.header_title'), trans('admin/pages/general.descriptions.edit')
        );

        $form = new PageForm($this->getLanguage(), $page);
        return $form->getView();
    }


    /**
     * Request: Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Admin\PageRequest $request
     * @param int $pageId
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \App\Exceptions\GridEditorException
     */
    public function update(PageRequest $request, $pageId)
    {
        /** @var \App\Models\Page\Page $page */
        $page = Page::withTestingCounterparts()->findOrFail($pageId);

        $this->pageManager->update($page, $request->getValues());
        $content = $this->pageManager->updateActivePageContent(
            $page, $request->input('active_content_id'), $request->getGridEditorContent()
        );

        $successMessage = trans('admin/pages/general.notifications.updated');

        if ($request->get('prevent_redirect')) {
            return response()->json([
                'message' => $successMessage,
                'newContent' => $content->wasRecentlyCreated ? $content->getRaw() : null,
                'versions' => $content->wasRecentlyCreated  ? $page->getGridEditorVersions() : null,
                'url' => $page->url
            ]);
        }

        flash($successMessage, 'success');
        return $this->redirect(route('admin.pages.index'));
    }


    /**
     * Request: Duplicate specified page.
     *
     * @param \App\Models\Page\Page $page
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function duplicate(Page $page)
    {
        $page->replicateFull();
        flash(trans('admin/pages/general.notifications.duplicated'), 'success');
        return $this->refresh();
    }

    /**
     * Request: Make A/B test for specified page.
     *
     * @param \App\Models\Page\Page $page
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function makeABTest(Page $page)
    {
        if ($page->hasTestingCounterpart()) {
            return response()->json([
                'message' => trans('admin/pages/general.notifications.ab_test_already_created')
            ], 409);
        }

        $this->pageManager->setUpABTesting($page);
        flash(trans('admin/pages/general.notifications.ab_test_created'), 'success');
        return $this->refresh();
    }

    /**
     * Request: Make A/B test for specified page.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Page\Page $page
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function stopABTest(Request $request, Page $page)
    {
        if (!$page->hasTestingCounterpart()) {
            return response()->json([
                'message' => trans('admin/pages/general.notifications.ab_test_not_available')
            ], 400);
        }

        $this->pageManager->stopABTesting($page, $request->input('keep'));
        flash(trans('admin/pages/general.notifications.ab_test_stopped'), 'success');
        return $this->refresh();
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Page\Page $page
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function updateABTestCookie(Request $request, Page $page)
    {
        if ($request->has('variant') && $page->hasTestingCounterpart()) {
            $variant = $request->input('variant') === 'a' ? 'a' : 'b';
            $cookiePrefix = config('cms.cookie_prefix');
            $cookie = $cookiePrefix . 'abt_' . $page->getKey();
            return response('', 204)->cookie($cookie, $variant, 43200);
        }

        return response('Bad request.', 400);
    }


    /**
     * Request: delete page.
     *
     * @param \App\Models\Page\Page $page
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function delete(Page $page)
    {
        $page->delete();
        flash(trans('admin/pages/general.notifications.deleted'), 'success');
        return $this->refresh();
    }


    /**
     * Switch active content.
     *
     * @param int $pageId
     * @param int|null $versionId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function switchVersion($pageId, $versionId = null): JsonResponse
    {
        /** @var \App\Models\Page\Page $page */
        $page = Page::withTestingCounterparts()->findOrFail($pageId);

        if (!$versionId) {
            abort(404);
        }

        /** @var \App\Models\Page\Content $content */
        $content = $page->contents()->find($versionId);

        if (!$content) {
            abort(404);
        }

        return response()->json([
            'content' => $content->getRaw()
        ]);
    }
}
