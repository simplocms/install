<?php

namespace App\Listeners;

use App\Events\PageSaving;
use App\Models\Page\Page;

class HandleSavingPage
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\PageSaving  $event
     * @return void
     */
    public function handle(PageSaving $event)
    {
        $becameHomepage = $event->page->is_homepage && $event->page->isDirty('is_homepage');
        if ($becameHomepage) {
            $previousHomepage = Page::getHomepage($event->page->language_id);

            if ($previousHomepage && $previousHomepage->getKey() !== $event->page->getKey()) {
                $previousHomepage->update([
                    'is_homepage' => 0,
                    'url' => strlen($previousHomepage->url) ? $previousHomepage->url : $previousHomepage->name
                ]);
            }
        }
    }
}
