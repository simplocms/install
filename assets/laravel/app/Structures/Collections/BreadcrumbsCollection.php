<?php

namespace App\Structures\Collections;

use App\Contracts\ConvertableToStructuredDataInterface;
use App\Contracts\StructuredDataTypeInterface;
use App\Structures\StructuredData\Types\TypeBreadcrumbList;

/**
 * Class BreadcrumbsCollection
 * @package App\Structures\Collections
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 */
class BreadcrumbsCollection extends \Illuminate\Support\Collection implements ConvertableToStructuredDataInterface
{
    /**
     * Get properties of the type.
     *
     * @return \App\Contracts\StructuredDataTypeInterface
     */
    public function toStructuredData(): StructuredDataTypeInterface
    {
        $position = 1;
        $breadcrumbs = [];
        /** @var \App\Structures\DataTypes\Breadcrumb $item */
        foreach (array_values($this->items) as $item) {
            if (!$item->hasUrl()) {
                continue;
            }

            $breadcrumbs[] = $item->toStructuredData()
                ->setContext(null)
                ->setAttribute('position', $position++);
        }

        return new TypeBreadcrumbList([
            'itemListElement' => $breadcrumbs
        ]);
    }
}
