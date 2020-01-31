<?php

namespace App\Http\Requests\Admin;

use App\Models\Web\Language;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LanguageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $language = $this->route('language');
        return [
            'name' => 'required|max:50',
            'country_code' => 'required|max:3',
            'language_code' => [
                'required', 'max:3',
                Rule::unique(Language::getTableName(), 'language_code')
                    ->ignore($language->id ?? 0)
            ],
            'domain' => 'max:255'
        ];
    }


    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return trans('admin/languages/form.messages');
    }


    /**
     * Get all of the input and files for the request.
     *
     * @return array
     */
    public function getValues()
    {
        $all = $this->all(['name', 'country_code', 'language_code', 'enabled', 'domain']);
        $all['enabled'] = isset($all['enabled']) ? 1 : 0;

        return $all;
    }
}
