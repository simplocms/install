<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\AbstractFormRequest;
use App\Services\UniversalModules\UniversalModule;
use App\Structures\Enums\SingletonEnum;
use App\Structures\FormFields\AbstractFormField;
use App\Traits\Requests\ValidatesOpenGraphTrait;
use App\Traits\Requests\ValidatesSeoTrait;

class UniversalModuleRequest extends AbstractFormRequest
{
    use ValidatesSeoTrait, ValidatesOpenGraphTrait;

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
        $module = $this->getUniversalModule();
        $rules = $module->getValidationRules();

        if ($module->isAllowedOrdering()) {
            $rules['order'] = ['required', 'integer', 'min:1'];
        }

        if ($module->hasUrl()) {
            $rules['url'] = ['required', 'string', 'max:200'];
        }

        if ($module->isAllowedToggling()) {
            $rules['enabled'] = ['bool'];
        }

        if ($module->hasUrl()) {
            return $this->mergeRules($rules, $this->getSeoRules(), $this->getOpenGraphRules());
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
        return $this->mergeMessages(
            $this->getSeoMessages(), $this->getOpenGraphMessages()
        );
    }


    /**
     * Return input values
     *
     * @return array
     */
    public function getContentValues(): array
    {
        $inputs = array_map(function (AbstractFormField $field) {
            return $field->getName();
        }, $this->getUniversalModule()->getFields());

        return $this->all($inputs);
    }


    /**
     * Return input values
     *
     * @return array
     */
    public function getValues(): array
    {
        $module = $this->getUniversalModule();

        $output = [
            'order' => $module->isAllowedOrdering() ? (int)$this->input('order') : null,
            'enabled' => $module->isAllowedToggling() ? boolval($this->input('enabled')) : true,
        ];

        if ($module->hasUrl()) {
            $output = array_merge($output, [
                'url' => $this->input('url'),
                'open_graph' => $this->input('open_graph'),
                'seo_title' => $this->input('seo_title'),
                'seo_description' => $this->input('seo_description'),
                'seo_index' => $this->input('seo_index', false),
                'seo_follow' => $this->input('seo_follow', false),
                'seo_sitemap' => $this->input('seo_sitemap', false),
            ]);
        }

        return $output;
    }


    /**
     * Get universal module by prefix in route.
     *
     * @return \App\Services\UniversalModules\UniversalModule
     */
    protected function getUniversalModule(): UniversalModule
    {
        return SingletonEnum::universalModules()->findOrFail($this->route('prefix'));
    }
}
