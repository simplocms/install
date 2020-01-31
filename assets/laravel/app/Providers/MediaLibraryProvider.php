<?php

namespace App\Providers;

use App\Services\MediaLibrary\MediaLibrary;
use App\Structures\Enums\SingletonEnum;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Schema\Blueprint;

class MediaLibraryProvider extends ServiceProvider
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
        $this->app->singleton(SingletonEnum::MEDIA_LIBRARY, function () {
            return new MediaLibrary('local');
        });

        Blueprint::macro('media', function (string $column) {
            $this->unsignedInteger($column)->nullable();
            $this->foreign($column)->references('id')->on('media_files')
                ->onUpdate('cascade')->onDelete('set null');
        });

        Blueprint::macro('dropMedia', function ($columns) {
            $columns = is_array($columns) ? $columns : func_get_args();

            foreach ($columns as $column) {
                $this->dropForeign([$column]);
            }

            $this->dropColumn($columns);
        });
    }
}
