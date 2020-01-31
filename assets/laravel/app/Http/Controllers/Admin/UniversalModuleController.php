<?php

namespace App\Http\Controllers\Admin;

use App\Components\DataTables\UniversalModuleTable;
use App\Helpers\Functions;
use App\Helpers\ViewHelper;
use App\Http\Requests\Admin\UniversalModuleEntityRequest;
use App\Http\Requests\Admin\UniversalModuleRequest;
use App\Models\UniversalModule\UniversalModuleItem;
use App\Models\UniversalModule\UniversalModuleEntity;
use App\Components\Forms\UniversalModuleForm;
use App\Services\UniversalModules\UniversalModule;
use App\Structures\Enums\SingletonEnum;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

class UniversalModuleController extends AdminController
{
    /**
     * UniversalModuleController constructor.
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct();

        $prefix = $request->route('prefix');
        $this->activeMenuItem = $prefix;

        $this->middleware("permission:universal_module_{$prefix}-show")->only('index');
        $this->middleware("permission:universal_module_{$prefix}-create")->only(['create', 'store']);
        $this->middleware("permission:universal_module_{$prefix}-edit")->only(['edit', 'update']);
        $this->middleware("permission:universal_module_{$prefix}-delete")->only('delete');
    }


    /**
     * Show list of added items.
     *
     * @param string $prefix
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(string $prefix, Request $request)
    {
        $module = SingletonEnum::universalModules()->findOrFail($prefix);
        $this->setTitleDescription(
            $module->getName(),
            $module->getDescription() ?? trans('admin/universal_modules.descriptions.index')
        );

        $language = SingletonEnum::languagesCollection()->getContentLanguage();
        $table = new UniversalModuleTable($module, $language, $this->getUser());

        return $table->toResponse($request, 'admin.universalmodule.index', compact('prefix'));
    }


    /**
     * GET: Show form for add new item.
     *
     * @param string $prefix
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function create(string $prefix)
    {
        $module = SingletonEnum::universalModules()->findOrFail($prefix);

        $this->setTitleDescription($module->getName(), trans('admin/universal_modules.descriptions.create'));

        $moduleData = new UniversalModuleItem([
            'prefix' => $prefix,
            'content' => ['_' => null],
            'enabled' => true,
            'seo_index' => true,
            'seo_follow' => true,
            'seo_sitemap' => true
        ]);

        if ($module->isAllowedOrdering()) {
            $language = SingletonEnum::languagesCollection()->getContentLanguage();
            $moduleData->order = 1 + (UniversalModuleItem::getAllQuery($prefix, $language)->max('order') ?? 0);
        }

        $form = new UniversalModuleForm($prefix, $module, $moduleData);
        return $form->getView();
    }


    /**
     * POST: Store module in database.
     *
     * @param string $prefix
     * @param \App\Http\Requests\Admin\UniversalModuleRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(string $prefix, UniversalModuleRequest $request)
    {
        $language = SingletonEnum::languagesCollection()->getContentLanguage();
        $module = SingletonEnum::universalModules()->findOrFail($prefix);

        $item = new UniversalModuleItem(['prefix' => $prefix]);
        $item->fill($request->getValues());

        if ($module->isMultilangualApart()) {
            $item->language_id = $language->getKey();
        }

        $item->setContent($request->getContentValues(), $language);
        $item->save();

        flash(trans('admin/universal_modules.notifications.created'), 'success');
        return response()->json(['redirect' => route('admin.universalmodule.index', $prefix)]);
    }


    /**
     * GET: Show form for edit.
     *
     * @param string $prefix
     * @param \App\Models\UniversalModule\UniversalModuleItem $item
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function edit(string $prefix, UniversalModuleItem $item)
    {
        $module = SingletonEnum::universalModules()->findOrFail($prefix);
        $this->setTitleDescription($module->getName(), trans('admin/universal_modules.descriptions.edit'));

        $form = new UniversalModuleForm($prefix, $module, $item);
        return $form->getView();
    }


    /**
     * POST: Update module in database.
     *
     * @param string $prefix
     * @param \App\Models\UniversalModule\UniversalModuleItem $item
     * @param \App\Http\Requests\Admin\UniversalModuleRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(string $prefix, UniversalModuleItem $item, UniversalModuleRequest $request)
    {
        $item->setContent($request->getContentValues(), SingletonEnum::languagesCollection()->getContentLanguage());
        $item->fill($request->getValues());
        $item->save();

        flash(trans('admin/universal_modules.notifications.updated'), 'success');
        return response()->json(['redirect' => route('admin.universalmodule.index', $prefix)]);

    }


    /**
     * DELETE: delete module
     *
     * @param string $prefix
     * @param \App\Models\UniversalModule\UniversalModuleItem $item
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function delete(string $prefix, UniversalModuleItem $item)
    {
        SingletonEnum::universalModules()->findOrFail($prefix);

        $item->delete();

        flash(trans('admin/universal_modules.notifications.deleted'), 'success');
        return $this->refresh();
    }


    /**
     * POST: toggle item.
     *
     * @param string $prefix
     * @param \App\Models\UniversalModule\UniversalModuleItem $item
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function toggle(string $prefix, UniversalModuleItem $item)
    {
        SingletonEnum::universalModules()->findOrFail($prefix);

        $item->toggle()->save();
        flash(
            trans('admin/universal_modules.notifications.' . ($item->enabled ? 'enabled' : 'disabled')),
            'success'
        );
        return $this->refresh();
    }


    /**
     * Show form for new module in the grid editor.
     *
     * @param string $prefix
     * @return \Illuminate\View\View
     */
    public function showCreateForm(string $prefix)
    {
        SingletonEnum::universalModules()->findOrFail($prefix);
        return $this->getGridEditorForm(new UniversalModuleEntity([
            'prefix' => $prefix
        ]));
    }


    /**
     * Show form for updating module in the grid editor.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function showEditForm($id)
    {
        return $this->getGridEditorForm(UniversalModuleEntity::findOrFail($id));
    }


    /**
     * Prepare and return form for grid editor.
     *
     * @param \App\Models\UniversalModule\UniversalModuleEntity $entity
     * @return \Illuminate\View\View
     */
    private function getGridEditorForm(UniversalModuleEntity $entity)
    {
        $language = SingletonEnum::languagesCollection()->getContentLanguage();
        $model = $entity->getFormAttributes(['view', 'all_items', 'prefix']);
        $model['items'] = collect([]);

        if (!$entity->exists) {
            $model['all_items'] = true;
        } elseif (!$entity->all_items) {
            $model['items'] = $entity->items->pluck('id');
        }

        $items = UniversalModuleItem::getAll($entity->prefix, $language)->map(
            static function (UniversalModuleItem $item) use ($language): array {
                return [
                    'id' => $item->getKey(),
                    'name' => $item->getName($language)
                ];
            }
        );

        $views = Functions::associativeArrayToSequentialArray(
            ViewHelper::getDemarcatedViews('universal_modules.' . $entity->prefix),
            'key',
            'label',
            'children'
        );

        return view(
            'admin.universalmodule.grideditor',
            compact('views', 'items', 'model', 'entity', 'language')
        );
    }


    /**
     * Validate form data and return module preview.
     *
     * @param \App\Http\Requests\Admin\UniversalModuleEntityRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateAndPreview(UniversalModuleEntityRequest $request)
    {
        $module = SingletonEnum::universalModules()->findOrFail($request->getModuleName());
        $items = $request->shouldHaveItems() ? UniversalModuleItem::findOrFail($request->getItems()) : null;
        return response()->json([
            'content' => $this->generatePreview($module, $items)
        ]);
    }


    /**
     * Load previews of specified universal modules.
     *
     * @param Request $request
     *
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function loadPreviews(Request $request)
    {
        if (!$idsInput = $request->input('ids')) {
            return [];
        }

        $ids = json_decode($idsInput);
        if (!$ids) {
            return [];
        }

        $modules = [];
        /** @var \App\Models\UniversalModule\UniversalModuleEntity[] $entities */
        $entities = UniversalModuleEntity::query()->whereIn('id', $ids)->get();

        foreach ($entities as $entity) {
            if (!SingletonEnum::universalModules()->has($entity->prefix)) {
                continue;
            }

            $module = SingletonEnum::universalModules()->findOrFail($entity->prefix);
            $modules[] = [
                'id' => $entity->getKey(),
                'name' => $entity->prefix,
                'title' => $module->getName(),
                'icon' => $module->getIcon(),
                'content' => $this->generatePreview($module, $entity->all_items ? null : $entity->items)
            ];
        }

        return response()->json([
            'modules' => $modules
        ]);
    }


    /**
     * Generate preview for a module
     *
     * @param \App\Services\UniversalModules\UniversalModule $module
     * @param \App\Models\UniversalModule\UniversalModuleItem[]|\Illuminate\Database\Eloquent\Collection $items
     * @return string
     */
    protected function generatePreview(UniversalModule $module, ?Collection $items)
    {
        $language = SingletonEnum::languagesCollection()->getContentLanguage();
        $html = '<strong>' . $module->getName() . '</strong>';

        if (is_null($items)) {
            return "$html - " . trans('admin/universal_modules.grid_editor_preview_all_records');
        }

        $html .= '<ul>';
        foreach ($items as $item) {
            $html .= '<li>' . array_first($item->getContent($language)) . '</li>';
        }
        $html .= '</ul>';

        return $html;
    }
}
