<?php

namespace App\Structures\StructuredData;

use App\Contracts\ConvertableToStructuredDataInterface;
use App\Contracts\StructuredDataTypeInterface;

/**
 * Class Serializer
 * @package App\Structures\StructuredData
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 */
class Serializer
{
    /**
     * Serializer type
     *
     * @var \App\Contracts\StructuredDataTypeInterface
     */
    protected $model = null;

    /** @var array */
    protected $additionalData;

    /**
     * Create a new Serializer instance
     *
     * @param \App\Contracts\ConvertableToStructuredDataInterface|\App\Contracts\StructuredDataTypeInterface|mixed $model
     * @param array $data
     */
    public function __construct($model, array $data = [])
    {
        if ($model instanceof ConvertableToStructuredDataInterface) {
            $this->model = $model->toStructuredData();
        } elseif ($model instanceof StructuredDataTypeInterface) {
            $this->model = $model;
        }

        $this->additionalData = $data;
    }


    /**
     * Present given data as a JSON-LD object.
     *
     * @param \App\Contracts\ConvertableToStructuredDataInterface|\App\Contracts\StructuredDataTypeInterface|mixed $context
     * @param array $data
     *
     * @return static
     */
    public static function make($context, array $data = [])
    {
        return new static($context, $data);
    }


    /**
     * Return the array of context properties.
     *
     * @return array
     */
    public function getProperties(): array
    {
        if ($this->model && $this->model instanceof StructuredDataTypeInterface) {
            $properties = array_merge($this->model->getProperties(), $this->additionalData);
            if (!$this->model->isValid($properties)) {
                return [];
            }

            return $properties;
        }

        return $this->additionalData;
    }


    /**
     * Generate the JSON-LD script tag.
     *
     * @return string
     */
    public function generate()
    {
        $properties = $this->getProperties();
        return $properties ? "<script type=\"application/ld+json\">" .
            json_encode($properties,JSON_UNESCAPED_UNICODE) . "</script>" : '';
    }


    /**
     * Return script tag.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->generate();
    }
}
