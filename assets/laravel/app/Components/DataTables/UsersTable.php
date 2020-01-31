<?php

namespace App\Components\DataTables;

use App\Models\User;
use App\Structures\DataTable\FilterOptions;
use Illuminate\Support\Collection;

class UsersTable extends AbstractDataTable
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
        $this->createColumn('toggle', trans('admin/users/general.index.table_columns.toggle'))
            ->setAlign('center')
            ->setWidth(50);

        $this->createColumn('username', trans('admin/users/general.index.table_columns.username'))
            ->makeSortable();

        $this->createColumn('name', trans('admin/users/general.index.table_columns.name'))
            ->makeSortable();

        $this->createColumn('email', trans('admin/users/general.index.table_columns.email'))
            ->makeSortable();

        $this->setActionsVisibility($this->user->can(['users-edit', 'users-delete']));
    }


    /**
     * Get data query.
     *
     * @param \App\Structures\DataTable\FilterOptions $filterOptions
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getDataQuery(FilterOptions $filterOptions)
    {
        $query = User::query();

        switch ($filterOptions->getSortingColumn()) {
            case 'username':
            case 'email':
                $filterOptions->sort();
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
        $query = User::query();
        return $query;
    }


    /**
     * Fill table with fetched data.
     *
     * @param \Illuminate\Support\Collection|\App\Models\User[] $users
     * @return void
     */
    protected function fill(Collection $users): void
    {
        foreach ($users as $user) {
            $row = $this->addRow($user->getKey());

            // Data
            $row->setData('enabled', $user->enabled)
                ->setData('toggleUrl', route('admin.users.toggle', $user->getKey()))
                ->setData('toggleEnabled', !$user->protected && $this->user->can('users-edit'));

            // Edit
            if (!$user->protected && $this->user->can('users-edit')) {
                $row->setDoubleClickAction(route('admin.users.edit', $user->getKey()));
                $row->addControl(
                    trans('admin/users/general.index.btn_edit'),
                    route('admin.users.edit', $user->getKey()),
                    'pencil-square-o'
                );
            }

            // Delete
            if (!$user->protected && $this->user->can('users-delete')) {
                $row->addControl(
                    trans('admin/users/general.index.btn_delete'),
                    route('admin.users.delete', $user->getKey()),
                    'trash'
                )->setDelete(trans('admin/users/general.confirm_delete'));
            }

            // Columns
            $row->addColumn('toggle', $user->enabled ? trans('admin/users/general.index.title_disable') : trans('admin/users/general.index.title_enable'));
            $row->addColumn('username', $user->username);
            $row->addColumn('name', $user->name);
            $row->addColumn('email', $user->email);
        }
    }
}
