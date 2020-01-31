<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\AbstractFormRequest;
use App\Models\Web\Redirect;
use Illuminate\Validation\Rule;

class RedirectRequest extends AbstractFormRequest
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
            'from' => [
                "required",
                "string",
                "max:250",
                "regex:$urlSlugRegex",
                function ($attribute, $value, $fail) {
                    // Check if is valid absolute url -> than fail
                    if (filter_var($value, FILTER_VALIDATE_URL)) {
                        return $fail(trans('admin/redirects/form.messages.from_absolute_url'));
                    }

                    $fullFrom = $this->getFullFrom();

                    /** @var \App\Models\Web\Redirect $redirect */
                    $redirect = $this->route('redirect');
                    if ($redirect && $redirect->from === $fullFrom) {
                        // Ignore URL of currently updated redirect.
                        return true;
                    }

                    if (Redirect::findBySourceUrl($fullFrom)) {
                        return $fail(trans('admin/redirects/form.messages.from_unique'));
                    }
                },
            ],
            'to' => [
                "required",
                "string",
                "max:250",
                "regex:$urlSlugRegex",
                function ($attribute, $value, $fail) {
                    if (filter_var($value, FILTER_VALIDATE_URL)) {
                        return true;
                    }

                    $fullTo = $this->getFullTo();

                    /** @var \App\Models\Web\Redirect $redirect */
                    $redirect = $this->route('redirect');
                    if ($redirect && $redirect->to === $fullTo) {
                        // Ignore URL of currently updated redirect.
                        return true;
                    }

                    // Prevent chaining redirects.
                    $redirect = Redirect::findBySourceUrl($fullTo);
                    if ($redirect) {
                        return $fail(trans('admin/redirects/form.messages.to_redirect', [
                            'url' => $redirect->to
                        ]));
                    }
                },
            ],
            'status_code' => ['required', Rule::in([301, 302, 307, 308])]
        ];
    }


    /**
     * Get full "from" address with language code if sent.
     *
     * @return string
     */
    private function getFullFrom(): string
    {
        $value = $this->input('from');
        if (is_null($value)) {
            $value = '';
        }

        $fromLanguage = $this->input('from_language') ? $this->input('from_language') . '/' : '';
        return $fromLanguage . Redirect::normalizeUrl($value);
    }


    /**
     * Get full "to" address with language code if sent.
     *
     * @return string
     */
    private function getFullTo(): string
    {
        $value = $this->input('to');
        if (is_null($value)) {
            $value = '';
        }

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        $toLanguage = $this->input('to_language') ? $this->input('to_language') . '/' : '';
        return $toLanguage . Redirect::normalizeUrl($value);
    }


    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return $this->mergeMessages('admin/redirects/form.messages');
    }


    /**
     * Return input values.
     *
     * @return array
     */
    public function getValues(): array
    {
        return [
            'from' => $this->getFullFrom(),
            'to' => $this->getFullTo(),
            'status_code' => $this->input('status_code')
        ];
    }
}
