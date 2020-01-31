<?php

namespace App\Components\DataTables;

use App\Models\Photogallery\Photogallery;
use App\Models\User;
use App\Models\Web\Language;
use App\Structures\DataTable\FilterOptions;
use Illuminate\Support\Collection;

class PhotogalleriesTable extends AbstractDataTable
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
        $this->createColumn('title', trans('admin/photogalleries/general.index.table_columns.name'))
            ->makeSortable();

        $this->createColumn(
            'publish_at', trans('admin/photogalleries/general.index.table_columns.publish_at')
        )->makeSortable('datetime');

        $this->createColumn(
            'unpublish_at', trans('admin/photogalleries/general.index.table_columns.unpublish_at')
        )->makeSortable('datetime');

        $this->createColumn('author', trans('admin/photogalleries/general.index.table_columns.author'));
        $this->createColumn('status', trans('admin/photogalleries/general.index.table_columns.status'))
            ->setAlign('center')->setWidth(100);

        $this->setActionsVisibility($this->user->can(['photogalleries-edit', 'photogalleries-delete']));
    }


    /**
     * Get data query.
     *
     * @param \App\Structures\DataTable\FilterOptions $filterOptions
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getDataQuery(FilterOptions $filterOptions)
    {
        $query = Photogallery::query()->whereLanguage($this->language)->with('user');

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
        $query = Photogallery::query()->whereLanguage($this->language);
        $filterOptions->searchOnColumns('title');
        return $query;
    }


    /**
     * Fill table with fetched data.
     *
     * @param \Illuminate\Support\Collection|\App\Models\Photogallery\Photogallery[] $photogalleries
     * @return void
     */
    protected function fill(Collection $photogalleries): void
    {
        foreach ($photogalleries as $photogallery) {
            $row = $this->addRow($photogallery->getKey());

            // Edit
            if ($this->user->can('photogalleries-edit')) {
                $row->setDoubleClickAction(route('admin.photogalleries.edit', $photogallery->getKey()));
                $row->addControl(
                    trans('admin/photogalleries/general.index.btn_edit'),
                    route('admin.photogalleries.edit', $photogallery->getKey()),
                    'pencil-square-o'
                );
            }

            // Preview
            $row->addControl(
                trans('admin/photogalleries/general.index.btn_preview'), $photogallery->full_url, 'eye'
            )->setTarget('_blank');

            // Delete
            if ($this->user->can('photogalleries-delete')) {
                $row->addControl(
                    trans('admin/photogalleries/general.index.btn_delete'),
                    route('admin.photogalleries.delete', $photogallery->getKey()),
                    'trash'
                )->setDelete(trans('admin/photogalleries/general.confirm_delete'));
            }

            // Columns
            $row->addColumn('title', $photogallery->title);

            $row->addColumn(
                'publish_at',
                $photogallery->publish_at ? $photogallery->publish_at->format('j.n.Y H:i') : null
            );

            $row->addColumn(
                'unpublish_at',
                $photogallery->unpublish_at ?  $photogallery->unpublish_at->format('j.n.Y H:i') : null
            );

            $row->setData('statusColor', $photogallery->isPublic() ? 'success' : 'danger')
                ->addColumn(
                'status',
                    $photogallery->isPublic() ? trans('admin/photogalleries/general.status.published') :
                    trans('admin/photogalleries/general.status.unpublished')
            );

            $row->addColumn('author', $photogallery->user->name);
        }
    }
}
