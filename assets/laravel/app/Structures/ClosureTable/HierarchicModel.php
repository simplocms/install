<?php
/**
 * ClosureTable.php created by Patrik Václavek
 */

namespace App\Structures\ClosureTable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * Class HierarchicModel - implements closure table hierarchy.
 * @package App\Structures\ClosureTable
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 *
 * @property-read \App\Structures\ClosureTable\HierarchicModel parent
 * @property-read \App\Structures\ClosureTable\Collection|\App\Structures\ClosureTable\HierarchicModel[] children
 * @property-read \App\Structures\ClosureTable\Collection|\App\Structures\ClosureTable\HierarchicModel[] ancestors
 * @property-read \App\Structures\ClosureTable\Collection|\App\Structures\ClosureTable\HierarchicModel[] descendants
 * @property-read \App\Structures\ClosureTable\Collection|\App\Structures\ClosureTable\HierarchicModel[] siblings
 */
abstract class HierarchicModel extends Model
{
    /**
     * @var string COLUMN ASCENDANT NAME
     */
    const COLUMN_PARENT = 'parent_id';

    /**
     * Closure model instance.
     *
     * @var \App\Structures\ClosureTable\Closure
     */
    protected $closure;

    /**
     * Indicates whether to delete all children of deleted node.
     *
     * @var bool
     */
    static protected $cascadeOnDelete = true;

    /**
     * Identifiers of children which will be deleted after model is deleted.
     *
     * @var array
     */
    private $cachedChildrenToDelete;

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        // If model's parent was changed, the closure table rows will update automatically.
        static::updating(function (HierarchicModel $model) {
            if ($model->isDirty(self::COLUMN_PARENT)) {
                $model->getClosure()->changeAncestor($model->hasParent() ? $model->getParentId() : null);

                // Make sure that parent id attribute is synced to original,
                // to prevent repeated updating (some hooks might update the model again).
                $model->syncParentAttributeToOriginal();
            }
        });

        // When entity is created, the appropriate, relations will be inserted to closure table.
        static::created(function (HierarchicModel $model) {
            $model->getClosure()->insertClosure(
                $model->hasParent() ? $model->getParentId() : $model->getKey(), $model->getKey()
            );

            // Make sure that parent id attribute is synced to original,
            // to prevent updating it after creating (some hooks might update the model after creation).
            $model->syncParentAttributeToOriginal();
        });

        // Delete all descendants of deleted node.
        if (self::$cascadeOnDelete) {
            static::deleting(function (HierarchicModel $model) {
                $model->cacheChildrenToDelete();
            });

            static::deleted(function (HierarchicModel $model) {
                $model->deleteCachedChildren();
            });
        }

        parent::boot();
    }


    /**
     * Parent of the node.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(get_class($this), self::COLUMN_PARENT);
    }


    /**
     * Ancestors of the node.
     * Collection contains current object. To prevent this use `skipCurrent` scope.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function ancestors(): HasManyThrough
    {
        return $this->newHasManyThrough(
            $this->newRelatedInstance(get_class($this))->newQuery(), $this, $this->getClosure(),
            $this->getClosure()->getDescendantColumn(), $this->getKeyName(), $this->getKeyName(),
            $this->getClosure()->getAncestorColumn()
        )->where($this->getClosure()->getQualifiedDepthColumn(), '>=', 0);
    }


    /**
     * Descendants of the node.
     * Collection contains current object. To prevent this use `skipCurrent` scope.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function descendants(): HasManyThrough
    {
        return $this->newHasManyThrough(
            $this->newRelatedInstance(get_class($this))->newQuery(), $this, $this->getClosure(),
            $this->getClosure()->getAncestorColumn(), $this->getKeyName(), $this->getKeyName(),
            $this->getClosure()->getDescendantColumn()
        )->where($this->getClosure()->getQualifiedDepthColumn(), '>=', 0);
    }


    /**
     * Direct children of the node.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(get_class($this), self::COLUMN_PARENT, $this->getKeyName());
    }


    /**
     * Siblings of the node.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function siblings(): HasMany
    {
        return $this->hasMany(get_class($this), self::COLUMN_PARENT, self::COLUMN_PARENT)
            ->where($this->getKeyName(), '<>', $this->getKey());
    }


    /**
     * Indicates whether the model has no ancestors.
     *
     * @return bool
     */
    public function isRoot(): bool
    {
        return $this->exists && !$this->hasParent();
    }


    /**
     * Get closure model.
     *
     * @return \App\Structures\ClosureTable\Closure
     */
    public function getClosure(): Closure
    {
        if (is_null($this->closure)) {
            $this->closure = new Closure();
            $this->closure->setTable($this->getTable() . '_closures');
        }

        // setup closure values if not already set and model exists.
        if (!$this->closure->hasAncestor() && $this->exists) {
            $this->closure->setAncestor($this->getKey());
            $this->closure->setDescendant($this->getKey());
            $this->closure->setDepth(0);
        }

        return $this->closure;
    }


    /**
     * Get parent id. Root node returns null.
     *
     * @return int
     */
    public function getParentId(): int
    {
        return $this->getAttribute(self::COLUMN_PARENT);
    }


    /**
     * Check if node has parent.
     *
     * @return bool
     */
    public function hasParent(): bool
    {
        return boolval($this->getAttribute(self::COLUMN_PARENT));
    }


    /**
     * Get depth of the node.
     *
     * @return int
     */
    public function getDepth(): int
    {
        if (!$this->exists) {
            return 0;
        }

        return \DB::table($this->getClosure()->getTable())
            ->where($this->getClosure()->getDescendantColumn(), $this->getKey())
            ->max($this->getClosure()->getDepthColumn());
    }


    /**
     * Scope to not include nodes with zero depth.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeSkipCurrent(Builder $query)
    {
        $query->where($this->getClosure()->getQualifiedDepthColumn(), '>', 0);
    }


    /**
     * Create a new Hierarchic Eloquent Collection instance.
     *
     * @param  \Illuminate\Database\Eloquent\Model[] $models
     * @return \App\Structures\ClosureTable\Collection
     */
    public function newCollection(array $models = [])
    {
        return new Collection($models);
    }


    /**
     * Get tree.
     *
     * @return \App\Structures\ClosureTable\Collection
     */
    public static function getTree(): Collection
    {
        return (new static)->get()->toTree();
    }


    /**
     * Sync parent attribute to original.
     *
     * @return void
     */
    private function syncParentAttributeToOriginal(): void
    {
        if (isset($this->attributes[self::COLUMN_PARENT])) {
            $this->syncOriginalAttribute(self::COLUMN_PARENT);
        }
    }


    /**
     * Cache children of the node, which will be deleted after node is deleted.
     */
    private function cacheChildrenToDelete()
    {
        $this->cachedChildrenToDelete = $this->children()->pluck('id')->toArray();
    }


    /**
     * Cache children of the node, which will be deleted after node is deleted.
     */
    private function deleteCachedChildren()
    {
        if (!$this->cachedChildrenToDelete) {
            return;
        }

        self::findMany($this->cachedChildrenToDelete)->each(function (HierarchicModel $model) {
            $model->delete();
        });
    }
}
