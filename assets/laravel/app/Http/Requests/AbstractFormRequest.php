<?php

namespace App\Http\Requests;

use App\Helpers\Functions;
use Illuminate\Foundation\Http\FormRequest;

abstract class AbstractFormRequest extends FormRequest
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
     * Get merged rules.
     *
     * @param mixed ...$args
     * @return array
     */
    protected function mergeRules(...$args): array
    {
        $result = [];

        foreach ($args as $arg) {
            $result = Functions::arrayDistinctMerge($result, $arg);
        }

        return $result;
    }


    /**
     * Get merged messages.
     *
     * @param mixed ...$args
     * @return array
     */
    protected function mergeMessages(...$args): array
    {
        $result = [];

        foreach ($args as $arg) {
            if (is_string($arg)) {
                $arg = trans($arg);
            }

            $result = array_merge($result, is_array($arg) ? $arg : []);
        }

        return $result;
    }


    /**
     * @param string $key
     * @param bool $default
     * @return bool
     */
    protected function extractBool(string $key, bool $default = false): bool
    {
        return filter_var($this->input($key, $default), FILTER_VALIDATE_BOOLEAN);
    }
}
