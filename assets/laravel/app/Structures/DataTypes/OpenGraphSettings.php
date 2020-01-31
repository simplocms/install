<?php

namespace App\Structures\DataTypes;
use App\Models\Media\File;
use App\Services\MediaLibrary\ImageBuilder;

/**
 * Class OpenGraphSettings
 * @package App\Structures\DataTypes
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 */
class OpenGraphSettings implements \JsonSerializable
{
    /** @var string[] */
    private $attributes;

    /** @var string[] - Fillable OG tags */
    private $fillable = ['title', 'type', 'description', 'url', 'image_id'];

    /** @var \App\Models\Media\File|false */
    private $cachedImage;

    /**
     * OpenGraphSettings constructor.
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        $this->fill($values);
    }


    /**
     * Helper for chainability.
     *
     * @param array $values
     * @return \App\Structures\DataTypes\OpenGraphSettings
     */
    public static function make(array $values = []): OpenGraphSettings
    {
        return new self($values);
    }


    /**
     * Fill attributes with given values.
     *
     * @param array $values
     */
    private function fill(array $values)
    {
        $this->attributes = [];

        foreach ($this->fillable as $fillableAttribute) {
            $value = trim($values[$fillableAttribute] ?? '');
            if (strlen($value)) {
                $this->attributes[$fillableAttribute] = $value;
            }
        }
    }


    /**
     * Convert to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $result = $this->attributes;

        if (isset($result['image_id'])) {
            $result['image_id'] = (int)$result['image_id'];
        }

        return $result;
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
        return $this->toArray() ?: new \stdClass();
    }


    /**
     * Convert to JSON string.
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }


    /**
     * Convert to JSON string for database.
     *
     * @return string
     */
    public function toDatabaseJson(): string
    {
        return json_encode($this->attributes);
    }


    /**
     * Get field value.
     *
     * @param string $field
     * @param mixed $default
     * @return mixed
     */
    public function get(string $field, $default = null)
    {
        return $this->attributes[$field] ?? $default;
    }


    /**
     * Has image?
     *
     * @return bool
     */
    public function hasImage(): bool
    {
        // image already cached
        if (isset($this->cachedImage)) {
            return $this->cachedImage !== false;
        }

        $id = $this->get('image_id');
        if (!$id) {
            return false;
        }

        /** @var \App\Models\Media\File $image */
        $this->cachedImage = File::find($id) ?? false;
        return $this->cachedImage && $this->cachedImage->isSelectableImage();
    }


    /**
     * Get instance of image builder.
     * Placeholder is going to be returned when relation returns null OR file that is not image!
     *
     * @return \App\Services\MediaLibrary\ImageBuilder
     */
    public function makeImageLink(): ImageBuilder
    {
        $file = $this->hasImage() ? $this->cachedImage : File::imagePlaceholder();
        return $file->makeLink();
    }
}
