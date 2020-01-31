<?php

namespace Modules\Photogallery\Http\Requests;

use App\Helpers\ViewHelper;
use App\Models\Photogallery\Photogallery;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'view' => ['required', Rule::in(ViewHelper::getDemarcatedViewsKeys('modules.photogallery'))],
            'photogallery_id' => 'required|exists:' . Photogallery::getTableName() . ',id',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return trans('module-photogallery::admin.validation_messages');
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
