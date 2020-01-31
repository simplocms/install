<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\AbstractFormRequest;
use App\Models\Page\Page;
use App\Rules\MediaImageRule;
use App\Traits\Requests\ReceivesPlannedPublishingTrait;
use App\Traits\Requests\ValidatesOpenGraphTrait;
use App\Traits\Requests\ValidatesSeoTrait;
use Illuminate\Validation\Rule;

class PageRequest extends AbstractFormRequest
{
    use ReceivesPlannedPublishingTrait, ValidatesSeoTrait, ValidatesOpenGraphTrait;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $urlRule = $this->input('is_homepage') === true ? 'nullable' : 'required';

        return $this->mergeRules([
            'name' => 'required|max:150',
            'url' => "$urlRule|string|max:100",
            'parent_id' => ['nullable', Rule::exists(Page::getTableName(), 'id')],
            'image_id' => ['nullable', new MediaImageRule],
        ], $this->getSeoRules(), $this->getOpenGraphRules());
    }


    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return $this->mergeMessages(
            'admin/pages/form.messages', $this->getSeoMessages(), $this->getOpenGraphMessages()
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
            'name', 'parent_id', 'url', 'view', 'image_id',
            'seo_title', 'seo_description', 'open_graph',
        ]);

        // Publish at
        $input['publish_at'] = $this->getPublishAt();

        // Unpublish at
        $input['unpublish_at'] = $this->get('set_unpublish_at') ? $this->getUnpublishAt() : null;

        // Is homepage
        $input['is_homepage'] = $this->input('is_homepage', false);

        // Is published
        $input['published'] = $this->input('published', false);

        // SEO index
        $input['seo_index'] = $this->input('seo_index', false);

        // SEO follow
        $input['seo_follow'] = $this->input('seo_follow', false);

        // SEO sitemap
        $input['seo_sitemap'] = $this->input('seo_sitemap', false);

        return $input;
    }

    /**
     * Get content.
     *
     * @return array|null
     */
    public function getGridEditorContent(): ?array
    {
        return $this->input('content');
    }
}
