<?php

namespace Modules\Image\Http\Requests;

use App\Rules\MediaImageRule;
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
        $isSized = intval($this->input('is_sized'));
        $rules = [
            'image_id' => ['required', new MediaImageRule],
            'alt' => 'required|string',
            'is_sized' => 'required|boolean',
        ];

        if ($isSized) {
            $rules['width'] = 'required|integer|min:1';
            $rules['height'] = 'required|integer|min:1';
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
        return trans('module-image::admin.validation_messages');
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
