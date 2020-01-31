<?php

namespace App\Structures\GridEditor;

use App\Exceptions\GridEditorException;

abstract class ContentItem extends GridEditorContent
{
    const TYPE_CONTAINER = 'container';
    const TYPE_ROW = 'row';
    const TYPE_COLUMN = 'column';
    const TYPE_MODULE = 'module';

    /** @var string */
    protected $path;

    /** @var string */
    protected $type;

    /** @var string|null */
    protected $id;

    /** @var string|null */
    protected $class;

    /** @var string|null */
    protected $tag;

    /** @var string|null */
    protected $bgColor;

    /** @var bool */
    protected $isActive = true;

    /** @var array */
    protected $attributes = [];

    /**
     * Parse content item and add to parent.
     *
     * @param array $item
     * @param \App\Structures\GridEditor\GridEditorContent $parent
     * @return \App\Structures\GridEditor\ContentItem
     * @throws \App\Exceptions\GridEditorException
     */
    public static function parseAndAdd(array $item, GridEditorContent $parent): ContentItem
    {
        switch ($item['type'] ?? null) {
            case self::TYPE_CONTAINER:
                $model = new ContentContainer();
                break;
            case self::TYPE_ROW:
                $model = new ContentRow();
                break;
            case self::TYPE_COLUMN:
                $model = new ContentColumn();
                break;
            case self::TYPE_MODULE:
                $model = new ContentModule();
                break;
            default:
                throw GridEditorException::invalidItemType($parent->getNextItemPath());
        }

        $model->path = $parent->getNextItemPath();

        // It is important to first fill item,
        $model->fill($item);
        $parent->addContentItem($model);

        return $model;
    }


    /**
     * Fill item properties with data from given array.
     *
     * @param array $item
     * @throws \App\Exceptions\GridEditorException
     */
    protected function fill(array $item): void
    {
        // ID
        $id = trim($item['id'] ?? '');
        $this->id = strlen($id) ? $id : null;

        // Class
        $class = trim($item['class'] ?? '');
        $this->class = strlen($class) ? $class : null;

        // Tag
        $this->tag = trim($item['tag'] ?? 'div');
        if (!in_array($this->tag, config('admin.grideditor.allowed_tags'))) {
            throw GridEditorException::invalidItemTag($this);
        }

        // BgColor
        $bgColor = trim($item['bg'] ?? '');
        $this->bgColor = strlen($bgColor) ? $bgColor : null;

        // isActive
        $this->isActive = boolval($item['active'] ?? true);

        // attributes
        $attributes = $item['attributes'] ?? [];
        if (!is_array($attributes)) {
            throw GridEditorException::invalidItemAttributes($this);
        }

        $this->attributes = [];
        foreach ($attributes as $attribute) {
            if (!strlen($attribute['name'] ?? '')) {
                continue;
            }

            $this->attributes[$attribute['name']] = $attribute['value'] ?? '';
        }

        // Content
        if (isset($item['content']) && $item['content']) {
            foreach ($item['content'] as $index => $item) {
                self::parseAndAdd($item, $this);
            }
        }
    }


    /**
     * Get item type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }


    /**
     * Get item path.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path ?? '-';
    }


    /**
     * Get path of the next item.
     *
     * @return string
     */
    public function getNextItemPath(): string
    {
        $nextStep = count($this->getContent());
        return is_null($this->path) ? $nextStep : "{$this->path}-{$nextStep}";
    }


    /**
     * Is this item equal to specified item?
     *
     * @param \App\Structures\GridEditor\ContentItem $item
     * @return bool
     */
    public function isEqual(ContentItem $item): bool
    {
        $content = $item->getContent();
        if (count($content) !== count($this->getContent())) {
            return false;
        }

        // Compare properties
        if ($item->isActive !== $this->isActive || $item->type !== $this->type || $item->id !== $this->id ||
            $item->class !== $this->class || $item->bgColor !== $this->bgColor || $item->tag !== $this->tag
        ) {
            return false;
        }

        // Compare attributes
        if (count($this->attributes) !== count($item->attributes)) {
            return false;
        }

        if (count($this->attributes) || count($item->attributes)) {
            foreach ($this->attributes as $name => $value) {
                if (!isset($item->attributes[$name]) || $this->attributes[$name] !== $item->attributes[$name]) {
                    return false;
                }
            }
        }

        // Compare content
        foreach ($this->getContent() as $index => $item) {
            if (!isset($content[$index]) || !$item->isEqual($content[$index])) {
                return false;
            }
        }

        return true;
    }


    /**
     * Convert content item to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $output = [
            'type' => $this->type,
            'tag' => $this->tag ?? 'div'
        ];

        if (!is_null($this->id)) {
            $output['id'] = $this->id;
        }

        if (!is_null($this->class)) {
            $output['class'] = $this->class;
        }

        if (!is_null($this->bgColor)) {
            $output['bg'] = $this->bgColor;
        }

        if ($this->isActive === false) {
            $output['active'] = false;
        }

        foreach ($this->attributes as $name => $value) {
            $output['attributes'][] = compact('name', 'value');
        }

        if ($this->content) {
            $output['content'] = array_map(function (ContentItem $item) {
                return $item->toArray();
            }, $this->content);
        }

        return $output;
    }


    /**
     * Convert item to html.
     *
     * @param array $renderAttributes
     * @return string
     */
    public function toHtml(array $renderAttributes = []): string
    {
        if ($this->isActive === false) {
            return '';
        }

        $contentHtml = $this->getContentHtml($renderAttributes);
        $tag = $this->tag ?? 'div';
        $attributes = $this->getAttributesChain();
        $openTag = $tag . ($attributes ? " $attributes" : $attributes);

        return "<$openTag>$contentHtml</$tag>";
    }


    /**
     * Get chain of attributes for HTML.
     *
     * @return string
     */
    protected function getAttributesChain(): string
    {
        $attributes = $this->attributes;

        if ($this->bgColor) {
            if (isset($attributes['style'])) {
                $attributes['style'] .= 'background-color:' . $this->bgColor;
            } else {
                $attributes['style'] = 'background-color:' . $this->bgColor;
            }
        }

        if (!is_null($this->id)) {
            $attributes['id'] = $this->id;
        }

        $elementClass = $this->getHtmlClass();
        if ($elementClass) {
            $attributes['class'] = $elementClass;
        }

        $chain = '';
        foreach ($attributes as $name => $value) {
            $chain .= "$name" . (strlen($value) ? "=\"$value\"" : '');
        }

        return trim($chain);
    }


    /**
     * Get class for element in html.
     *
     * @return null|string
     */
    protected function getHtmlClass(): ?string
    {
        return $this->class;
    }
}