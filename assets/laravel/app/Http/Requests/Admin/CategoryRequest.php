<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\AbstractFormRequest;
use App\Models\Article\Category;
use App\Traits\Requests\ValidatesOpenGraphTrait;
use App\Traits\Requests\ValidatesSeoTrait;
use Illuminate\Validation\Rule;

class CategoryRequest extends AbstractFormRequest
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
            'name' => 'required|string|max:150',
            'url' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
            'parent_id' => [
                'nullable',
                Rule::exists(Category::getTableName(), 'id')
            ],
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
            'admin/category/form.messages', $this->getSeoMessages(), $this->getOpenGraphMessages()
        );
    }


    /**
     * Get all input values.
     *
     * @return array
     */
    public function getValues(): array
    {
        return $this->all([
            'name', 'url', 'show', 'parent_id', 'open_graph', 'description',
            'seo_title', 'seo_description', 'seo_index', 'seo_follow', 'seo_sitemap'
        ]);
    }
}
