<?php

namespace App\Models\UniversalModule;

use App\Contracts\IsGridEditorEntity;
use App\Models\Interfaces\IsGridEditorContent;
use App\Structures\Enums\SingletonEnum;
use App\Traits\AdvancedEloquentTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\View;


/**
 * Represents general module in grid editor.
 * Specifies type of module and its configuration.
 *
 * @property int id  - identifier of the entity
 * @property string prefix - the identifier of universal module to which the entity belongs
 * @property string view - the identifier of this entity's view
 * @property bool all_items - should be loaded all items of the module?
 * @property-read \App\Models\UniversalModule\UniversalModuleItem[]|\Illuminate\Database\Eloquent\Collection items
 */
class UniversalModuleEntity extends Model implements IsGridEditorEntity
{
    use AdvancedEloquentTrait;

    /**
     * Model table.
     *
     * @var string
     */
    protected $table = 'universal_module_entities';

    /**
     * Mass assignable attributes.
     *
     * @var array
     */
    protected $fillable = ['prefix', 'view', 'all_items'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'all_items' => 'boolean',
    ];

    /**
     * The items that are attached to the entity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(UniversalModuleItem::class,
            'universal_module_entity_item',
            'universal_module_entity_id',
            'universal_module_item_id'
        );
    }


    /**
     * Render entity content.
     *
     * @param array $options
     * @return mixed
     * @throws \Throwable
     */
    public function render(array $options = []): string
    {
        if (!View::exists($this->view)) {
            return view('module-view::missing_view', ['name' => $this->view])->render();
        }

        $language = $options['language'] ?? SingletonEnum::languagesCollection()->getContentLanguage();
        $items = $this->all_items ? UniversalModuleItem::getAll($this->prefix, $language) : $this->items;
        return view($this->view, compact('items'))->render();
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
        $configuration = $configuration ?? [];
        $newEntity = UniversalModuleEntity::create([
            'prefix' => $this->prefix,
            'view' => $configuration['view'] ?? $this->view,
            'all_items' => $configuration['all_items'] ?? $this->all_items,
        ]);

        if (!$newEntity->all_items) {
            if (isset($configuration['items'])) {
                $newEntity->items()->attach($configuration['items']);
            } else {
                $newEntity->items()->attach($this->items->pluck('id'));
            }
        }

        return $newEntity;
    }


    /**
     * Create entity from configuration from grid editor.
     *
     * @param string $prefix
     * @param array $configuration
     * @return \App\Models\UniversalModule\UniversalModuleEntity
     */
    public static function createFromConfiguration(string $prefix, array $configuration): UniversalModuleEntity
    {
        $entity = UniversalModuleEntity::create([
            'prefix' => $prefix,
            'view' => $configuration['view'],
            'all_items' => $configuration['all_items'] ?? true,
        ]);

        if (!$entity->all_items) {
            $entity->items()->attach($configuration['items'] ?? []);
        }

        return $entity;
    }


    /**
     * Has configuration changed - hence entity was changed.
     *
     * @param array $configuration
     * @return bool
     */
    public function hasChanged(array $configuration): bool
    {
        $items = array_map('intval', $configuration['items'] ?? []);
        return ($configuration['view'] ?? null) !== $this->view ||
            $configuration['all_items'] !== $this->all_items ||
            $this->items->count() !== count($items) ||
            $this->items->pluck('id')->intersect($items)->count() !== count($items);
    }


    /**
     * Does entity belong to specified content?
     *
     * @param \App\Models\Interfaces\IsGridEditorContent $content
     * @return bool
     */
    public function doesBelongToContent(IsGridEditorContent $content): bool
    {
        return true; // TODO: Do exclusive for content?
    }


    /**
     * Update configuration of the entity.
     *
     * @param array $configuration
     */
    public function updateConfiguration(array $configuration): void
    {
        $this->update(array_only($configuration, ['view', 'all_items']));

        $this->items()->sync($this->all_items ? [] : $configuration['items']);
    }
}
