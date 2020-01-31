<?php
/**
 * Select.php created by Patrik Václavek
 */

namespace App\Structures\FormFields;

use Illuminate\Validation\Rule;

class Select extends AbstractFormField
{
    /**
     * Select options.
     *
     * @var array
     */
    protected $options;

    /**
     * Specifies that multiple options can be selected at once.
     *
     * @var bool
     */
    protected $multiple;

    /**
     * Select constructor.
     * @param string $name
     * @param string|null $label
     */
    public function __construct(string $name, string $label = null)
    {
        parent::__construct(self::TYPE_SELECT, $name, $label);
    }


    /**
     * Set select options.
     *
     * @param array $options
     * @return \App\Structures\FormFields\Select
     */
    public function options(array $options): Select
    {
        $this->options = $options;
        return $this;
    }


    /**
     * Set select as multiple select.
     *
     * @param bool $multiple
     * @return \App\Structures\FormFields\Select
     */
    public function multiple(bool $multiple = true): Select
    {
        $this->multiple = $multiple;
        return $this;
    }


    /**
     * Is select multiple?
     *
     * @return bool
     */
    public function isMultiple(): bool
    {
        return $this->multiple ?? false;
    }


    /**
     * Convert to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'options' => $this->options,
            'multiple' => $this->multiple,
        ]);
    }

    /**
     * Extend validation rules.
     *
     * @param array $rules
     * @return array
     */
    public function extendValidationRules(array $rules): array
    {
        $fieldRules = $this->getValidationRules();

        if ($this->multiple) {
            $fieldRules[] = 'array';
            $rules[$this->getName()] = $fieldRules;
            $rules[$this->getName() . '.*'] = Rule::in(array_keys($this->options));
        } else {
            $fieldRules[] = Rule::in(array_keys($this->options));
            $rules[$this->getName()] = $fieldRules;
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
