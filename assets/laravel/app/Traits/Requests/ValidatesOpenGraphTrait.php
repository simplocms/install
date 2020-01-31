<?php

namespace App\Traits\Requests;

use App\Rules\MediaImageRule;

/**
 * Trait ValidatesOpenGraphTrait
 * @package App\Traits\Requests
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 */
trait ValidatesOpenGraphTrait
{
    /**
     * Get validation rules for OpenGraph inputs.
     *
     * @return array
     */
    protected function getOpenGraphRules(): array
    {
        return [
            'open_graph' => 'nullable|array',
            'open_graph.title' => 'nullable|string|max:90',
            'open_graph.description' => 'nullable|string|max:300',
            'open_graph.url' => 'nullable|string|url',
            'open_graph.image_id' => ['nullable', new MediaImageRule],
        ];
    }


    /**
     * Return messages for OpenGraph validation.
     *
     * @return string[]
     */
    public function getOpenGraphMessages(): array
    {
        $messages = trans('validation.open_graph');
        return is_array($messages) ? $messages : [];
    }
}
