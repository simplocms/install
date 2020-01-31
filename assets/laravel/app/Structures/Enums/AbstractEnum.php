<?php

namespace App\Structures\Enums;

use Illuminate\Support\Collection;

abstract class AbstractEnum
{
    /**
     * The enum value.
     *
     * @var mixed
     */
    protected $value;

    /**
     * The enum constants.
     *
     * @var array
     */
    protected static $constants = [];

    /**
     * Get the enum keys.
     *
     * @return array
     */
    public static function keys(): array
    {
        return static::constants()->keys()->all();
    }


    /**
     * Get the enum values.
     *
     * @return array
     */
    public static function values(): array
    {
        return static::constants()->values()->toArray();
    }


    /**
     * Get the enum constants.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function constants(): Collection
    {
        if (!isset(static::$constants[static::class])) {
            static::$constants[static::class] = collect(
                (new \ReflectionClass(static::class))->getConstants()
            );
        }

        return static::$constants[static::class];
    }


    /**
     * Convert enum to array.
     *
     * @return array
     */
    public static function toArray(): array
    {
        $result = [];
        $hasLabels = method_exists(static::class, 'labels');
        $labels = $hasLabels ? static::labels() : [];
        foreach (static::constants() as $const => $value) {
            $result[$const] = [
                'value' => $value,
                'label' => $labels[$value] ?? null
            ];
        }

        return $result;
    }


    /**
     * Convert enum to JSON object string.
     *
     * @return string
     */
    public static function toJson(): string
    {
        return json_encode(static::toArray());
    }
}
