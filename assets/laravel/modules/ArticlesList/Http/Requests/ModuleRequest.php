<?php

namespace Modules\ArticlesList\Http\Requests;

use App\Helpers\ViewHelper;
use App\Http\Requests\AbstractFormRequest;
use App\Models\Article\Category;
use App\Models\Article\Flag;
use App\Rules\ExistsArrayRule;
use Illuminate\Validation\Rule;
use Modules\ArticlesList\Models\SortTypeEnum;

class ModuleRequest extends AbstractFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'view' => ['required', Rule::in(ViewHelper::getDemarcatedViewsKeys('modules.articles_list'))],
            'category_ids' => [
                'array',
                ExistsArrayRule::make(Category::class)
                    ->setMessage(trans('module-articleslist::admin.grid_editor_form.messages.category_ids_exists'))
            ],
            'flag_ids' => [
                'array',
                ExistsArrayRule::make(Flag::class)
                    ->setMessage(trans('module-articleslist::admin.grid_editor_form.messages.flag_ids_exists'))
            ],
            'sort_type' => [
                'required',
                Rule::in(SortTypeEnum::values())
            ],
            'limit' => [
                'nullable',
                'int',
                'min:0'
            ]
        ];
    }


    /**
     * @return string[]
     */
    public function messages()
    {
        return trans('module-articleslist::admin.grid_editor_form.messages');
    }
}
