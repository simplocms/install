<?php

namespace App\Traits;

use App\Models\Photogallery\Photo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Trait HasPhotogallery
 * @package App\Traits
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 * @mixin \Illuminate\Database\Eloquent\Model
 *
 * @property-read \App\Models\Photogallery\Photo[]|\Illuminate\Database\Eloquent\Collection photos
 */
trait PhotogalleryTrait
{
    /**
     * Relation to photos of the photogallery.
     * Use method 'hasManyPhotos' in this method.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    abstract public function photos(): HasMany;

    /**
     * Relation to photogallery photos.
     *
     * @param string $table
     * @param string|null $foreignKey
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    protected function hasManyPhotos(string $table, string $foreignKey = null)
    {
        $instance = new Photo;
        $instance->setTable($table);

        $foreignKey = $foreignKey ?: $this->getForeignKey();

        return $this->newHasMany(
            $instance->newQuery(), $this, $instance->getTable() . '.' . $foreignKey, $this->getKeyName()
        );
    }


    /**
     * Save photogallery photos.
     *
     * @param \App\Models\Photogallery\Photo[] $photos
     */
    public function savePhotogallery(array $photos): void
    {
        $notDelete = [];
        foreach ($photos as $photo) {
            $this->photos()->save($photo);
            $notDelete[] = $photo->getKey();
        }

        $this->photos()->whereNotIn('id', $notDelete)->delete();
    }


    /**
     * Create a new instance of the photogallery photo.
     *
     * @param array $attributes
     * @return \App\Models\Photogallery\Photo
     */
    public function newPhotoInstance(array $attributes = []): Photo
    {
        /** @var \App\Models\Photogallery\Photo $photo */
        $photo = $this->photos()->newModelInstance($attributes);
        $photo->fill($attributes);

        // Set model exists if ID is present
        if (isset($attributes['id'])) {
            $photo->setAttribute('id', $attributes['id']);
            $photo->exists = true;
        }

        return $photo;
    }
}
