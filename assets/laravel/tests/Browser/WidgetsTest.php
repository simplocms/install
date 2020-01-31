<?php

namespace Tests\Browser;

use App\Models\Entrust\Role;
use App\Models\User;
use App\Models\Web\Language;
use App\Models\Widget\Content;
use App\Models\Widget\Widget;
use Tests\Browser\Components\GridEditor;
use Tests\Browser\Components\JGrowl;
use Tests\Browser\Components\SweetAlertModal;
use Tests\Browser\Components\TableActions;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class WidgetsTest extends DuskTestCase
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
                'widgets-create', 'widgets-edit', 'widgets-delete', 'ge-edit-layout'
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

        Widget::getQuery()->delete();
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
        /** @var \App\Models\Widget\Widget $widget */
        $widget = factory(Widget::class)->create();

        // Full permissions
        $this->browse(function (Browser $browser) use ($widget) {
            $browser->login()
                ->visit(route('admin.widgets.index'))
                ->assertUrlIs(route('admin.widgets.index'))
                ->with('.page-title', function (Browser $browser) {
                    $browser->assertSee(trans('admin/widgets/general.header_title'))
                        ->assertSee(trans('admin/widgets/general.descriptions.index'));
                })
                ->assertSeeIn('.content > a.btn', trans('admin/widgets/general.index.btn_create'))
                ->assertSeeIn('.breadcrumb-elements', trans('admin/widgets/general.index.btn_create'))
                ->waitFor($this->tableRow(1))
                ->within($this->tableRow(1), function (Browser $browser) use ($widget) {
                    $browser->assertSeeIn('> td:nth-child(1)', $widget->name)
                        ->assertSeeIn('> td:nth-child(2)', $widget->id)
                        ->assertVisible('> td:nth-child(3)')
                        ->within(new TableActions, function (Browser $browser) {
                            $browser->click('@toggle')
                                ->assertSeeIn(
                                    '@item:nth-child(1)', trans('admin/widgets/general.index.btn_edit')
                                )
                                ->assertSeeIn(
                                    '@item:nth-child(2)',
                                    trans('admin/widgets/general.index.btn_delete')
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
                ->visit(route('admin.widgets.index'))
                ->assertUrlIs(route('admin.widgets.index'))
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
        $user = $this->createUserWithPermissions(['widgets-create']);

        factory(Widget::class)->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit(route('admin.widgets.index'))
                ->with('.page-title', function (Browser $browser) {
                    $browser->assertSee(trans('admin/widgets/general.header_title'))
                        ->assertSee(trans('admin/widgets/general.descriptions.index'));
                })
                ->assertSeeIn('.content > a.btn', trans('admin/widgets/general.index.btn_create'))
                ->assertSeeIn('.breadcrumb-elements', trans('admin/widgets/general.index.btn_create'))
                ->waitFor($this->tableRow(1))
                ->within($this->tableRow(1), function (Browser $browser) {
                    $browser->assertMissing('> td:nth-child(3)');
                });
        });
    }


    /**
     * Test button to create widget.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testButtonCreateWidget()
    {
        $this->browse(function (Browser $browser) {
            $browser->login()
                ->visit(route('admin.widgets.index'))
                ->clickLink(trans('admin/widgets/general.index.btn_create'), '.content > a.btn')
                ->assertUrlIs(route('admin.widgets.create'));
        });
    }


    /**
     * Test action to edit a widget.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testActionEditWidget()
    {
        /** @var \App\Models\Widget\Widget $widget */
        $widget = factory(Widget::class)->create();

        $this->browse(function (Browser $browser) use ($widget) {
            $browser->login()
                ->visit(route('admin.widgets.index'))
                ->waitFor($this->tableRow(1))
                ->within($this->tableRow(1), function (Browser $browser) use ($widget) {
                    $browser
                        ->within(new TableActions, function (Browser $browser) {
                            $browser->clickNthChild(1);
                        })
                        ->assertUrlIs(route('admin.widgets.edit', $widget->getKey()));
                });
        });
    }


    /**
     * Test action to delete a widget.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testActionDeleteWidget()
    {
        /** @var \App\Models\Widget\Widget $widget */
        $widget = factory(Widget::class)->create();

        $this->browse(function (Browser $browser) use ($widget) {
            $browser->login()
                ->visit(route('admin.widgets.index'))
                ->waitFor($this->tableRow(1))
                ->within($this->tableRow(1), function (Browser $browser) {
                    $browser->within(new TableActions, function (Browser $browser) {
                        $browser->clickNthChild(2);
                    });
                })
                ->waitFor((new SweetAlertModal)->selector())
                ->within(new SweetAlertModal, function (Browser $browser) {
                    $browser->assertIsDelete(trans('admin/widgets/general.confirm_delete.title'))->confirm();
                })
                ->waitForReload()
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/widgets/general.notifications.deleted')
                    );
                });
        });

        $this->assertNull(Widget::find($widget->getKey()));
    }


    /**
     * Test for creating widget.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testFormCreateWidget()
    {
        /** @var \App\Models\Widget\Widget $widget */
        $widget = factory(Widget::class)->make();

        $this->browse(function (Browser $browser) use ($widget) {
            $tabSelector = ".content .tabbable > .nav > li";

            $browser->login()
                ->visit(route('admin.widgets.create'))
                ->with('.page-title', function (Browser $browser) {
                    $browser->assertSee(trans('admin/widgets/general.header_title'))
                        ->assertSee(trans('admin/widgets/general.descriptions.create'));
                })
                ->assertSeeIn("$tabSelector.active", trans('admin/widgets/form.tabs.details'))
                // Fill values
                ->type('id', $widget->id)
                ->type('name', $widget->name)
                // Switch to GridEditor tab
                ->click("$tabSelector > a[href='#content-tab']")
                // Fill values
                ->within(new GridEditor, function (Browser $browser) {
                    $browser->addNewRow('6-6');
                })
                // Submit
                ->click('button[type="submit"]')
                ->waitForReload()
                ->assertUrlIs(route('admin.widgets.index'))
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/widgets/general.notifications.created')
                    );
                });
        });

        $newWidget = Widget::first();
        $this->assertNotNull($newWidget);
        $this->assertEquals($widget->id, $newWidget->id);
        $this->assertEquals($widget->name, $newWidget->name);

        $content = $newWidget->getLanguageContent(Language::findDefault());
        $this->assertNotNull($content);
//        $this->assertNotNull($content->getRaw());
    }


    /**
     * Test for editing a widget.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testFormEditWidget()
    {
        /** @var \App\Models\Widget\Widget $widget */
        $widget = factory(Widget::class)->create();
        /** @var \App\Models\Widget\Widget $newValues */
        $newValues = factory(Widget::class)->make();

        $this->browse(function (Browser $browser) use ($widget, $newValues) {
            $tabSelector = ".content .tabbable > .nav > li";

            $browser->login()
                ->visit(route('admin.widgets.edit', $widget->getKey()))
                ->with('.page-title', function (Browser $browser) {
                    $browser->assertSee(trans('admin/widgets/general.header_title'))
                        ->assertSee(trans('admin/widgets/general.descriptions.edit'));
                })
                ->assertSeeIn("$tabSelector.active", trans('admin/widgets/form.tabs.details'))
                // Check fields for default values:
                ->assertInputValue('id', $widget->id)
                ->assertInputValue('name', $widget->name)
                // Fill new values
                ->type('id', $newValues->id)
                ->type('name', $newValues->name)
                // Switch to SEO tab
                ->click("$tabSelector > a[href='#content-tab']")
                // Fill values
                ->within(new GridEditor, function (Browser $browser) {
                    $browser->addNewRow('3-3-3-3');
                })
                // Submit
                ->click('button[type="submit"]')
                ->assertVisible('form > .element-lock-hover')
                ->waitForReload()
                ->assertUrlIs(route('admin.widgets.index'))
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/widgets/general.notifications.updated')
                    );
                });
        });

        $widget = Widget::find($newValues->getKey());

        $this->assertNotNull($widget);
        $this->assertEquals($widget->id, $newValues->id);
        $this->assertEquals($widget->name, $newValues->name);

        $content = $widget->getLanguageContent(Language::findDefault());
        $this->assertNotNull($content);
//        $this->assertNotNull($content->getRaw());
    }


    /**
     * Test for editing a category with existing identifier.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testFormEditCategoryWithIdentifierCollision()
    {
        /** @var \App\Models\Widget\Widget $widgetA */
        $widgetA = factory(Widget::class)->create();
        factory(Content::class)->create(['widget_id' => $widgetA->getKey()]);

        /** @var \App\Models\Widget\Widget $widgetB */
        $widgetB = factory(Widget::class)->create();

        $this->browse(function (Browser $browser) use ($widgetA, $widgetB) {
            $browser->login()
                ->visit(route('admin.widgets.edit', $widgetA->getKey()))
                ->assertInputValue('id', $widgetA->id)
                ->type('id', $widgetB->id)
                // Submit
                ->click('#widgets-form-submit')
                ->assertVisible('form > .element-lock-hover')
                ->waitUntilMissing('form > .element-lock-hover')
                ->assertUrlIs(route('admin.widgets.edit', $widgetA->getKey()))
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.danger'),
                        trans('admin/general.notifications.validation_failed')
                    );
                });
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
