<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Arr;

/**
 * Trait AdvancedEloquentTrait
 * @package App\Traits
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait AdvancedEloquentTrait
{
    /**
     * Mark model is using this trait.
     *
     * @var bool
     */
    static protected $usesAdvancedEloquentTrait = true;

    /**
     * Mutators for setting the attribute.
     *
     * @var callable[]
     */
    protected static $setAttributeMutators = [];

    /**
     * Mutators for getting the attribute.
     *
     * @var callable[]
     */
    protected static $getAttributeMutators = [];

    /**
     * Array of used scopes
     * @var array
     */
    protected $scopeUsages = [];

    /**
     * Determine if a model has a global scope.
     *
     * @param \Illuminate\Database\Eloquent\Scope|string $scope
     * @return bool
     */
    abstract public static function hasGlobalScope($scope);

    /**
     * Boot trait.
     */
    public static function bootAdvancedEloquentTrait()
    {
        static::registerSetAttributeMutator(
            function (Model $model, string $key, $value, bool &$interrupt) {
                /** @var \App\Traits\AdvancedEloquentTrait $model */
                if ($model->isFieldNullable($key) && $value === '') {
                    $value = null;
                }

                $interrupt = false;
                return $value;
            }
        );
    }


    /**
     * Register attribute mutator for setter.
     *
     * @param callable $callback
     */
    public static function registerSetAttributeMutator($callback)
    {
        static::$setAttributeMutators[] = $callback;
    }


    /**
     * Register attribute mutator for getter.
     *
     * @param callable $callback
     */
    public static function registerGetAttributeMutator($callback)
    {
        static::$getAttributeMutators[] = $callback;
    }


    /**
     * Returns model table name.
     *
     * @return mixed
     */
    public static function getTableName()
    {
        return ((new self)->getTable());
    }


    /**
     * Returns true if scope was not used and marks scope as used
     *
     * @param $name
     * @return bool
     */
    private function uniqueScope($name)
    {
        if (isset($this->scopeUsages[$name])) {
            return false;
        }
        return $this->scopeUsages[$name] = true;
    }


    /**
     * Pass joins
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopePassJoins($query)
    {
        if (!$this->uniqueScope('passJoins')) return;

        $query->select([$this->getTable() . '.*']);
    }


    /**
     * Get nullable fields.
     *
     * @return array
     */
    public function getNullableFields(): array
    {
        return $this->nullIfEmpty ?? [];
    }


    /**
     * is field nullable?
     *
     * @param string $field
     * @return bool
     */
    public function isFieldNullable(string $field): bool
    {
        return in_array($field, $this->getNullableFields());
    }


    /**
     * Set a given attribute on the model.
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        foreach (static::$setAttributeMutators as $mutator) {
            $interrupt = false;
            $value = $mutator($this, $key, $value, $interrupt);

            if ($interrupt) {
                return $this;
            }
        }

        if (property_exists(self::class, 'setAttribute')) {
            return $this::setAttribute($key, $value);
        }

        return parent::setAttribute($key, $value);
    }


    /**
     * Get an attribute from the model.
     *
     * @param string $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        if (property_exists(self::class, 'getAttribute')) {
            $value = $this::getAttribute($key);
        } else {
            $value = parent::getAttribute($key);
        }

        if (is_null($value)) {
            foreach (static::$getAttributeMutators as $mutator) {
                $value = $mutator($this, $key, $value);
            }
        }

        return $value;
    }


    /**
     * Get form attributes from request's old inputs if available,
     * otherwise from model attributes.
     *
     * @param array $attributes
     * @return array
     */
    public function getFormAttributes(array $attributes): array
    {
        $output = [];

        foreach ($attributes as $key => $value) {
            $inputAttribute = is_int($key) ? $value : $key;
            $outputAttribute = is_int($key) ? $value : $value;

            $oldFormValue = old($inputAttribute);
            $output[$outputAttribute] = is_null($oldFormValue) ? $this->getAttribute($inputAttribute) : $oldFormValue;
        }

        return $output;
    }


    /**
     * Get form attributes from request's old inputs if available,
     * otherwise from model attributes. As JSON string.
     *
     * @param array $attributes
     * @return string
     */
    public function getFormAttributesJson(array $attributes): string
    {
        return json_encode($this->getFormAttributes($attributes));
    }


    /**
     * @param string $scopeClass
     * @param callable $callback
     */
    public static function withoutScope(string $scopeClass, callable $callback): void
    {
        $key = static::class . '.' . $scopeClass;
        $scope = Arr::get(static::$globalScopes, $key);
        if (!$scope) {
            $callback();
        }

        Arr::forget(static::$globalScopes, $key);
        $callback();
        Arr::set(static::$globalScopes, $key, $scope);
    }
}
