<?php

namespace App\Rules;

use App\Models\Media\File;
use Illuminate\Contracts\Validation\Rule;

class MediaImageRule implements Rule
{
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

        $file = (new File)->find($value);
        return (bool)optional($file)->isSelectableImage();
    }


    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('admin/media_library.validation_messages.invalid_image');
    }
}
