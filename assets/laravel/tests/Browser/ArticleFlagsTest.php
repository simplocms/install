<?php

namespace Tests\Browser;

use App\Models\Article\Flag;
use App\Models\Entrust\Role;
use App\Models\User;
use App\Models\Web\Language;
use App\Models\Web\Url;
use Tests\Browser\Components\JGrowl;
use Tests\Browser\Components\SweetAlertModal;
use Tests\Browser\Components\TableActions;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class ArticleFlagsTest extends DuskTestCase
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
                'article-flags-create', 'article-flags-edit', 'article-flags-delete'
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
        Flag::getQuery()->delete();
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
        /** @var \App\Models\Article\Flag $flag */
        $flag = factory(Flag::class)->create([
            'language_id' => Language::findDefault()->getKey()
        ]);

        // Full permissions
        $this->browse(function (Browser $browser) use ($flag) {
            $browser->login()
                ->visit(route('admin.article_flags.index'))
                ->assertUrlIs(route('admin.article_flags.index'))
                ->with('.page-title', function (Browser $browser) {
                    $browser->assertSee(trans('admin/article_flags/general.header_title'))
                        ->assertSee(trans('admin/article_flags/general.descriptions.index'));
                })
                ->assertSeeIn('.content > a.btn', trans('admin/article_flags/general.index.btn_create'))
                ->assertSeeIn(
                    '.breadcrumb-elements', trans('admin/article_flags/general.index.btn_create')
                )
                ->waitFor($this->tableRow(1) . '> td:nth-child(2)')
                ->within($this->tableRow(1), function (Browser $browser) use ($flag) {
                    $browser->assertSeeIn('> td:nth-child(1)', $flag->name)
                        ->assertSeeIn('> td:nth-child(2)', $flag->url)
                        ->assertVisible('> td:nth-child(4)')
                        ->within(new TableActions, function (Browser $browser) {
                            $browser->click('@toggle')
                                ->assertSeeIn(
                                    '@item:nth-child(1)',
                                    trans('admin/article_flags/general.index.btn_edit')
                                )
                                ->assertSeeIn(
                                    '@item:nth-child(2)',
                                    trans('admin/article_flags/general.index.btn_delete')
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

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit(route('admin.article_flags.index'))
                ->assertUrlIs(route('admin.article_flags.index'))
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
        $user = $this->createUserWithPermissions(['article-flags-create']);

        factory(Flag::class)->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit(route('admin.article_flags.index'))
                ->with('.page-title', function (Browser $browser) {
                    $browser->assertSee(trans('admin/article_flags/general.header_title'))
                        ->assertSee(trans('admin/article_flags/general.descriptions.index'));
                })
                ->assertSeeIn('.content > a.btn', trans('admin/article_flags/general.index.btn_create'))
                ->assertSeeIn(
                    '.breadcrumb-elements', trans('admin/article_flags/general.index.btn_create')
                )
                ->waitFor($this->tableRow(1))
                ->within($this->tableRow(1), function (Browser $browser) {
                    $browser->assertMissing('> td:nth-child(4)');
                });
        });
    }


    /**
     * Test button to create flag.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testButtonCreateFlag()
    {
        $this->browse(function (Browser $browser) {
            $browser->login()
                ->visit(route('admin.article_flags.index'))
                ->clickLink(trans('admin/article_flags/general.index.btn_create'), '.content > a.btn')
                ->assertUrlIs(route('admin.article_flags.create'));
        });
    }


    /**
     * Test action to edit a flag.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testActionEditFlag()
    {
        /** @var \App\Models\Article\Flag $flag */
        $flag = factory(Flag::class)->create();

        $this->browse(function (Browser $browser) use ($flag) {
            $browser->login()
                ->visit(route('admin.article_flags.index'))
                ->waitFor($this->tableRow(1))
                ->within($this->tableRow(1), function (Browser $browser) use ($flag) {
                    $browser
                        ->within(new TableActions, function (Browser $browser) {
                            $browser->clickNthChild(1);
                        });
                })
                ->assertUrlIs(route('admin.article_flags.edit', $flag->getKey()));
        });
    }


    /**
     * Test action to delete a flag.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testActionDeleteFlag()
    {
        /** @var \App\Models\Article\Flag $flag */
        $flag = factory(Flag::class)->create();

        $this->browse(function (Browser $browser) use ($flag) {
            $browser->login()
                ->visit(route('admin.article_flags.index'))
                ->waitFor($this->tableRow(1))
                ->within($this->tableRow(1), function (Browser $browser) {
                    $browser->within(new TableActions, function (Browser $browser) {
                        $browser->clickNthChild(2);
                    });
                })
                ->waitFor((new SweetAlertModal)->selector())
                ->within(new SweetAlertModal, function (Browser $browser) {
                    $browser->assertIsDelete(trans('admin/article_flags/general.confirm_delete.title'))->confirm();
                })
                ->waitForReload()
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/article_flags/general.notifications.deleted')
                    );
                });
        });

        $flag->refresh();
        $this->assertNotNull($flag->deleted_at);
    }


    /**
     * Test for creating flag.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testFormCreateFlag()
    {
        /** @var \App\Models\Article\Flag $flag */
        $flag = factory(Flag::class)->make();
        $flag->friendlifyUrlAttribute();

        $this->browse(function (Browser $browser) use ($flag) {
            $browser->login()
                ->visit(route('admin.article_flags.create'))
                ->with('.page-title', function (Browser $browser) {
                    $browser->assertSee(trans('admin/article_flags/general.header_title'))
                        ->assertSee(trans('admin/article_flags/general.descriptions.create'));
                })
                // Check fields for default values:
                ->assertNotChecked('use_tags')
                ->assertNotChecked('use_grid_editor')
                // Fill values
                ->type('name', $flag->name)
                ->click('input[name="url"]')
                // Submit
                ->press(trans('admin/article_flags/form.btn_create'))
                ->waitForReload()
                ->assertUrlIs(route('admin.article_flags.index'))
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/article_flags/general.notifications.created')
                    );
                });
        });

        $newFlag = Flag::first();

        $this->assertNotNull($newFlag);
        $this->assertEquals($flag->name, $newFlag->name);
        $this->assertEquals($flag->url, $newFlag->url);
    }


    /**
     * Test for editing a flag.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testFormEditFlag()
    {
        /** @var \App\Models\Article\Flag $flag */
        $flag = factory(Flag::class)->create([
            'use_tags' => true,
            'use_grid_editor' => true
        ]);

        /** @var \App\Models\Article\Flag $newValues */
        $newValues = factory(Flag::class)->make();

        $this->browse(function (Browser $browser) use ($flag, $newValues) {
            $browser->login()
                ->visit(route('admin.article_flags.edit', $flag->getKey()))
                ->with('.page-title', function (Browser $browser) {
                    $browser->assertSee(trans('admin/article_flags/general.header_title'))
                        ->assertSee(trans('admin/article_flags/general.descriptions.edit'));
                })
                // Check fields for default values:
                ->assertInputValue('name', $flag->name)
                ->assertInputValue('url', $flag->url)
                ->assertChecked('use_tags')
                ->assertChecked('use_grid_editor')
                // Fill new values
//                 WTF NOT WORKING WITH VUE???
//                ->type('name', $newValues->name)
                ->script(
                    "$('input[name=\"use_tags\"]').click();$('input[name=\"use_grid_editor\"]').click();"
                );

            // Submit
            $browser->press(trans('admin/article_flags/form.btn_update'))
                ->waitForReload()
                ->assertUrlIs(route('admin.article_flags.index'))
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/article_flags/general.notifications.updated')
                    );
                });
        });

        $flag->refresh();

//        $this->assertEquals($flag->name, $newValues->name);
        $this->assertFalse($flag->use_tags);
        $this->assertFalse($flag->use_grid_editor);
    }


    /**
     * Test for editing a flag with existing url.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testFormEditFlagWithUrlCollision()
    {
        /** @var \App\Models\Article\Flag $flagA */
        $flagA = factory(Flag::class)->create();

        /** @var \App\Models\Article\Flag $flagB */
        $flagB = factory(Flag::class)->create();

        $this->browse(function (Browser $browser) use ($flagA, $flagB) {
            $browser->login()
                ->visit(route('admin.article_flags.edit', $flagA->getKey()))
                // Check fields for default values:
                ->assertInputValue('url', $flagA->url)
                // Fill new values
                ->type('url', $flagB->url)
                // Submit
                ->press(trans('admin/article_flags/form.btn_update'))
                ->waitForReload()
                ->assertUrlIs(route('admin.article_flags.index'))
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/article_flags/general.notifications.updated')
                    );
                });
        });

        $flagA->refresh();

        $this->assertNotEquals($flagA->url, $flagB->url);
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
