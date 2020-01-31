<?php

namespace Tests\Browser;

use App\Models\Entrust\Role;
use App\Models\Photogallery\Photogallery;
use App\Models\User;
use App\Models\Web\Url;
use Carbon\Carbon;
use Tests\Browser\Components\CKEditor;
use Tests\Browser\Components\DatePicker;
use Tests\Browser\Components\JGrowl;
use Tests\Browser\Components\SweetAlertModal;
use Tests\Browser\Components\TableActions;
use Tests\Browser\Components\TimePicker;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class PhotogalleriesTest extends DuskTestCase
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
                'photogalleries-create', 'photogalleries-edit', 'photogalleries-delete'
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
        Photogallery::getQuery()->delete();
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
        /** @var \App\Models\Photogallery\Photogallery $photogallery */
        $photogallery = factory(Photogallery::class)->create();

        // Full permissions
        $this->browse(function (Browser $browser) use ($photogallery) {
            $browser->login()
                ->visit(route('admin.photogalleries'))
                ->assertUrlIs(route('admin.photogalleries'))
                ->with('.page-title', function (Browser $browser) use ($photogallery) {
                    $browser->assertSee(trans('admin/photogalleries/general.header_title'))
                        ->assertSee(trans('admin/photogalleries/general.descriptions.index'));
                })
                ->assertSeeIn('.content > a.btn', trans('admin/photogalleries/general.index.btn_create'))
                ->assertSeeIn(
                    '.breadcrumb-elements', trans('admin/photogalleries/general.index.btn_create')
                )
                ->waitFor($this->tableRow(1))
                ->within($this->tableRow(1), function (Browser $browser) use ($photogallery) {
                    $browser->assertSeeIn('> td:nth-child(1)', $photogallery->title)
                        ->assertSeeIn(
                            '> td:nth-child(5)',
                            mb_strtoupper(trans('admin/photogalleries/general.status.published'))
                        )
                        ->assertVisible('> td:nth-child(6)')
                        ->within(new TableActions, function (Browser $browser) {
                            $browser->click('@toggle')
                                ->assertSeeIn(
                                    '@item:nth-child(1)',
                                    trans('admin/photogalleries/general.index.btn_edit')
                                )
                                ->assertSeeIn(
                                    '@item:nth-child(2)',
                                    trans('admin/photogalleries/general.index.btn_preview')
                                )
                                ->assertSeeIn(
                                    '@item:nth-child(3)',
                                    trans('admin/photogalleries/general.index.btn_delete')
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
        /** @var \App\Models\User $user */
        $user = factory(\App\Models\User::class)->create();
        /** @var \App\Models\Photogallery\Photogallery $photogallery */
        $photogallery = factory(Photogallery::class)->create();

        $this->browse(function (Browser $browser) use ($user, $photogallery) {
            $browser->loginAs($user)
                ->visit(route('admin.photogalleries'))
                ->assertUrlIs(route('admin.photogalleries'))
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
        /** @var \App\Models\User $user */
        $user = $this->createUserWithPermissions(['photogalleries-create']);
        /** @var \App\Models\Photogallery\Photogallery $photogallery */
        $photogallery = factory(Photogallery::class)->create();

        $this->browse(function (Browser $browser) use ($user, $photogallery) {
            $browser->loginAs($user)
                ->visit(route('admin.photogalleries'))
                ->with('.page-title', function (Browser $browser) {
                    $browser->assertSee(trans('admin/photogalleries/general.header_title'))
                        ->assertSee(trans('admin/photogalleries/general.descriptions.index'));
                })
                ->assertSeeIn('.content > a.btn', trans('admin/photogalleries/general.index.btn_create'))
                ->assertSeeIn(
                    '.breadcrumb-elements', trans('admin/photogalleries/general.index.btn_create')
                )
                ->waitFor($this->tableRow(1))
                ->within($this->tableRow(1), function (Browser $browser) {
                    $browser->assertMissing('> td:nth-child(6)');
                });
        });
    }


    /**
     * Test button to create a photogallery.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testButtonCreatePhotogallery()
    {
        $this->browse(function (Browser $browser) {
            $browser->login()
                ->visit(route('admin.photogalleries'))
                ->clickLink(trans('admin/photogalleries/general.index.btn_create'), '.content > a.btn')
                ->assertUrlIs(route('admin.photogalleries.create'))
                ->with('.page-title', function (Browser $browser) {
                    $browser->assertSee(trans('admin/photogalleries/general.header_title'))
                        ->assertSee(trans('admin/photogalleries/general.descriptions.create'));
                });
        });
    }


    /**
     * Test action to edit a photogallery.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testActionEditPhotogallery()
    {
        /** @var \App\Models\Photogallery\Photogallery $photogallery */
        $photogallery = factory(Photogallery::class)->create();

        $this->browse(function (Browser $browser) use ($photogallery) {
            $browser->login()
                ->visit(route('admin.photogalleries'))
                ->waitFor($this->tableRow(1))
                ->within($this->tableRow(1), function (Browser $browser) use ($photogallery) {
                    $browser
                        ->within(new TableActions, function (Browser $browser) {
                            $browser->clickNthChild(1);
                        })
                        ->assertUrlIs(route('admin.photogalleries.edit', $photogallery->getKey()));
                });
        });
    }


    /**
     * Test action to delete a photogallery.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testActionDeletePhotogallery()
    {
        /** @var \App\Models\Photogallery\Photogallery $photogallery */
        $photogallery = factory(Photogallery::class)->create();

        $this->browse(function (Browser $browser) {
            $browser->login()
                ->visit(route('admin.photogalleries'))
                ->waitFor($this->tableRow(1))
                ->within($this->tableRow(1), function (Browser $browser) {
                    $browser->within(new TableActions, function (Browser $browser) {
                        $browser->clickNthChild(3);
                    });
                })
                ->waitFor((new SweetAlertModal)->selector())
                ->within(new SweetAlertModal, function (Browser $browser) {
                    $browser->assertIsDelete(trans('admin/photogalleries/general.confirm_delete.title'))->confirm();
                })
                ->waitForReload()
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/photogalleries/general.notifications.deleted')
                    );
                });
        });

        $photogallery->refresh();
        $this->assertNotNull($photogallery->deleted_at);
    }


    /**
     * Test for creating photogallery.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testFormCreatePhotogallery()
    {
        /** @var \App\Models\Photogallery\Photogallery $photogallery */
        $photogallery = factory(Photogallery::class)->make([
            'unpublish_at' => Carbon::now()->addMonth(1)->second(0)->minute(0)
        ]);

        $this->browse(function (Browser $browser) use ($photogallery) {
            $tabSelector = ".content .tabbable > .nav > li";

            $browser->login()
                ->resize(1280, 1280)
                ->visit(route('admin.photogalleries.create'))
                ->with('.page-title', function (Browser $browser) {
                    $browser->assertSee(trans('admin/photogalleries/general.header_title'))
                        ->assertSee(trans('admin/photogalleries/general.descriptions.create'));
                })
                ->assertSeeIn("$tabSelector.active", trans('admin/photogalleries/form.tabs.details'))
                // Check fields for important default values:
                ->assertInputValue('sort', 1)
                ->assertInputValueIsNot('publish_at_date', '')
                ->assertInputValueIsNot('publish_at_time', '')
                // Fill values
                ->type('title', $photogallery->title)
                ->within(new CKEditor('textarea[name="text"]'), function (Browser $browser) use ($photogallery) {
                    $browser->typeIn($photogallery->text);
                })
                //// Switch to SEO tab
                ->click("$tabSelector > a[href='#tab_seo']")
                // Fill values
                ->type('seo_title', $photogallery->seo_title)
                ->type('seo_description', $photogallery->seo_description)
                //// Switch to publish tab
                ->click("$tabSelector > a[href='#tab_publish']")
                // Fill values
                ->within(new DatePicker("input[name='unpublish_at_date']"), function (Browser $browser) use ($photogallery) {
                    $browser->selectDate($photogallery->unpublish_at);
                })
                ->within(new TimePicker("input[name='unpublish_at_time']"), function (Browser $browser) use ($photogallery) {
                    $browser->selectTime($photogallery->unpublish_at);
                })
                //// Submit
                ->click('#photogalleries-form button.btn[type="submit"]')
                ->waitForReload()
                ->assertUrlIs(route('admin.photogalleries'))
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/photogalleries/general.notifications.created')
                    );
                });
        });

        $newPhotogallery = Photogallery::first();

        $this->assertNotNull($newPhotogallery);
        $this->assertEquals($photogallery->title, $newPhotogallery->title);
        $this->assertNotNull($newPhotogallery->text);
        $this->assertEquals(1, $newPhotogallery->sort);

        $this->assertEquals($photogallery->seo_title, $newPhotogallery->seo_title);
        $this->assertEquals($photogallery->seo_description, $newPhotogallery->seo_description);

        $this->assertEquals($photogallery->unpublish_at, $newPhotogallery->unpublish_at);
    }


    /**
     * Test for editing a photogallery.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testFormEditPhotogallery()
    {
        /** @var \App\Models\Photogallery\Photogallery $photogallery */
        $photogallery = factory(Photogallery::class)->create([
            'unpublish_at' => Carbon::now()->addMonth(1)->second(0)->minute(0)
        ]);

        /** @var \App\Models\Photogallery\Photogallery $newValues */
        $newValues = factory(Photogallery::class)->make([
            'sort' => 5
        ]);

        $this->browse(function (Browser $browser) use ($photogallery, $newValues) {
            $tabSelector = ".content .tabbable > .nav > li";

            $browser->login()
                ->resize(1280, 1280)
                ->visit(route('admin.photogalleries.edit', $photogallery->getKey()))
                ->with('.page-title', function (Browser $browser) {
                    $browser->assertSee( trans('admin/photogalleries/general.header_title'))
                        ->assertSee( trans('admin/photogalleries/general.descriptions.edit'));
                })
                ->assertSeeIn("$tabSelector.active", trans('admin/photogalleries/form.tabs.details'))
                // Check fields for important default values:
                ->assertInputValue('title', $photogallery->title)
                ->assertInputValue('url', $photogallery->url)
                ->assertInputValue('text', $photogallery->text)
                ->assertInputValue('seo_title', $photogallery->seo_title)
                ->assertInputValue('seo_description', $photogallery->seo_description)
                ->assertInputValue('publish_at_date', $photogallery->publish_at->format('d.m.Y'))
                ->assertInputValue('publish_at_time', $photogallery->publish_at->format('H:i'))
                ->assertInputValue('unpublish_at_date', $photogallery->unpublish_at->format('d.m.Y'))
                ->assertInputValue('unpublish_at_time', $photogallery->unpublish_at->format('H:i'))
                // Fill values
                ->type('title', $newValues->title)
                //// Switch to SEO tab
                ->click("$tabSelector > a[href='#tab_seo']")
                // Fill values
                ->type('seo_title', $newValues->seo_title)
                ->type('seo_description', $newValues->seo_description)
                //// Switch to publish tab
                ->click("$tabSelector > a[href='#tab_publish']")
                // Fill values
                ->within(new DatePicker("input[name='unpublish_at_date']"), function (Browser $browser) {
                    $browser->clearPicker();
                })
                ->within(new TimePicker("input[name='unpublish_at_time']"), function (Browser $browser) {
                    $browser->clearPicker();
                })
                //// Submit
                ->click('#photogalleries-form button.btn[type="submit"]')
                ->waitForReload()
                ->assertUrlIs(route('admin.photogalleries'))
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/photogalleries/general.notifications.updated')
                    );
                });
        });

        $photogallery->refresh();

        $this->assertEquals($newValues->title, $photogallery->title);

        $this->assertEquals($newValues->seo_title, $photogallery->seo_title);
        $this->assertEquals($newValues->seo_description, $photogallery->seo_description);

        $this->assertNull($photogallery->unpublish_at);
    }


    /**
     * Test for editing a photogallery with existing url.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testFormEditPhotogalleryWithUrlCollision()
    {
        /** @var \App\Models\Photogallery\Photogallery $photogalleryA */
        $photogalleryA = factory(Photogallery::class)->create();

        /** @var \App\Models\Photogallery\Photogallery $photogalleryB */
        $photogalleryB = factory(Photogallery::class)->create();

        $this->browse(function (Browser $browser) use ($photogalleryA, $photogalleryB) {
            $browser->login()
                ->visit(route('admin.photogalleries.edit', $photogalleryA->getKey()))
                // Check fields for important default values:
                ->assertInputValue('url', $photogalleryA->url)
                // Fill
                ->type('url', $photogalleryB->url)
                //// Submit
                ->click('#photogalleries-form button.btn[type="submit"]')
                ->waitForReload()
                ->assertUrlIs(route('admin.photogalleries'))
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/photogalleries/general.notifications.updated')
                    );
                });
        });

        $photogalleryA->refresh();

        $this->assertNotEquals($photogalleryB->url, $photogalleryA->url);
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
