<?php

namespace App\Contracts;
use App\Structures\Collections\BreadcrumbsCollection;

/**
 * Interface ProvidesBreadcrumbsInterface
 * @package App\Contracts
 * @mixin \Illuminate\Database\Eloquent\Model
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 */
interface ProvidesBreadcrumbsInterface
{
    /**
     * Get breadcrumbs of the model.
     *
     * @return \App\Structures\DataTypes\Breadcrumb[]|\App\Structures\Collections\BreadcrumbsCollection
     */
    public function getBreadcrumbs(): BreadcrumbsCollection;
}
