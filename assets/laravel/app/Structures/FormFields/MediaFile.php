<?php
/**
 * MediaFile.php created by Patrik Václavek
 */

namespace App\Structures\FormFields;

use App\Rules\MediaFileRule;

class MediaFile extends AbstractFormField
{
    /**
     * Allowed mime type of the media file.
     *
     * @var string
     */
    protected $fileType;

    /**
     * MediaFile constructor.
     * @param string $name
     * @param string|null $label
     */
    public function __construct(string $name, string $label = null)
    {
        parent::__construct(self::TYPE_MEDIA_FILE, $name, $label);
    }


    /**
     * Set allowed mime type of the media file.
     *
     * @param string $fileType
     * @return \App\Structures\FormFields\MediaFile
     */
    public function acceptedType(string $fileType): MediaFile
    {
        $this->fileType = $fileType;
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
            'fileType' => $this->fileType,
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

        if ($this->fileType) {
            $rules[] = new MediaFileRule([$this->fileType]);
        } else {
            $rules[] = new MediaFileRule;
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
