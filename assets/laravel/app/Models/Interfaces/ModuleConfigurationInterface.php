<?php

namespace App\Models\Interfaces;

/**
 * Interface ModuleConfigurationInterface
 * @package App\Models\Interfaces
 * @author Patrik Václavek
 * @mixin \Illuminate\Database\Eloquent\Model
 * @copyright SIMPLO, s.r.o.
 */
interface ModuleConfigurationInterface 
{
    /**
     * Fill model with input values with mutators inside.
     *
     * @param array $inputs
     * @return $this
     */
    public function inputFill(array $inputs);

    /**
     * Clone the model into a new, non-existing instance.
     *
     * @param  array|null  $except
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function replicate(array $except = null);
}