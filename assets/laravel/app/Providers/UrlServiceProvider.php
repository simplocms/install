<?php

namespace App\Providers;

use App\Helpers\UrlFactory;
use App\Structures\Enums\SingletonEnum;
use Illuminate\Support\ServiceProvider;

class UrlServiceProvider extends ServiceProvider
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
        $this->app->singleton(SingletonEnum::URL_FACTORY, function ($app) {
            return new UrlFactory;
        });
    }
}
