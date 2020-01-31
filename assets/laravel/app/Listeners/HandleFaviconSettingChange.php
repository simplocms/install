<?php

namespace App\Listeners;

use App\Events\SettingRecordDeleted;
use App\Events\SettingRecordSaved;
use App\Services\FaviconGenerator\FaviconGenerator;
use App\Structures\Enums\SingletonEnum;

class HandleFaviconSettingChange
{
    /**
     * Handle the event.
     *
     * @param \App\Events\SettingRecordSaved|\App\Events\SettingRecordDeleted $event
     * @return void
     */
    public function handle($event)
    {
        if ($event->record->name !== 'favicon') {
            return;
        }

        if ($event instanceof SettingRecordDeleted) {
            FaviconGenerator::clear();
            return;
        }

        if ($event instanceof SettingRecordSaved && !$event->record->isDirty('value')) {
            return;
        }

        $faviconFile = SingletonEnum::settings()->getMediaFile('favicon');
        if ($faviconFile) {
            FaviconGenerator::generate($faviconFile);
        } else {
            FaviconGenerator::clear();
        }
    }
}
