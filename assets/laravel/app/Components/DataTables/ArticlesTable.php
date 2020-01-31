<?php

namespace App\Components\DataTables;

use App\Models\Article\Article;
use App\Models\Article\Flag;
use App\Models\User;
use App\Models\Web\Language;
use App\Structures\DataTable\FilterOptions;
use App\Structures\Enums\PublishingStateEnum;
use Illuminate\Support\Collection;

class ArticlesTable extends AbstractDataTable
{
    /** @var \App\Models\Web\Language */
    private $language;

    /** @var \App\Models\User */
    private $user;

    /** @var \App\Models\Article\Flag */
    private $flag;

    /**
     * ArticlesTable constructor.
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
        $this->createColumn('title', trans('admin/article/general.index.table_columns.title'))
            ->makeSortable();

        $this->createColumn('publish_at', trans('admin/article/general.index.table_columns.publish_at'))
            ->makeSortable('datetime');

        $this->createColumn(
            'unpublish_at', trans('admin/article/general.index.table_columns.unpublish_at')
        )->makeSortable('datetime');

        $this->createColumn('author', trans('admin/article/general.index.table_columns.author'));
        $this->createColumn('status', trans('admin/article/general.index.table_columns.status'))
            ->setAlign('center')->setWidth(100);

        $this->setActionsVisibility($this->user->can(['articles-edit', 'articles-delete']));
    }


    /**
     * Get data query.
     *
     * @param \App\Structures\DataTable\FilterOptions $filterOptions
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getDataQuery(FilterOptions $filterOptions)
    {
        $query = Article::query()->whereLanguage($this->language)->whereFlag($this->flag)->with('user');

        switch ($filterOptions->getSortingColumn()) {
            case 'title':
            case 'publish_at':
            case 'unpublish_at':
                $filterOptions->sort();
                break;
        }

        $filterOptions->searchOnColumns('title');
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
        $query = Article::query()->whereLanguage($this->language)->whereFlag($this->flag);
        $filterOptions->searchOnColumns('title');
        return $query;
    }


    /**
     * Fill table with fetched data.
     *
     * @param \Illuminate\Support\Collection|\App\Models\Article\Article[] $articles
     * @return void
     */
    protected function fill(Collection $articles): void
    {
        foreach ($articles as $article) {
            $row = $this->addRow($article->getKey());
            $routeParams = [
                'flag' => $this->flag->url,
                'category' => $article->getKey()
            ];

            // Edit
            if ($this->user->can('articles-edit')) {
                $row->setDoubleClickAction(route('admin.articles.edit', $routeParams));
                $row->addControl(
                    trans('admin/article/general.index.btn_edit'),
                    route('admin.articles.edit', $routeParams),
                    'pencil-square-o'
                );
            }

            // Preview
            $row->addControl(trans('admin/article/general.index.btn_preview'), $article->full_url, 'eye')
                ->setTarget('_blank');

            // Duplicate
            if ($this->user->can('articles-create')) {
                $row->addControl(
                    trans('admin/article/general.index.btn_duplicate'),
                    route('admin.articles.duplicate', $routeParams),
                    'files-o'
                )->setAutomaticPost();
            }

            // Delete
            if ($this->user->can('articles-delete')) {
                $row->addControl(
                    trans('admin/article/general.index.btn_delete'),
                    route('admin.articles.delete', $routeParams),
                    'trash'
                )->setDelete(trans('admin/article/general.confirm_delete'));
            }

            // Columns
            $row->addColumn('title', $article->title);

            $row->addColumn(
                'publish_at',
                $article->publish_at ? $article->publish_at->format('j.n.Y H:i') : null
            );
            $row->addColumn(
                'unpublish_at',
                $article->unpublish_at ?  $article->unpublish_at->format('j.n.Y H:i') : null
            );

            $statusText = trans('admin/general.publishing_states.concept');
            $statusColor = 'primary';
            if ($article->state === PublishingStateEnum::PUBLISHED) {
                $statusText = trans('admin/general.publishing_states.published');
                $statusColor = $article->isPublic() ? 'success' : 'warning';
            } elseif ($article->state === PublishingStateEnum::UNPUBLISHED) {
                $statusText = trans('admin/general.publishing_states.unpublished');
                $statusColor = 'danger';
            }

            $row->setData('statusColor', $statusColor)
                ->addColumn('status', $statusText);

            $row->addColumn('author', $article->user->name);
        }
    }
}
