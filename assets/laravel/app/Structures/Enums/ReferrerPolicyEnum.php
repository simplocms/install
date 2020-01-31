<?php

namespace App\Structures\Enums;

final class ReferrerPolicyEnum extends AbstractEnum
{
    const DO_NOT_USE = '-';

    const NO_REFERER = 'no-referrer';
    const NO_REFERER_WHEN_DOWNGRADE = 'no-referrer-when-downgrade';
    const ORIGIN = 'origin';
    const ORIGIN_WHEN_CROSS_ORIGIN = 'origin-when-cross-origin';
    const SAME_ORIGIN = 'same-origin';
    const STRICT_ORIGIN = 'strict-origin';
    const STRICT_ORIGIN_WHEN_CROSS_ORIGIN = 'strict-origin-when-cross-origin';
    const UNSAFE_URL = 'unsafe-url';
}
