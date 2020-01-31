<?php

namespace App\Components\DataTables;

use App\Models\Article\Flag;
use App\Models\User;
use App\Models\Web\Language;
use App\Structures\DataTable\FilterOptions;
use Illuminate\Support\Collection;

class ArticleFlagsTable extends AbstractDataTable
{
    /** @var \App\Models\Web\Language */
    private $language;

    /** @var \App\Models\User */
    private $user;

    /**
     * ArticlesTable constructor.
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
        $this->createColumn('name', trans('admin/article_flags/general.index.table_columns.name'))
            ->makeSortable();

        $this->createColumn('url', trans('admin/article_flags/general.index.table_columns.url'));
        $this->createColumn('author', trans('admin/article_flags/general.index.table_columns.author'));

        $this->setActionsVisibility($this->user->can(['article-flags-edit', 'article-flags-delete']));
    }


    /**
     * Get data query.
     *
     * @param \App\Structures\DataTable\FilterOptions $filterOptions
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getDataQuery(FilterOptions $filterOptions)
    {
        $query = Flag::query()->whereLanguage($this->language)->with('author');

        switch ($filterOptions->getSortingColumn()) {
            case 'name':
                $filterOptions->sort();
                break;
        }

        $filterOptions->searchOnColumns(['name', 'url']);
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
        $query = Flag::query()->whereLanguage($this->language);
        $filterOptions->searchOnColumns(['name', 'url']);
        return $query;
    }


    /**
     * Fill table with fetched data.
     *
     * @param \Illuminate\Support\Collection|\App\Models\Article\Flag[] $flags
     * @return void
     */
    protected function fill(Collection $flags): void
    {
        foreach ($flags as $flag) {
            $row = $this->addRow($flag->getKey());

            // Edit
            if ($this->user->can('article-flags-edit')) {
                $row->setDoubleClickAction(route('admin.article_flags.edit', $flag->getKey()));
                $row->addControl(
                    trans('admin/article_flags/general.index.btn_edit'),
                    route('admin.article_flags.edit', $flag->getKey()),
                    'pencil-square-o'
                );
            }

            // Delete
            if ($this->user->can('article-flags-delete')) {
                $row->addControl(
                    trans('admin/article_flags/general.index.btn_delete'),
                    route('admin.article_flags.delete', $flag->getKey()),
                    'trash'
                )->setDelete(trans('admin/article_flags/general.confirm_delete'));
            }

            // Columns
            $row->addColumn('name', $flag->name);
            $row->addColumn('url', $flag->url);
            $row->addColumn(
                'author',
                $flag->author->name  ?? trans('admin/article_flags/general.index.default_author')
            );
        }
    }
}
