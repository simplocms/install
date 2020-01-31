<?php

namespace App\Providers;

use App\Services\UniversalModules\Collector;
use App\Structures\Enums\SingletonEnum;
use Illuminate\Support\ServiceProvider;

class UniversalModulesProvider extends ServiceProvider
{
    /**
     * Register the categories collector.
     */
    public function register()
    {
        $this->app->singleton(SingletonEnum::UNIVERSAL_MODULES, function () {
            return new Collector;
        });
    }
}
