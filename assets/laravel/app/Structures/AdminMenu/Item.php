<?php

namespace App\Structures\AdminMenu;

use App\Models\Module\InstalledModule;
use App\Models\Web\Language;
use App\Structures\Enums\SingletonEnum;
use Illuminate\Support\Collection;

final class Item implements \Serializable
{
    private const PLACEHOLDER_MODULE = 'module';
    private const PLACEHOLDER_UNIVERSAL_MODULE = 'universal_module';

    /**
     * @var string
     */
    private $text;

    /**
     * @var string|array
     */
    private $route;

    /**
     * @var string
     */
    private $icon;

    /**
     * @var string
     */
    private $permission;

    /**
     * @var string
     */
    private $nickname;

    /**
     * @var string
     */
    private $placeholder;

    /**
     * @param string $text
     * @param string|array $route
     * @param string|null $icon
     */
    public function __construct(string $text, $route, ?string $icon = null)
    {
        $this->text = $text;
        $this->route = $route;
        $this->icon = $icon;
    }


    /**
     * @param \Lavary\Menu\Builder|\Lavary\Menu\Item $menu
     * @param \App\Models\Web\Language $language
     * @param \Illuminate\Support\Collection $installedModules
     * @return void
     */
    public function fillMenu($menu, Language $language, Collection $installedModules): void
    {
        switch ($this->placeholder) {
            case self::PLACEHOLDER_MODULE:
                $this->fillModulePlaceholder($menu, $language, $installedModules);
                break;
            case self::PLACEHOLDER_UNIVERSAL_MODULE:
                $this->fillUniversalModulePlaceholder($menu);
                break;
            case null:
                $this->fillItem($menu);
                break;
        }
    }


    /**
     * @param string $text
     * @param string|array $route
     * @param string|null $icon
     * @return \App\Structures\AdminMenu\Item
     */
    public static function make(string $text, $route, ?string $icon = null): Item
    {
        return new static($text, $route, $icon);
    }


    /**
     * Make dashboard menu item.
     *
     * @param string $text
     * @param string $route
     * @param null|string $icon
     * @return \App\Structures\AdminMenu\Item
     */
    public static function dashboard(
        string $text = 'Dashboard',
        $route = 'admin',
        ?string $icon = 'fa fa-home'
    ): Item
    {
        return new static($text, $route, $icon);
    }


    /**
     * Make pages menu item.
     *
     * @param string $text
     * @param string $route
     * @param null|string $icon
     * @return \App\Structures\AdminMenu\Item
     */
    public static function pages(
        string $text = 'admin/layout.menu.pages',
        $route = 'admin.pages.index',
        ?string $icon = 'fa fa-file-text'
    ): Item
    {
        return self::make($text, $route, $icon)
            ->requirePermission('pages-show')
            ->setNickname('pages');
    }


    /**
     * Make photogalleries menu item.
     *
     * @param string $text
     * @param string $route
     * @param null|string $icon
     * @return \App\Structures\AdminMenu\Item
     */
    public static function photogalleries(
        string $text = 'admin/layout.menu.photogalleries',
        $route = 'admin.photogalleries',
        ?string $icon = 'fa fa-picture-o'
    ): Item
    {
        return self::make($text, $route, $icon)
            ->requirePermission('photogalleries-show')
            ->setNickname('photogalleries');
    }


    /**
     * Make front-web menu-management menu item.
     *
     * @param string $text
     * @param string $route
     * @param null|string $icon
     * @return \App\Structures\AdminMenu\Item
     */
    public static function menu(
        string $text = 'admin/layout.menu.menu',
        $route = 'admin.menu',
        ?string $icon = 'fa fa-trello'
    ): Item
    {
        return self::make($text, $route, $icon)
            ->requirePermission('menu-show')
            ->setNickname('menu');
    }


    /**
     * Make media library menu item.
     *
     * @param string $text
     * @param string $route
     * @param null|string $icon
     * @return \App\Structures\AdminMenu\Item
     */
    public static function mediaLibrary(
        string $text = 'admin/layout.menu.media_library',
        $route = 'admin.media',
        ?string $icon = 'fa fa-picture-o'
    ): Item
    {
        return self::make($text, $route, $icon)->setNickname('media');
    }


    /**
     * Make widgets menu item.
     *
     * @param string $text
     * @param string $route
     * @param null|string $icon
     * @return \App\Structures\AdminMenu\Item
     */
    public static function widgets(
        string $text = 'admin/layout.menu.widgets',
        $route = 'admin.widgets.index',
        ?string $icon = 'fa fa-object-group'
    ): Item
    {
        return self::make($text, $route, $icon)
            ->requirePermission('widgets-show')
            ->setNickname('widgets');
    }


    /**
     * Make users menu item.
     *
     * @param string $text
     * @param string $route
     * @param null|string $icon
     * @return \App\Structures\AdminMenu\Item
     */
    public static function users(
        string $text = 'admin/layout.menu.users',
        $route = 'admin.users',
        ?string $icon = null
    ): Item
    {
        return self::make($text, $route, $icon)
            ->requirePermission('users-show')
            ->setNickname('users');
    }


    /**
     * Make roles menu item.
     *
     * @param string $text
     * @param string $route
     * @param null|string $icon
     * @return \App\Structures\AdminMenu\Item
     */
    public static function roles(
        string $text = 'admin/layout.menu.roles',
        $route = 'admin.roles',
        ?string $icon = null
    ): Item
    {
        return self::make($text, $route, $icon)
            ->requirePermission('roles-show')
            ->setNickname('roles');
    }


    /**
     * Make general-settings menu item.
     *
     * @param string $text
     * @param string $route
     * @param null|string $icon
     * @return \App\Structures\AdminMenu\Item
     */
    public static function generalSettings(
        string $text = 'admin/layout.menu.settings',
        $route = 'admin.settings',
        ?string $icon = null
    ): Item
    {
        return self::make($text, $route, $icon)
            ->requirePermission('administrator')
            ->setNickname('settings');
    }


    /**
     * Make languages menu item.
     *
     * @param string $text
     * @param string $route
     * @param null|string $icon
     * @return \App\Structures\AdminMenu\Item
     */
    public static function languages(
        string $text = 'admin/layout.menu.languages',
        $route = 'admin.languages.index',
        ?string $icon = null
    ): Item
    {
        return self::make($text, $route, $icon)
            ->requirePermission('languages-show')
            ->setNickname('languages');
    }


    /**
     * Make article-flags menu item.
     *
     * @param string $text
     * @param string $route
     * @param null|string $icon
     * @return \App\Structures\AdminMenu\Item
     */
    public static function articleFlags(
        string $text = 'admin/layout.menu.article_flags',
        $route = 'admin.article_flags.index',
        ?string $icon = null
    ): Item
    {
        return self::make($text, $route, $icon)
            ->requirePermission('article-flags-show')
            ->setNickname('article_flags');
    }


    /**
     * Make redirects menu item.
     *
     * @param string $text
     * @param string $route
     * @param null|string $icon
     * @return \App\Structures\AdminMenu\Item
     */
    public static function redirects(
        string $text = 'admin/layout.menu.redirects',
        $route = 'admin.redirects.index',
        ?string $icon = null
    ): Item
    {
        return self::make($text, $route, $icon)
            ->requirePermission('redirects-show')
            ->setNickname('redirects');
    }


    /**
     * Make modules-management menu item.
     *
     * @param string $text
     * @param string $route
     * @param null|string $icon
     * @return \App\Structures\AdminMenu\Item
     */
    public static function modulesManagement(
        string $text = 'admin/layout.menu.modules',
        $route = 'admin.modules',
        ?string $icon = null
    ): Item
    {
        return self::make($text, $route, $icon)
            ->requirePermission('administrator')
            ->setNickname('modules');
    }


    /**
     * Make menu item for specified module.
     *
     * @param string $moduleName
     * @return \App\Structures\AdminMenu\Item
     */
    public static function module(string $moduleName): Item
    {
        return self::make('', null)
            ->setPlaceholder(self::PLACEHOLDER_MODULE)
            ->setNickname($moduleName);
    }


    /**
     * Make menu item for specified universal module.
     *
     * @param string $key
     * @return \App\Structures\AdminMenu\Item
     */
    public static function universalModule(string $key): Item
    {
        return self::make('', null)
            ->setPlaceholder(self::PLACEHOLDER_UNIVERSAL_MODULE)
            ->setNickname($key);
    }


    /**
     * Menu item is only shown when user has required permissions.
     *
     * @param string $permission
     * @return \App\Structures\AdminMenu\Item
     */
    public function requirePermission(string $permission): Item
    {
        $this->permission = $permission;
        return $this;
    }


    /**
     * Set nickname of menu item.
     *
     * @param string $nickname
     * @return \App\Structures\AdminMenu\Item
     */
    public function setNickname(string $nickname): Item
    {
        $this->nickname = $nickname;
        return $this;
    }


    /**
     * Set placeholder type for menu item.
     *
     * @param string $type
     * @return \App\Structures\AdminMenu\Item
     */
    private function setPlaceholder(string $type): Item
    {
        $this->placeholder = $type;
        return $this;
    }


    /**
     * @param \Lavary\Menu\Builder|\Lavary\Menu\Item $menu
     * @param \App\Models\Web\Language $language
     * @param \Illuminate\Support\Collection $installedModules
     * @return void
     */
    private function fillModulePlaceholder($menu, Language $language, Collection $installedModules): void
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        /** @var \App\Models\Module\InstalledModule $installedModule */
        $installedModule = $installedModules->first(function (InstalledModule $installedModule) {
            return $installedModule->name === $this->nickname;
        });

        if (!$installedModule || !$installedModule->checkModuleExists()) {
            return;
        }

        $module = $installedModule->module;
        $alias = $module->getLowerName();
        $moduleMenuFields = $module->config('admin.menu', []);
        $toGroup = count($moduleMenuFields) > 1;
        $groupItems = [];

        foreach ($moduleMenuFields as $index => $moduleMenuField) {
            if (!isset($moduleMenuField['route']) || !$alias || !$user->can("module_{$alias}-show")) {
                continue;
            }

            $item = Item::make(
                $module->trans("admin.menu.{$index}"),
                $moduleMenuField['route'],
                $moduleMenuField['icon'] ?? null
            );

            if (isset($moduleMenuField['nickname'])) {
                $item->setNickname($module->getLowerName() . "_" . $moduleMenuField['nickname']);
            }

            if ($toGroup) {
                $groupItems[] = $item;
            } else {
                $item->fillItem($menu);
            }
        }

        if ($toGroup && $groupItems) {
            $group = Group::withItems(
                $module->trans('admin.menu.group_text'),
                $module->config('admin.menu_group_icon', 'fa fa-trello'),
                $groupItems
            );

            $group->fillMenu($menu, $language, $installedModules);
        }
    }


    /**
     * @param \Lavary\Menu\Builder|\Lavary\Menu\Item $menu
     * @return void
     */
    private function fillUniversalModulePlaceholder($menu): void
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $module = SingletonEnum::universalModules()->find($this->nickname);

        if (!$user->can("universal_module_{$module->getKey()}-show")) {
            return;
        }

        Item::make(
            $module->getName(),
            ['admin.universalmodule.index', 'prefix' => $module->getKey()],
            'fa fa-' . $module->getIcon()
        )
            ->setNickname($module->getKey())
            ->fillItem($menu);
    }


    /**
     * @param \Lavary\Menu\Builder|\Lavary\Menu\Item $menu
     * @return void
     */
    private function fillItem($menu): void
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if ($this->permission && !$user->can($this->permission)) {
            return;
        }

        $menu->add(trans($this->text), [
            'route' => $this->route,
            'icon' => $this->icon,
            'nickname' => $this->nickname
        ]);
    }


    /**
     * String representation of object
     * @link https://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize([
            'text' => $this->text,
            'route' => $this->route,
            'icon' => $this->icon,
            'permission' => $this->permission,
            'nickname' => $this->nickname,
            'placeholder' => $this->placeholder
        ]);
    }


    /**
     * Constructs the object
     * @link https://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        $this->text = $data['text'];
        $this->route = $data['route'];
        $this->permission = $data['permission'] ?? null;
        $this->nickname = $data['nickname'] ?? null;
        $this->icon = $data['icon'] ?? null;
        $this->placeholder = $data['placeholder'] ?? null;
    }


    /**
     * @param array $an_array
     * @return \App\Structures\AdminMenu\Item
     */
    public static function __set_state($an_array)
    {
        $item = new self($an_array['text'], $an_array['route'], $an_array['icon']);
        $item->placeholder = $an_array['placeholder'];
        $item->permission = $an_array['permission'];
        $item->nickname = $an_array['nickname'];
        return $item;
    }
}
