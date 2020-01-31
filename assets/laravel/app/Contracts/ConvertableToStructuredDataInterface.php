<?php

namespace App\Contracts;

/**
 * Interface ConvertableToStructuredDataInterface
 * @package App\Contracts
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 */
interface ConvertableToStructuredDataInterface
{
    /**
     * Get properties of the type.
     *
     * @return \App\Contracts\StructuredDataTypeInterface
     */
    public function toStructuredData(): StructuredDataTypeInterface;
}
