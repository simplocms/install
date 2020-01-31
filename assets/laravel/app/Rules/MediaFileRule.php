<?php

namespace App\Rules;

use App\Models\Media\File;
use Illuminate\Contracts\Validation\Rule;

class MediaFileRule implements Rule
{
    /**
     * Allowed mime types.
     *
     * @var array
     */
    private $mimeTypes;

    /**
     * MediaImageRule constructor.
     * @param array|null $allowedMimeTypes
     */
    public function __construct(array $allowedMimeTypes = null)
    {
        $this->mimeTypes = $allowedMimeTypes;
    }


    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (is_array($value)) {
            return false;
        }

        /** @var \App\Models\Media\File $file */
        $file = (new File)->find($value);

        if ($this->mimeTypes) {
            return !is_null($file) && in_array($file->mime_type, $this->mimeTypes);
        }

        return !is_null($file);
    }


    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('admin/media_library.validation_messages.invalid_file');
    }
}
