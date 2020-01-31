<?php

namespace Modules\Link\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Link\Models\Configuration;

class ModuleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'text' => 'required',
            'url' => 'required_if:custom_url,1',
            'model_type' => 'required_without:custom_url|in:' . join(',', array_keys(Configuration::$modelTypeClass))
        ];

        foreach(Configuration::$modelTypeClass as $type => $class){
            $rules["{$type}_id"] = "required_if:model_type,{$type}|exists:" . $class::getTableName() . ",id";
        }

        return $rules;
    }


    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return trans('module-link::admin.validation_messages');
    }


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
