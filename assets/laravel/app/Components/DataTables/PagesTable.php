<?php

namespace App\Components\DataTables;

use App\Models\Page\Page;
use App\Models\User;
use App\Models\Web\Language;
use App\Structures\DataTable\FilterOptions;
use Illuminate\Support\Collection;

class PagesTable extends AbstractDataTable
{
    /** @var \App\Models\Web\Language */
    private $language;

    /** @var \App\Models\User */
    private $user;

    /**
     * PagesTable constructor.
     * @param \App\Models\Web\Language $language
     * @param \App\Models\User $user
     */
    public function __construct(Language $language, User $user)
    {
        $this->language = $language;
        $this->user = $user;

        parent::__construct();
    }


    /**
     * Initialize datatable.
     */
    protected function initialize(): void
    {
        $this->createColumn('name', trans('admin/pages/general.index.table_columns.name'))
            ->makeSortable();

        $this->createColumn('publish_at', trans('admin/pages/general.index.table_columns.publish_at'))
            ->makeSortable('datetime');

        $this->createColumn('unpublish_at', trans('admin/pages/general.index.table_columns.unpublish_at'))
            ->makeSortable('datetime');

        $this->createColumn('status', trans('admin/pages/general.index.table_columns.status'))
            ->setAlign('center')->setWidth(100);

        $this->setActionsVisibility($this->user->can(['pages-edit', 'pages-delete']));
    }


    /**
     * Get data query.
     *
     * @param \App\Structures\DataTable\FilterOptions $filterOptions
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getDataQuery(FilterOptions $filterOptions)
    {
        $query = Page::query()->whereLanguage($this->language);

        switch ($filterOptions->getSortingColumn()) {
            case 'name':
            case 'publish_at':
            case 'unpublish_at':
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
        $query = Page::query()->whereLanguage($this->language);
        $filterOptions->searchOnColumns('name');
        return $query;
    }


    /**
     * Fill table with fetched data.
     *
     * @param \Illuminate\Support\Collection|\App\Models\Page\Page[] $pages
     * @return void
     */
    protected function fill(Collection $pages): void
    {
        $pages->each(function (Page $page) {
            $this->addPageRow($page, 0);
        });
    }


    /**
     * Add row to table for given page.
     *
     * @param \App\Models\Page\Page $page
     * @param int $level
     */
    private function addPageRow(Page $page, int $level): void
    {
        $isSearching = $this->configuration->getFilterOptions()->isSearching();

        // Setup
        $row = $this->addRow($page->getKey());
        $row->setData('statusColor', $page->isPublic() ? 'success' : 'danger');

        // Edit
        if ($this->user->can('pages-edit')) {
            $row->setDoubleClickAction(route('admin.pages.edit', $page->getKey()));
            $row->addControl(
                trans('admin/pages/general.index.btn_edit'),
                route('admin.pages.edit', $page->getKey()),
                'pencil-square-o'
            );
        }

        // Preview
        $row->addControl(trans('admin/pages/general.index.btn_preview'), $page->full_url, 'eye')
            ->setTarget('_blank');

        // Duplicate
        if ($this->user->can('pages-create')) {
            $row->addControl(
                trans('admin/pages/general.index.btn_duplicate'),
                route('admin.pages.duplicate', $page->getKey()),
                'files-o'
            )->setAutomaticPost();
        }

        // A/B testing
        if ($this->user->can('pages-create') && $this->user->can('pages-edit')) {
            if (!$page->hasTestingCounterpart()) {
                $row->addControl(
                    trans('admin/pages/general.index.btn_ab_test_start'),
                    route('admin.pages.make_ab_test', $page->getKey()),
                    'code-fork'
                )->setAutomaticPost();
            } else {
                $row->addControl(
                    trans('admin/pages/general.index.btn_ab_test_stop'),
                    route('admin.pages.stop_ab_test', $page->getKey()),
                    'code-fork'
                )->setEventEmitter('stop-testing');
            }
        }


        // Delete
        if ($this->user->can('pages-delete')) {
            $row->addControl(
                trans('admin/pages/general.index.btn_delete'),
                route('admin.pages.delete', $page->getKey()),
                'trash'
            )->setDelete(trans('admin/pages/general.confirm_delete'));
        }

        // Columns
        $icon = $page->is_homepage ? '<i class="fa fa-home"></i> ' : '';
        if ($level) {
            $icon = '<span style="padding-left: ' . (10 * $level) . 'px">⤷</span> ' . $icon;
        }

        $name = $icon . htmlentities($page->name);

        if ($page->hasTestingCounterpart()) {
            $name .= ' <span class="label label-primary label-roundless">A/B</span>';
        }

        $row->addColumn('name', $name)->doNotEscape();

        $row->addColumn('publish_at', $page->publish_at->format('j.n.Y H:i'));
        $row->addColumn(
            'unpublish_at',
            $page->unpublish_at ? $page->unpublish_at->format('j.n.Y H:i') : null
        );
        $row->addColumn(
            'status',
            $page->isPublic() ? trans('admin/pages/general.status.published') :
                trans('admin/pages/general.status.unpublished')
        );

        if (!$isSearching) {
            $page->children->each(function (Page $page) use ($level) {
                $this->addPageRow($page, $level + 1);
            });
        }
    }
}
