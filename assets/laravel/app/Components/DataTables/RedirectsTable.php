<?php

namespace App\Components\DataTables;

use App\Models\User;
use App\Models\Web\Language;
use App\Models\Web\Redirect;
use App\Structures\DataTable\FilterOptions;
use App\Structures\Enums\SingletonEnum;
use Illuminate\Support\Collection;

class RedirectsTable extends AbstractDataTable
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
        $this->createColumn('from', trans('admin/redirects/general.index.table_columns.source'))
            ->makeSortable();

        $this->createColumn('to', trans('admin/redirects/general.index.table_columns.target'))
            ->makeSortable();

        $this->createColumn('code', trans('admin/redirects/general.index.table_columns.code'));
        $this->createColumn('author', trans('admin/redirects/general.index.table_columns.author'));

        $this->setActionsVisibility($this->user->can(['redirects-edit', 'redirects-delete']));
    }


    /**
     * Get data query.
     *
     * @param \App\Structures\DataTable\FilterOptions $filterOptions
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getDataQuery(FilterOptions $filterOptions)
    {
        $query = Redirect::query()->with('author');

        switch ($filterOptions->getSortingColumn()) {
            case 'from':
            case 'to':
                $filterOptions->sort();
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $filterOptions->searchOnColumns(['from', 'to']);
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
        $query = Redirect::query();
        $filterOptions->searchOnColumns(['from', 'to']);
        return $query;
    }


    /**
     * Fill table with fetched data.
     *
     * @param \Illuminate\Support\Collection|\App\Models\Web\Redirect[] $redirects
     * @return void
     */
    protected function fill(Collection $redirects): void
    {
        $languages = Language::get()->keyBy('language_code');
        $languageByDir = config('admin.language_url.directory');
        $byDir = SingletonEnum::settings()->getInt('language_display', $languageByDir) === $languageByDir;
        $hideDefault = SingletonEnum::settings()->getBoolean('default_language_hidden', false);

        foreach ($redirects as $redirect) {
            $row = $this->addRow($redirect->getKey());

            // Edit
            if ($this->user->can('redirects-edit')) {
                $row->setDoubleClickAction(route('admin.redirects.edit', $redirect->getKey()));
                $row->addControl(
                    trans('admin/redirects/general.index.btn_edit'),
                    route('admin.redirects.edit', $redirect->getKey()),
                    'pencil-square-o'
                );
            }

            // Delete
            if ($this->user->can('redirects-delete')) {
                $row->addControl(
                    trans('admin/redirects/general.index.btn_delete'),
                    route('admin.redirects.delete', $redirect->getKey()),
                    'trash'
                )->setDelete(trans('admin/redirects/general.confirm_delete'));
            }

            $toChunks = explode('/', $redirect->from);
            $language = $languages->get($toChunks[0]);
            if ($language && (!$byDir || $language->default && $hideDefault)) {
                array_shift($toChunks);
            }
            $showFrom = join('/', $toChunks);
            $urlFrom = SingletonEnum::urlFactory()->getAbsoluteUrlFromShortUrl(
                $redirect->from, $language ?? new Language
            );
            $showTo = null;

            if (!$redirect->pointToUrl()) {
                $toChunks = explode('/', $redirect->to);
                $language = $languages->get($toChunks[0]);
                if ($language && (!$byDir || $language->default && $hideDefault)) {
                    array_shift($toChunks);
                }
                $showTo = join('/', $toChunks);
            }

            // Columns
            $row->setData('urlFrom', $urlFrom);
            $row->addColumn('from', $showFrom);
            $row->addColumn('to', $showTo ?? $redirect->to);
            $row->addColumn('code', $redirect->status_code);
            $row->addColumn('author', $redirect->author ?
                $redirect->author ->name :
                trans('admin/redirects/general.index.author_system')
            );
        }
    }
}
