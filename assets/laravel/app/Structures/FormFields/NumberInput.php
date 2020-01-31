<?php
/**
 * NumberInput.php created by Patrik Václavek
 */

namespace App\Structures\FormFields;

class NumberInput extends Input
{
    /**
     * Specifies the maximum value for an input.
     *
     * @var int
     */
    protected $max;

    /**
     * Specifies a minimum value for an input.
     *
     * @var int
     */
    protected $min;

    /**
     * Specifies the legal number intervals for an input field.
     *
     * @var int
     */
    protected $step;

    /**
     * Specifies float the legal number.
     *
     * @var bool
     */
    protected $isFloat = false;

    /**
     * NumberInput constructor.
     * @param string $name
     * @param string|null $label
     */
    public function __construct(string $name, string $label = null)
    {
        parent::__construct(self::TYPE_NUMBER, $name, $label);
    }


    /**
     * Set maximum value for an input.
     *
     * @param int $max
     * @return \App\Structures\FormFields\NumberInput
     */
    public function max(int $max): NumberInput
    {
        $this->max = $max;
        return $this;
    }


    /**
     * Set minimum value for an input.
     *
     * @param int $min
     * @return \App\Structures\FormFields\NumberInput
     */
    public function min(int $min): NumberInput
    {
        $this->min = $min;
        return $this;
    }


    /**
     * Set the legal number intervals for an input field.
     *
     * @param int|float|string $step
     * @return \App\Structures\FormFields\NumberInput
     */
    public function step($step): NumberInput
    {
        $this->step = $step;
        return $this;
    }

    /**
     * Set float as the legal number.
     * Needed for validation.
     *
     * @return \App\Structures\FormFields\NumberInput
     */
    public function float(): NumberInput
    {
        $this->isFloat = true;
        return $this;
    }


    /**
     * Convert to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'max' => $this->max,
            'min' => $this->min,
            'step' => $this->step,
        ]);
    }


    /**
     * Get validation rules for the field.
     *
     * @return array
     */
    public function getValidationRules(): array
    {
        $rules = parent::getValidationRules();
        $rules[] = $this->isFloat ? 'numeric' : 'int';

        if ($this->max) {
            $rules[] = 'max:' . $this->max;
        }

        if ($this->min) {
            $rules[] = 'min:' . $this->min;
        }

        return $rules;
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
