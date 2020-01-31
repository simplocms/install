<?php

namespace App\Structures\DataTable;

class FilterOptions implements \JsonSerializable
{
    /** @var string */
    private $direction;

    /** @var string */
    private $column;

    /** @var string */
    private $searchValue;

    /** @var string */
    private $appliedSortingColumn;

    /** @var string[] */
    private $appliedSearchColumns;

    /**
     * DataTable sort options constructor.
     *
     * @param string|null $column
     * @param string $direction
     * @param null|string $searchValue
     */
    public function __construct(?string $column = null, string $direction = 'asc', ?string $searchValue = null)
    {
        $this->column = $column;
        $this->direction = $direction;
        $this->searchValue = $searchValue;
    }


    /**
     * Get name of the sorted column.
     *
     * @return string|null
     */
    public function getSortingColumn(): ?string
    {
        return $this->column;
    }


    /**
     * Get sorting direction. Should be "asc" or "desc".
     *
     * @return string
     */
    public function getSortingDirection(): string
    {
        return $this->direction;
    }


    /**
     * Is searching?
     *
     * @return bool
     */
    public function isSearching(): bool
    {
        return !is_null($this->searchValue);
    }


    /**
     * Get search value.
     *
     * @return string
     */
    public function getSearchValue(): string
    {
        return $this->searchValue;
    }


    /**
     * Sort by column.
     *
     * @param string|null $column
     */
    public function sort(string $column = null): void
    {
        $this->appliedSortingColumn = $column ?? $this->column;
    }


    /**
     * Search on columns.
     *
     * @param string|string[] $columns
     */
    public function searchOnColumns($columns): void
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        $this->appliedSearchColumns = $columns;
    }


    /**
     * Apply filters on query.
     *
     * @internal This method is used internally and there is no need to call it manually.
     * @param \Illuminate\Database\Query\Builder $query
     */
    public function applyOnQuery($query): void
    {
        if (!is_null($this->searchValue) && !is_null($this->appliedSearchColumns)) {
            $query->where(function ($query) {
                /** @var \Illuminate\Database\Query\Builder $query */
                $term = preg_replace('%([_\%]+)%', '\\\\$1', $this->searchValue);
                foreach ($this->appliedSearchColumns as $column) {
                    $query->orWhere($column, 'LIKE', "%$term%");
                }
            });
        }

        if (!is_null($this->column) && !is_null($this->appliedSortingColumn)) {
            $query->orderBy($this->appliedSortingColumn, $this->direction);
        }
    }


    /**
     * Was sorting applied?
     * When column name is null, always returns true.
     *
     * @return bool
     */
    public function wasSortingApplied(): bool
    {
        return is_null($this->column) || !is_null($this->appliedSortingColumn);
    }


    /**
     * Was search applied?
     * When column name is null, always returns true.
     *
     * @return bool
     */
    public function wasSearchApplied(): bool
    {
        return is_null($this->searchValue) || !is_null($this->appliedSearchColumns);
    }


    /**
     * Return sort options array. Returns null when sorting is not set.
     *
     * @return array
     */
    public function toSortOptionsArray(): ?array
    {
        if (is_null($this->column)) {
            return null;
        }

        return [
            'column' => $this->column,
            'direction' => $this->direction,
        ];
    }


    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->toSortOptionsArray();
    }
}
