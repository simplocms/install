<?php

namespace App\Structures\StructuredData\Types;


class TypeImageObject extends AbstractType
{
    /**
     * Type structure.
     *
     * @var array
     */
    protected $fillable = [
        'url', 'uploadDate', 'name', 'encodingFormat', 'contentSize', 'description', 'caption', 'thumbnail'
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
