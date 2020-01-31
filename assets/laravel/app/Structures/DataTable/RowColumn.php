<?php

namespace App\Structures\DataTable;

use App\Helpers\Functions;
use Illuminate\Support\Str;

class RowColumn implements \JsonSerializable
{
    /** @var string */
    private $name;

    /** @var string|int */
    private $content;

    /** @var callable */
    private $contentCallback;

    /** @var bool */
    private $escape;

    /** @var int */
    private $truncate;

    /** @var string|int|\DateTime */
    private $sortValue;

    /** @var string */
    private $fallbackText;

    /** @var bool */
    private $hasManualSortValue;

    /** @var string */
    private $searchValue;

    /** @var boolean */
    private $hasManualSearchValue;

    /**
     * DataTable column of the row constructor.
     *
     * @param string $name
     * @param string|int|callable $content
     */
    public function __construct(string $name, $content)
    {
        $this->name = $name;

        if (!is_string($content) && is_callable($content)) {
            $this->contentCallback = $content;
        } else {
            $this->content = $content;
        }

        $this->hasManualSortValue = false;
        $this->hasManualSearchValue = false;
        $this->escape = true;
        $this->fallbackText = trans('admin/datatable.empty_value');
    }


    /**
     * Set sort value.
     *
     * @param string|int|\DateTime $value
     *
     * @return $this
     */
    public function setSortValue($value): RowColumn
    {
        $this->sortValue = $value;
        $this->hasManualSortValue = true;
        return $this;
    }


    /**
     * Get sort value.
     *
     * @return mixed
     */
    public function getSortValue()
    {
        if ($this->hasManualSortValue) {
            return $this->sortValue;
        }

        return is_null($this->contentCallback) ? $this->content : null;
    }


    /**
     * Get value for searching.
     *
     * @return string
     */
    public function getSearchText(): ?string
    {
        $searchValue = $this->hasManualSearchValue ? $this->searchValue : $this->content;
        return is_string($searchValue) ? Functions::normalizeSearchText($this->content) : null;
    }


    /**
     * Set fallback text.
     *
     * @param string $text
     * @return \App\Structures\DataTable\RowColumn
     */
    public function setFallbackText(string $text): RowColumn
    {
        $this->fallbackText = $text;
        return $this;
    }


    /**
     * Get fallback text.
     *
     * @return string
     */
    public function getFallbackText(): ?string
    {
        return $this->fallbackText;
    }


    /**
     * Get row column name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }


    /**
     * Get row column content
     *
     * @return string
     */
    public function getContent(): string
    {
        if ($this->contentCallback && is_null($this->content)) {
            $this->content = call_user_func($this->contentCallback);
        }

        $content = strval($this->content);

        if (!strlen($content)) {
            return "<em>{$this->fallbackText}</em>";
        }

        if ($this->truncate) {
            $content = Str::limit(strval($content), $this->truncate);
        }

        if ($this->escape) {
            $content = htmlspecialchars($content);
        }

        return $content;
    }


    /**
     * Truncate content.
     *
     * @param int $length
     *
     * @return \App\Structures\DataTable\RowColumn
     */
    public function truncate(int $length): RowColumn
    {
        $this->truncate = $length;
        return $this;
    }


    /**
     * Do not escape content.
     *
     * @return \App\Structures\DataTable\RowColumn
     */
    public function doNotEscape(): RowColumn
    {
        $this->escape = false;
        return $this;
    }


    /**
     * Convert row column to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'content' => $this->getContent(),
            'isEmpty' => !strlen($this->content)
        ];
    }


    /**
     * Set search text.
     *
     * @param string|null $value
     *
     * @return \App\Structures\DataTable\RowColumn
     */
    public function setSearchValue(?string $value): RowColumn
    {
        $this->searchValue = $value;
        $this->hasManualSearchValue = true;
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
}
