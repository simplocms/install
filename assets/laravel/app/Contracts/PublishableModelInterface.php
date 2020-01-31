<?php

namespace App\Contracts;

/**
 * Interface PublishableModelInterface
 * @package App\Contracts
 * @mixin \Illuminate\Database\Eloquent\Model
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 */
interface PublishableModelInterface
{
    /**
     * Check if model is public.
     *
     * @return bool
     */
    public function isPublic(): bool;
}
