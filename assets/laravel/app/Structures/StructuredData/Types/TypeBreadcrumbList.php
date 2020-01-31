<?php

namespace App\Structures\StructuredData\Types;


class TypeBreadcrumbList extends AbstractType
{
    /**
     * Type structure.
     *
     * @var array
     */
    protected $fillable = [
        'itemListElement', 'itemListOrder', 'numberOfItems'
    ];

    /**
     * Required parameters of a type to be valid.
     *
     * @var array
     */
    protected $required = [
        'itemListElement'
    ];
}
