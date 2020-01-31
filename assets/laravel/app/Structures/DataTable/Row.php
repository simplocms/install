<?php

namespace App\Structures\DataTable;

class Row implements \JsonSerializable
{
    /** @var string|int */
    private $key;

    /** @var \App\Structures\DataTable\Column[] */
    private $columns;

    /**
     * Key => value array of additional data.
     *
     * @var mixed[]
     */
    protected $data;

    /** @var string */
    private $doubleClickAction;

    /** @var \App\Structures\DataTable\Control[] */
    private $controls;

    /**
     * DataTable row constructor.
     *
     * @param string|int $key
     */
    public function __construct($key)
    {
        $this->key = $key;
        $this->columns = [];
        $this->data = [];
        $this->controls = [];
    }


    /**
     * Add row column.
     *
     * @param string $name
     * @param string|int|callable $content
     *
     * @return \App\Structures\DataTable\RowColumn
     */
    public function addColumn(string $name, $content): RowColumn
    {
        return $this->columns[$name] = new RowColumn($name, $content);
    }


    /**
     * Get row columns.
     *
     * @return \App\Structures\DataTable\RowColumn[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }


    /**
     * Get row column by its name.
     *
     * @param string $name
     * @return \App\Structures\DataTable\RowColumn
     */
    public function getColumn(string $name): RowColumn
    {
        return $this->columns[$name];
    }


    /**
     * Get row identifier.
     *
     * @return int|string
     */
    public function getKey()
    {
        return $this->key;
    }


    /**
     * Set data value.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return \App\Structures\DataTable\Row
     */
    public function setData(string $key, $value): Row
    {
        $this->data[$key] = $value;
        return $this;
    }


    /**
     * Convert row to array.
     * @return mixed[]
     */
    public function toArray(): array
    {
        $columns = [];
        /** @var RowColumn $column */
        foreach ($this->getColumns() as $column) {
            $columns[$column->getName()] = $column->toArray();
        }

        return [
            'key' => $this->getKey(),
            'columns' => $columns,
            'isSelected' => false,
            'data' => $this->data ?: null,
            'controls' => $this->controls,
            'dblAction' => $this->doubleClickAction
        ];
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
     * Add control.
     *
     * @param string $text
     * @param string $url
     * @param string|null $icon
     * @return \App\Structures\DataTable\Control
     */
    public function addControl(string $text, string $url, string $icon = null): Control
    {
        return $this->controls[] = new Control($text, $url, $icon);
    }


    /**
     * Set action for double click on row.
     *
     * @param string $url
     * @return \App\Structures\DataTable\Row
     */
    public function setDoubleClickAction(string $url): Row
    {
        $this->doubleClickAction = $url;
        return $this;
    }
}
