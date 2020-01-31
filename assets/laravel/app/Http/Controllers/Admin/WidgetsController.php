<?php

namespace App\Http\Controllers\Admin;

use App\Components\DataTables\WidgetsTable;
use App\Http\Requests\Admin\WidgetRequest;
use App\Models\Widget\Widget;
use App\Components\Forms\WidgetForm;
use Illuminate\Http\Request;

class WidgetsController extends AdminController
{
    /**
     * Active menu item nickname.
     *
     * @var string
     */
    protected $activeMenuItem = 'widgets';

    /**
     * WidgetsController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:widgets-show')->only('index');
        $this->middleware('permission:widgets-create')->only([ 'create', 'store' ]);
        $this->middleware('permission:widgets-edit')->only([ 'edit', 'update' ]);
        $this->middleware('permission:widgets-delete')->only('delete');
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
            trans('admin/widgets/general.header_title'), trans('admin/widgets/general.descriptions.index')
        );

        $table = new WidgetsTable($this->getUser());
        return $table->toResponse($request, 'admin.widgets.index');
    }


    /**
     * Request: Show the form for creating a new widget.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $this->setTitleDescription(
            trans('admin/widgets/general.header_title'), trans('admin/widgets/general.descriptions.create')
        );

        $form = new WidgetForm($this->getLanguage(), new Widget);
        return $form->getView();
    }


    /**
     * POST: Store new widget.
     *
     * @param \App\Http\Requests\Admin\WidgetRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \App\Exceptions\GridEditorException
     */
    public function store(WidgetRequest $request)
    {
        // Create new widget.
        $widget = new Widget($request->only(['name', 'id']));
        $widget->author_user_id = auth()->id();
        $widget->save();

        // Create new content.
        $this->saveContentAndModules($widget, $request);

        flash(trans('admin/widgets/general.notifications.created'), 'success');
        if ($request->ajax()) {
            return response()->json([
                'redirect' => route('admin.widgets.index')
            ]);
        }

        return $this->redirect(route('admin.widgets.index'));
    }


    /**
     * Request: Show the form for editing the specified widget.
     *
     * @param Widget $widget
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Widget $widget)
    {
        $this->setTitleDescription(
            trans('admin/widgets/general.header_title'), trans('admin/widgets/general.descriptions.edit')
        );

        $form = new WidgetForm($this->getLanguage(), $widget);
        return $form->getView();
    }


    /**
     * POST: Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Admin\WidgetRequest $request
     * @param \App\Models\Widget\Widget $widget
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \App\Exceptions\GridEditorException
     */
    public function update(WidgetRequest $request, Widget $widget)
    {
        // Fill model with input.
        $widget->fill($request->only(['name', 'id']))->save();

        // Examine content for changes and save it with modules.
        $content = $this->saveContentAndModules($widget, $request);

        if ($request->ajax() && $request->get('is_saving')) {
            return response()->json([
                'message' => trans('admin/widgets/general.notifications.updated'),
                'new_content' => $content->getRaw()
            ]);
        }

        flash(trans('admin/widgets/general.notifications.updated'), 'success');

        if ($request->ajax()) {
            return response()->json([
                'redirect' => route('admin.widgets.index')
            ]);
        }

        return $this->redirect(route('admin.widgets.index'));
    }


    /**
     * Save existing or store new content.
     * Save all entities and configurations.
     *
     * @param \App\Models\Widget\Widget $widget
     * @param \App\Http\Requests\Admin\WidgetRequest $request
     * @return \App\Models\Interfaces\IsGridEditorContent|null
     * @throws \App\Exceptions\GridEditorException
     */
    private function saveContentAndModules(Widget $widget, WidgetRequest $request) 
    {
        $languageId = (int)$request->input('language_id');

        $activeContent = $widget->getLanguageContent($languageId, true);
        $activeContent->updateContentAndModules($request->getGridEditorContent());

        return $activeContent;
    }


    /**
     * Request: delete widget
     *
     * @param Widget $widget
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function delete(Widget $widget)
    {
        $widget->delete();
        flash(trans('admin/widgets/general.notifications.deleted'), 'success');
        return $this->refresh();
    }
}
