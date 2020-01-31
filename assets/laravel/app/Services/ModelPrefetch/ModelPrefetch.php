<?php

namespace App\Services\ModelPrefetch;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

final class ModelPrefetch
{
    /** @var int */
    private const CACHE_LIFETIME = 60 * 24 * 7; // week

    /**
     * @var bool
     */
    private $isActive;

    /**
     * @var bool
     */
    private $isRecording;

    /**
     * @var array
     */
    private $fetchedModelsMap;

    /**
     * @var array
     */
    private $loadedModels;

    /**
     * ModelPrefetch constructor.
     * @param bool $active
     */
    public function __construct(bool $active)
    {
        $this->isActive = $active;
        $this->fetchedModelsMap = [];
        $this->loadedModels = [];
    }


    public function startRecording(): void
    {
        $this->isRecording = true;
    }


    public function stopRecording(): void
    {
        $this->isRecording = false;
    }


    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function catchModelRestored(Model $model): void
    {
        if (!$this->isActive()) {
            return;
        }

        $this->fetchedModelsMap[get_class($model)][$model->getKey()] = $model->getKey();
    }


    /**
     * @param string $url
     */
    public function updateCache(string $url): void
    {
        if (!$this->isActive) {
            return;
        }

        \Cache::put(md5($url), serialize($this->fetchedModelsMap), self::CACHE_LIFETIME);
    }


    /**
     * @param string $url
     */
    public function prefetch(string $url): void
    {
        if (!$this->isActive) {
            return;
        }

        $cached = \Cache::get(md5($url));
        if (!$cached) {
            return;
        }

        $this->loadedModels = [];
        $models = unserialize($cached);

        /**
         * @var string $modelClass
         * @var int[]|string[] $ids
         */
        foreach ($models as $modelClass => $ids) {
            /** @var \Illuminate\Database\Eloquent\Model $modelInstance */
            $modelInstance = new $modelClass;
            $models = $modelInstance->newQuery()->find($ids);

            if ($models instanceof Collection) {
                $this->loadedModels[$modelClass] = $models->keyBy($modelInstance->getKeyName())->all();
            } else {
                $this->loadedModels[$modelClass][$models->getKey()] = $models;
            }
        }
    }


    /**
     * @param string $class
     * @param $key
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getPrefetchedModel(string $class, $key): ?Model
    {
        if (!$this->isActive()) {
            return null;
        }

        return $this->loadedModels[$class][$key] ?? null;
    }


    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isRecording && $this->isActive;
    }
}
