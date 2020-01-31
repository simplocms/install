<?php

namespace App\Http\Requests\Admin;

use App\Contracts\PhotogalleryInterface;
use App\Http\Requests\AbstractFormRequest;
use App\Models\Photogallery\Photogallery;
use App\Traits\Requests\ReceivesPlannedPublishingTrait;
use App\Traits\Requests\ValidatesAndReceivesPhotogalleryTrait;
use App\Traits\Requests\ValidatesOpenGraphTrait;
use App\Traits\Requests\ValidatesSeoTrait;

class PhotogalleryRequest extends AbstractFormRequest
{
    use ReceivesPlannedPublishingTrait,
        ValidatesAndReceivesPhotogalleryTrait,
        ValidatesSeoTrait,
        ValidatesOpenGraphTrait;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->mergeRules([
            'title' => 'required|string|max:150',
            'url' => 'required|string|max:100',
            'sort' => 'nullable|numeric|min:0',
        ], $this->getPhotogalleryRules(), $this->getSeoRules(), $this->getOpenGraphRules());
    }


    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return $this->mergeMessages(
            'admin/photogalleries/form.messages', $this->getSeoMessages(), $this->getOpenGraphMessages()
        );
    }


    /**
     * Return input values
     *
     * @return array
     */
    public function getValues(): array
    {
        $input = $this->all([
            'title', 'text', 'sort', 'url', 'open_graph',
            'seo_title', 'seo_description', 'seo_index', 'seo_follow', 'seo_sitemap'
        ]);

        // Publish at
        $input['publish_at'] = $this->getPublishAt();

        // Unpublish at
        $input['unpublish_at'] = $this->getUnpublishAt();

        $sort = $this->input('sort');
        if (is_null($sort) || !strlen($sort)) {
            $maxSort = Photogallery::max('sort') ?: 0;
            $input['sort'] = $maxSort + 1;
        }

        return $input;
    }


    /**
     * Get instance of model using photogallery.
     *
     * @return \App\Contracts\PhotogalleryInterface
     */
    protected function getModelWithPhotogallery(): PhotogalleryInterface
    {
        /** @var \App\Models\Photogallery\Photogallery $photogallery */
        $photogallery = $this->route('photogallery');
        return $photogallery ?? new Photogallery;
    }
}
