<?php

namespace App\Structures\StructuredData\Types;


class TypeMediaObject extends AbstractType
{
    /**
     * Type structure.
     *
     * @var array
     */
    protected $fillable = [
        'url', 'uploadDate', 'name', 'encodingFormat', 'contentSize', 'description'
    ];

    /**
     * Required parameters of a type to be valid.
     *
     * @var array
     */
    protected $required = [
        'url'
    ];
}
