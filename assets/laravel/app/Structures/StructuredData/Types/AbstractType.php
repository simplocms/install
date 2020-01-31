<?php

namespace App\Structures\StructuredData\Types;

use App\Contracts\ConvertableToStructuredDataInterface;
use App\Contracts\StructuredDataTypeInterface;

abstract class AbstractType implements StructuredDataTypeInterface
{
    /**
     * Content type.
     *
     * @var string
     */
    protected $type;

    /**
     * Type attributes.
     *
     * @var array
     */
    protected $attributes;

    /**
     * Type structure.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * Required parameters of a type to be valid.
     *
     * @var array
     */
    protected $required = [];

    /**
     * Structured data context.
     *
     * @var string
     */
    protected $context = 'http://schema.org';

    /**
     * Create a new context type instance
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        // Set type
        $path = explode('\\', get_class($this));
        $this->type = str_replace('Type', '', end($path));

        // Set attributes
        $this->attributes = $attributes;
    }


    /**
     * Get properties of the type.
     *
     * @return array
     */
    public function getProperties(): array
    {
        $properties = [
            '@type' => $this->type,
        ];

        if (!is_null($this->context)) {
            $properties['@context'] = $this->context;
        }

        foreach ($this->fillable as $key) {
            $value = $this->getAttribute($key);

            if (is_null($value) || is_array($value) && count($value) === 0) {
                continue;
            }

            $properties[$key] = $value;
        }

        return $properties;
    }


    /**
     * Check if type has valid structure with given properties.
     *
     * @param array $properties
     * @return bool
     */
    public function isValid(array $properties = null): bool
    {
        if (is_null($properties)) {
            $properties = $this->getProperties();
        }

        return count(array_only($properties, $this->required)) === count($this->required);
    }


    /**
     * Get attribute value.
     *
     * @param string $key
     * @return mixed
     */
    public function getAttribute(string $key)
    {
        $value = $this->attributes[$key] ?? null;

        if ($this->hasGetMutator($key)) {
            $value = $this->{$this->getMutatorName($key)}();
        }

        if ($value instanceof \DateTime) {
            $value = $value->format(\DateTime::ISO8601);
        } else if ($value instanceof ConvertableToStructuredDataInterface) {
            $value = $value->toStructuredData();
        }

        if ($value instanceof StructuredDataTypeInterface && $value->isValid()) {
            $value->setContext(null);
            $value = $value->getProperties();
        } else if (is_array($value)) {
            $filteredValue = [];
            /** @var \App\Contracts\StructuredDataTypeInterface $dataType */
            foreach ($value as $key => $dataType) {
                if ($dataType->isValid()) {
                    $filteredValue[$key] = $dataType->getProperties();
                }
            }

            $value = $filteredValue ?: null;
        } else if (!is_scalar($value)) {
            $value = null;
        }

        return $value;
    }


    /**
     * Determine if a get mutator exists for an attribute.
     *
     * @param  string $key
     * @return bool
     */
    protected function hasGetMutator(string $key): bool
    {
        return method_exists($this, $this->getMutatorName($key));
    }


    /**
     * Get mutator name for specified key.
     *
     * @param  string $key
     * @return string
     */
    protected function getMutatorName(string $key): string
    {
        $key = ucwords(str_replace(['-', '_'], ' ', $key));
        return 'get' . lcfirst(str_replace(' ', '', $key)) . 'Attribute';
    }


    /**
     * Set attribute.
     *
     * @param string $key
     * @param $value
     * @return $this
     */
    public function setAttribute(string $key, $value)
    {
        $this->attributes[$key] = $value;
        return $this;
    }


    /**
     * Set context.
     *
     * @param null|string $context
     * @return $this
     */
    public function setContext(?string $context)
    {
        $this->context = $context;
        return $this;
    }
}
