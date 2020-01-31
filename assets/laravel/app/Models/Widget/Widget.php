<?php

namespace App\Models\Widget;

use App\Models\User;
use App\Traits\AdvancedEloquentTrait;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GridEditor\GridEditorModel;
use App\Models\Interfaces\UsesGridEditor;

class Widget extends Model implements UsesGridEditor
{
    use AdvancedEloquentTrait, GridEditorModel;

    /**
     * Mass assignable attributes.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name'
    ];

    /**
     * Disable auto-incrementing identifier.
     * 
     * @var bool
     */
    public $incrementing = false;


    /**
     * Contents (versions).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Content::class, 'widget_id', 'id');
    }


    /**
     * Author of content version.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function author() 
    {
        return $this->hasOne(User::class, 'id', 'author_user_id');
    }


    /**
     * Get content for specified language.
     * If is attribute $canCreate set to true, content will be created for recently created widget.
     *
     * @param \App\Models\Web\Language|int $language
     * @param bool $canCreate
     * 
     * @return \App\Models\Interfaces\IsGridEditorContent|null
     */
    public function getLanguageContent($language, $canCreate = false)
    {
        if ($this->cachedContent) {
            return $this->cachedContent;
        }

        if (!$this->wasRecentlyCreated) {
            $this->cachedContent = $this->contents()->whereLanguage($language)->first();
        }

        if (!$this->cachedContent && $canCreate) {
            $this->cachedContent = $this->createNewContent("", [
                'language_id' => is_scalar($language) ? $language : $language->id
            ]);
        }

        return $this->cachedContent;
    }
}
