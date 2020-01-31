<?php

namespace Tests\Browser;

use App\Models\Entrust\Role;
use App\Models\Module\InstalledModule;
use App\Models\User;
use Tests\Browser\Components\JGrowl;
use Tests\Browser\Components\SweetAlertModal;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class ModulesTest extends DuskTestCase
{
    /**
     * Selector for table row.
     *
     * @var string
     */
    private $tableRowSelector = '.content > .panel > table > tbody > tr';

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        Browser::$userResolver = function () {
            return $this->createUserWithPermissions([
                'modules-toggle', 'modules-install'
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

        /** @var \App\Models\Module\InstalledModule $installedModule */
        foreach (InstalledModule::all() as $installedModule) {
            $installedModule->module->uninstall();
        }

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
        $this->browse(function (Browser $browser) {
            $browser->login()
                ->visit(route('admin.modules'))
                ->assertUrlIs(route('admin.modules'))
                ->with('.page-title', function (Browser $browser) {
                    $browser->assertSee(trans('admin/modules.header_title'))
                        ->assertSee(trans('admin/modules.descriptions.index'));
                })
                ->waitFor($this->tableRow(1))
                ->within($this->tableRow(1), function (Browser $browser) {
                    $browser->assertSeeIn(
                        '> td:nth-child(2)', mb_strtoupper(trans('admin/modules.status.disabled'))
                    )
                        ->assertSeeIn(
                            '> td:nth-child(3)',
                            mb_strtoupper(trans('admin/modules.index.btn_install'))
                        );
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
                ->visit(route('admin.modules'))
                ->assertUrlIs(route('admin.modules'))
                ->assertSee('403');
        });
    }


    /**
     * Test index page as a user with show modules permission.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testIndexPageWithShowPermissions()
    {
        /** @var \App\Models\User $user */
        $user = $this->createUserWithPermissions(['modules-show']);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit(route('admin.modules'))
                ->with('.page-title', function (Browser $browser) {
                    $browser->assertSee(trans('admin/modules.header_title'))
                        ->assertSee(trans('admin/modules.descriptions.index'));
                })
                ->within($this->tableRow(1), function (Browser $browser) {
                    $browser->assertMissing('> td:nth-child(2)')
                        ->assertMissing('> td:nth-child(3)');
                });
        });
    }


    /**
     * Test index page as a user with toggle modules permission.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testIndexPageWithTogglePermissions()
    {
        /** @var \App\Models\User $user */
        $user = $this->createUserWithPermissions(['modules-toggle']);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit(route('admin.modules'))
                ->with('.page-title', function (Browser $browser) {
                    $browser->assertSee(trans('admin/modules.header_title'))
                        ->assertSee(trans('admin/modules.descriptions.index'));
                })
                ->within($this->tableRow(1), function (Browser $browser) {
                    $browser->assertMissing('> td:nth-child(3)');
                });
        });
    }


    /**
     * Test for installing a module.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testInstallModule()
    {
        $modules = \Module::all();
        $this->assertTrue(count($modules) > 0);
        /** @var \App\Models\Module\Module $module */
        $module = current($modules);

        $this->browse(function (Browser $browser) use ($module) {
            $browser->login()
                ->visit(route('admin.modules'))
                ->within($this->tableRow(1), function (Browser $browser) use ($module) {
                    $browser->assertSeeIn('> td:nth-child(1)', $module->getName())
                        ->click('> td:nth-child(3) > a');
                })
                ->waitForReload()
                ->assertSeeIn(
                    $this->tableRow(1) . ' > td:nth-child(2)',
                    strtoupper(trans('admin/modules.status.enabled'))
                )
                ->assertSeeIn(
                    $this->tableRow(1) . ' > td:nth-child(3)',
                    strtoupper(trans('admin/modules.index.btn_uninstall'))
                );
        });

        /** @var \App\Models\Module\InstalledModule $installed */
        $installed = InstalledModule::findNamed($module->getName());
        $this->assertNotNull($installed);
    }


    /**
     * Test for installing a module.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testUninstallModule()
    {
        $modules = \Module::all();
        $this->assertTrue(count($modules) > 0);

        /** @var \App\Models\Module\Module $module */
        $module = current($modules);
        $module->install();

        $this->browse(function (Browser $browser) use ($module) {
            $browser->login()
                ->visit(route('admin.modules'))
                ->within($this->tableRow(1), function (Browser $browser) use ($module) {
                    $browser->assertSeeIn('> td:nth-child(1)', $module->getName())
                        ->assertSeeIn('> td:nth-child(2)', strtoupper(trans('admin/modules.status.enabled')))
                        ->assertSeeIn(
                            '> td:nth-child(3)', strtoupper(trans('admin/modules.index.btn_uninstall'))
                        )
                        ->click('> td:nth-child(3) > a');
                })
                ->waitFor((new SweetAlertModal)->selector())
                ->within(new SweetAlertModal, function (Browser $browser) {
                    $browser->assertVisible('> .swal-icon--warning')
                        ->assertSeeIn('> .swal-title', trans('admin/modules.confirm_uninstall.title'))
                        ->assertVisible('@confirm.swal-button--danger')
                        ->assertSeeIn('@confirm', trans('admin/modules.confirm_uninstall.confirm'))
                        ->confirm();
                })
                ->waitForReload()
                ->assertSeeIn(
                    $this->tableRow(1) . ' > td:nth-child(2)',
                    mb_strtoupper(trans('admin/modules.status.disabled'))
                )
                ->assertSeeIn(
                    $this->tableRow(1) . ' > td:nth-child(3)',
                    strtoupper(trans('admin/modules.index.btn_install'))
                );
        });

        $installed = InstalledModule::findNamed($module->getName());
        $this->assertNull($installed);
    }


    /**
     * Test for installing a module.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testEnableDisableModule()
    {
        $modules = \Module::all();
        $this->assertTrue(count($modules) > 0);
        /** @var \App\Models\Module\Module $module */
        $module = current($modules);
        $module->install();

        $this->browse(function (Browser $browser) use ($module) {
            $browser->login()
                ->visit(route('admin.modules'))
                ->within($this->tableRow(1), function (Browser $browser) use ($module) {
                    $browser->assertSeeIn('> td:nth-child(1)', $module->getName())
                        ->assertSeeIn(
                            '> td:nth-child(2)', strtoupper(trans('admin/modules.status.enabled'))
                        )
                        ->click('> td:nth-child(2) > a');
                })
                ->waitForReload()
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) use ($module) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/modules.notifications.disabled', ['name' => $module->getName()])
                    );
                });

            /** @var \App\Models\Module\InstalledModule $installed */
            $installed = InstalledModule::findNamed($module->getName());
            $this->assertNotNull($installed);
            $this->assertFalse($installed->enabled);

            $browser->within($this->tableRow(1), function (Browser $browser) use ($module) {
                $browser->assertSeeIn(
                    '> td:nth-child(2)', mb_strtoupper(trans('admin/modules.status.disabled'))
                )->click('> td:nth-child(2) > a');
            })
                ->waitForReload()
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) use ($module) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/modules.notifications.enabled', ['name' => $module->getName()])
                    );
                })
                ->assertSeeIn(
                    $this->tableRow(1) . ' > td:nth-child(2)',
                    strtoupper(trans('admin/modules.status.enabled'))
                )
                ->assertSeeIn(
                    $this->tableRow(1) . ' > td:nth-child(3)',
                    strtoupper(trans('admin/modules.index.btn_uninstall'))
                );
        });

        /** @var \App\Models\Module\InstalledModule $installed */
        $installed = InstalledModule::findNamed($module->getName());
        $this->assertNotNull($installed);
        $this->assertTrue($installed->enabled);
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
