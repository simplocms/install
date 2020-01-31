<?php

namespace App\Structures\Enums;

final class XSSProtectionEnum extends AbstractEnum
{
    const DO_NOT_USE = '-';
    const DISABLE = 0;
    const ENABLE = 1;
    const BLOCK_ATTACKS = '1; mode=block';
}
