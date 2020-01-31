<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\AbstractFormRequest;
use App\Traits\Requests\ValidatesOpenGraphTrait;
use App\Traits\Requests\ValidatesSeoTrait;

class ArticleFlagRequest extends AbstractFormRequest
{
    use ValidatesSeoTrait, ValidatesOpenGraphTrait;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->mergeRules([
            'name' => 'required|max:50',
            'url' => 'required|max:50',
            'description' => 'nullable|string|max:1000',
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
            'admin/article_flags/form.messages', $this->getSeoMessages(), $this->getOpenGraphMessages()
        );
    }


    /**
     * Return input values.
     *
     * @return array
     */
    public function getValues()
    {
        $values = $this->all([
            'name', 'url', 'seo_title', 'seo_description',
            'seo_index', 'seo_follow', 'seo_sitemap', 'open_graph',
            'should_bound_articles_to_category', 'description'
        ]);

        $values['use_tags'] = $this->extractBool('use_tags');
        $values['use_grid_editor'] = $this->extractBool('use_grid_editor');
        $values['should_bound_articles_to_category'] = $this->extractBool('should_bound_articles_to_category');

        return $values;
    }
}
