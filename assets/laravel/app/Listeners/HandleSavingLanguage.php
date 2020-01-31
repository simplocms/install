<?php

namespace App\Listeners;

use App\Events\LanguageSaving;
use App\Models\Web\Language;

class HandleSavingLanguage
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\LanguageSaving  $event
     * @return void
     */
    public function handle(LanguageSaving $event)
    {
        // If user is disabling language which is default, find another enabled language to be default.
        // If there is no another enabled language, prevent from disabling.
        if ($event->language->isDirty('enabled') && 
            !$event->language->getAttribute('enabled') && $event->language->getOriginal('default')
        ) {
            $nextDefaultLanguage = Language::where('default', false)
                ->where('enabled', true)
                ->first();

            if (!$nextDefaultLanguage) {
                $event->language->setAttribute('enabled', true);
                $event->language->setAttribute('default', true);
            } else {
                $event->language->setAttribute('default', false);
                $nextDefaultLanguage->update([
                    'default' => true
                ]);
            }
        }
    }
}
