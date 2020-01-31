<?php

namespace App\Components\DataTables;

use App\Models\User;
use App\Models\Widget\Widget;
use App\Structures\DataTable\FilterOptions;
use Illuminate\Support\Collection;

class WidgetsTable extends AbstractDataTable
{
    /** @var \App\Models\User */
    private $user;

    /**
     * ArticlesTable constructor.
     * @param \App\Models\User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;

        parent::__construct();
    }


    /**
     * Initialize datatable.
     */
    protected function initialize(): void
    {
        $this->createColumn('name', trans('admin/widgets/general.index.table_columns.name'))
            ->makeSortable();

        $this->createColumn('identifier', trans('admin/widgets/general.index.table_columns.id'));

        $this->setActionsVisibility($this->user->can(['widgets-edit', 'widgets-delete']));
    }


    /**
     * Get data query.
     *
     * @param \App\Structures\DataTable\FilterOptions $filterOptions
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getDataQuery(FilterOptions $filterOptions)
    {
        $query = Widget::query();

        switch ($filterOptions->getSortingColumn()) {
            case 'name':
                $filterOptions->sort();
                break;
        }

        $filterOptions->searchOnColumns(['name', 'id']);
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
        $query = Widget::query();
        $filterOptions->searchOnColumns(['name', 'id']);
        return $query;
    }


    /**
     * Fill table with fetched data.
     *
     * @param \Illuminate\Support\Collection|\App\Models\Widget\Widget[] $widgets
     * @return void
     */
    protected function fill(Collection $widgets): void
    {
        foreach ($widgets as $widget) {
            $row = $this->addRow($widget->getKey());

            // Data
            $row->setDoubleClickAction(route('admin.widgets.edit', $widget->getKey()));

            // Edit
            if ($this->user->can('widgets-edit')) {
                $row->addControl(
                    trans('admin/widgets/general.index.btn_edit'),
                    route('admin.widgets.edit', $widget->getKey()),
                    'pencil-square-o'
                );
            }

            // Delete
            if ($this->user->can('widgets-delete')) {
                $row->addControl(
                    trans('admin/widgets/general.index.btn_delete'),
                    route('admin.widgets.delete', $widget->getKey()),
                    'trash'
                )->setDelete(trans('admin/widgets/general.confirm_delete'));
            }

            // Columns
            $row->addColumn('name', $widget->name);
            $row->addColumn('identifier', $widget->id);
        }
    }
}
