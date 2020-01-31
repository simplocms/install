<?php

namespace App\Structures\FormFields;


final class Checkbox extends Input
{
    /**
     * Checkbox constructor.
     * @param string $name
     * @param string|null $label
     */
    public function __construct(string $name, string $label = null)
    {
        parent::__construct(self::TYPE_CHECKBOX, $name, $label);
    }

    /**
     * Helper method for chainability.
     *
     * @param string $name
     * @param string|null $label
     * @param string|null $_
     * @return static
     */
    public static function make(string $name, string $label = null, string $_ = null)
    {
        return new static($name, $label);
    }
}
