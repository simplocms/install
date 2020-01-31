<?php

namespace Modules\Text\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ModuleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'content' => 'required'
        ];
    }


    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return trans('module-text::admin.validation_messages');
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
