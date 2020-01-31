<?php

namespace App\Structures\GridEditor;

class ContentColumn extends ContentItem
{
    /** @var array */
    protected $size;

    /**
     * ContentColumn constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->type = self::TYPE_COLUMN;
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

        // Size
        $this->size = [];
        foreach ($item['size'] ?? [] as $size => $columns) {
            $size = strtolower($size);
            $columns = (int)$columns;

            if ($columns >= 1 && $columns <= 12 && in_array($size, ['col', 'sm', 'md', 'lg', 'xl'])) {
                $this->size[$size] = $columns;
            }
        }
    }


    public function isEqual(ContentItem $item): bool
    {
        if (!$item instanceof ContentColumn) {
            return false;
        }

        // Compare sizes
        if (count($this->size) !== count($item->size)) {
            return false;
        }

        if (count($this->size) || count($item->size)) {
            foreach ($this->size as $size => $cols) {
                if (!isset($item->size[$size]) || $this->size[$size] !== $item->size[$size]) {
                    return false;
                }
            }
        }

        return parent::isEqual($item);
    }


    /**
     * Get class for element in html.
     *
     * @return null|string
     */
    protected function getHtmlClass(): ?string
    {
        $classes = [];
        foreach ($this->size ?: ['lg' => 12] as $size => $cols) {
            $classes[] = $size === 'col' ? "$size-$cols" : "col-$size-$cols";
        }

        if (!is_null($this->class)) {
            $classes[] = $this->class;
        }

        return join(' ', $classes);
    }


    /**
     * Convert content container to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $output = parent::toArray();

        $output['size'] = $this->size ?? ['col' => 12];

        return $output;
    }
}
