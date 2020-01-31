<?php

namespace App\Components\DataTables;

use App\Models\Article\Category;
use App\Models\Article\Flag;
use App\Models\User;
use App\Models\Web\Language;
use App\Structures\DataTable\FilterOptions;
use Illuminate\Support\Collection;

class CategoriesTable extends AbstractDataTable
{
    /** @var \App\Models\Web\Language */
    private $language;

    /** @var \App\Models\User */
    private $user;

    /** @var \App\Models\Article\Flag */
    private $flag;

    /**
     * CategoriesTable constructor.
     * @param \App\Models\Article\Flag $flag
     * @param \App\Models\Web\Language $language
     * @param \App\Models\User $user
     */
    public function __construct(Flag $flag, Language $language, User $user)
    {
        $this->language = $language;
        $this->user = $user;
        $this->flag = $flag;

        parent::__construct();
    }


    /**
     * Initialize datatable.
     */
    protected function initialize(): void
    {
        $this->createColumn('name', trans('admin/category/general.index.table_columns.name'))
            ->makeSortable();

        $this->createColumn('status', trans('admin/category/general.index.table_columns.status'))
            ->setAlign('center')->setWidth(100);

        $this->createColumn('created_at', trans('admin/category/general.index.table_columns.created'))
            ->makeSortable('datetime');

        $this->createColumn('author', trans('admin/category/general.index.table_columns.author'));

        $this->setActionsVisibility($this->user->can(['article-categories-edit', 'article-categories-delete']));
    }


    /**
     * Get data query.
     *
     * @param \App\Structures\DataTable\FilterOptions $filterOptions
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getDataQuery(FilterOptions $filterOptions)
    {
        $query = Category::query()->whereLanguage($this->language)
            ->whereFlag($this->flag)
            ->with('author');

        switch ($filterOptions->getSortingColumn()) {
            case 'name':
            case 'created_at':
                $filterOptions->sort();
                break;
        }

        // For searching we search all rows (including children)
        if (!$filterOptions->isSearching()) {
            $query->with('children')->whereNull('parent_id');
        }

        $filterOptions->searchOnColumns('name');
        return $query;
    }


    /**
     * Use database query to limit and offset results?
     *
     * @return bool
     */
    public function limitAndOffsetUsingQuery(): bool
    {
        return $this->configuration->getFilterOptions()->isSearching();
    }


    /**
     * Get query for total count of rows.
     *
     * @param \App\Structures\DataTable\FilterOptions $filterOptions
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getCountQuery(FilterOptions $filterOptions)
    {
        $query = Category::query()->whereLanguage($this->language)->whereFlag($this->flag);
        $filterOptions->searchOnColumns('name');
        return $query;
    }


    /**
     * Fill table with fetched data.
     *
     * @param \Illuminate\Support\Collection|\App\Models\Article\Category[] $categories
     * @return void
     */
    protected function fill(Collection $categories): void
    {
        $categories->each(function (Category $category) {
            $this->addCategoryRow($category, 0);
        });
    }


    /**
     * Add row to table for given page.
     *
     * @param \App\Models\Article\Category $category
     * @param int $level
     */
    private function addCategoryRow(Category $category, int $level): void
    {
        $isSearching = $this->configuration->getFilterOptions()->isSearching();
        $routeParams = [
            'flag' => $this->flag->url,
            'category' => $category->getKey()
        ];

        // Setup
        $row = $this->addRow($category->getKey());
        $row->setData('statusColor', $category->isPublic() ? 'success' : 'danger');

        // Edit
        if ($this->user->can('article-categories-edit')) {
            $row->setDoubleClickAction(route('admin.categories.edit', $routeParams));
            $row->addControl(
                trans('admin/category/general.index.btn_edit'),
                route('admin.categories.edit', $routeParams),
                'pencil-square-o'
            );
        }

        // Preview
        $row->addControl(trans('admin/category/general.index.btn_preview'), $category->full_url, 'eye')
            ->setTarget('_blank');

        // Delete
        if ($this->user->can('article-categories-delete')) {
            $row->addControl(
                trans('admin/category/general.index.btn_delete'),
                route('admin.categories.delete', $routeParams),
                'trash'
            )->setDelete(trans('admin/category/general.confirm_delete'));
        }

        // Columns
        $icon = $level ? '<span style="padding-left: ' . (10 * $level) . 'px">⤷</span> ' : '';

        $row->addColumn('name', $icon . htmlentities($category->name))->doNotEscape();

        $row->addColumn(
            'status',
            $category->isPublic() ? trans('admin/category/general.status.published') :
                trans('admin/category/general.status.unpublished')
        );

        $row->addColumn('created_at', $category->created_at->format('j.n.Y H:i'));

        $row->addColumn('author', $category->author->name);

        if (!$isSearching) {
            $category->children->each(function (Category $category) use ($level) {
                $this->addCategoryRow($category, $level + 1);
            });
        }
    }
}
