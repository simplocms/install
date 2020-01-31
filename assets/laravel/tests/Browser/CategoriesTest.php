<?php

namespace Tests\Browser;

use App\Models\Article\Category;
use App\Models\Article\Flag;
use App\Models\Entrust\Role;
use App\Models\User;
use Tests\Browser\Components\JGrowl;
use Tests\Browser\Components\SweetAlertModal;
use Tests\Browser\Components\TableActions;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class CategoriesTest extends DuskTestCase
{
    /**
     * Selector for table row.
     *
     * @var string
     */
    private $tableRowSelector = '.datatable > tbody > tr';

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        Browser::$userResolver = function () {
            return $this->createUserWithPermissions([
                'article-categories-create', 'article-categories-edit', 'article-categories-delete'
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
        User::getQuery()->delete();
        Role::getQuery()->delete();
    }


    /**
     * Test index page as a user with full permissions.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testIndexPageWithFullPermissions()
    {
        /** @var \App\Models\Article\Category $category */
        $category = factory(Category::class)->create();

        // Full permissions
        $this->browse(function (Browser $browser) use ($category) {
            $browser->login()
                ->visit(route('admin.categories.index', $category->flag->url))
                ->assertUrlIs(route('admin.categories.index', $category->flag->url))
                ->with('.page-title', function (Browser $browser) use ($category) {
                    $browser->assertSee($category->flag->name)
                        ->assertSee(trans('admin/category/general.descriptions.index'));
                })
                ->waitFor($this->tableRow(1))
                ->assertSeeIn('.content > a.btn', trans('admin/category/general.index.btn_create'))
                ->assertSeeIn('.breadcrumb-elements', trans('admin/category/general.index.btn_create'))
                ->within($this->tableRow(1), function (Browser $browser) use ($category) {
                    $browser->assertSeeIn('> td:nth-child(1)', $category->name)
                        ->assertSeeIn(
                            '> td:nth-child(2)',
                            mb_strtoupper(trans('admin/category/general.status.published'))
                        )
                        ->assertVisible('> td:nth-child(5)')
                        ->within(new TableActions, function (Browser $browser) {
                            $browser->click('@toggle')
                                ->assertSeeIn(
                                    '@item:nth-child(1)',
                                    trans('admin/category/general.index.btn_edit')
                                )
                                ->assertSeeIn(
                                    '@item:nth-child(2)',
                                    trans('admin/category/general.index.btn_preview')
                                )
                                ->assertSeeIn(
                                    '@item:nth-child(3)',
                                    trans('admin/category/general.index.btn_delete')
                                );
                        });
                });
        });
    }


    /**
     * Test index page as a user with no permissions.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testIndexPageWithNoPermissions()
    {
        $this->browse(function (Browser $browser) {
            /** @var \App\Models\User $user */
            $user = factory(\App\Models\User::class)->create();

            /** @var \App\Models\Article\Category $category */
            $category = factory(Category::class)->create();

            $browser->loginAs($user)
                ->visit(route('admin.categories.index', $category->flag->url))
                ->assertUrlIs(route('admin.categories.index', $category->flag->url))
                ->assertSee('403');
        });
    }


    /**
     * Test index page as a user with no permissions.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testIndexPageWithCreatePermissions()
    {
        $this->browse(function (Browser $browser) {
            /** @var \App\Models\User $user */
            $user = $this->createUserWithPermissions(['article-categories-create']);

            /** @var \App\Models\Article\Category $category */
            $category = factory(Category::class)->create();

            $browser->loginAs($user)
                ->visit(route('admin.categories.index', $category->flag->url))
                ->with('.page-title', function (Browser $browser) use ($category) {
                    $browser->assertSee($category->flag->name)
                        ->assertSee(trans('admin/category/general.descriptions.index'));
                })
                ->assertSeeIn('.content > a.btn', trans('admin/category/general.index.btn_create'))
                ->assertSeeIn('.breadcrumb-elements', trans('admin/category/general.index.btn_create'))
                ->waitFor($this->tableRow(1))
                ->within($this->tableRow(1), function (Browser $browser) {
                    $browser->assertMissing('> td:nth-child(5)');
                });
        });
    }


    /**
     * Test button to create a category.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testButtonCreateCategory()
    {
        $this->browse(function (Browser $browser) {
            /** @var \App\Models\Article\Category $category */
            $category = factory(Category::class)->create();

            $browser->login()
                ->visit(route('admin.categories.index', $category->flag->url))
                ->clickLink(trans('admin/category/general.index.btn_create'), '.content > a.btn')
                ->assertUrlIs(route('admin.categories.create', $category->flag->url));
        });
    }


    /**
     * Test action to edit the category.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testActionEditCategory()
    {
        /** @var \App\Models\Article\Category $category */
        $category = factory(Category::class)->create();

        $this->browse(function (Browser $browser) use ($category) {
            $browser->login()
                ->visit(route('admin.categories.index', $category->flag->url))
                ->waitFor($this->tableRow(1))
                ->within($this->tableRow(1), function (Browser $browser) use ($category) {
                    $browser
                        ->within(new TableActions, function (Browser $browser) {
                            $browser->clickNthChild(1);
                        })
                        ->assertUrlIs(route('admin.categories.edit', [
                            'flag' => $category->flag->url,
                            'category' => $category->getKey()
                        ]));
                });
        });
    }


    /**
     * Test action to delete the category.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testActionDeleteCategory()
    {
        /** @var \App\Models\Article\Category $category */
        $category = factory(Category::class)->create();

        $this->browse(function (Browser $browser) use ($category) {
            $browser->login()
                ->visit(route('admin.categories.index', $category->flag->url))
                ->waitFor($this->tableRow(1))
                ->within($this->tableRow(1), function (Browser $browser) {
                    $browser->within(new TableActions, function (Browser $browser) {
                        $browser->clickNthChild(3);
                    });
                })
                ->waitFor((new SweetAlertModal)->selector())
                ->within(new SweetAlertModal, function (Browser $browser) {
                    $browser->assertIsDelete(trans('admin/category/general.confirm_delete.title'))->confirm();
                })
                ->waitForReload()
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/category/general.notifications.deleted')
                    );
                });
        });

        $category->refresh();
        $this->assertNotNull($category->deleted_at);
    }


    /**
     * Test for creating page.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testFormCreateCategory()
    {
        $this->browse(function (Browser $browser) {
            /** @var \App\Models\Article\Category $category */
            $category = factory(Category::class)->make();

            $tabSelector = ".content .tabbable > .nav > li";

            $browser->login()
                ->visit(route('admin.categories.create', $category->flag->url))
                ->with('.page-title', function (Browser $browser) use ($category) {
                    $browser->assertSee($category->flag->name)
                        ->assertSee(trans('admin/category/general.descriptions.create'));
                })
                ->assertSeeIn("$tabSelector.active", trans('admin/category/form.tabs.info'))
                // Check fields for important default values:
                ->assertChecked('show')

                // Fill values
                ->type('name', $category->name)

                // Switch to SEO tab
                ->click("$tabSelector > a[href='#tab_seo']")

                // Fill values
                ->type('seo_title', $category->seo_title)
                ->type('seo_description', $category->seo_description)

                // Submit
                ->click('#btn-submit-edit')
                ->waitForReload()
                ->assertUrlIs(route('admin.categories.index', $category->flag->url))
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/category/general.notifications.created')
                    );
                });

            $newCategory = Category::first();
            $this->assertNotNull($newCategory);
            $this->assertEquals($category->name, $newCategory->name);
            $this->assertEquals($category->seo_title, $newCategory->seo_title);
            $this->assertEquals($category->seo_description, $newCategory->seo_description);
            $this->assertTrue($newCategory->show);
        });
    }


    /**
     * Test for editing the category.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    // TODO: bugged dusk
//    public function testFormEditCategory()
//    {
//        $this->browse(function (Browser $browser) {
//            /** @var \App\Models\Article\Category $category */
//            $category = factory(Category::class)->create([
//                'show' => false
//            ]);
//
//            /** @var \App\Models\Article\Category $newValues */
//            $newValues = factory(Category::class)->make();
//
//            $tabSelector = ".content .tabbable > .nav > li";
//
//            $browser->login()
//                ->visit(route('admin.categories.edit', [
//                    'flag' => $category->flag->url,
//                    'category' => $category->id
//                ]))
//                ->with('.page-title', function (Browser $browser) use ($category) {
//                    $browser->assertSee($category->flag->name)
//                        ->assertSee(trans('admin/category/general.descriptions.edit'));
//                })
//                ->assertSeeIn("$tabSelector.active", trans('admin/category/form.tabs.info'))
//
//                // Check fields for default values:
//                ->assertInputValue('name', $category->name)
//                ->assertInputValue('url', $category->url)
//                ->assertNotChecked('show')
//                ->assertInputValue('seo_title', $category->seo_title)
//                ->assertInputValue('seo_description', $category->seo_description)
//
//                // Fill new values
//                ->type('name', $newValues->name);
//            $browser->type('url', "    +ěščřžýáíé=   =IO/*   _:?lop   ")
//                ->script("$('input[name=\"show\"]').click();");
//
//                // Switch to SEO tab
//            $browser->click("$tabSelector > a[href='#tab_seo']")
//                // Fill values
//                ->type('seo_title', $newValues->seo_title)
//                ->type('seo_description', $newValues->seo_description)
//                ->click("$tabSelector > a[href='#tab_details']")
//                // Submit
//                ->click('#btn-submit-edit')
//                ->waitForReload()
//                ->assertUrlIs(route('admin.categories.index', $category->flag->url))
//                ->waitFor((new JGrowl())->selector())
//                ->within(new JGrowl, function (Browser $browser) {
//                    $browser->assertSays(
//                        trans('admin/general.flash_level.success'),
//                        trans('admin/category/general.notifications.updated')
//                    );
//                })
//            ;
//
//            $category->refresh();
//
//            $this->assertEquals($category->name, $newValues->name);
//            $this->assertEquals($category->url,"escrzyaie-io-lop");
//            $this->assertTrue($category->show);
//            $this->assertEquals($category->seo_title, $newValues->seo_title);
//            $this->assertEquals($category->seo_description, $newValues->seo_description);
//        });
//    }


    /**
     * Test for editing the category with existing url.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testFormEditCategoryWithUrlCollision()
    {
        /** @var \App\Models\Article\Category $categoryA */
        $categoryA = factory(Category::class)->create();

        /** @var \App\Models\Article\Category $categoryA */
        $categoryB = factory(Category::class)->create();

        $this->browse(function (Browser $browser) use ($categoryA, $categoryB) {
            $browser->login()
                ->visit(route('admin.categories.edit', [
                    'flag' => $categoryA->flag->url,
                    'category' => $categoryA->id
                ]))
                ->assertInputValue('url', $categoryA->url)
                ->type('url', $categoryB->url)
                // Submit
                ->click('#btn-submit-edit')
                ->waitForReload()
                ->assertUrlIs(route('admin.categories.index', $categoryA->flag->url))
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/category/general.notifications.updated')
                    );
                })
            ;
        });

        $this->assertNotEquals($categoryB->url, $categoryA->url);
    }


    /**
     * Get selector of table row on specified index.
     *
     * @param int $rowIndex
     * @return string
     */
    private function tableRow(int $rowIndex): string
    {
        return "{$this->tableRowSelector}:nth-child($rowIndex):not(.datatable-empty-row)";
    }
}
