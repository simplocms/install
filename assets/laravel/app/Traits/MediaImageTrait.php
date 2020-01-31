<?php

namespace App\Traits;

use App\Models\Media\File;
use App\Services\MediaLibrary\ImageBuilder;
use App\Services\ModelPrefetch\ModelPrefetch;

/**
 * Trait MediaImageTrait
 * @package App\Traits
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait MediaImageTrait
{
    /**
     * Has image on specified relation?
     *
     * @param string $relationName
     * @return bool
     */
    public function hasImage(string $relationName): bool
    {
        /** @var \Illuminate\Database\Eloquent\Relations\BelongsTo $relation */
        $relation = $this->{$relationName}();

        $prefetch = resolve(ModelPrefetch::class);
        $imageId = $this->getAttribute($relation->getForeignKey());
        $image = null;

        if ($imageId) {
            $image = $prefetch->getPrefetchedModel(File::class, $imageId);
            if ($image) {
                $this->setRelation($relationName, $image);
            }

            /** @var \App\Models\Media\File $image */
            $image = $this->getRelationValue($relationName);
        }

        return !is_null($image) && $image->isSelectableImage();
    }


    /**
     * Get instance of image file.
     * Placeholder is going to be returned when relation returns null OR file that is not image!
     *
     * Use only when generating image preview.
     *
     * @param string $relation
     * @return \App\Services\MediaLibrary\ImageBuilder
     */
    public function makeImageLink(string $relation): ImageBuilder
    {
        $file = $this->hasImage($relation) ? $this->getRelationValue($relation) : File::imagePlaceholder();

        return $file->makeLink();
    }
}
