<?php

namespace App\Structures\GridEditor;

abstract class GridEditorContent
{
    /** @var \App\Structures\GridEditor\ContentItem[] */
    protected $content;

    /** @var \App\Structures\GridEditor\ContentModule[] - modules in content */
    protected $modules;

    /**
     * Get path of the next item.
     *
     * @return string
     */
    abstract public function getNextItemPath(): string;


    /**
     * ContentItem constructor.
     */
    public function __construct()
    {
        $this->content = [];
        $this->modules = [];
    }


    /**
     * Get content.
     *
     * @return \App\Structures\GridEditor\ContentItem[]
     */
    public function getContent(): array
    {
        return $this->content;
    }


    /**
     * Add content item.
     *
     * @param \App\Structures\GridEditor\ContentItem $item
     */
    public function addContentItem(ContentItem $item): void
    {
        $this->content[] = $item;

        if ($item->getType() === ContentItem::TYPE_MODULE) {
            $this->modules[] = $item;
        } elseif ($item->hasModules()) {
            $this->modules = array_merge($this->modules, $item->getModules());
        }
    }


    /**
     * Has modules in content?
     *
     * @return bool
     */
    public function hasModules(): bool
    {
        return boolval($this->modules);
    }


    /**
     * Get modules in content.
     *
     * @return \App\Structures\GridEditor\ContentModule[]
     */
    public function getModules(): array
    {
        return $this->modules;
    }


    /**
     * Get HTML of content.
     *
     * @param array $renderAttributes
     * @return string
     */
    protected function getContentHtml(array $renderAttributes = []): string
    {
        $output = '';

        foreach ($this->getContent() as $item) {
            $output .= $item->toHtml($renderAttributes);
        }

        return $output;
    }
}