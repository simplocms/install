<?php

namespace App\Components\Forms;

use App\Models\Web\Language;
use App\Models\Web\Redirect;
use App\Models\Web\Url;
use App\Structures\Enums\SingletonEnum;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class RedirectForm extends AbstractForm
{
    /**
     * View name.
     *
     * @var string
     */
    protected $view = 'admin.redirects.form';

    /**
     * Redirect.
     *
     * @var \App\Models\Web\Redirect
     */
    protected $redirect;

    /**
     * Enabled languages.
     *
     * @var \App\Models\Web\Language[]|\Illuminate\Support\Collection
     */
    protected $languages;

    /**
     * Widget form.
     *
     * @param \App\Models\Web\Redirect $redirect
     * @throws \Exception
     */
    public function __construct(Redirect $redirect)
    {
        parent::__construct();
        $this->redirect = $redirect;
        $this->languages = Language::get()->keyBy('language_code');

        $this->addScript(mix('js/redirects.form.js'));
    }


    /**
     * Get view data.
     *
     * @return array
     */
    public function getViewData(): array
    {
        return [
            'redirect' => $this->redirect,
            'fromLanguageOptions' => $this->getLanguageOptions(),
            'toLanguageOptions' => $this->getLanguageOptions()
                ->prepend(trans('admin/redirects/form.custom_url_option'), ''),
            'urlsByLanguage' => $this->getUrls(),
            'statusCodes' => [
                301 => trans('admin/redirects/form.status_codes.301'),
                302 => trans('admin/redirects/form.status_codes.302'),
                307 => trans('admin/redirects/form.status_codes.307'),
                308 => trans('admin/redirects/form.status_codes.308'),
            ],
            'formValuesJson' => json_encode($this->getFormValues()),
            'submitUrl' => $this->redirect->exists ?
                route('admin.redirects.edit', $this->redirect->getKey()) :
                route('admin.redirects.store'),
        ];
    }


    /**
     * Get form values as array.
     *
     * @return array
     */
    private function getFormValues(): array
    {
        $toLanguage = $fromLanguage = '';

        // From
        $urlChunks = explode('/', $this->redirect->from);
        /** @var \App\Models\Web\Language $language */
        $language = $this->languages->get($urlChunks[0]);

        if ($language) {
            array_shift($urlChunks);
            $fromLanguage = $language->language_code;
        }

        $from = join('/', $urlChunks);

        // To
        $to = $this->redirect->to;
        if (!$this->redirect->pointToUrl()) {
            $urlChunks = explode('/', $to);
            $language = $this->languages->get($urlChunks[0]);

            if ($language) {
                $toLanguage = array_shift($urlChunks);
                $to = join('/', $urlChunks);
            }
        }

        return [
            'from' => $from,
            'from_language' => $fromLanguage,
            'to' => $to,
            'to_language' => $toLanguage,
            'status_code' => $this->redirect->status_code
        ];
    }


    /**
     * Get options for language selects.
     *
     * @return \Illuminate\Support\Collection
     */
    private function getLanguageOptions(): Collection
    {
        $settings = SingletonEnum::settings();
        $languageDisplay = $settings->getInt('language_display', config('admin.language_url.directory'));

        $options = $this->languages->map(function (Language $language): string {
            $url = SingletonEnum::urlFactory()->getHomepageUrl($language);
            $url = Str::endsWith($url, '/') ? $url : "$url/";
            return preg_replace("(^https?://)", "", $url);
        });

        if ($languageDisplay === config('admin.language_url.directory') &&
            !SingletonEnum::settings()->getBoolean('default_language_hidden', false)
        ) {
            $url = route('homepage');
            $url = Str::endsWith($url, '/') ? $url : "$url/";
            $options->prepend(preg_replace("(^https?://)", "", $url), '');
        }

        return $options;
    }


    /**
     * Get existing url addresses grouped by language
     *
     * @return array
     */
    private function getUrls(): array
    {
        $urls = Url::get();
        $urlsByLanguage = [];

        foreach ($urls as $url) {
            $urlChunks = explode('/', $url->url);
            if (count($urlChunks) < 2) {
                continue;
            }

            $languageCode = array_shift($urlChunks);
            $urlWithoutLanguage = join('/', $urlChunks);
            $urlsByLanguage[$languageCode][] = $urlWithoutLanguage;
        }

        return $urlsByLanguage;
    }
}
