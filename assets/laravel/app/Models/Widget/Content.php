<?php

namespace App\Models\Widget;

use App\Traits\AdvancedEloquentTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Interfaces\IsGridEditorContent;
use App\Traits\GridEditor\GridEditorContent;
use App\Traits\HasLanguage;

class Content extends Model implements IsGridEditorContent
{
    use AdvancedEloquentTrait, SoftDeletes, GridEditorContent, HasLanguage;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'widget_contents';

    /**
     * Mass assignable attributes.
     *
     * @var array
     */
    protected $fillable = ['content'];

    /**
     * Does grid editor creates new version fro every change?
     *
     * @return bool
     */
    protected function createsVersions(): bool
    {
        return false;
    }
}
