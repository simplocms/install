<?php
/**
 * Image.php created by Patrik Václavek
 */

namespace App\Structures\FormFields;

use App\Rules\MediaImageRule;

class Image extends AbstractFormField
{
    /**
     * MediaFile constructor.
     * @param string $name
     * @param string|null $label
     */
    public function __construct(string $name, string $label = null)
    {
        parent::__construct(self::TYPE_IMAGE, $name, $label);
    }


    /**
     * Get validation rules for the field.
     *
     * @return array
     */
    public function getValidationRules(): array
    {
        $rules = parent::getValidationRules();
        $rules[] = new MediaImageRule;
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
