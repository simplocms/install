<?php
/**
 * TextInput.php created by Patrik Václavek
 */

namespace App\Structures\FormFields;


class TextInput extends Input
{
    /**
     * Specifies the maximum number of characters allowed in input.
     *
     * @var int
     */
    protected $maxLength;

    /**
     * TextInput constructor.
     * @param string $name
     * @param string|null $label
     */
    public function __construct(string $name, string $label = null)
    {
        parent::__construct(self::TYPE_TEXT, $name, $label);
    }


    /**
     * Set maximum number of characters allowed in input.
     *
     * @param int $maxLength
     * @return $this
     */
    public function maxLength(int $maxLength): self
    {
        $this->maxLength = $maxLength;
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
        $attributes['maxlength'] = $this->maxLength;
        return $attributes;
    }


    /**
     * Get validation rules for the field.
     *
     * @return array
     */
    public function getValidationRules(): array
    {
        $rules = parent::getValidationRules();
        $rules[] = 'string';

        if ($this->maxLength) {
            $rules[] = 'max:' . $this->maxLength;
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
