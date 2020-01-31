<?php

namespace App\Providers;

use App\Helpers\UrlFactory;
use App\Models\Article\Article;
use App\Models\Article\Category;
use App\Models\Media\File;
use App\Models\Page\Page;
use App\Models\Photogallery\Photogallery;
use App\Models\UniversalModule\UniversalModuleItem;
use App\Models\Web\Language;
use App\Models\Web\Theme;
use App\Services\ModelPrefetch\ModelPrefetch;
use App\Services\ResponseManager\ResponseManager;
use App\Services\Settings\Settings;
use App\Structures\Collections\LanguagesCollection;
use App\Structures\Enums\SingletonEnum;
use App\Structures\Paginator;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use App\Models\Article\Flag;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		if (!empty($_SERVER['HTTPS'])) {
			\URL::forceScheme('https');
		}

        if (config('app.force_url') !== null) {
            \URL::forceRootUrl(config('app.force_url'));
        }

		if (app()->environment('testing')) {
			\DB::setDefaultConnection('testing');
		}

		try {
			SingletonEnum::theme()->registerSystemConfig();
		} catch (\Exception $e) {
			// fails when database is not migrated
		}

		Article::registerUrlObserver();
		Category::registerUrlObserver();
		Page::registerUrlObserver();
		Photogallery::registerUrlObserver();
		Flag::registerUrlObserver();
		UniversalModuleItem::registerUrlObserver();

		File::registerModelPrefetching();
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		if (!config('app.debug')) {
			config([
				'debugbar.enabled' => false
			]);
		}

		$this->app->singleton(SingletonEnum::THEME, function () {
			return Theme::getDefault();
		});

		$this->app->singleton(SingletonEnum::SETTINGS, function () {
			return new Settings;
		});

        $this->app->singleton(SingletonEnum::LANGUAGES_COLLECTION, function () {
            $collection = new LanguagesCollection();
            $collection->initialize();
            return $collection;
        });

		$this->app->singleton(SingletonEnum::RESPONSE_MANAGER, function () {
			return new ResponseManager;
		});

		$this->app->singleton(ModelPrefetch::class, function (Application $app) {
			return new ModelPrefetch(!$app->runningInConsole());
		});

		$this->app->bind(\Illuminate\Pagination\LengthAwarePaginator::class, function ($app, $attributes) {
			return new Paginator(...array_values($attributes));
		});

		if (!$this->app->environment('production')) {
			$this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
			$this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
			$this->app->register(\Laravel\Dusk\DuskServiceProvider::class);
			$this->app->register(\Laravel\Tinker\TinkerServiceProvider::class);
		}
	}
}
