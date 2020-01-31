<?php

namespace Tests\Browser;

use App\Models\Entrust\Role;
use App\Models\Page\Content;
use App\Models\Page\Page;
use App\Models\User;
use App\Models\Web\Url;
use Carbon\Carbon;
use Tests\Browser\Components\DatePicker;
use Tests\Browser\Components\GridEditor;
use Tests\Browser\Components\JGrowl;
use Tests\Browser\Components\SweetAlertModal;
use Tests\Browser\Components\TableActions;
use Tests\Browser\Components\TimePicker;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class PagesTest extends DuskTestCase
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
            return $this->createUserWithPermissions(['pages-create', 'pages-edit', 'pages-delete']);
        };
    }


    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    public function tearDown()
    {
        Page::getQuery()->delete();
        User::getQuery()->delete();
        Role::getQuery()->delete();
        Url::getQuery()->delete();

        parent::tearDown();

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
    public function testIndexPageWithFullPermissions()
    {
        /** @var \App\Models\Page\Page $homepage */
        $homepage = factory(Page::class)->create([
            'is_homepage' => true
        ]);

        // Full permissions
        $this->browse(function (Browser $browser) use ($homepage) {
            $browser->login()
                ->visit(route('admin.pages.index'))
                ->assertUrlIs(route('admin.pages.index'))
                ->assertSee(trans('admin/pages/general.header_title'))
                ->assertSeeIn('.content > a.btn', trans('admin/pages/general.index.btn_create'))
                ->assertSeeIn('.breadcrumb-elements', trans('admin/pages/general.index.btn_create'))
                ->waitFor($this->tableRow(1))
                ->within($this->tableRow(1), function (Browser $browser) use ($homepage) {
                    $browser->assertSeeIn('> td:nth-child(1)', $homepage->name)
                        ->assertSeeIn(
                            '> td:nth-child(4)',
                            mb_strtoupper(trans('admin/pages/general.status.published'))
                        )
                        ->assertVisible('> td:nth-child(5)')
                        ->within(new TableActions, function (Browser $browser) {
                            $browser->click('@toggle')
                                ->assertSeeIn(
                                    '@item:nth-child(1)', trans('admin/pages/general.index.btn_edit')
                                )
                                ->assertSeeIn(
                                    '@item:nth-child(2)', trans('admin/pages/general.index.btn_preview')
                                )
                                ->assertSeeIn(
                                    '@item:nth-child(3)',
                                    trans('admin/pages/general.index.btn_duplicate')
                                )
                                ->assertSeeIn(
                                    '@item:nth-child(5)', trans('admin/pages/general.index.btn_delete')
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

            $browser->loginAs($user)
                ->visit(route('admin.pages.index'))
                ->assertUrlIs(route('admin.pages.index'))
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
            $user = $this->createUserWithPermissions(['pages-create']);

            $browser->loginAs($user)
                ->visit(route('admin.pages.index'))
                ->assertSee(trans('admin/pages/general.header_title'))
                ->assertSeeIn('.content > a.btn', trans('admin/pages/general.index.btn_create'))
                ->assertSeeIn('.breadcrumb-elements', trans('admin/pages/general.index.btn_create'))
                ->within($this->tableRow(1), function (Browser $browser) {
                    $browser->assertMissing('> td:nth-child(4)');
                });
        });
    }


    /**
     * Test button to create a page.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testButtonCreatePage()
    {
        $this->browse(function (Browser $browser) {
            $browser->login()
                ->visit(route('admin.pages.index'))
                ->clickLink(trans('admin/pages/general.index.btn_create'), '.content > a.btn')
                ->assertUrlIs(route('admin.pages.create'));
        });
    }


    /**
     * Test action to edit the page.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testActionEditPage()
    {
        /** @var \App\Models\Page\Page $homepage */
        $homepage = factory(Page::class)->create([
            'is_homepage' => true
        ]);

        $this->browse(function (Browser $browser) use ($homepage) {
            $browser->login()
                ->visit(route('admin.pages.index'))
                ->waitFor($this->tableRow(1))
                ->within($this->tableRow(1), function (Browser $browser) use ($homepage) {
                    $browser
                        ->within(new TableActions, function (Browser $browser) {
                            $browser->clickNthChild(1);
                        })
                        ->assertUrlIs(route('admin.pages.edit', $homepage->getKey()));
                });
        });
    }


    /**
     * Test action to duplicate the page.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testActionDuplicatePage()
    {
        factory(Page::class)->create([
            'is_homepage' => true
        ]);

        $this->browse(function (Browser $browser) {
            $browser->login()
                ->visit(route('admin.pages.index'))
                ->waitFor($this->tableRow(1))
                ->within($this->tableRow(1), function (Browser $browser) {
                    $browser->within(new TableActions, function (Browser $browser) {
                        $browser->clickNthChild(3);
                    });
                })
                ->waitForReload()
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/pages/general.notifications.duplicated')
                    );
                });
        });
    }


    /**
     * Test action to delete the page.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testActionDeletePage()
    {
        /** @var \App\Models\Page\Page $homepage */
        $homepage = factory(Page::class)->create([
            'is_homepage' => true
        ]);

        $this->browse(function (Browser $browser) {
            $browser->login()
                ->visit(route('admin.pages.index'))
                ->waitFor($this->tableRow(1))
                ->within($this->tableRow(1), function (Browser $browser) {
                    $browser->within(new TableActions, function (Browser $browser) {
                        $browser->clickNthChild(5);
                    });
                })
                ->waitFor((new SweetAlertModal)->selector())
                ->within(new SweetAlertModal, function (Browser $browser) {
                    $browser->assertIsDelete(trans('admin/pages/general.confirm_delete.title'))->confirm();
                })
                ->waitForReload()
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/pages/general.notifications.deleted')
                    );
                });
        });

        $homepage->refresh();
        $this->assertNotNull($homepage->deleted_at);
    }


    /**
     * Test for creating page.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testFormCreatePage()
    {
        $this->browse(function (Browser $browser) {
            /** @var \App\Models\Page\Page $page */
            $page = factory(Page::class)->make([
                'unpublish_at' => Carbon::now()->addMonth()->second(0)->minute(0)
            ]);

            $tabSelector = ".content .tabbable > .nav > li";

            $browser->login()
                ->resize(1280, 1280)
                ->visit(route('admin.pages.create'))
                ->assertSee(trans('admin/pages/general.descriptions.create'))
                ->assertSeeIn("$tabSelector.active", trans('admin/pages/form.tabs.details'))
                // Check fields for important default values:
                ->assertVisible('input[name="url"][disabled]')
                ->assertChecked('is_homepage')
                ->assertChecked('published')
                ->assertChecked('seo_index')
                ->assertChecked('seo_follow')
                ->assertChecked('seo_sitemap')
                ->assertNotChecked('set_unpublish_at')
                ->assertInputValueIsNot('publish_at_date', '')
                ->assertInputValueIsNot('publish_at_time', '')

                // Fill values
                ->type('name', $page->name)

                // Switch to SEO tab
                ->click("$tabSelector > a[href='#seo']")
                // Fill values
                ->type('seo_title', $page->seo_title)
                ->type('seo_description', $page->seo_description)
                ->script("$('input[name=\"seo_follow\"]').click()");

                // Switch to publish tab
            $browser->click("$tabSelector > a[href='#planning']")
                // Fill values
                ->script("$('input[name=\"set_unpublish_at\"]').click()");

            $browser->waitFor($unpublishInput = "input[name='unpublish_at_date']")
                ->within(new DatePicker($unpublishInput), function (Browser $browser) use ($page) {
                    $browser->selectDate($page->unpublish_at);
                })
                ->within(new TimePicker("input[name='unpublish_at_time']"), function (Browser $browser) use ($page) {
                    $browser->selectTime($page->unpublish_at);
                })

                // Switch to OG tags tab
                ->click("$tabSelector > a[href='#open-graph']")
                // Fill values
                ->type('input[name="open_graph[title]"]', $page->open_graph->get('title'))
                ->type('open_graph[type]', $page->open_graph->get('type'))
                ->type('open_graph[url]', $page->open_graph->get('url'))
                ->type('open_graph[description]', $page->open_graph->get('description'))

                // Switch to GridEditor tab
                ->click("$tabSelector > a[href='#grid']")
                // Fill values
//                ->within(new GridEditor, function (Browser $browser) {
//                    $browser->addNewRow('6-6');
//                })

                // Submit
                ->click('#pages-form-submit')
                ->waitForReload()
                ->assertUrlIs(route('admin.pages.index'))
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/pages/general.notifications.created')
                    );
                });

            $newPage = Page::first();
            $this->assertNotNull($newPage);
            $this->assertEquals($page->name, $newPage->name);
            $this->assertEquals($page->seo_title, $newPage->seo_title);
            $this->assertEquals($page->seo_description, $newPage->seo_description);
            $this->assertFalse($newPage->seo_follow);
            $this->assertTrue($newPage->seo_index);
            $this->assertTrue($newPage->seo_sitemap);
            $this->assertEquals($page->unpublish_at, $newPage->unpublish_at);
            $this->assertEquals($page->open_graph->get('title'), $newPage->open_graph->get('title'));
            $this->assertEquals($page->open_graph->get('type'), $newPage->open_graph->get('type'));
            $this->assertEquals($page->open_graph->get('url'), $newPage->open_graph->get('url'));
            $this->assertEquals(
                $page->open_graph->get('description'), $newPage->open_graph->get('description')
            );
            $this->assertNotNull($newPage->getActiveContent());
        });
    }


    /**
     * Test for editing the page.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testFormEditPage()
    {
        $this->browse(function (Browser $browser) {
            /** @var \App\Models\Page\Page $page */
            $page = factory(Page::class)->create([
                'published' => true,
                'seo_index' => true,
                'seo_follow' => true,
                'seo_sitemap' => true,
            ]);
            factory(Content::class)->create([
                'page_id' => $page->id
            ]);
            /** @var \App\Models\Page\Page $newValues */
            $newValues = factory(Page::class)->make();

            $tabSelector = ".content .tabbable > .nav > li";

            $browser->login()
                ->visit(route('admin.pages.edit', $page->id))
                ->assertSee(trans('admin/pages/general.descriptions.edit'))
                ->assertSeeIn("$tabSelector.active", trans('admin/pages/form.tabs.details'))
                // Check fields for default values:
                ->assertInputValue('name', $page->name)
                ->assertInputValue('url', $page->url)
                ->assertNotChecked('is_homepage')
                ->assertChecked('published')
                ->assertInputValue('seo_title', $page->seo_title)
                ->assertInputValue('seo_description', $page->seo_description)
                ->assertChecked('seo_index')
                ->assertChecked('seo_follow')
                ->assertChecked('seo_sitemap')
                ->assertInputValue('publish_at_date', $page->publish_at->format('d.m.Y'))
                ->assertInputValue('publish_at_time', $page->publish_at->format('H:i'))
                ->assertNotChecked('set_unpublish_at')
                ->assertInputValue('open_graph[title]', $page->open_graph->get('title'))
                ->assertInputValue('open_graph[type]', $page->open_graph->get('type'))
                ->assertInputValue('open_graph[url]', $page->open_graph->get('url'))
                ->assertInputValue('open_graph[description]', $page->open_graph->get('description'))
                // Fill new values
                ->type('name', $newValues->name)
                ->type('url', "    +ěščřžýáíé=   =IO/*   _:?lop   ")
                // Switch to SEO tab
                ->click("$tabSelector > a[href='#seo']")
                // Fill values
                ->type('seo_title', $newValues->seo_title)
                ->type('seo_description', $newValues->seo_description)
                // Switch to publish tab
                ->click("$tabSelector > a[href='#planning']")
                // Fill values
                ->within(new DatePicker('input[name="publish_at_date"]'), function (Browser $browser) use ($newValues) {
                    $browser->selectDate($newValues->publish_at);
                })
                ->within(new TimePicker("input[name='publish_at_time']"), function (Browser $browser) use ($newValues) {
                    $browser->selectTime($newValues->publish_at);
                })
                // Switch to OG tags tab
                ->click("$tabSelector > a[href='#open-graph']")
                // Fill values
                ->type('input[name="open_graph[title]"]', $newValues->open_graph->get('title'))
                ->type('open_graph[type]', $newValues->open_graph->get('type'))
                ->type('open_graph[url]', $newValues->open_graph->get('url'))
                ->type('open_graph[description]', $newValues->open_graph->get('description'))
                // Submit
                ->click('#pages-form-submit')
                ->waitForReload()
                ->assertUrlIs(route('admin.pages.index'))
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/pages/general.notifications.updated')
                    );
                })
            ;

            $page->refresh();

            $this->assertEquals($page->name, $newValues->name);
            $this->assertEquals($page->url,"escrzyaie-io-lop");
            $this->assertEquals($page->seo_title, $newValues->seo_title);
            $this->assertEquals($page->seo_description, $newValues->seo_description);
            $this->assertEquals($page->publish_at, $newValues->publish_at);
        });
    }


    /**
     * Test for editing the page.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testFormEditPageWithUrlCollisionAndSaveButton()
    {
        /** @var \App\Models\Page\Page $pageA */
        $pageA = factory(Page::class)->create();
        /** @var \App\Models\Page\Page $pageB */
        $pageB = factory(Page::class)->create();
        factory(Content::class)->create(['page_id' => $pageB->id]);

        $this->browse(function (Browser $browser) use ($pageA, $pageB) {
            $browser->login()
                ->visit(route('admin.pages.edit', $pageB->id))
                ->assertInputValue('url', $pageB->url)
                ->type('url', $pageA->url)
                // Submit
                ->click('#pages-form-save')
                ->assertVisible('form > .element-lock-hover')
                ->waitUntilMissing('form > .element-lock-hover')
                ->assertUrlIs(route('admin.pages.edit', $pageB->id))
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/pages/general.notifications.updated')
                    );
                })
                ->assertInputValue('url', $pageA->url . '-1')
            ;
        });
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
