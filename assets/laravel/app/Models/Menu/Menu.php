<?php

namespace App\Models\Menu;

use App\Models\Page\Page;
use App\Models\Web\Language;
use App\Traits\AdvancedEloquentTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Lavary\Menu\Builder;

/**
 * Class Menu
 * @package App\Models\Menu
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 *
 * @property string name
 * @property int language_id
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Menu\Item[] items
 */
class Menu extends Model
{

    use SoftDeletes, AdvancedEloquentTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'menu';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * The attributes that are dates
     *
     * @var array
     */
    protected $dates = ['deleted_at'];


    /**
     * Items
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany('App\Models\Menu\Item', 'menu_id');
    }


    /**
     * Convert to json structure
     *
     * @param \App\Models\Web\Language $language
     * @return array
     */
    static function toJsonStructure(Language $language): array
    {
        $structure = [];

        foreach (self::all() as $menu) {
            $structure[] = $menu->toJsonObject($language);
        }

        return $structure;
    }


    /**
     * Convert model to json object.
     *
     * @param \App\Models\Web\Language $language
     * @return object
     */
    public function toJsonObject(Language $language)
    {
        $menuObject = (object)[
            'id' => $this->id,
            'name' => $this->name,
            'items' => [],
        ];

        $homepage = Page::getHomepage($language);
        /** @var \Illuminate\Database\Eloquent\Collection $items */
        $items = $this->items()->whereLanguage($language)->sorted()->get();
        $items->each(function (Item $item) use ($language): void {
            $item->setRelation('language', $language);
        });
        $items = self::itemsToHierarchy($items);

        foreach ($items as $item) {
            $menuObject->items[] = $item->toJsonStructure($homepage);
        }

        return $menuObject;
    }


    /**
     * @param \Illuminate\Support\Collection|\App\Models\Menu\Item[] $items
     * @return \Illuminate\Support\Collection|\App\Models\Menu\Item[]
     */
    private static function itemsToHierarchy(Collection $items): Collection
    {
        $root = collect([]);
        /** @var \App\Models\Menu\Item[] $unresolvedLeafs */
        $unresolvedLeafs = [];
        /** @var \App\Models\Menu\Item[] $liefMap */
        $liefMap = [];

        foreach ($items as $item) {
            $item->setRelation('children', collect([]));
            $liefMap[$item->getKey()] = $item;

            // If is parent of some unresolved leafs, push leafs into relation
            if (isset($unresolvedLeafs[$item->getKey()])) {
                foreach ($unresolvedLeafs[$item->getKey()] as $lief) {
                    $item->children->push($lief);
                }
                unset($unresolvedLeafs[$item->getKey()]);
            }

            if ($item->parent_id) {
                if (isset($liefMap[$item->parent_id])) {
                    $liefMap[$item->parent_id]->children->push($item);
                } else {
                    $unresolvedLeafs[$item->parent_id][] = $item;
                }
            } else {
                $root->push($item);
            }
        }

        return $root;
    }


    /**
     * @param \Lavary\Menu\Builder $menu
     * @param \App\Models\Web\Language $language
     */
    public function fillMenu(Builder $menu, Language $language): void
    {
        $items = $this->toJsonObject($language)->items;

        foreach ($items as $item) {
            self::addMenuItem($menu, $item);
        }
    }


    /**
     * @param \Lavary\Menu\Builder|\Lavary\Menu\Item $parent
     * @param object $item
     * @return \Lavary\Menu\Item
     */
    private static function addMenuItem($parent, $item): \Lavary\Menu\Item
    {
        $menuItem = $parent->add($item->name, [
            'url' => $item->full_url,
            'class' => $item->class
        ]);

        if (!$item->children) {
            return $menuItem;
        }

        $active = false;

        foreach ($item->children as $subItem) {
            $menuSubItem = self::addMenuItem($menuItem, $subItem);
            $active = $active || $menuSubItem->isActive;
        }

        if ($active) {
            $menuItem->active();
        }

        return $menuItem;
    }
}
