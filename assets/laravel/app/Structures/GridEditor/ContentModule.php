<?php

namespace App\Structures\GridEditor;

use App\Contracts\IsGridEditorEntity;
use App\Exceptions\GridEditorException;
use App\Models\Interfaces\IsGridEditorContent;
use App\Models\Module\Entity;
use App\Models\Module\InstalledModule;
use App\Models\Module\Module;
use App\Models\UniversalModule\UniversalModuleEntity;
use App\Structures\Enums\SingletonEnum;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ContentModule extends ContentItem
{
    /** @var \Illuminate\Database\Eloquent\Collection */
    protected static $enabledModules;

    /** @var int */
    protected $entityId;

    /** @var string */
    protected $name;

    /** @var bool */
    protected $isUniversal;

    /** @var array */
    protected $configuration;

    /** @var \App\Models\Module\Entity */
    protected $cachedEntity;

    /**
     * ContentModule constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->type = self::TYPE_MODULE;
    }


    /**
     * Fill item properties with data from given array.
     *
     * @param array $item
     * @throws \App\Exceptions\GridEditorException
     */
    protected function fill(array $item): void
    {
        // isUniversal
        $this->isUniversal = boolval($item['universal'] ?? false);

        // Existing entity
        if (isset($item['entity_id'])) {
            // Entity ID
            $this->entityId = intval($item['entity_id']);
            if (!$this->entityId || !is_numeric($item['entity_id'])) {
                throw GridEditorException::invalidModuleEntityId($this);
            }
        } else {
            $this->name = $item['name'] ?? null;
            if (!$this->name || !$this->moduleExists()) {
                throw GridEditorException::invalidModule($this);
            }
        }

        // Configuration
        if (isset($item['configuration'])) {
            $this->configuration = $item['configuration'];
            if (!is_array($this->configuration)) {
                throw GridEditorException::invalidModuleConfiguration($this);
            }
        }
    }


    /**
     * Is this item equal to specified item?
     *
     * @param \App\Structures\GridEditor\ContentItem $item
     * @return bool
     */
    public function isEqual(ContentItem $item): bool
    {
        return $item instanceof ContentModule &&
            !$this->hasConfiguration() &&
            !$item->hasConfiguration() &&
            $item->entityId === $this->entityId &&
            $item->getModuleName() === $this->getModuleName() &&
            $item->isUniversal === $this->isUniversal;
    }


    /**
     * Should module entity exist?
     *
     * @return bool
     */
    public function shouldExist(): bool
    {
        return boolval($this->entityId);
    }


    /**
     * Get entity instance.
     *
     * @return \App\Contracts\IsGridEditorEntity
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @cached
     */
    public function getEntity(): IsGridEditorEntity
    {
        if ($this->isUniversal()) {
            return $this->getUniversalEntity();
        }

        if (ContentTree::$entities && isset(ContentTree::$entities[$this->entityId])) {
            return ContentTree::$entities[$this->entityId];
        }

        $entity = Entity::find($this->entityId);

        if (!$entity) {
            throw new ModelNotFoundException();
        }

        return ContentTree::$entities[$this->entityId] = $entity;
    }


    /**
     * Get universal module entity.
     *
     * @return \App\Models\UniversalModule\UniversalModuleEntity
     */
    public function getUniversalEntity(): UniversalModuleEntity
    {
        if (ContentTree::$universalEntities && isset(ContentTree::$universalEntities[$this->entityId])) {
            return ContentTree::$universalEntities[$this->entityId];
        }

        return ContentTree::$universalEntities[$this->entityId] = UniversalModuleEntity::find($this->entityId);
    }


    /**
     * Get entity identifier.
     *
     * @return int
     */
    public function getEntityId(): int
    {
        return (int)$this->entityId;
    }


    /**
     * Get module instance.
     *
     * @return bool
     */
    public function moduleExists(): bool
    {
        if ($this->isUniversal) {
            return SingletonEnum::universalModules()->has($this->getModuleName());
        }

        return boolval(\Module::find($this->getModuleName()));
    }


    /**
     * Get module name.
     *
     * @return string
     */
    public function getModuleName(): string
    {
        if ($this->name) {
            return $this->name;
        }

        if ($this->isUniversal()) {
            $this->name = $this->getEntity()->prefix;
        } else {
            $this->name = $this->getEntity()->module;
        }

        return $this->name;
    }


    /**
     * Is universal module?
     *
     * @return bool
     */
    public function isUniversal(): bool
    {
        return $this->isUniversal;
    }


    /**
     * Has module configuration?
     *
     * @return bool
     */
    public function hasConfiguration(): bool
    {
        return !is_null($this->configuration);
    }


    /**
     * Get module configuration.
     *
     * @return array
     */
    public function getConfiguration(): array
    {
        return $this->configuration;
    }


    /**
     * Create entity for specified content.
     *
     * @param \App\Models\Interfaces\IsGridEditorContent $content
     * @return \App\Contracts\IsGridEditorEntity
     */
    public function createEntityForContent(IsGridEditorContent $content): IsGridEditorEntity
    {
        if ($this->isUniversal()) {
            $entity = UniversalModuleEntity::createFromConfiguration(
                $this->getModuleName(), $this->getConfiguration()
            );
        } else {
            $entity = $content->createModuleEntity($this->getModuleName(), $this->getConfiguration());
        }

        $this->updateEntity($entity);
        return $entity;
    }


    /**
     * Duplicate entity to specified content.
     *
     * @param \App\Models\Interfaces\IsGridEditorContent $content
     * @return \App\Models\Module\Entity
     */
    public function duplicateToContent(IsGridEditorContent $content): IsGridEditorEntity
    {
        $entity = $this->getEntity()->duplicateForNewContent(
            $content,
            $this->hasConfiguration() ? $this->getConfiguration() : null
        );

        $this->updateEntity($entity);
        return $entity;
    }


    /**
     * Update entity.
     *
     * @param \App\Contracts\IsGridEditorEntity $entity
     */
    public function updateEntity(IsGridEditorEntity $entity): void
    {
        if ($entity instanceof UniversalModuleEntity) {
            $this->isUniversal = true;
            ContentTree::$universalEntities[$this->entityId] = $entity;
        } else {
            $this->isUniversal = false;
            ContentTree::$entities[$this->entityId] = $entity;
        }

        $this->entityId = $entity->getKey();
        $this->cachedEntity = $entity;
    }


    /**
     * Convert content container to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $output = [
            'type' => $this->type,
            'entity_id' => $this->entityId
        ];

        if ($this->isUniversal) {
            $output['universal'] = true;
        }

        return $output;
    }


    /**
     * Check if this module is enabled.
     *
     * @return bool
     */
    protected function isEntityModuleEnabled(): bool
    {
        if ($this->isUniversal()) {
            return $this->moduleExists();
        }

        if (is_null(static::$enabledModules)) {
            static::$enabledModules = InstalledModule::enabled()->get()->keyBy('name');
        }

        try {
            return static::$enabledModules->has($this->getModuleName());
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }


    /**
     * Convert module to html.
     *
     * @param array $renderAttributes
     * @return string
     */
    public function toHtml(array $renderAttributes = []): string
    {
        if ($this->isActive === false || !$this->canModuleRender()) {
            return '';
        }

        return $this->getEntity()->render($renderAttributes);
    }

    /**
     * Check if module can render.
     *
     * @return bool
     */
    private function canModuleRender(): bool
    {
        if (!$this->isEntityModuleEnabled()) {
            return false;
        }

        if (Module::$searchRendering && !$this->isUniversal()) {
            /** @var \App\Models\Module\InstalledModule $module */
            $module = static::$enabledModules->get($this->getModuleName());
            return !$module->module->config('exclude_from_search', false);
        }

        return true;
    }
}
