<?php

namespace App\Structures\StructuredData\Types;


class TypePerson extends AbstractType
{
    /**
     * Type structure.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'image', 'familyName', 'email', 'gender', 'givenName', 'jobTitle'
    ];

    /**
     * Required parameters of a type to be valid.
     *
     * @var array
     */
    protected $required = [
        'name'
    ];
}
