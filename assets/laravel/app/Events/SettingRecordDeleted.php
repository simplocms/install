<?php

namespace App\Events;

use App\Models\Web\SettingRecord;
use Illuminate\Foundation\Events\Dispatchable;

class SettingRecordDeleted
{
    use Dispatchable;

    /**
     * @var \App\Models\Web\SettingRecord
     */
    public $record;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Web\SettingRecord $record
     */
    public function __construct(SettingRecord $record)
    {
        $this->record = $record;
    }
}
