<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MediaFileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'sometimes|required|string|max:100',
            'description' => 'sometimes|nullable|string|max:250',
            'rotate' => ['sometimes','required', Rule::in(['left', 'right'])],
            'width' => ['sometimes','required', 'int', 'min:1'],
            'height' => ['sometimes','required', 'int', 'min:1'],
        ];
    }


    /**
     * Return input values.
     *
     * @return array
     */
    public function getValues(): array
    {
        $output = [];

        if ($this->has('name')) {
            $output['name'] = $this->input('name');
        }

        if ($this->has('description')) {
            $output['description'] = $this->input('description');
        }

        return $output;
    }


    /**
     * Check if file should be rotated left.
     *
     * @return bool
     */
    public function rotateLeft(): bool
    {
        return $this->input('rotate') === 'left';
    }


    /**
     * Check if file should be rotated right.
     *
     * @return bool
     */
    public function rotateRight(): bool
    {
        return $this->input('rotate') === 'right';
    }


    /**
     * Check if file (image) should be resized.
     *
     * @return bool
     */
    public function resize(): bool
    {
        return $this->has(['width', 'height']);
    }
}
