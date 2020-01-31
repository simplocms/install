<?php
/**
 * Collection.php created by Patrik Václavek
 */

namespace App\Structures\ClosureTable;

/**
 * Class Collection - extends laravel Collection with methods for working with a tree.
 * @package App\Structures\ClosureTable
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 */
class Collection extends \Illuminate\Support\Collection
{
    /**
     * Convert collection to tree.
     *
     * @return \App\Structures\ClosureTable\Collection
     */
    public function toTree(): Collection
    {
        $byKey = [];
        $roots = [];

        /** @var \App\Structures\ClosureTable\HierarchicModel $item */
        foreach ($this->items as $item) {
            $byKey[$item->getKey()] = $item;
        }

        foreach ($this->items as $item) {
            if (!$item->hasParent() || !isset($byKey[$item->getParentId()])) {
                $roots[] = $item;
            } else {
                /** @var \App\Structures\ClosureTable\HierarchicModel $parent */
                $parent = $byKey[$item->getParentId()];

                // If node already has relation initialized, push item into it, otherwise initialize the relation.
                if (!array_key_exists('children', $parent->getRelations())) {
                    $parent->setRelation('children', new self([$item]));
                } else {
                    $parent->getRelation('children')->push($item);
                }
            }
        }

        return new Collection($roots);
    }
}
