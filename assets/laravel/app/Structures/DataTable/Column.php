<?php

namespace App\Structures\DataTable;

class Column implements \JsonSerializable
{
    /** @var string */
    private $name;

    /** @var string */
    private $label;

    /** @var string */
    private $sortableType;

    /** @var \App\Structures\DataTable\Column[] */
    private $subColumns;

    /** @var string */
    private $align;

    /** @var int */
    private $width;

    /**
     * DataTable column constructor.
     *
     * @param string $name
     * @param string $label
     */
    public function __construct(string $name, string $label)
    {
        $this->name = $name;
        $this->label = $label;
        $this->sortableType = null;
        $this->subColumns = [];
    }


    /**
     * Is column sortable?
     *
     * @return bool
     */
    public function isSortable(): bool
    {
        return !is_null($this->sortableType);
    }


    /**
     * Make column sortable.
     *
     * @param string $sortableType
     * @return \App\Structures\DataTable\Column
     */
    public function makeSortable(string $sortableType = 'string'): Column
    {
        $this->sortableType = $sortableType;
        return $this;
    }


    /**
     * Convert column to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'name' => $this->name,
            'isSortable' => $this->isSortable(),
            'sortableType' => $this->sortableType,
            'subColumns' => array_map(function (Column $column) {
                return $column->toArray();
            }, $this->subColumns),
            'align' => $this->align ?? 'left',
            'width' => $this->width,
        ];
    }


    /**
     * Add sub-column.
     *
     * @param string $name
     * @param string $text
     * @param callable|null $callback
     * @return \App\Structures\DataTable\Column
     */
    public function addSubColumn(string $name, string $text, callable $callback = null): Column
    {
        $column = new Column($name, $text);
        $this->subColumns[] = $column;

        if ($callback) {
            $callback($column);
        }

        return $this;
    }


    /**
     * Specify data which should be serialized to JSON.
     *
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *               which is a value of any type other than a resource.
     *
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }


    /**
     * Set content align.
     *
     * @param string $align
     * @return \App\Structures\DataTable\Column
     */
    public function setAlign(string $align = 'left'): Column
    {
        $this->align = $align;
        return $this;
    }


    /**
     * Set column width.
     *
     * @param int $width
     * @return \App\Structures\DataTable\Column
     */
    public function setWidth(int $width): Column
    {
        $this->width = $width;
        return $this;
    }


    /**
     * Get column name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }


    /**
     * Get sub columns.
     *
     * @return \App\Structures\DataTable\Column[]
     */
    public function getSubColumns(): array
    {
        return $this->subColumns;
    }


    /**
     * Get column name.
     *
     * @return string
     */
    public function getSortableType(): string
    {
        return $this->sortableType;
    }
}
