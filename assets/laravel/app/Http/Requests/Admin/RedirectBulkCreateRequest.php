<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\AbstractFormRequest;
use App\Models\Web\Redirect;
use Illuminate\Validation\Rule;

class RedirectBulkCreateRequest extends AbstractFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $urlSlugRegex = "/^[a-zA-Z0-9-._~\:\/\?#\[\]@!$&'()*+,;=]+$/";
        return [
            'redirects' => ['required', 'array'],
            'redirects.*.from' => [
                "required",
                "string",
                "distinct",
                "max:250",
                "regex:$urlSlugRegex",
                function ($attribute, $value, $fail) {
                    if (Redirect::findBySourceUrl(Redirect::normalizeUrl($value))) {
                        return $fail(trans('admin/redirects/form.messages.from_unique'));
                    }
                },
            ],
            'redirects.*.to' => ["required", "string", "max:250", "regex:$urlSlugRegex"],
            'redirects.*.status_code' => ['required', Rule::in([301, 302, 307, 308])]
        ];
    }


    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        $flatMessages = trans('admin/redirects/form.messages');
        if (!is_array($flatMessages)) {
            $flatMessages = [];
        }

        $messages = [];
        foreach ($flatMessages as $key => $message) {
            $messages["redirects.*.$key"] = $message;
        }

        return $this->mergeMessages('admin/redirects/form.bulk_create.messages', $messages);
    }


    /**
     * Return input redirects.
     *
     * @return array[]
     */
    public function getRedirects(): array
    {
        return $this->input('redirects');
    }
}
