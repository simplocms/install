<?php

namespace Tests\Browser;

use App\Models\Article\Category;
use App\Models\Article\Flag;
use App\Models\Entrust\Role;
use App\Models\Menu\Item;
use App\Models\Menu\Menu;
use App\Models\Page\Page;
use App\Models\User;
use Illuminate\Support\Str;
use Tests\Browser\Components\JGrowl;
use Tests\Browser\Components\SweetAlertModal;
use Tests\Browser\Pages\MenuPage;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class MenuTest extends DuskTestCase
{
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        Browser::$userResolver = function () {
            return $this->createUserWithPermissions([
                'menu-create', 'menu-edit', 'menu-delete'
            ]);
        };
    }


    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        Category::getQuery()->delete();
        Flag::getQuery()->delete();
        Page::getQuery()->delete();
        Menu::getQuery()->delete();
        Item::getQuery()->delete();
        User::getQuery()->delete();
        Role::getQuery()->delete();

        // This line is necessary to avoid problems with asynchronous request,
        // that causes alert, making other test fail. (https://github.com/laravel/dusk/issues/460)
        $this->closeAll();
    }


    /**
     * Test index page as a user with full permissions.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testWithFullPermissions()
    {
        /** @var \App\Models\Menu\Menu $menu */
        $menu = factory(Menu::class)->create();
        factory(Item::class, 3)->create([
            'menu_id' => $menu->getKey()
        ]);

        $this->browse(function (Browser $browser) {
            $browser->login()
                ->visit(new MenuPage)
                ->assertHeaderTitle(
                    trans('admin/menu.header_title'), trans('admin/menu.header_description')
                )
                ->assertVisible('@contentCreator')
                ->assertVisible('@pagesTab')
                ->assertVisible('@createButton')
                ->assertVisible('@deleteMenuButton')
                ->assertVisible('@saveButton')
            ;
        });
    }


    /**
     * Test page as a user with no permissions.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testWithNoPermissions()
    {
        /** @var \App\Models\User $user */
        $user = factory(\App\Models\User::class)->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visitRoute('admin.menu')
                ->assertRouteIs('admin.menu')
                ->assertSee('403');
        });
    }


    /**
     * Test index page as a user with only show permissions.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testWithShowPermissions()
    {
        $user = $this->createUserWithPermissions(['menu-show']);
        factory(Menu::class)->create();
        factory(Item::class, 2)->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit(new MenuPage)
                ->assertHeaderTitle(
                    trans('admin/menu.header_title'), trans('admin/menu.header_description')
                )
                ->assertMissing('@contentCreator')
                ->assertMissing('@createButton')
                ->assertMissing('@deleteMenuButton')
                ->assertMissing('@saveButton')
            ;
        });
    }


    /**
     * Test create new menu.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testCreateMenu()
    {
        $name = Str::random(10);

        $this->browse(function (Browser $browser) use ($name) {
            $browser->login()
                ->visit(new MenuPage)
                ->createNewMenu($name);
        });

        $menu = Menu::first();
        $this->assertNotNull($menu);
        $this->assertEquals($name, $menu->name);
    }


    /**
     * Test delete a menu.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testDeleteMenu()
    {
        /** @var \App\Models\Menu\Menu $menu */
        $menu = factory(Menu::class)->create();
        factory(Item::class, 3)->create([
            'menu_id' => $menu->getKey()
        ]);

        $this->browse(function (Browser $browser) {
            $browser->login()
                ->visit(new MenuPage)
                ->click('@deleteMenuButton')
                ->waitFor((new SweetAlertModal)->selector())
                ->within(new SweetAlertModal, function (Browser $browser) {
                    $browser->assertIsDelete(trans('admin/menu.confirm_delete.title'))->confirm();
                })
                ->waitUntil('!$.active')
                ->assertMissing('@contentPanel')
            ;
        });

        $menu->refresh();
        $this->assertNotNull($menu->deleted_at);
    }



    /**
     * Test add pages to a menu.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testAddPagesToMenu()
    {
        /** @var \App\Models\Menu\Menu $menu */
        $menu = factory(Menu::class)->create();
        $pageNames = factory(Page::class, 3)->create()->pluck('name')->toArray();

        $this->browse(function (Browser $browser) use ($pageNames) {
            $browser->login()
                ->visit(new MenuPage)
                ->addPages($pageNames)
                ->assertVisible('@menuItem')
                ->click('@saveButton')
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/menu.notifications.updated')
                    );
                })
            ;
        });

        $menu->refresh();
        $this->assertEquals(count($pageNames), $menu->items->count());
        $this->assertEquals($pageNames, $menu->items->pluck('name')->toArray());
    }


    /**
     * Test add url to a menu.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testAddUrlToMenu()
    {
        /** @var \App\Models\Menu\Menu $menu */
        $menu = factory(Menu::class)->create();
        $item = factory(Item::class)->states('url')->make();

        $this->browse(function (Browser $browser) use ($item) {
            $browser->login()
                ->visit(new MenuPage)
                ->addUrl($item->name, $item->url)
                ->assertVisible('@menuItem')
                ->click('@saveButton')
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/menu.notifications.updated')
                    );
                })
            ;
        });

        $menu->refresh();
        $this->assertEquals(1, $menu->items->count());
        $this->assertEquals($item->name, $menu->items->first()->name);
        $this->assertEquals($item->url, $menu->items->first()->url);
    }


    /**
     * Test add categories to a menu.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testAddCategoriesToMenu()
    {
        /** @var \App\Models\Menu\Menu $menu */
        $menu = factory(Menu::class)->create();
        /** @var \App\Models\Article\Flag $menu */
        $flag = factory(Flag::class)->create();
        $categoryNames = factory(Category::class, 3)->create([
            'flag_id' => $flag->getKey()
        ])->pluck('name')->toArray();

        $this->browse(function (Browser $browser) use ($flag, $categoryNames) {
            $browser->login()
                ->visit(new MenuPage)
                ->addCategories($flag->getKey(), $categoryNames)
                ->assertVisible('@menuItem')
                ->click('@saveButton')
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/menu.notifications.updated')
                    );
                })
            ;
        });

        $menu->refresh();
        $this->assertEquals(count($categoryNames), $menu->items->count());
        $this->assertEquals($categoryNames, $menu->items->pluck('name')->toArray());
    }


    /**
     * Test menu item.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testItem()
    {
        /** @var \App\Models\Menu\Item $item */
        $item = factory(Item::class)->states('url')->create();

        $this->browse(function (Browser $browser) use ($item) {
            $browser->login()
                ->visit(new MenuPage)
                ->assertVisible('@menuItem')
                ->with('@menuItem > .dd-content', function (Browser $browser) use ($item) {
                    $browser->assertSeeIn('> a', $item->name)
                        ->click('> a')
                        ->waitFor('> .collapse.in')
                        ->with('> .collapse > .dd-item-setting-content', function (Browser $browser)  use ($item) {
                            $browser->assertInputValue('.row > .col-md-6:nth-child(1) input', $item->name)
                                ->assertInputValue('.row > .col-md-6:nth-child(2) input', $item->class)
                                ->assertVisible('.row > .col-md-6:nth-child(3)')
                                ->assertInputValue('.row > .col-md-6:nth-child(3) input', $item->url);

                            if ($item->open_new_window) {
                                $browser->assertChecked('.row:nth-child(2) input[type="checkbox"]', $item->open_new_window);
                            } else {
                                $browser->assertNotChecked('.row:nth-child(2) input[type="checkbox"]', $item->open_new_window);
                            }
                        });
                })
            ;
        });
    }


    /**
     * Test edit menu item.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testEditItem()
    {
        /** @var \App\Models\Menu\Item $item */
        $item = factory(Item::class)->states('url')->create([
            'open_new_window' => false
        ]);

        /** @var \App\Models\Menu\Item $newValues */
        $newValues = factory(Item::class)->states('url')->make([
            'open_new_window' => true
        ]);

        $this->browse(function (Browser $browser) use ($item, $newValues) {
            $browser->login()
                ->visit(new MenuPage)
                ->assertVisible('@menuItem')
                ->with('@menuItem > .dd-content', function (Browser $browser) use ($item, $newValues) {
                    $browser->assertSeeIn('> a', $item->name)
                        ->click('> a')
                        ->waitFor('> .collapse.in')
                        ->with('> .collapse > .dd-item-setting-content', function (Browser $browser)  use ($item, $newValues) {
                            $browser->type('.row > .col-md-6:nth-child(1) input', $newValues->name)
                                ->type('.row > .col-md-6:nth-child(2) input', $newValues->class)
                                ->type('.row > .col-md-6:nth-child(3) input', $newValues->url)
                                ->check('.row:nth-child(2) input[type="checkbox"]');
                        });
                })
                ->click('@saveButton')
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/menu.notifications.updated')
                    );
                })
            ;
        });

        $item->refresh();
        $this->assertEquals($newValues->name, $item->name);
        $this->assertEquals($newValues->class, $item->class);
        $this->assertEquals($newValues->url, $item->url);
        $this->assertEquals($newValues->open_new_window, $item->open_new_window);
    }


    /**
     * Test delete menu item.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testDeleteItem()
    {
        /** @var \App\Models\Menu\Item $item */
        $item = factory(Item::class)->create();

        $this->browse(function (Browser $browser) use ($item) {
            $browser->login()
                ->visit(new MenuPage)
                ->assertVisible('@menuItem')
                ->with('@menuItem > .dd-content', function (Browser $browser) use ($item) {
                    $browser->assertSeeIn('> a', $item->name)
                        ->click('> a')
                        ->waitFor('> .collapse.in')
                        ->with('> .collapse > .dd-item-setting-content', function (Browser $browser)  use ($item) {
                            $browser->press(trans('admin/menu.menu_item.btn_delete'));
                        });
                })
                ->assertMissing('@menuItem')
                ->click('@saveButton')
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/menu.notifications.updated')
                    );
                })
            ;
        });

        $item->refresh();
        $this->assertNotNull($item->deleted_at);
    }
}
