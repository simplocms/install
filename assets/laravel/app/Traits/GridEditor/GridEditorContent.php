<?php

namespace App\Traits\GridEditor;

use App\Exceptions\GridEditorException;
use App\Models\Module\Entity;
use App\Structures\GridEditor\ContentTree;

/**
 * Trait GridEditorContent
 * @package App\Traits\GridEditor
 * @author Patrik Václavek
 * @mixin \App\Models\Interfaces\IsGridEditorContent
 * @copyright SIMPLO, s.r.o.
 */
trait GridEditorContent
{
    /**
     * Entities in content.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function entities(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Entity::class, 'model', 'model', 'model_id', 'id');
    }


    /**
     * Get raw content.
     *
     * @return string
     */
    public function getRaw(): string
    {
        return $this->attributes['content'] ?? '';
    }


    /**
     * Get content tree.
     *
     * @return \App\Structures\GridEditor\ContentTree|null
     * @throws \App\Exceptions\GridEditorException
     */
    public function getContentTree(): ?ContentTree
    {
        return $this->content ? ContentTree::parse($this->content) : null;
    }


    /**
     * Set content attribute.
     *
     * @param string $value
     */
    public function setContentAttribute(string $value = null)
    {
        if (is_null($value) || !strlen($value)) {
            $this->attributes['content'] = null;
            return;
        }

        $this->attributes['content'] = $value;
    }


    /**
     * Get content attribute.
     *
     * @return array|null
     */
    public function getContentAttribute(): ?array
    {
        if (!$this->attributes['content']) {
            return null;
        }

        return json_decode($this->attributes['content'], true);
    }


    /**
     * Get HTML content.
     *
     * @param array $renderAttributes
     *
     * @return string
     */
    public function getHtml(array $renderAttributes = []): string
    {
        if (is_null($this->content)) {
            return '';
        }

        try {
            $content = $this->getContentTree();
        } catch (GridEditorException $e) {
            return '';
        }

        $content->prefetchAllEntities();

        $renderAttributes['rendered_content'] = $this;

        return $content->toHtml($renderAttributes);
    }


    /**
     * Replicate content with all entities.
     *
     * @param array|null $attributes
     * @return \App\Traits\GridEditor\GridEditorContent
     * @throws \App\Exceptions\GridEditorException
     */
    public function replicateFull(array $attributes = null)
    {
        /** @var self $newContent */
        $newContent = $this->replicate();
        if ($attributes) {
            $newContent->forceFill($attributes);
        }
        $newContent->push();

        $contentTree = $this->getContentTree();

        if ($contentTree) {
            $contentTree->prefetchAllEntities();

            foreach ($contentTree->getModules() as $module) {
                $newEntity = $module->getEntity()->duplicateForNewContent($newContent);
                if (!$module->isUniversal()) {
                    $newEntity->previous_entity_id = null;
                }
                $newEntity->save();

                $module->updateEntity($newEntity);
            }

            $newContent->setContentAttribute($contentTree->toJson());
        }

        $newContent->save();
        return $newContent;
    }


    /**
     * Update content.
     *
     * @param array|null $content
     * @throws \App\Exceptions\GridEditorException
     */
    public function updateContentAndModules(array $content = null): void
    {
        if (is_null($content)) {
            $this->content = $content;
            $this->save();
            return;
        }

        $contentTree = ContentTree::parse($content);
        $contentTree->prefetchAllEntities();

        $presentEntities = [];

        foreach ($contentTree->getModules() as $index => $module) {
            if ($module->shouldExist()) {
                $entity = $module->getEntity();

                // When entity does not exist, continue.
                if (!$entity) {
                    continue;
                }

                // When content was recently created - is new.
                // Or when updating existing content, but entity is for different one.
                // Entity needs to be copied to this content.
                if ($this->wasRecentlyCreated || !$entity->doesBelongToContent($this)) {
                    $newEntity = $module->duplicateToContent($this);
                    $presentEntities[] = $newEntity->getKey();
                } // Otherwise update configuration if changed.
                else if ($module->hasConfiguration()) {
                    $presentEntities[] = $entity->getKey();
                    $entity->updateConfiguration($module->getConfiguration());
                } // Prevent from deleting entities without configuration (when does not create new versions).
                else if (!$this->createsVersions()) {
                    $presentEntities[] = $entity->getKey();
                }
            } elseif ($module->moduleExists()) {
                // CREATING NEW MODULES
                // New module always has configuration.
                $entity = $module->createEntityForContent($this);
                $presentEntities[] = $entity->getKey();
            }
        }

        $this->entities()->whereNotIn('id', $presentEntities)->delete();
        $this->content = $contentTree->toJson();
        $this->save();
    }


    /**
     * Does grid editor creates new version fro every change?
     *
     * @return bool
     */
    protected function createsVersions(): bool
    {
        return true;
    }


    /**
     * Create module entity for the content.
     *
     * @param string $moduleName
     * @param array $configuration
     * @return \App\Models\Module\Entity
     */
    public function createModuleEntity(string $moduleName, array $configuration): Entity
    {
        $entity = new Entity(['module' => $moduleName]);
        $entity->createConfiguration($configuration);
        $this->entities()->save($entity);

        return $entity;
    }
}
