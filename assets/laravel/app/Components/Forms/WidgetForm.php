<?php

namespace App\Components\Forms;

use App\Models\Widget\Widget;
use App\Models\Web\Language;
use App\Models\Widget\Content;
use App\Components\Forms\Templates\AbstractFormWithGridEditor;


class WidgetForm extends AbstractFormWithGridEditor
{
    /**
     * View name.
     *
     * @var string
     */
    protected $view = 'admin.widgets.form';

    /**
     * Language.
     *
     * @var \App\Models\Web\Language
     */
    protected $language;

    /**
     * Widget.
     *
     * @var \App\Models\Widget\Widget
     */
    protected $widget;

    /**
     * Widget form.
     *
     * @param \App\Models\Web\language $language
     * @param \App\Models\Widget\Widget $widget
     */
    public function __construct(Language $language, Widget $widget)
    {
        parent::__construct();
        $this->widget = $widget;
        $this->language = $language;
        $this->addGridEditorScriptsAndStyle();
        $this->addScript(mix('js/widgets.form.js'));
    }


    /**
     * Get view data.
     *
     * @return array
     */
    public function getViewData(): array
    {
        return $this->getGridEditorData([
            'languageId' => $this->language->id,
            'widget' => $this->widget,
            'widgetData' => $this->widget->getFormAttributesJson(['id', 'name']),
            'submitUrl' => $this->widget->exists ?
                route('admin.widgets.update', $this->widget->getKey()) :
                route('admin.widgets.store'),
            '_GE_CREATES_VERSIONS' => false
        ]);
    }


    /**
     * Get content of the grid editor.
     *
     * @return \App\Models\Interfaces\IsGridEditorContent
     */
    protected function getGridEditorContent(): \App\Models\Interfaces\IsGridEditorContent
    {
        return $this->widget->getLanguageContent($this->language) ?: new Content;
    }
}
