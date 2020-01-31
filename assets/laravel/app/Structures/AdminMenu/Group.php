<?php

namespace App\Structures\AdminMenu;


use App\Models\Article\Flag;
use App\Models\Module\InstalledModule;
use App\Models\Web\Language;
use App\Structures\Enums\SingletonEnum;
use Illuminate\Support\Collection;

final class Group implements \Serializable
{
    private const PLACEHOLDER_ARTICLES_AND_CATEGORIES_OF_ALL_FLAGS = 'articles_and_categories_of_all_flags';
    private const PLACEHOLDER_ALL_UNIVERSAL_MODULES = 'all_universal_modules';
    private const PLACEHOLDER_ALL_MODULES = 'all_modules';

    /**
     * @var string
     */
    private $text;

    /**
     * @var string
     */
    private $icon;

    /**
     * @var \App\Structures\AdminMenu\Item[]
     */
    private $items;

    /**
     * @var string|null
     */
    private $placeholder;

    /**
     * @param string $text
     * @param string $icon
     */
    public function __construct(string $text, string $icon)
    {
        $this->text = $text;
        $this->icon = $icon;
    }


    /**
     * Make group with items.
     *
     * @param string $text
     * @param string $icon
     * @param array $items
     * @return \App\Structures\AdminMenu\Group
     */
    public static function withItems(string $text, string $icon, array $items): Group
    {
        $group = new static($text, $icon);
        $group->items = $items;
        return $group;
    }


    /**
     * Make placeholder group.
     *
     * @param string $text
     * @param string $icon
     * @param string $type
     * @return \App\Structures\AdminMenu\Group
     */
    private static function placeholder(string $text, string $icon, string $type): Group
    {
        $group = new static($text, $icon);
        $group->placeholder = $type;
        return $group;
    }


    /**
     * Placeholder for all articles and categories of all flags.
     *
     * @return \App\Structures\AdminMenu\Group
     */
    public static function articlesAndCategoriesOfAllFlags(): Group
    {
        return self::placeholder('', '', self::PLACEHOLDER_ARTICLES_AND_CATEGORIES_OF_ALL_FLAGS);
    }


    /**
     * Placeholder for all universal modules.
     *
     * @return \App\Structures\AdminMenu\Group
     */
    public static function allUniversalModules(): Group
    {
        return self::placeholder('', '', self::PLACEHOLDER_ALL_UNIVERSAL_MODULES);
    }


    /**
     * Placeholder for all installed modules.
     *
     * @return \App\Structures\AdminMenu\Group
     */
    public static function allModules(): Group
    {
        return self::placeholder('', '', self::PLACEHOLDER_ALL_MODULES);
    }


    /**
     * Make group for users and roles.
     *
     * @param string $text
     * @param string $icon
     * @return \App\Structures\AdminMenu\Group
     */
    public static function usersAndRoles(string $text = 'admin/layout.menu.users', string $icon = 'fa fa-user'): Group
    {
        return self::withItems($text, $icon, [
            Item::users(), Item::roles()
        ]);
    }


    /**
     * Make group for settings.
     *
     * @param string $text
     * @param string $icon
     * @return \App\Structures\AdminMenu\Group
     */
    public static function settings(
        string $text = 'admin/layout.menu.settings_group',
        string $icon = 'fa fa-cogs'
    ): Group
    {
        return self::withItems($text, $icon, [
            Item::generalSettings(),
            Item::languages(),
            Item::articleFlags(),
            Item::redirects(),
            Item::modulesManagement()
        ]);
    }


    /**
     * Fill given menu.
     *
     * @param \Lavary\Menu\Builder|\Lavary\Menu\Item $menu
     * @param \App\Models\Web\Language $language
     * @param \Illuminate\Support\Collection $installedModules
     */
    public function fillMenu($menu, Language $language, Collection $installedModules): void
    {
        if ($this->placeholder !== null) {
            $this->fillPlaceholder($language);
        }

        if (strlen($this->text) && $this->items) {
            $targetItem = $menu->add(trans($this->text), ['icon' => $this->icon]);
            $targetItem->data('is_group', true);
        } else {
            $targetItem = $menu;
        }

        foreach ($this->items ?? [] as $item) {
            if ($item instanceof Group || $item instanceof Item) {
                $item->fillMenu($targetItem, $language, $installedModules);
            }
        }
    }


    /**
     * Fill placeholder.
     *
     * @param \App\Models\Web\Language $language
     */
    private function fillPlaceholder(Language $language)
    {
        switch ($this->placeholder) {
            case self::PLACEHOLDER_ARTICLES_AND_CATEGORIES_OF_ALL_FLAGS:
                $this->fillWithArticlesAndCategoriesOfAllFlags($language);
                break;
            case self::PLACEHOLDER_ALL_MODULES:
                $this->fillWithAllModules();
                break;
            case self::PLACEHOLDER_ALL_UNIVERSAL_MODULES:
                $this->fillWithAllUniversalModules();
                break;
        }
    }


    /**
     * @param \App\Models\Web\Language $language
     */
    private function fillWithArticlesAndCategoriesOfAllFlags(Language $language): void
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $canShowArticles = $user->can('articles-show');
        $canShowCategories = $user->can('article-categories-show');

        if (!$canShowArticles && !$canShowCategories) {
            return;
        }

        $this->items = [];

        foreach (Flag::whereLanguage($language)->get() as $flag) {
            $group = new self($flag->name, 'fa fa-newspaper-o');
            $group->items = [];

            if ($canShowArticles) {
                $group->items[] = Item::make($flag->name, ['admin.articles.index', 'flag' => $flag->url])
                    ->setNickname($flag->url . '-articles');
            }

            if ($canShowCategories) {
                $group->items[] = Item::make(
                    'admin/layout.menu.categories',
                    ['admin.categories.index', 'flag' => $flag->url]
                )->setNickname($flag->url . '-categories');
            }

            $this->items[] = $group;
        }
    }


    /**
     * Fill group with all modules.
     */
    private function fillWithAllModules(): void
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $this->items = [];

        /** @var \App\Models\Module\InstalledModule $installedModule */
        foreach (InstalledModule::enabled()->get() as $installedModule) {
            if (!$installedModule->checkModuleExists()) {
                continue;
            }

            $module = $installedModule->module;
            $alias = $module->getLowerName();
            $moduleMenuFields = $module->config('admin.menu', []);
            $addNewLinksTo = &$this;

            if (count($moduleMenuFields) > 1) {
                $group = new self(
                    $module->trans('admin.menu.group_text'),
                    $module->config('admin.menu_group_icon', 'fa fa-trello')
                );
                $group->items = [];
                $this->items[] = $group;
                $addNewLinksTo = &$group;
            }

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

                $addNewLinksTo->items[] = $item;
            }
        }
    }


    /**
     * Fill group with all universal modules.
     */
    private function fillWithAllUniversalModules(): void
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $this->items = [];

        foreach (SingletonEnum::universalModules()->all() as $module) {
            if (!$user->can("universal_module_{$module->getKey()}-show")) {
                continue;
            }

            $this->items[] = Item::make(
                $module->getName(),
                ['admin.universalmodule.index', 'prefix' => $module->getKey()],
                'fa fa-' . $module->getIcon()
            )->setNickname($module->getKey());
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
        return serialize([
            'text' => $this->text,
            'items' => $this->items,
            'icon' => $this->icon,
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
        $this->items = $data['items'];
        $this->icon = $data['icon'] ?? null;
        $this->placeholder = $data['placeholder'] ?? null;
    }


    /**
     * @param array $an_array
     * @return \App\Structures\AdminMenu\Group
     */
    public static function __set_state($an_array)
    {
        $item = new self($an_array['text'], $an_array['icon']);
        $item->placeholder = $an_array['placeholder'];
        $item->items = $an_array['items'];
        return $item;
    }
}
