<?php

namespace App\Structures\GridEditor;

class ContentContainer extends ContentItem
{
    /** @var bool */
    protected $isFluid;

    /** @var string */
    protected $wrapper;

    /**
     * ContentContainer constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->type = self::TYPE_CONTAINER;
    }


    /**
     * Fill item properties with data from given array.
     *
     * @param array $item
     * @throws \App\Exceptions\GridEditorException
     */
    protected function fill(array $item): void
    {
        parent::fill($item);

        // isFluid
        $this->isFluid = boolval($item['fluid'] ?? false);

        // Wrapper
        $wrapper = trim($item['wrapper'] ?? '');
        $this->wrapper = strlen($wrapper) ? $wrapper : null;
    }


    /**
     * Is this item equal to specified item?
     *
     * @param \App\Structures\GridEditor\ContentItem $item
     * @return bool
     */
    public function isEqual(ContentItem $item): bool
    {
        if (!$item instanceof ContentContainer ||
            $item->isFluid !== $this->isFluid ||
            $this->wrapper !== $item->wrapper
        ) {
            return false;
        }

        return parent::isEqual($item);
    }


    /**
     * Convert content container to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $output = parent::toArray();

        if ($this->isFluid) {
            $output['fluid'] = true;
        }

        if ($this->wrapper) {
            $output['wrapper'] = $this->wrapper;
        }

        return $output;
    }


    /**
     * Convert container to html.
     *
     * @param array $renderAttributes
     * @return string
     */
    public function toHtml(array $renderAttributes = []): string
    {
        $html = parent::toHtml($renderAttributes);

        if ($this->wrapper) {
            $html = str_replace('[container]', $html, $this->wrapper);
        }

        return $html;
    }


    /**
     * Get class for element in html.
     *
     * @return null|string
     */
    protected function getHtmlClass(): ?string
    {
        $classes = [$this->isFluid ? 'container-fluid' : 'container'];

        if (!is_null($this->class)) {
            $classes[] = $this->class;
        }

        return join(' ', $classes);
    }
}