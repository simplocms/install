<?php

namespace App\Models\Article;

use App\Models\User;
use App\Traits\AdvancedEloquentTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GridEditor\GridEditorContent;
use App\Models\Interfaces\IsGridEditorContent;

class Content extends Model implements IsGridEditorContent
{
    use AdvancedEloquentTrait, SoftDeletes, GridEditorContent;

    /**
     * Model table.
     *
     * @var string
     */
    protected $table = 'article_contents';

    /**
     * Mass assignable attributes.
     *
     * @var array
     */
    protected $fillable = ['content', 'is_active'];

    /**
     * Author of content version.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function author() {
        return $this->hasOne(User::class, 'id', 'author_user_id');
    }


    /**
     * Set current content active.
     */
    public function setActive() {
        self::where('article_id', $this->article_id)
            ->where('id', '<>', $this->id)
            ->update([
                'is_active' => false
            ]);

        $this->update([
            'is_active' => true
        ]);
    }

    /**
     * Get article identifier.
     *
     * @return int
     */
    public function getArticleId(): int
    {
        return (int)$this->getAttribute('article_id');
    }
}
