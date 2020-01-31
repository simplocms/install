<?php

namespace App\Models\Module;

use App\Contracts\IsGridEditorEntity;
use App\Models\Interfaces\ModuleConfigurationInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\Interfaces\IsGridEditorContent;

/**
 * Represents general module in grid editor.
 * Specifies type of module and its configuration.
 *
 * @property int id  - identifier of the entity
 * @property string module - class of the module
 * @property bool enabled - is entity enabled?
 * @property string model - the class of model to which the entity belongs
 * @property int model_id - the identifier of model to which the entity belongs
 * @property int configuration_id - identifier of specific configuration
 */
class Entity extends Model implements IsGridEditorEntity
{
    /**
     * Model table.
     *
     * @var string
     */
    protected $table = 'module_entities';

    /**
     * Mass assignable attributes.
     *
     * @var array
     */
    protected $fillable = ['module', 'enabled', 'model', 'model_id'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'model_id' => 'int',
        'enabled' => 'boolean'
    ];

    /**
     * Configuration cached.
     *
     * @var \App\Models\Interfaces\ModuleConfigurationInterface
     */
    private $cachedConfiguration;


    /**
     * Get entity configuration.
     * @cached
     *
     * @return \App\Models\Interfaces\ModuleConfigurationInterface
     */
    public function getConfiguration()
    {
        if (!$this->cachedConfiguration) {
            $this->cachedConfiguration = $this->getModule()
                ->getConfiguration($this->configuration_id);
        }

        return $this->cachedConfiguration;
    }


    /**
     * Create new entity configuration.
     *
     * @param array $attributes
     *
     * @return \App\Models\Interfaces\ModuleConfigurationInterface|null
     */
    public function createConfiguration(array $attributes)
    {
        $module = $this->getModule();

        if ($class = $module->getConfigurationClass()) {
            $newConfiguration = new $class();
            $newConfiguration->inputFill($attributes);
            $newConfiguration->save();
            $this->configuration_id = $newConfiguration->id;
            return $this->cachedConfiguration = $newConfiguration;
        }

        return null;
    }


    /**
     * Render entity content.
     *
     * @param array $options
     * @return mixed
     */
    public function render(array $options = []): string
    {
        return $this->getConfiguration()->render($options);
    }


    /**
     * Get module.
     *
     * @return \Module
     */
    public function getModule()
    {
        return \Module::findOrFail($this->module);
    }


    /**
     * Duplicate entity for specified content.
     *
     * @param \App\Models\Interfaces\IsGridEditorContent $content
     * @param array|null $configuration - configuration options
     *
     * @return self - new entity
     */
    public function duplicateForNewContent(
        IsGridEditorContent $content, array $configuration = null
    ): IsGridEditorEntity
    {
        $newEntity = $this->createDuplicate($configuration);
        $newEntity->model_id = $content->getKey();
        $newEntity->model = get_class($content);
        $newEntity->previous_entity_id = $this->id;
        $newEntity->save();

        return $newEntity;
    }


    /**
     * Create duplicate of the entity.
     *
     * @param array|null $newConfiguration - configuration of the entity to override.
     *
     * @return self
     */
    public function createDuplicate(array $newConfiguration = null)
    {
        $newEntity = new Entity([
            'module' => $this->module,
            'model' => $this->model,
            'model_id' => $this->model_id,
            'enabled' => true
        ]);

        $newConfiguration = $this->duplicateConfiguration($newConfiguration);

        $newEntity->previous_entity_id = $this->previous_entity_id;
        $newEntity->configuration_id = $newConfiguration->id;

        return $newEntity;
    }


    /**
     * Duplicate entity configuration.
     *
     * @param array|null $configuration - configuration of the entity to override.
     *
     * @return \App\Models\Interfaces\ModuleConfigurationInterface
     */
    private function duplicateConfiguration(array $configuration = null)
    {
        /** @var ModuleConfigurationInterface $newConfiguration */
        $newConfiguration = $this->getConfiguration()->replicate();

        if ($configuration) {
            $newConfiguration->inputFill($configuration);
        }

        $newConfiguration->created_at = Carbon::now();
        $newConfiguration->updated_at = $newConfiguration->created_at;
        $newConfiguration->save();

        return $newConfiguration;
    }


    /**
     * Render entity preview.
     *
     * @param \App\Models\Interfaces\ModuleConfigurationInterface|null $customConfiguration
     *
     * @return string
     */
    public function renderPreview($customConfiguration = null)
    {
        $configuration = $customConfiguration ?: $this->getConfiguration();

        return $this->getModule()->view('module_preview', [
            'configuration' => $configuration
        ])->render();
    }


    /**
     * Checks if entity belongs to given content.
     *
     * @param \App\Models\Interfaces\IsGridEditorContent $content
     * @return bool
     */
    public function doesBelongToContent(IsGridEditorContent $content): bool
    {
        return $this->model_id === $content->getKey() && $this->model !== get_class($content);
    }


    /**
     * Has configuration changed - hence entity was changed.
     *
     * @param array $configuration
     * @return bool
     */
    public function hasChanged(array $configuration): bool
    {
        return $this->getConfiguration()->inputFill($configuration)->isDirty();
    }


    /**
     * Update configuration of the entity.
     *
     * @param array $configuration
     */
    public function updateConfiguration(array $configuration): void
    {
        $this->getConfiguration()->inputFill($configuration)->save();
    }
}
