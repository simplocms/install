<?php

namespace App\Services\Pages;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

final class ABTestingScope implements Scope
{
    private const A_VARIANT_FOREIGN = 'testing_a_id';
    private const B_VARIANT_FOREIGN = 'testing_b_id';

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->whereNull(self::A_VARIANT_FOREIGN);
    }

    /**
     * Extend the query builder with the needed functions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    public function extend(Builder $builder): void
    {
        $this->addWithTestingCounterparts($builder);
        $this->addWithoutTestingCounterparts($builder);
        $this->addOnlyTestingCounterparts($builder);
        $this->addWithoutTestingInvolved($builder);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addWithTestingCounterparts(Builder $builder): void
    {
        $builder->macro('withTestingCounterparts', function (Builder $builder, $withCounterparts = true) {
            if (!$withCounterparts) {
                return $builder->withoutTestingCounterparts();
            }

            return $builder->withoutGlobalScope($this);
        });
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addWithoutTestingCounterparts(Builder $builder): void
    {
        $builder->macro('withoutTestingCounterparts', function (Builder $builder) {
            return $builder->withoutGlobalScope($this)->whereNull(self::A_VARIANT_FOREIGN);
        });
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addOnlyTestingCounterparts(Builder $builder): void
    {
        $builder->macro('onlyTestingCounterparts', function (Builder $builder) {
            return $builder->withoutGlobalScope($this)->whereNotNull(self::A_VARIANT_FOREIGN);
        });
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addWithoutTestingInvolved(Builder $builder): void
    {
        $builder->macro('withoutTestingInvolved', function (Builder $builder) {
            return $builder->withoutGlobalScope($this)
                ->whereNull(self::A_VARIANT_FOREIGN)
                ->whereNull(self::B_VARIANT_FOREIGN);
        });
    }
}
