<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \App\Events\LanguageSaving::class => [
            \App\Listeners\HandleSavingLanguage::class
        ],
        \App\Events\PageSaving::class => [
            \App\Listeners\HandleSavingPage::class
        ],
        \App\Events\SettingRecordSaved::class => [
            \App\Listeners\HandleFaviconSettingChange::class
        ],
        \App\Events\SettingRecordDeleted::class => [
            \App\Listeners\HandleFaviconSettingChange::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        \App\Models\Web\Language::saving(function (\App\Models\Web\Language $language) {
            event(new \App\Events\LanguageSaving($language));
        });
    }
}
