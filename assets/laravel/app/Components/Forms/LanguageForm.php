<?php

namespace App\Components\Forms;

use App\Models\Web\Language;

class LanguageForm extends AbstractForm
{
    /**
     * View name.
     *
     * @var string
     */
    protected $view = 'admin.languages.form';

    /**
     * Language.
     *
     * @var \App\Models\Web\Language
     */
    protected $language;

    /**
     * Language form.
     *
     * @param \App\Models\Web\Language $language
     */
    public function __construct(Language $language)
    {
        parent::__construct();
        $this->language = $language;
        
        $this->addScript(url('plugin/js/switchery.js'));
        $this->addScript(url('plugin/js/bootstrap-maxlength.js'));

        $this->addScript(mix('js/languages.form.js'));
    }


    /**
     * Get view data.
     *
     * @return array
     */
    public function getViewData(): array
    {
        return [
            'language' => $this->language,
            'submitUrl' => $this->getSubmitUrl()
        ];
    }


    /**
     * Get submit url for form.
     *
     * @return string
     */
    private function getSubmitUrl(): string
    {
        if ($this->language->exists) {
            return route('admin.languages.update', $this->language->id);
        } 

        return route('admin.languages.store');
    }
}
