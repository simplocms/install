<?php

namespace App\Traits;

use App\Services\ModelPrefetch\ModelPrefetch;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Trait PrefetchTrait
 * @package App\Traits
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait PrefetchTrait
{
    /**
     * Register model prefetching
     */
    public static function registerModelPrefetching(): void
    {
        static::registerModelEvent('retrieved', function (Model $model) {
            resolve(ModelPrefetch::class)->catchModelRestored($model);
        });
    }


    /**
     * @param \Illuminate\Contracts\Support\Arrayable|array $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function getPrefetched($id)
    {
        $prefetch = resolve(ModelPrefetch::class);

        if (!$prefetch || !$prefetch->isActive()) {
            return parent::find($id);
        }

        if (is_array($id) || $id instanceof Arrayable) {
            $models = $this->newCollection();
            $idsToLoad = [];
            foreach ($id as $modelKey) {
                $model = $prefetch->getPrefetchedModel(static::class, $modelKey);

                if (!$model) {
                    $idsToLoad[] = $modelKey;
                } else {
                    $models->push($model);
                }
            }

            if ($idsToLoad) {
                return $models->merge($this->findMany($idsToLoad));
            }

            return $models;
        }

        $model = $prefetch->getPrefetchedModel(static::class, $id);
        return $model ?? parent::find($id);
    }


    /**
     * @param \Illuminate\Contracts\Support\Arrayable|array $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function getPrefetchedOrFail($id)
    {
        $prefetch = resolve(ModelPrefetch::class);

        if (!$prefetch || !$prefetch->isActive()) {
            return parent::findOrFail($id);
        }

        $result = $this->findPrefetched($id);

        if (is_array($id) || $id instanceof Arrayable) {
            if ($result->count() === count(array_unique($id))) {
                return $result;
            }
        } elseif (!is_null($result)) {
            return $result;
        }

        throw (new ModelNotFoundException)->setModel(
            get_class($this->model), $id
        );
    }


    /**
     * @param \Illuminate\Contracts\Support\Arrayable|array $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public static function findPrefetched($id)
    {
        return (new static)->getPrefetched($id);
    }


    /**
     * @param \Illuminate\Contracts\Support\Arrayable|array $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public static function findPrefetchedOrFail($id)
    {
        return (new static)->getPrefetchedOrFail($id);
    }

}
