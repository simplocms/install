<?php
/**
 * AbstractFormField.php created by Patrik Václavek
 */

namespace App\Structures\FormFields;

abstract class AbstractFormField implements \JsonSerializable
{
    const TYPE_TEXT = 'text';
    const TYPE_NUMBER = 'number';
    const TYPE_FLOAT = 'float';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_SELECT = 'select';
    const TYPE_CKEDITOR = 'ckeditor';
    const TYPE_MEDIA_FILE = 'media_file';
    const TYPE_IMAGE = 'image';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_CHECKBOX_SWITCH = 'checkbox_switch';

    /**
     * Field name.
     *
     * @var string
     */
    protected $name;

    /**
     * Field label.
     *
     * @var string
     */
    protected $label;

    /**
     * Is field required?
     *
     * @var bool
     */
    protected $required;

    /**
     * Field type.
     *
     * @var string
     */
    protected $type;

    /**
     * Default value.
     *
     * @var string|int|float|null
     */
    protected $value;

    /**
     * AbstractFormField constructor.
     * @param string $type
     * @param string|null $name
     * @param string|null $label
     */
    public function __construct(string $type, string $name, string $label = null)
    {
        $this->type = $type;
        $this->name = $name;
        $this->label = $label;
    }


    /**
     * Set if is field required.
     *
     * @param bool $required
     * @return $this
     */
    public function required($required = true): self
    {
        $this->required = $required;
        return $this;
    }


    /**
     * Set default value.
     *
     * @param null $default
     * @return $this
     */
    public function value($default = null): AbstractFormField
    {
        $this->value = $default;
        return $this;
    }


    /**
     * Convert to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'name' => $this->name,
            'label' => $this->getLabel(),
            'required' => $this->required,
            'value' => $this->value
        ];
    }


    /**
     * Get validation rules for the field.
     *
     * @return array
     */
    public function getValidationRules(): array
    {
        $rules = [];

        if ($this->required) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }

        return $rules;
    }


    /**
     * Get name of the field.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }


    /**
     * Get label of the field.
     *
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return $this->label ? trans($this->label) : null;
    }


    /**
     * Specify data which should be serialized to JSON.
     *
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *               which is a value of any type other than a resource.
     *
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return array_filter($this->toArray(), function ($value) {
            return !is_null($value);
        });
    }


    /**
     * Helper method for chainability.
     *
     * @param string $type
     * @param string $name
     * @param string|null $label
     * @return static
     */
    public static function make(string $type, string $name, string $label = null)
    {
        return new static($type, $name, $label);
    }
}
