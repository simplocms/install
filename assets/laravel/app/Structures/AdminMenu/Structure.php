<?php

namespace App\Structures\AdminMenu;

use App\Models\Module\InstalledModule;
use App\Models\Web\Language;
use Lavary\Menu\Builder;

final class Structure implements \Serializable
{
    /**
     * @var \App\Structures\AdminMenu\Group[]|\App\Structures\AdminMenu\Item[]
     */
    private $items;

    /**
     * @param array $items
     * @return \App\Structures\AdminMenu\Structure
     */
    public static function make(array $items): Structure
    {
        return new self($items);
    }


    /**
     * Structure constructor.
     * @param array $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }


    /**
     * @param \Lavary\Menu\Builder $menu
     * @param \App\Models\Web\Language $language
     */
    public function fillMenu(Builder $menu, Language $language): void
    {
        $installedModules = InstalledModule::enabled()->get();

        foreach ($this->items as $item) {
            if ($item instanceof Group || $item instanceof Item) {
                $item->fillMenu($menu, $language, $installedModules);
            }
        }
    }

    /**
     * String representation of object
     * @link https://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize($this->items);
    }

    /**
     * Constructs the object
     * @link https://php.net/manual/en/serializable.unserialize.php
     * @param string $items <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($items)
    {
        $this->items = unserialize($items);
    }


    /**
     * @param array $an_array
     * @return \App\Structures\AdminMenu\Structure
     */
    public static function __set_state($an_array)
    {
        return new self($an_array['items'] ?? null);
    }
}
