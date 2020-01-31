<?php

namespace App\Structures\StructuredData\Types;


class TypeListItem extends AbstractType
{
    /**
     * Type structure.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'item', 'position'
    ];

    /**
     * Required parameters of a type to be valid.
     *
     * @var array
     */
    protected $required = [
        'name', 'item', 'position'
    ];
}
