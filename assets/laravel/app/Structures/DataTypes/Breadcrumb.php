<?php

namespace App\Structures\DataTypes;

use App\Contracts\ConvertableToStructuredDataInterface;
use App\Contracts\StructuredDataTypeInterface;
use App\Structures\StructuredData\Types\TypeListItem;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Breadcrumb
 * @package App\Structures\DataTypes
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 */
class Breadcrumb implements \JsonSerializable, ConvertableToStructuredDataInterface
{
    /**
     * @var string
     */
    private $text;

    /**
     * @var string
     */
    private $url;

    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    private $model;

    /**
     * OpenGraphSettings constructor.
     * @param string $text
     * @param string|null $url
     * @param \Illuminate\Database\Eloquent\Model|null $model
     */
    public function __construct(string $text, ?string $url = null, ?Model $model = null)
    {
        $this->text = $text;
        $this->url = $url;
        $this->model = $model;
    }


    /**
     * Has breadcrumb url?
     *
     * @return bool
     */
    public function hasUrl(): bool
    {
        return !is_null($this->url);
    }


    /**
     * Has breadcrumb url?
     *
     * @return bool
     */
    public function hasModel(): bool
    {
        return !is_null($this->model);
    }


    /**
     * Get instance of the model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }


    /**
     * Get text.
     *
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }


    /**
     * Get url.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
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
        return [
            'name' => $this->text,
            'url' => $this->url
        ];
    }


    /**
     * Get properties of the type.
     *
     * @return \App\Contracts\StructuredDataTypeInterface
     */
    public function toStructuredData(): StructuredDataTypeInterface
    {
        return new TypeListItem([
            'name' => $this->getText(),
            'item' => $this->hasUrl() ? $this->getUrl() : null
        ]);
    }
}
