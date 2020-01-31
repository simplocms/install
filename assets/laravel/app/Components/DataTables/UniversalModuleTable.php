<?php

namespace App\Components\DataTables;

use App\Models\UniversalModule\UniversalModuleItem;
use App\Models\User;
use App\Models\Web\Language;
use App\Services\UniversalModules\UniversalModule;
use App\Structures\DataTable\FilterOptions;
use App\Structures\Enums\SingletonEnum;
use Illuminate\Support\Collection;

class UniversalModuleTable extends AbstractDataTable
{
    /** @var \App\Models\User */
    private $user;

    /** @var \App\Services\UniversalModules\UniversalModule */
    private $module;

    /** @var \App\Models\Web\Language */
    private $language;

    /**
     * ArticlesTable constructor.
     * @param \App\Services\UniversalModules\UniversalModule $module
     * @param \App\Models\Web\Language $language
     * @param \App\Models\User $user
     */
    public function __construct(UniversalModule $module, Language $language, User $user)
    {
        $this->user = $user;
        $this->module = $module;
        $this->language = $language;

        parent::__construct();
    }


    /**
     * Initialize datatable.
     */
    protected function initialize(): void
    {
        if ($this->module->isAllowedToggling()) {
            $this->createColumn('__toggle', trans('admin/universal_modules.index.table_columns.toggle'))
                ->setAlign('center')
                ->setWidth(50);
        }

        foreach ($this->module->getFields() as $field) {
            if (
                ($field instanceof \App\Structures\FormFields\Select && !$field->isMultiple()) ||
                in_array(get_class($field), [
                    \App\Structures\FormFields\Input::class,
                    \App\Structures\FormFields\TextInput::class,
                    \App\Structures\FormFields\NumberInput::class,
                ], true)
            ) {
                $this->createColumn($field->getName(), $field->getLabel() ?? $field->getName());
            }
        }

        if ($this->module->isAllowedOrdering()) {
            $this->createColumn('__order', trans('admin/universal_modules.index.table_columns.order'))
                ->makeSortable('int')
                ->setAlign('center')
                ->setWidth(100);
        }

        $this->setActionsVisibility($this->user->can([
            $this->getPermissionName('edit'),
            $this->getPermissionName('delete')
        ]));
    }


    /**
     * Get data query.
     *
     * @param \App\Structures\DataTable\FilterOptions $filterOptions
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getDataQuery(FilterOptions $filterOptions)
    {
        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $query = UniversalModuleItem::query()->ofPrefix($this->module->getKey());

        // Multilangual apart
        if ($this->module->isMultilangualApart()) {
            $query->where('language_id', $this->language->getKey())->with('language');
        }

        switch ($filterOptions->getSortingColumn()) {
            case '__order':
                $filterOptions->sort('order');
                break;
            default:
                // Ordering
                if ($this->module->isAllowedOrdering()) {
                    $query->orderBy('order');
                }
                break;
        }

        return $query;
    }


    /**
     * Get query for total count of rows.
     *
     * @param \App\Structures\DataTable\FilterOptions $filterOptions
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getCountQuery(FilterOptions $filterOptions)
    {
        $query = UniversalModuleItem::query()->ofPrefix($this->module->getKey());

        // Multilangual apart
        if ($this->module->isMultilangualApart()) {
            $query->where('language_id', $this->language->getKey());
        }

        return $query;
    }


    /**
     * Fill table with fetched data.
     *
     * @param \Illuminate\Support\Collection|\App\Models\UniversalModule\UniversalModuleItem[] $items
     * @return void
     * @throws \Exception
     */
    protected function fill(Collection $items): void
    {
        foreach ($items as $item) {
            $row = $this->addRow($item->getKey());
            $routeParams = [
                'id' => $item->getKey(),
                'prefix' => $this->module->getKey()
            ];

            // Edit
            if ($this->user->can($this->getPermissionName('edit'))) {
                $row->setDoubleClickAction(route('admin.universalmodule.edit', $routeParams));
                $row->addControl(
                    trans('admin/universal_modules.index.btn_edit'),
                    route('admin.universalmodule.edit', $routeParams),
                    'pencil-square-o'
                );
            }

            // Preview
            if ($this->module->hasUrl()) {
                $row->addControl(
                    trans('admin/universal_modules.index.btn_preview'),
                    $item->getFullUrl(),
                    'eye'
                )->setTarget('_blank');
            }

            // Toggling
            if ($this->module->isAllowedToggling()) {
                $row->setData('enabled', $item->enabled)
                    ->setData('toggleUrl', route('admin.universalmodule.toggle', $routeParams))
                    ->setData('toggleEnabled', $this->getPermissionName('edit'));

                $row->addColumn('__toggle', $item->enabled ?
                    trans('admin/universal_modules.index.title_disable') :
                    trans('admin/universal_modules.index.title_enable')
                );
            }

            // Delete
            if ($this->user->can($this->getPermissionName('delete'))) {
                $row->addControl(
                    trans('admin/universal_modules.index.btn_delete'),
                    route('admin.universalmodule.delete', $routeParams),
                    'trash'
                )->setDelete(trans('admin/universal_modules.confirm_delete'));
            }

            // Columns
            foreach ($this->getColumns() as $column) {
                $row->addColumn(
                    $column->getName(),
                    $item->getAttributeOfContent($column->getName(), $this->language)
                );
            }

            if ($this->module->isAllowedOrdering()) {
                $row->addColumn('__order', $item->order ?? 1);
            }
        }
    }


    /**
     * Get name of the permission for current universal module.
     *
     * @param string $permission
     * @return string
     */
    private function getPermissionName(string $permission): string
    {
        return "universal_module_{$this->module->getKey()}-$permission";
    }
}
