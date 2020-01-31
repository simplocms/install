<?php

namespace App\Structures\GridEditor;

class ContentRow extends ContentItem
{
    /**
     * ContentRow constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->type = self::TYPE_ROW;
    }


    /**
     * Get class for element in html.
     *
     * @return null|string
     */
    protected function getHtmlClass(): ?string
    {
        $classes = ['row'];

        if (!is_null($this->class)) {
            $classes[] = $this->class;
        }

        return join(' ', $classes);
    }
}