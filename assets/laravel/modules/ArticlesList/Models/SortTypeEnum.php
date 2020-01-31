<?php

namespace Modules\ArticlesList\Models;

use App\Structures\Enums\AbstractEnum;

/**
 * @package Modules\ArticlesList\Models
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 */
final class SortTypeEnum extends AbstractEnum
{
    public const TITLE = 0;
    public const PUBLISH_DATE = 1;
    public const RANDOM = 2;

    /**
     * Get the enum labels.
     *
     * @return array
     */
    public static function labels(): array
    {
        return [
            self::TITLE => trans('module-articleslist::admin.sort_types.title'),
            self::PUBLISH_DATE => trans('module-articleslist::admin.sort_types.publish_date'),
            self::RANDOM => trans('module-articleslist::admin.sort_types.random'),
        ];
    }
}
