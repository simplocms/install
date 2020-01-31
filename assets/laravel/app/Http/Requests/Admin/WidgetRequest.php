<?php

namespace App\Http\Requests\Admin;

use App\Models\Web\Language;
use App\Models\Widget\Widget;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WidgetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $widget = $this->route('widget') ?: new Widget();

        return [
            'id' => [
                'required',
                'max:50',
                'regex:/^[a-zA-Z_]+$/',
                Rule::unique(Widget::getTableName(), 'id')
                    ->ignore($widget->id)
            ],
            'name' => 'required|max:255',
            'content' => ['required', 'array'],
            'language_id' => [
                'required',
                Rule::exists(Language::getTableName(), 'id')
                    ->where('enabled', 1)
            ]
        ];
    }

    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return trans('admin/widgets/form.messages');
    }

    /**
     * Get content.
     *
     * @return array|null
     */
    public function getGridEditorContent(): array
    {
        return $this->input('content');
    }
}
