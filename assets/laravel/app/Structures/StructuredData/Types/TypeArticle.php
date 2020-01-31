<?php

namespace App\Structures\StructuredData\Types;


class TypeArticle extends AbstractType
{
    /**
     * Type structure.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'url', 'description', 'image', 'thumbnailUrl', 'text', 'review', 'publisher', 'keywords',
        'inLanguage', 'dateCreated', 'dateModified', 'datePublished', 'expires', 'author', 'aggregateRating',
        'articleBody', 'articleSection', 'pageEnd', 'pageStart', 'pagination', 'mainEntityOfPage', 'headline',
    ];

    /**
     * Required parameters of a type to be valid.
     *
     * @var array
     */
    protected $required = [
        'image', 'publisher', 'dateModified', 'datePublished', 'author', 'mainEntityOfPage', 'headline'
    ];
}
