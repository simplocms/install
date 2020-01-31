<?php

namespace App\Traits\Requests;

/**
 * Trait ValidatesSeoTrait
 * @package App\Traits\Requests
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 */
trait ValidatesSeoTrait
{
    /**
     * Get validation rules for SEO inputs.
     *
     * @return array
     */
    protected function getSeoRules(): array
    {
        return [
            'seo_title' => 'nullable|string|max:65',
            'seo_description' => 'nullable|string|max:320',
        ];
    }


    /**
     * Return messages for seo validation.
     *
     * @return string[]
     */
    public function getSeoMessages(): array
    {
        $messages = trans('validation.seo');
        return is_array($messages) ? $messages : [];
    }
}
