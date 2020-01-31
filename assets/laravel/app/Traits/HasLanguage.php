<?php

namespace App\Traits;

use App\Models\Web\Language;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Trait HasLanguage
 * @package App\Traits
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 * @mixin \Illuminate\Database\Eloquent\Model
 *
 * @property int language_id
 * @property-read \App\Models\Web\Language language
 * @method static \Illuminate\Database\Eloquent\Builder whereLanguage(\App\Models\Web\Language $language)
 */
trait HasLanguage
{
    /**
     * Language of the content.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function language(): HasOne
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }


    /**
     * Select only given language mutations.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \App\Models\Web\Language|int $language
     */
    public function scopeWhereLanguage($query, $language)
    {
        $query->where(
            $this->getTable() . ".language_id",
            is_scalar($language) ? $language : $language->id
        );
    }


    /**
     * Set language of the model.
     *
     * @param \App\Models\Web\Language $language
     */
    public function setLanguage(Language $language): void
    {
        $this->language_id = $language->getKey();
        $this->setRelation('language', $language);
    }
}
