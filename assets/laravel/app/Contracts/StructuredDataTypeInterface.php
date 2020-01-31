<?php

namespace App\Contracts;

/**
 * Interface StructuredDataType
 * @package App\Contracts
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 */
interface StructuredDataTypeInterface
{
    /**
     * Get properties of the type.
     *
     * @return array
     */
    public function getProperties(): array;

    /**
     * Check if type has valid structure with given properties.
     *
     * @param array $properties
     * @return bool
     */
    public function isValid(array $properties = null): bool;

    /**
     * Get attribute value.
     *
     * @param string $key
     * @return mixed
     */
    public function getAttribute(string $key);

    /**
     * Set attribute.
     *
     * @param string $key
     * @param $value
     * @return $this
     */
    public function setAttribute(string $key, $value);

    /**
     * Set context.
     *
     * @param null|string $context
     * @return $this
     */
    public function setContext(?string $context);
}
