<?php

namespace App\Components\Forms;

abstract class AbstractForm
{
    /**
     * View name.
     *
     * @var string
     */
    protected $view;

    /**
     * Scripts of the form.
     *
     * @var array
     */
    protected $scripts;

    /**
     * Styles of the form.
     *
     * @var array
     */
    protected $styles;

    /**
     * Basic constructor.
     */
    public function __construct()
    {
        $this->scripts = [];
        $this->styles = [];
    }


    /**
     * Get view name.
     *
     * @return string
     */
    public function getViewName(): string
    {
        return $this->view;
    }


    /**
     * Get view data.
     *
     * @return array
     */
    public function getViewData(): array
    {
        return [];
    }


    /**
     * Get view.
     *
     * @param array $mergeData
     *
     * @return \Illuminate\View\View
     */
    public function getView(array $mergeData = [])
    {
        $mergeData['_FORM_SCRIPTS'] = $this->scripts;
        $mergeData['_FORM_STYLES'] = $this->styles;
        return view($this->getViewName(), $this->getViewData(), $mergeData);
    }


    /**
     * Add script to form.
     *
     * @param string $path
     *
     * @return void
     */
    public function addScript(string $path)
    {
        if (!in_array($path, $this->scripts)) {
            $this->scripts[] = $path;
        }
    }


    /**
     * Add style to form.
     *
     * @param string $path
     *
     * @return void
     */
    public function addStyle(string $path)
    {
        if (!in_array($path, $this->styles)) {
            $this->styles[] = $path;
        }
    }


    /**
     * Add CKEditor script with localization.
     */
    public function addCKEditorScript()
    {
        $this->addScript(url("plugin/js/ckeditor.js"));

        if (app()->getLocale() !== 'en') {
            $this->addScript(url("js/localizations/ckeditor/" . app()->getLocale() . ".js"));
        }
    }


    /**
     * Convert to string by returning view, which implements __toString.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getView();
    }
}
