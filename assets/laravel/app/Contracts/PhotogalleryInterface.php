<?php

namespace App\Contracts;

use App\Models\Photogallery\Photo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Interface PhotogalleryInterface
 * @package App\Contracts
 * @mixin \Illuminate\Database\Eloquent\Model
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 */
interface PhotogalleryInterface
{
    /**
     * Relation to photos of the photogallery.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function photos(): HasMany;

    /**
     * Save photogallery photos.
     *
     * @param \App\Models\Photogallery\Photo[] $photos
     */
    public function savePhotogallery(array $photos): void;

    /**
     * Create a new instance of the photogallery photo.
     *
     * @param array $attributes
     * @return \App\Models\Photogallery\Photo
     */
    public function newPhotoInstance(array $attributes = []): Photo;
}
