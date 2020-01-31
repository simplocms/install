<?php

namespace App\Components\DataTables;

use App\Helpers\Functions;
use App\Structures\DataTable\Column;
use App\Structures\DataTable\Configuration;
use App\Structures\DataTable\Row;
use App\Structures\DataTable\FilterOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

abstract class AbstractDataTable
{
    const DEFAULT_ROW_LIMIT = 15;

    const SORT_TYPE_STRING = 'string';
    const SORT_TYPE_INT = 'int';
    const SORT_TYPE_DATETIME = 'datetime';
    const SORT_TYPE_FLOAT = 'float';

    /**
     * Link to data source.
     * @var string
     */
    protected $dataLink;

    /** @var \App\Structures\DataTable\Configuration */
    protected $configuration;

    /** @var bool */
    private $isSearchEnabled;

    /** @var string */
    private $searchValue;

    /** @var \App\Structures\DataTable\Column[] */
    private $columns;

    /** @var \App\Structures\DataTable\Row[] */
    private $rows;

    /** @var bool */
    private $actionsVisible;

    /** @var int */
    private $totalRows;

    /**
     * Basic constructor.
     */
    public function __construct()
    {
        $this->columns = [];
        $this->isSearchEnabled = true;
        $this->configuration = new Configuration($this->getName());
        $this->initialize();
    }


    /**
     * Initialize datatable.
     */
    abstract protected function initialize(): void;


    /**
     * Get data query.
     *
     * @param \App\Structures\DataTable\FilterOptions $filterOptions
     * @return \Illuminate\Database\Query\Builder
     */
    abstract protected function getDataQuery(FilterOptions $filterOptions);


    /**
     * Fill table with fetched data.
     *
     * @param \Illuminate\Support\Collection $data
     * @return void
     */
    abstract protected function fill(Collection $data): void;


    /**
     * Get query for total count of rows.
     *
     * @param \App\Structures\DataTable\FilterOptions $filterOptions
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getCountQuery(FilterOptions $filterOptions)
    {
        return $this->getDataQuery($filterOptions);
    }


    /**
     * Get query for total count of rows.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function prepareCountQuery()
    {
        $filterOptions = $this->configuration->getFilterOptions();
        $query = $this->getCountQuery($filterOptions);
        $filterOptions->applyOnQuery($query);
        return $query;
    }


    /**
     * Create new column of a datatable.
     *
     * @param string $name
     * @param string|int $text
     *
     * @return \App\Structures\DataTable\Column
     */
    public function createColumn(string $name, string $text): Column
    {
        return $this->columns[$name] = new Column($name, $text);
    }


    /**
     * Get table name.
     *
     * @return string
     */
    public function getName(): string
    {
        return str_replace('\\', '_', get_class($this));
    }


    /**
     * Get view data.
     *
     * @return array
     */
    public function getViewData(): array
    {
        return [
            'table' => $this
        ];
    }


    /**
     * Get link to data source.
     *
     * @return string
     */
    public function getDataLink(): string
    {
        return $this->dataLink ?? \URL::current();
    }


    /**
     * Set link to data source.
     *
     * @param string $url
     *
     * @return self
     */
    public function setDataLink(string $url)
    {
        $this->dataLink = $url;
        return $this;
    }


    /**
     * Get page.
     *
     * @return int
     */
    public function getPage(): int
    {
        return (int)($this->configuration->get('page') ?: 1);
    }


    /**
     * Get page offset.
     *
     * @return int
     */
    public function getRowOffset(): int
    {
        return ($this->getPage() - 1) * $this->getRowLimit();
    }


    /**
     * Get row limit.
     *
     * @return int
     */
    public function getRowLimit(): int
    {
        return (int)($this->configuration->get('row_limit') ?: self::DEFAULT_ROW_LIMIT);
    }


    /**
     * Disable search.
     *
     * @return self
     */
    public function disableSearch()
    {
        $this->isSearchEnabled = false;
        return $this;
    }


    /**
     * Convert datatable to array.
     *
     * @return mixed[]
     */
    public function toArray(): array
    {
        return [
            'dataLink' => $this->getDataLink(),
            'currentPage' => $this->getPage(),
            'sortOptions' => $this->configuration->getFilterOptions()->toSortOptionsArray(),
            'rowLimit' => $this->getRowLimit(),
            'isSearchEnabled' => $this->isSearchEnabled,
            'searchValue' => $this->searchValue,
            'actionsVisible' => $this->actionsVisible,
            'columns' => $this->columns,
            'translations' => trans('admin/datatable')
        ];
    }


    /**
     * Convert table to JSON.
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }


    /**
     * Return rows for current configuration.
     *
     * @return array
     */
    public function getRowsBatch(): array
    {
        $filterOptions = $this->configuration->getFilterOptions();
        $query = $this->getDataQuery($filterOptions);

        // Apply filters on query.
        $filterOptions->applyOnQuery($query);

        $countQuery = $this->prepareCountQuery();

        // Simple batch is acquired directly from query.
        $simpleBatch = $filterOptions->wasSearchApplied() &&
            $filterOptions->wasSortingApplied() &&
            $this->limitAndOffsetUsingQuery();

        if ($simpleBatch) {
            $query->limit($this->getRowLimit())->offset($this->getRowOffset());
        }

        $data = $query->get();
        $this->fill($data);
        $rows = $this->rows ?? [];

        if ($simpleBatch) {
            $this->totalRows = $countQuery->count();
            return $rows;
        } else {
            $this->totalRows = count($rows);
        }

        // Apply search
        if (!$filterOptions->wasSearchApplied()) {
            $rows = $this->searchRows($rows, $filterOptions->getSearchValue());
        }

        // Apply sorting
        if (!$filterOptions->wasSortingApplied()) {
            $rows = $this->sortRows($rows, $filterOptions->getSortingColumn(), $filterOptions->getSortingDirection());
        }

        // Return "paginated" part of rows.
        return array_slice($rows, $this->getRowOffset(), $this->getRowLimit());
    }


    /**
     * Use database query to limit and offset results?
     *
     * @return bool
     */
    public function limitAndOffsetUsingQuery(): bool
    {
        return true;
    }


    /**
     * Search rows.
     *
     * @param \App\Structures\DataTable\Row[] $rows
     * @param string $term
     * @return \App\Structures\DataTable\Row[]
     */
    private function searchRows(array $rows, string $term): array
    {
        $marchingRows = [];
        $term = Functions::normalizeSearchText($term);

        foreach ($rows as $row) {
            foreach ($row->getColumns() as $key => $column) {
                $columnText = $column->getSearchText();
                if (!is_null($columnText) && stristr($columnText, $term) !== false) {
                    $marchingRows[] = $row;
                }
            }
        }

        return $marchingRows;
    }


    /**
     * Sort rows.
     * Quick sort algorithm.
     *
     * @param \App\Structures\DataTable\Row[] $rows
     * @param string $columnName
     * @param string $direction
     * @return \App\Structures\DataTable\Row[]
     */
    private function sortRows(array $rows, string $columnName, string $direction): array
    {
        if (count($rows) <= 1) {
            return $rows;
        }

        $columnMap = $this->getAllColumns();
        $left = $right = [];
        reset($rows);
        $pivotKey = key($rows);
        /** @var \App\Structures\DataTable\Row $pivot */
        $pivot = array_shift($rows);
        $pivotColumn = $pivot->getColumn($columnName);

        foreach ($rows as $key => $row) {
            $rowColumn = $row->getColumns()[$columnName];
            $tableColumn = $columnMap[$columnName];

            if (!$tableColumn->isSortable()) {
                continue;
            }

            $sortType = $tableColumn->getSortableType();
            $diff = null;

            switch ($sortType) {
                case self::SORT_TYPE_STRING:
                    $diff = strcmp($rowColumn->getSortValue(), $pivotColumn->getSortValue());
                    break;
                case self::SORT_TYPE_DATETIME:
                    $diff = Functions::compareDates($rowColumn->getSortValue(), $pivotColumn->getSortValue());
                    break;
                case self::SORT_TYPE_INT:
                    $rowValue = intval($rowColumn->getSortValue());
                    $pivotValue = intval($pivotColumn->getSortValue());
                    $diff = $rowValue > $pivotValue ? 1 : ($rowValue < $pivotValue ? -1 : 0);
                    break;
                case self::SORT_TYPE_FLOAT:
                    $rowValue = floatval($rowColumn->getSortValue());
                    $pivotValue = floatval($pivotColumn->getSortValue());
                    $diff = $rowValue > $pivotValue ? 1 : ($rowValue < $pivotValue ? -1 : 0);
                    break;
            }

            if ($diff < 0 && $direction === 'asc' || $diff > 0 && $direction === 'desc') {
                $left[$key] = $row;
            } else {
                $right[$key] = $row;
            }
        }

        return array_merge(
            $this->sortRows($left, $columnName, $direction),
            [$pivotKey => $pivot],
            $this->sortRows($right, $columnName, $direction)
        );
    }


    /**
     * Get all columns on all levels.
     *
     * @return \App\Structures\DataTable\Column[]
     */
    private function getAllColumns(): array
    {
        $result = [];
        $columns = $this->columns;

        while ($columns) {
            $subColumns = [];

            foreach ($columns as $column) {
                $result[$column->getName()] = $column;
                $subColumns = array_merge($subColumns, $column->getSubColumns());
            }

            $columns = $subColumns;
        }

        return $result;
    }


    /**
     * Render specified view with the table.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $view
     * @param array $data
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function toResponse(Request $request, string $view, array $data = [])
    {
        $this->configuration->getFromRequest($request);

        if ($request->ajax() || $request->wantsJson()) {
            $this->configuration->save();
            return response()->json([
                'rows' => $this->getRowsBatch(),
                'total' => $this->totalRows ?? 0
            ]);
        }

        return view($view, array_merge($data, $this->getViewData()));
    }


    /**
     * Add new row.
     *
     * @param string|int $key
     * @return \App\Structures\DataTable\Row
     */
    protected function addRow($key): Row
    {
        return $this->rows[] = new Row($key);
    }


    /**
     * Set visibility of actions column.
     *
     * @param bool $visibility
     */
    protected function setActionsVisibility(bool $visibility): void
    {
        $this->actionsVisible = $visibility;
    }


    /**
     * Get table columns.
     *
     * @return \App\Structures\DataTable\Column[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }
}
