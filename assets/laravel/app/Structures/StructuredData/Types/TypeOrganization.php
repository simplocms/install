<?php

namespace App\Structures\StructuredData\Types;


class TypeOrganization extends AbstractType
{
    /**
     * Type structure.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'logo', 'email'
    ];

    /**
     * Required parameters of a type to be valid.
     *
     * @var array
     */
    protected $required = [
        'name', 'logo'
    ];
}
