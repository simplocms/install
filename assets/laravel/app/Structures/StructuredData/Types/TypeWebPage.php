<?php

namespace App\Structures\StructuredData\Types;


class TypeWebPage extends AbstractType
{
    /**
     * Type structure.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description',
        'breadcrumb', 'lastReviewed', 'primaryImageOfPage',  'relatedLink',  'dateCreated',
        'dateModified',  'url',  'datePublished',  'expires',  'headline',  'inLanguage',
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
