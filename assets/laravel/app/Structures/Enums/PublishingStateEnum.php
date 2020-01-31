<?php

namespace App\Structures\Enums;

final class PublishingStateEnum extends AbstractEnum
{
    const PUBLISHED = 1;
    const UNPUBLISHED = 0;
    const CONCEPT = 2;

    /**
     * Get the enum labels.
     *
     * @return array
     */
    public static function labels(): array
    {
        return [
            self::PUBLISHED => trans('admin/general.publishing_states.published'),
            self::UNPUBLISHED => trans('admin/general.publishing_states.unpublished'),
            self::CONCEPT => trans('admin/general.publishing_states.concept'),
        ];
    }
}
