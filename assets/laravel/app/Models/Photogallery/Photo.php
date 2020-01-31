<?php

namespace App\Models\Photogallery;

use App\Models\Media\File;
use App\Traits\AdvancedEloquentTrait;
use App\Traits\MediaImageTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Photo
 * @package App\Models\Article
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 *
 * @property string|null title
 * @property string|null author
 * @property int image_id
 * @property int sort
 *
 * @property-read \App\Models\Media\File image
 */
class Photo extends Model
{
    use AdvancedEloquentTrait, MediaImageTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = '_undefined_';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'author', 'image_id', 'sort'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'sort' => 'int',
        'image_id' => 'int',
    ];

    /**
     * Relation to image file.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function image(): BelongsTo
    {
        return $this->belongsTo(File::class, 'image_id');
    }


    /**
     * Convert photo to array for photogallery.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->getKey(),
            'title' => $this->title,
            'author' => $this->author,
            'image' => optional($this->image)->toArray() ?? []
        ];
    }


    /**
     * Get image url.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->makeImageLink('image')->getUrl();
    }


    /**
     * Create a new instance of the given model.
     *
     * @param  array  $attributes
     * @param  bool  $exists
     * @return \App\Models\Photogallery\Photo
     */
    public function newInstance($attributes = [], $exists = false)
    {
        /** @var \App\Models\Photogallery\Photo $model */
        $model = parent::newInstance($attributes, $exists);
        $model->setTable($this->getTable());

        return $model;
    }
}
