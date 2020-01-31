<?php

namespace App\Events;

use App\Models\Page\Page;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class PageSaving
{
    use Dispatchable, SerializesModels;

    /**
     * @var \App\Models\Page\Page
     */
    public $page;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Page\Page $page
     *
     * @return void
     */
    public function __construct(Page $page)
    {
        $this->page = $page;
    }
}
