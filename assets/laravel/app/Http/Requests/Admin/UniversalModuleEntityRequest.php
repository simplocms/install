<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\AbstractFormRequest;
use App\Models\UniversalModule\UniversalModuleItem;
use App\Structures\Enums\SingletonEnum;
use Illuminate\Validation\Rule;

class UniversalModuleEntityRequest extends AbstractFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'entity_module_name' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!SingletonEnum::universalModules()->has($value)) {
                        return $fail($attribute . ' is invalid.');
                    }
                },
            ],
            'view' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!\App\Helpers\ViewHelper::isViewDemarcated(
                        'universal_modules.' . $this->getModuleName(), $value
                    )) {
                        return $fail(
                            trans('admin/universal_modules.grid_editor_validation_messages.invalid_view')
                        );
                    }
                },
            ],
        ];

        if ($this->shouldHaveItems()) {
            $rules['items'] = ['required', 'array'];
            $rules['items.*'] = [
                Rule::exists(UniversalModuleItem::getTableName(), 'id')
                    ->where('prefix', $this->getModuleName())
            ];
        }

        return $rules;
    }


    /**
     * Get validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return $this->mergeMessages('admin/universal_modules.grid_editor_validation_messages');
    }


    /**
     * Check if request should have items.
     *
     * @return bool
     */
    public function shouldHaveItems(): bool
    {
        return $this->input('all_items') !== 'true';
    }


    /**
     * Get module name.
     *
     * @return string
     */
    public function getModuleName(): string
    {
        return $this->input('entity_module_name', '');
    }


    /**
     * Get items.
     *
     * @return array
     */
    public function getItems(): array
    {
        return $this->input('items', []);
    }


    /**
     * Return input values.
     *
     * @return array
     */
    public function getValues(): array
    {
        $output = $this->only('view');
        $output['all_items'] = $this->shouldHaveItems();
        return $output;
    }
}
