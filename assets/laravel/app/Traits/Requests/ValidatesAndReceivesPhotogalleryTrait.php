<?php

namespace App\Traits\Requests;

use App\Contracts\PhotogalleryInterface;
use App\Rules\MediaImageRule;
use Illuminate\Validation\Rule;

/**
 * Trait ValidatesAndReceivesPhotogalleryTrait
 * @package App\Traits\Requests
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 */
trait ValidatesAndReceivesPhotogalleryTrait
{
    /**
     * Get instance of model using photogallery.
     *
     * @return \App\Contracts\PhotogalleryInterface
     */
    abstract protected function getModelWithPhotogallery(): PhotogalleryInterface;

    /**
     * Get validation rules for photogallery input.
     *
     * @return array
     */
    protected function getPhotogalleryRules(): array
    {
        $model = $this->getModelWithPhotogallery();
        $relation = $model->photos();

        return [
            'photogallery' => ['present', 'array'],
            'photogallery.*' => ['required', 'array'],
            'photogallery.*.id' => [
                'nullable',
                Rule::exists($model->newPhotoInstance()->getTable(), 'id')
                    ->where($relation->getForeignKeyName(), $model->getKey())
            ],
            'photogallery.*.image_id' => [
                'required',
                new MediaImageRule
            ],
            'photogallery.*.title' => ['nullable', 'string'],
            'photogallery.*.author' => ['nullable', 'string'],
        ];
    }


    /**
     * Return photos for photogallery.
     *
     * @return \App\Models\Photogallery\Photo[]
     */
    public function getPhotogallery(): array
    {
        $model = $this->getModelWithPhotogallery();
        $input = $this->input('photogallery');

        return array_map(function (array $photo, int $index) use ($model) {
            return $model->newPhotoInstance($photo + ['sort' => $index]);
        }, $input, array_keys($input));
    }
}
