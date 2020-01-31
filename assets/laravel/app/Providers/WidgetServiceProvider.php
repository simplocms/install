<?php

namespace App\Providers;

use App\Helpers\WidgetFactory;
use App\Structures\Enums\SingletonEnum;
use Illuminate\Support\ServiceProvider;

class WidgetServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SingletonEnum::WIDGET_FACTORY, function () {
            return new WidgetFactory();
        });
    }
}
