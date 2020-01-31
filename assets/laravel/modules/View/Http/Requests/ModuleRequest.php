<?php

namespace Modules\View\Http\Requests;

use App\Helpers\ViewHelper;
use App\Structures\FormFields\AbstractFormField;
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
        $view = $this->input('view');
        $fieldsRules = [];
        if ($view && ViewHelper::isViewDemarcated('modules.view', $view)) {
            foreach (ViewHelper::getViewVariables($view) as $field) {
                if ($field instanceof AbstractFormField) {
                    $fieldRules = $field->getValidationRules();
                    if ($fieldRules) {
                        $fieldsRules["variables.{$field->getName()}"] = $fieldRules;
                    }
                }
            }
        }

        return array_merge([
            'view' => ['required', Rule::in(ViewHelper::getDemarcatedViewsKeys('modules.view'))],
            'variables' => $fieldsRules ? 'array' : 'nullable'
        ], $fieldsRules);
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
