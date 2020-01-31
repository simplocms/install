<?php

namespace App\Components\DataTables;

use App\Models\Entrust\Role;
use App\Models\User;
use App\Structures\DataTable\FilterOptions;
use Illuminate\Support\Collection;

class RolesTable extends AbstractDataTable
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
        $this->createColumn('toggle', trans('admin/roles/general.index.table_columns.toggle'))
            ->setAlign('center')
            ->setWidth(50);

        $this->createColumn('name', trans('admin/roles/general.index.table_columns.name'))
            ->makeSortable();

        $this->createColumn('description', trans('admin/roles/general.index.table_columns.description'));

        $this->setActionsVisibility($this->user->can(['roles-edit', 'roles-delete']));
    }


    /**
     * Get data query.
     *
     * @param \App\Structures\DataTable\FilterOptions $filterOptions
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getDataQuery(FilterOptions $filterOptions)
    {
        $query = Role::query();

        switch ($filterOptions->getSortingColumn()) {
            case 'name':
                $filterOptions->sort('display_name');
                break;
        }

        $filterOptions->searchOnColumns(['name', 'description']);
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
        $query = Role::query();
        $filterOptions->searchOnColumns(['name', 'description']);
        return $query;
    }


    /**
     * Fill table with fetched data.
     *
     * @param \Illuminate\Support\Collection|\App\Models\User[] $roles
     * @return void
     */
    protected function fill(Collection $roles): void
    {
        foreach ($roles as $role) {
            $row = $this->addRow($role->getKey());

            // Data
            $row->setData('enabled', $role->enabled)
                ->setData('toggleUrl', route('admin.roles.toggle', $role->getKey()))
                ->setData('toggleEnabled', !$role->protected && $this->user->can('roles-edit'));

            // Edit
            if (!$role->protected && $this->user->can('roles-edit')) {
                $row->setDoubleClickAction(route('admin.roles.edit', $role->getKey()));
                $row->addControl(
                    trans('admin/roles/general.index.btn_edit'),
                    route('admin.roles.edit', $role->getKey()),
                    'pencil-square-o'
                );
            }

            // Delete
            if (!$role->protected && $this->user->can('roles-delete')) {
                $row->addControl(
                    trans('admin/roles/general.index.btn_delete'),
                    route('admin.roles.delete', $role->getKey()),
                    'trash'
                )->setDelete(trans('admin/roles/general.confirm_delete'));
            }

            // Columns
            $row->addColumn('toggle', $role->enabled ? trans('admin/roles/general.index.title_disable') : trans('admin/roles/general.index.title_enable'));
            $row->addColumn('name', $role->display_name ?? $role->name);
            $row->addColumn('description', $role->description);
        }
    }
}
