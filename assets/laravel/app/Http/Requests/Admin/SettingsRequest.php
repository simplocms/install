<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\AbstractFormRequest;
use App\Rules\MediaImageRule;
use App\Rules\TwitterAccountRule;
use App\Structures\Enums\ReferrerPolicyEnum;
use App\Structures\Enums\SingletonEnum;
use App\Structures\Enums\XSSProtectionEnum;
use App\Traits\Requests\ValidatesSeoTrait;
use Illuminate\Validation\Rule;

class SettingsRequest extends AbstractFormRequest
{
    use ValidatesSeoTrait;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->mergeRules([
            'site_name' => 'nullable|string|max:65',
            'company_name' => 'nullable|string|max:100',
            'logo' => ['nullable', new MediaImageRule],
            'favicon' => ['nullable', new MediaImageRule],
            'theme_color' => ['nullable', 'regex:/^#[a-fA-F0-9]{6}$/'],
            'og_title' => 'nullable|string|max:90',
            'og_description' => 'nullable|string|max:300',
            'og_image' => ['nullable', new MediaImageRule],
            'x_frame_options' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value === 'allow' || $value === 'deny' || $value === 'sameorigin') {
                        return true;
                    }

                    $patternForUrl = "/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/";
                    if (preg_match($patternForUrl, $value) !== 1) {
                        return $fail(trans('admin/settings.validation_messages.invalid_x_frame_options'));
                    }
                },
            ],
            'x_xss_protection' => ['nullable', Rule::in(XSSProtectionEnum::values())],
            'referrer_policy' => ['nullable', Rule::in(ReferrerPolicyEnum::values())],
            'hsts_max_age' => ['int', 'min:1'],
            'search_uri' => ['required_if:search_enabled,true', 'max:100'],
            'twitter_account' => ['nullable', new TwitterAccountRule]
        ], $this->getSeoRules());
    }


    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return $this->mergeMessages(
            'admin/settings.validation_messages', $this->getSeoMessages()
        );
    }


    /**
     * Return input values.
     *
     * @return array
     */
    public function getValues(): array
    {
        $values = $this->all(array_merge([
            'og_image', 'x_frame_options', 'x_xss_protection', 'referrer_policy', 'x_content_type_options',
            'hsts_enabled', 'hsts_include_subdomains', 'hsts_max_age', 'logo', 'favicon', 'company_name',
            'search_enabled', 'search_uri', 'search_in_pages', 'search_in_articles', 'search_in_categories',
            'search_in_photogalleries', 'theme_color', 'twitter_account'
        ], SingletonEnum::theme()->getSettingsKeys()));

        $localized = ['site_name', 'og_title', 'og_description', 'seo_title', 'seo_description'];
        $lang = SingletonEnum::languagesCollection()->getContentLanguage()->language_code;
        foreach ($localized as $name) {
            $values[$name . '_' . $lang] = $this->input($name);
        }

        if ($this->input('search_enabled')) {
            $values['search_uri'] = str_slug($values['search_uri']);
        }

        return $values;
    }
}
