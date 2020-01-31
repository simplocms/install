<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use App\Models\Web\Language;

class LanguageSaving
{
    use SerializesModels;

    /** @var Language $language */
    public $language;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Language $language)
    {
        $this->language = $language;
    }
}
