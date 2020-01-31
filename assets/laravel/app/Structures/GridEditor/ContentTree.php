<?php

namespace App\Structures\GridEditor;

use App\Models\Module\Entity;
use App\Models\UniversalModule\UniversalModuleEntity;

class ContentTree extends GridEditorContent
{
    /**
     * @var \App\Models\Module\Entity[]
     */
    public static $entities = [];

    /**
     * @var \App\Models\UniversalModule\UniversalModuleEntity[]
     */
    public static $universalEntities = [];

    /**
     * Parse content tree.
     *
     * @param array $content
     * @return \App\Structures\GridEditor\ContentTree
     * @throws \App\Exceptions\GridEditorException
     */
    public static function parse(array $content): ContentTree
    {
        $tree = new self();
        foreach ($content as $index => $item) {
            ContentItem::parseAndAdd($item, $tree);
        }

        return $tree;
    }


    /**
     * Get path of the next item.
     *
     * @return string
     */
    public function getNextItemPath(): string
    {
        return count($this->content);
    }


    /**
     * Check of content tree is equal to specified content tree.
     *
     * @param \App\Structures\GridEditor\ContentTree $contentTree
     * @return bool
     */
    public function isEqual(ContentTree $contentTree): bool
    {
        $content = $contentTree->getContent();
        if (count($content) !== count($this->getContent())) {
            return false;
        }

        foreach ($this->getContent() as $index => $item) {
            if (!isset($content[$index]) || !$item->isEqual($content[$index])) {
                return false;
            }
        }

        return true;
    }


    /**
     * Convert tree to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_map(function (ContentItem $item) {
            return $item->toArray();
        }, $this->content);
    }


    /**
     * Convert to json.
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }


    /**
     * Convert tree to html.
     *
     * @param array $renderAttributes
     * @return string
     */
    public function toHtml(array $renderAttributes = []): string
    {
        $this->prefetchAllEntities();
        return $this->getContentHtml($renderAttributes);
    }


    /**
     * Get all entities.
     *
     * @return void
     */
    public function prefetchAllEntities(): void
    {
        $entityIds = [];
        $universalEntityIds = [];
        foreach ($this->getModules() as $module) {
            if ($module->shouldExist()) {
                if ($module->isUniversal()) {
                    if (!isset(static::$universalEntities[$module->getEntityId()])) {
                        $universalEntityIds[] = $module->getEntityId();
                    }
                } elseif (!isset(static::$entities[$module->getEntityId()])) {
                    $entityIds[] = $module->getEntityId();
                }
            }
        }

        if ($entityIds) {
            /** @var \App\Models\Module\Entity $entity */
            foreach (Entity::query()->whereIn('id', $entityIds)->get() as $entity) {
                static::$entities[$entity->getKey()] = $entity;
            }
        }

        if ($universalEntityIds) {
            $universalEntities = UniversalModuleEntity::query()->whereIn('id', $universalEntityIds)->get();

            /** @var \App\Models\UniversalModule\UniversalModuleEntity $entity */
            foreach ($universalEntities as $entity) {
                static::$universalEntities[$entity->getKey()] = $entity;
            }
        }
    }
}
