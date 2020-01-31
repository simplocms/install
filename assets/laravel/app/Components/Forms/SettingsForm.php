<?php

namespace App\Components\Forms;

use App\Models\Web\Theme;
use App\Structures\Enums\ReferrerPolicyEnum;
use App\Structures\Enums\SingletonEnum;
use App\Structures\Enums\XSSProtectionEnum;

class SettingsForm extends AbstractForm
{
    /**
     * View name.
     *
     * @var string
     */
    protected $view = 'admin.settings.index';

    /**
     * Settings form.
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->addScript(mix('js/settings.form.js'));
    }


    /**
     * Get view data.
     *
     * @return array
     */
    public function getViewData(): array
    {
        $language = SingletonEnum::languagesCollection()->getContentLanguage();
        $languageCode = $language->language_code;
        return [
            'themes' => Theme::all(),
            'contentLanguage' => $language,
            'defaultTheme' => SingletonEnum::theme(),
            'referrerPolicyOptions' => $this->getReferrerPolicyOptions(),
            'xssProtectionOptions' => $this->getXSSProtectionOptions(),
            'settings' => SingletonEnum::settings()->collect()
                ->getDictionary('site_name', trans('general.settings.site_name', [], $languageCode))
                ->getDictionary('company_name')
                ->getInt('logo')
                ->getInt('favicon')
                ->get('theme_color', '#ffffff')
                // OpenGraph and Seo and Twitter
                ->get('twitter_account')
                ->getDictionary('seo_title', config('app.default_settings.seo_title'))
                ->getDictionary('og_title', config('app.default_settings.og_title'))
                ->getDictionary('seo_description')
                ->getDictionary('og_description')
                ->getInt('og_image')
                // Security headers
                ->get('x_frame_options', 'sameorigin')
                ->get('x_xss_protection', XSSProtectionEnum::BLOCK_ATTACKS)
                ->get('referrer_policy', ReferrerPolicyEnum::STRICT_ORIGIN_WHEN_CROSS_ORIGIN)
                ->getBool('x_content_type_options', true)
                ->getBool('hsts_enabled', true)
                ->getBool('hsts_include_subdomains', true)
                ->getInt('hsts_max_age', 31536000)
                // Search
                ->getBool('search_enabled', true)
                ->getDictionary('search_uri', trans('general.settings.search_uri', [], $languageCode))
                ->getBool('search_in_pages', true)
                ->getBool('search_in_articles', true)
                ->getBool('search_in_categories', true)
                ->getBool('search_in_photogalleries', true)
                // Get all and merge with theme settings
                ->getAll()->merge(SingletonEnum::theme()->getSettings())
        ];
    }


    /**
     * Get referrer policy options.
     *
     * @return array
     */
    private function getReferrerPolicyOptions(): array
    {
        $options = [];
        foreach (ReferrerPolicyEnum::values() as $value) {
            $options[$value] = $value;
        }
        $options[ReferrerPolicyEnum::DO_NOT_USE] = trans('admin/settings.security_headers.do_not_use');
        return $options;
    }


    /**
     * Get referrer policy options.
     *
     * @return array
     */
    private function getXSSProtectionOptions(): array
    {
        $options = [];
        foreach (XSSProtectionEnum::values() as $value) {
            $options[$value] = $value;
        }
        $options[XSSProtectionEnum::DO_NOT_USE] = trans('admin/settings.security_headers.do_not_use');
        return $options;
    }
}
