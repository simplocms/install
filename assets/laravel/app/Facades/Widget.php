<?php

namespace App\Facades;

use App\Structures\Enums\SingletonEnum;
use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Helpers\WidgetFactory
 */
class Widget extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return SingletonEnum::WIDGET_FACTORY;
    }
}