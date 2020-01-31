<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Tests\Browser\Components\BootstrapModal;
use Tests\Browser\Components\FancyTree;

class MenuPage extends AdminPage
{

    /**
     * Assert that the browser is on the page.
     *
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertRouteIs('admin.menu');
    }


    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return route('admin.menu');
    }


    /**
     * Get the global element shortcuts for the site.
     *
     * @return array
     */
    public static function siteElements()
    {
        return [
            '@pagesTab' => '#tab_tree',
            '@urlsTab' => '#tab_url',
            '@categoriesTab' => '#tab_categories',
            '@createButton' => '#create-new-menu-button',
            '@deleteMenuButton' => '#delete-menu-button',
            '@saveButton' => '#save-menu-button',
            '@contentCreator' => '#menu-content-creator',
            '@contentPanel' => '#menu-content-panel',
            '@menuName' => '#menu-content-panel > .panel-body > h3',
            '@menuItems' => '#menu-items-list',
            '@menuItem' => '#menu-items-list > li',
            '@pagesTabLink' => '#menu-content-creator > .tabbable > .nav > li > a[href="#tab_tree"]',
            '@urlsTabLink' => '#menu-content-creator > .tabbable > .nav > li > a[href="#tab_url"]',
            '@categoriesTabLink' => '#menu-content-creator > .tabbable > .nav > li > a[href="#tab_categories"]',
            '@addPagesButton' => '#tab_tree > button',
            '@addUrlButton' => '#tab_url > button',
            '@addCategoriesButton' => '#tab_categories > button'
        ];
    }


    /**
     * Create new menu.
     *
     * @param \Laravel\Dusk\Browser $browser
     * @param string $name
     */
    public function createNewMenu(Browser $browser, string $name)
    {
        $browser->click('@createButton')
            ->within(new BootstrapModal('#add-menu-modal'), function (Browser $browser) use ($name) {
                $browser->type('name', $name)
                    ->confirm()
                    ->assertClose()
                    ->waitUntil('!$.active');
            });
    }


    /**
     * Add pages to menu.
     *
     * @param \Laravel\Dusk\Browser $browser
     * @param string[] $pageNames
     */
    public function addPages(Browser $browser, array $pageNames)
    {
        $browser->waitUntil('!$.active')
            ->click('@pagesTabLink')
            ->within(new FancyTree('#page-tree'), function (Browser $browser) use ($pageNames) {
                foreach ($pageNames as $pageName) {
                    $browser->clickNode($pageName);
                }
            })
            ->click('@addPagesButton');
    }


    /**
     * Add url to menu.
     *
     * @param \Laravel\Dusk\Browser $browser
     * @param string $name
     * @param string $url
     */
    public function addUrl(Browser $browser, string $name, string $url)
    {
        $browser->click('@urlsTabLink')
            ->type('@urlsTab input[name="custom_page_name"]', $name)
            ->type('@urlsTab input[name="custom_page_url"]', $url)
            ->click('@addUrlButton');
    }


    /**
     * Add categories to menu.
     *
     * @param \Laravel\Dusk\Browser $browser
     * @param int $flagId
     * @param string[] $categoryNames
     */
    public function addCategories(Browser $browser, int $flagId, array $categoryNames)
    {
        $browser->click('@categoriesTabLink')
            ->select('@categoriesTab > select', $flagId)
            ->waitUntil('!$.active')
            ->within(new FancyTree('#category-tree'), function (Browser $browser) use ($categoryNames) {
                foreach ($categoryNames as $categoryName) {
                    $browser->clickNode($categoryName);
                }
            })
            ->click('@addCategoriesButton');
    }
}
