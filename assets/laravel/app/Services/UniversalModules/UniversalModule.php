<?php
/**
 * UniversalModule.php created by Patrik Václavek
 */

namespace App\Services\UniversalModules;

class UniversalModule
{
    public const MULTILANGUAL_TYPE_APART = 'apart';

    public const MULTILANGUAL_TYPE_LOCALIZABLE = 'localizable';

    /**
     * Key of the universal module.
     *
     * @var string
     */
    private $key;

    /**
     * Name of the universal module.
     *
     * @var string
     */
    private $name;

    /**
     * Icon of the universal module in menu.
     *
     * @var string
     */
    private $icon;

    /**
     * Description of the universal module.
     *
     * @var string
     */
    private $description;

    /**
     * Is true when ordering is allowed for items of this universal module.
     *
     * @var bool
     */
    private $order;

    /**
     * Is true when toggling is allowed for items of this universal module.
     *
     * @var bool
     */
    private $toggling;

    /**
     * Is true when items of this universal module have own url.
     *
     * @var bool
     */
    private $url;

    /**
     * Form fields of the universal module.
     *
     * @var \App\Structures\FormFields\AbstractFormField[]
     */
    private $fields;

    /**
     * Type of module localization.
     *
     * @var string
     */
    private $multilangualType;

    /**
     * @var string
     */
    private $urlPrefix;

    /**
     * UniversalModule constructor.
     * @param string $key
     * @param string $name
     * @param string $icon
     */
    public function __construct(string $key, string $name, string $icon)
    {
        $this->key = str_slug($key);
        $this->name = $name;
        $this->icon = $icon;
        $this->order = false;
        $this->toggling = false;
        $this->url = false;
        $this->order = false;
        $this->fields = [];
    }


    /**
     * Helper method for chainability.
     *
     * @param string $key
     * @param string $name
     * @param string $icon
     * @return \App\Services\UniversalModules\UniversalModule
     */
    public static function make(string $key, string $name, string $icon = 'magic')
    {
        return new self($key, $name, $icon);
    }


    /**
     * Set description of the universal module.
     *
     * @param string $description
     * @return \App\Services\UniversalModules\UniversalModule
     */
    public function setDescription(string $description): UniversalModule
    {
        $this->description = $description;
        return $this;
    }


    /**
     * Allow ordering of items of this universal module.
     *
     * @return \App\Services\UniversalModules\UniversalModule
     */
    public function allowOrdering(): UniversalModule
    {
        $this->order = true;
        return $this;
    }


    /**
     * Allow toggling of items of this universal module.
     *
     * @return \App\Services\UniversalModules\UniversalModule
     */
    public function allowToggling(): UniversalModule
    {
        $this->toggling = true;
        return $this;
    }


    /**
     * Every item of this universal module will have own url.
     *
     * @param string $prefix
     * @return \App\Services\UniversalModules\UniversalModule
     */
    public function withUrl(string $prefix = null): UniversalModule
    {
        $this->url = true;
        $this->urlPrefix = $prefix;
        return $this;
    }


    /**
     * Set module to be multilangual. Type can be specified.
     *
     * @param string $type
     * @return \App\Services\UniversalModules\UniversalModule
     */
    public function multilangual(string $type = self::MULTILANGUAL_TYPE_APART): UniversalModule
    {
        $this->multilangualType = $type;
        return $this;
    }


    /**
     * Set form fields of the universal module.
     *
     * @param array $fields
     * @return \App\Services\UniversalModules\UniversalModule
     */
    public function setFields(array $fields): UniversalModule
    {
        $this->fields = $fields;
        return $this;
    }


    /**
     * Get key of the universal module.
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }


    /**
     * Get name of the universal module.
     *
     * @return string
     */
    public function getName(): string
    {
        return trans($this->name);
    }


    /**
     * Check if is allowed ordering of items of this universal module.
     *
     * @return bool
     */
    public function isAllowedOrdering(): bool
    {
        return $this->order;
    }


    /**
     * Check if is allowed toggling of items of this universal module.
     *
     * @return bool
     */
    public function isAllowedToggling(): bool
    {
        return $this->toggling;
    }


    /**
     * Check if items of this universal module have own url.
     *
     * @return bool
     */
    public function hasUrl(): bool
    {
        return $this->url;
    }


    /**
     * Get url prefix for universal module items.
     *
     * @return string|null
     */
    public function getUrlPrefix(): ?string
    {
        return $this->urlPrefix;
    }


    /**
     * Check if is module multilanguagal type apart.
     *
     * @return bool
     */
    public function isMultilangualApart(): bool
    {
        return $this->multilangualType === self::MULTILANGUAL_TYPE_APART;
    }


    /**
     * Check if is module multilanguagal type localizable.
     *
     * @return bool
     */
    public function isMultilangualLocalizable(): bool
    {
        return $this->multilangualType === self::MULTILANGUAL_TYPE_LOCALIZABLE;
    }


    /**
     * Get description of the universal module.
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description ? trans($this->description) : null;
    }


    /**
     * Get icon of the universal module.
     *
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }


    /**
     * Get form fields of the universal module.
     *
     * @return \App\Structures\FormFields\AbstractFormField[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }


    /**
     * Get form fields that represent files of the universal module.
     *
     * @return \App\Structures\FormFields\MediaFile[]|\App\Structures\FormFields\Image[]
     */
    public function getFileFields(): array
    {
        $fileFields = [];

        foreach ($this->getFields() as $field) {
            if ($field instanceof \App\Structures\FormFields\MediaFile ||
                $field instanceof \App\Structures\FormFields\Image
            ) {
                $fileFields[] = $field;
            }
        }

        return $fileFields;
    }


    /**
     * Get validation rules.
     *
     * @return array[]
     */
    public function getValidationRules(): array
    {
        $rules = [];
        foreach ($this->fields as $field) {
            if (method_exists($field, 'extendValidationRules')) {
                $rules = $field->extendValidationRules($rules);
            } else {
                $rules[$field->getName()] = $field->getValidationRules();
            }
        }

        return $rules;
    }


    /**
     * Check if module has file field.
     *
     * @param string $name
     * @return bool
     */
    public function hasFile(string $name): bool
    {
        return $this->hasField($name, \App\Structures\FormFields\MediaFile::class);
    }


    /**
     * Check if module has image field.
     *
     * @param string $name
     * @return bool
     */
    public function hasImage(string $name): bool
    {
        return $this->hasField($name, \App\Structures\FormFields\Image::class);
    }


    /**
     * Check if universal module has specified field.
     * Optionally can be checked field type by specifying class of a type.
     *
     * @param string $name
     * @param string|null $class
     * @return bool
     */
    public function hasField(string $name, string $class = null): bool
    {
        foreach ($this->fields as $field) {
            if ($field->getName() === $name) {
                return is_null($class) ? true : get_class($field) === $class;
            }
        }

        return false;
    }
}
