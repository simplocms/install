<?php
/**
 * Input.php created by Patrik Václavek
 */

namespace App\Structures\FormFields;


class Input extends AbstractFormField
{
    /**
     * Specifies a short hint that describes the expected value of an input.
     *
     * @var string
     */
    protected $placeholder;

    /**
     * Set a short hint that describes the expected value of an input.
     *
     * @param string $placeholder
     * @return $this
     */
    public function placeholder(string $placeholder): Input
    {
        $this->placeholder = $placeholder;
        return $this;
    }


    /**
     * Convert to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $attributes = parent::toArray();
        $attributes['placeholder'] = $this->placeholder;
        return $attributes;
    }
}
