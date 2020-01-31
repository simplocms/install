<?php

namespace App\Helpers;

use App\Models\Web\Language;
use App\Models\Widget\Widget;
use App\Structures\Enums\SingletonEnum;

class WidgetFactory
{
    /** @var Widget[] */
    private $widgets;

    /**
     * WidgetFactory constructor.
     */
    public function __construct()
    {
        $this->widgets = [];
    }


    /**
     * Get language.
     *
     * @return Language
     */
    public function getLanguage()
    {
        return SingletonEnum::languagesCollection()->getContentLanguage();
    }


    /**
     * Does widget exists?
     *
     * @param string $id
     * @return bool
     */
    public function exists($id)
    {
        return !!$this->get($id);
    }


    /**
     * Get widget.
     *
     * @param string $id
     * @return Widget|null
     */
    public function get($id)
    {
        if (!array_key_exists($id, $this->widgets)) {
            $this->widgets[$id] = Widget::find($id);
        }

        return $this->widgets[$id];
    }


    /**
     * Render widget.
     *
     * @param string $id
     * @param int|null $languageId
     * @return string
     */
    public function render($id, $languageId = null)
    {
        $widget = $this->get($id);

        if (!$widget) {
            return $this->renderNotFound($id);
        }

        $content = $widget->getLanguageContent(
            is_null($languageId) ? $this->getLanguage() : $languageId
        );

        if (is_null($content)) {
            return $this->renderNotFoundLanguage($id,
                is_null($languageId) ? $this->getLanguage()->name : null
            );
        }

        return $content->getHtml();
    }


    /**
     * Render not found content.
     *
     * @param string $id
     * @return string
     */
    public function renderNotFound($id)
    {
        return view('vendor.widgets.no_found')->with('id', $id)->render();
    }


    /**
     * Render not found content.
     *
     * @param string $id
     * @return string
     */
    public function renderNotFoundLanguage($id, $languageName)
    {
        return view('vendor.widgets.no_found_language',
            compact('id', 'languageName')
        )->render();
    }
}
