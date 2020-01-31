<?php

namespace Tests\Browser;

use App\Models\Entrust\Role;
use App\Models\User;
use Tests\Browser\Components\JGrowl;
use Tests\Browser\Components\SweetAlertModal;
use Tests\Browser\Components\TableActions;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class UsersTest extends DuskTestCase
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
                'users-create', 'users-edit', 'users-delete'
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
        User::getQuery()->delete();
        Role::getQuery()->delete();

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
        /** @var \App\Models\User $protectedUser */
        $protectedUser = factory(User::class)->create([
            'protected' => true
        ]);

        /** @var \App\Models\User $user */
        $user = factory(User::class)->create([
            'enabled' => false
        ]);

        // Full permissions
        $this->browse(function (Browser $browser) use ($protectedUser, $user) {
            $browser->login()
                ->resize(1280, 1280)
                ->visit(route('admin.users'))
                ->assertUrlIs(route('admin.users'))
                ->with('.page-title', function (Browser $browser) {
                    $browser->assertSee(trans('admin/users/general.header_title'))
                        ->assertSee(trans('admin/users/general.descriptions.index'));
                })
                ->assertSeeIn('.content > a.btn', trans('admin/users/general.index.btn_create'))
                ->assertSeeIn('.breadcrumb-elements', trans('admin/users/general.index.btn_create'))
                ->waitFor($this->tableRow(1))
                ->within($this->tableRow(1), function (Browser $browser) use ($protectedUser) {
                    $browser
                        ->assertVisible('> td:nth-child(1) > span')
                        ->assertSeeIn('> td:nth-child(2)', $protectedUser->username)
                        ->assertSeeIn('> td:nth-child(3)', $protectedUser->name)
                        ->assertSeeIn('> td:nth-child(4)', $protectedUser->email)
                        ->assertMissing('> td:nth-child(5) > ul');
                })
                ->within($this->tableRow(2), function (Browser $browser) use ($user) {
                    $browser
                        ->assertVisible(
                            '> td:nth-child(1) > a[title="' .
                            trans('admin/users/general.index.title_enable') . '"]'
                        )
                        ->assertSeeIn('> td:nth-child(2)', $user->username)
                        ->assertSeeIn('> td:nth-child(3)', $user->name)
                        ->assertSeeIn('> td:nth-child(4)', $user->email)
                        ->assertVisible('> td:nth-child(5)')
                        ->within(new TableActions, function (Browser $browser) {
                            $browser->click('@toggle')
                                ->assertSeeIn(
                                    '@item:nth-child(1)', trans('admin/users/general.index.btn_edit')
                                )
                                ->assertSeeIn(
                                    '@item:nth-child(2)', trans('admin/users/general.index.btn_delete')
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
                ->visit(route('admin.users'))
                ->assertUrlIs(route('admin.users'))
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
        $user = $this->createUserWithPermissions(['users-create']);

        factory(User::class)->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit(route('admin.users'))
                ->with('.page-title', function (Browser $browser) {
                    $browser->assertSee(trans('admin/users/general.header_title'))
                        ->assertSee(trans('admin/users/general.descriptions.index'));
                })
                ->assertSeeIn('.content > a.btn', trans('admin/users/general.index.btn_create'))
                ->assertSeeIn('.breadcrumb-elements', trans('admin/users/general.index.btn_create'))
                ->waitFor($this->tableRow(1))
                ->within($this->tableRow(1), function (Browser $browser) {
                    $browser->assertMissing('> td:nth-child(5)');
                });
        });
    }


    /**
     * Test for enabling and disabling language.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testDeactivateUser()
    {
        /** @var \App\Models\User $user */
        $user = factory(\App\Models\User::class)->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->login()
                ->visit(route('admin.users'))
                ->waitFor($this->tableRow(1))
                ->assertVisible(
                    $this->tableRow(1) . ' > td:nth-child(1) > a[title="' .
                    trans('admin/users/general.index.title_disable') . '"]'
                )
                ->click($this->tableRow(1) . ' > td:nth-child(1) > a')
                ->waitForReload()
                ->waitFor(
                    $this->tableRow(1) . ' > td:nth-child(1) > a[title="' .
                    trans('admin/users/general.index.title_enable') . '"]'
                )
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/users/general.notifications.disabled')
                    );
                });
        });

        $user->refresh();
        $this->assertFalse($user->enabled);
    }


    /**
     * Test for enabling and disabling language.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testActivateUser()
    {
        /** @var \App\Models\User $user */
        $user = factory(\App\Models\User::class)->create([
            'enabled' => false
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->login()
                ->visit(route('admin.users'))
                ->waitFor($this->tableRow(1))
                ->assertVisible(
                    $this->tableRow(1) . ' > td:nth-child(1) > a[title="' .
                    trans('admin/users/general.index.title_enable') . '"]'
                )
                ->click($this->tableRow(1) . ' > td:nth-child(1) > a')
                ->waitForReload()
                ->waitFor(
                    $this->tableRow(1) . ' > td:nth-child(1) > a[title="' .
                    trans('admin/users/general.index.title_disable') . '"]'
                )
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/users/general.notifications.enabled')
                    );
                });
        });

        $user->refresh();
        $this->assertTrue($user->enabled);
    }


    /**
     * Test button to create user.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testButtonCreateUser()
    {
        $this->browse(function (Browser $browser) {
            $browser->login()
                ->visit(route('admin.users'))
                ->clickLink(trans('admin/users/general.index.btn_create'), '.content > a.btn')
                ->assertUrlIs(route('admin.users.create'));
        });
    }


    /**
     * Test action to edit a user.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testActionEditUser()
    {
        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->login()
                ->visit(route('admin.users'))
                ->waitFor($this->tableRow(1))
                ->within($this->tableRow(1), function (Browser $browser) {
                    $browser
                        ->within(new TableActions, function (Browser $browser) {
                            $browser->clickNthChild(1);
                        });
                })
                ->assertUrlIs(route('admin.users.edit', $user->getKey()));
        });
    }


    /**
     * Test action to delete a flag.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testActionDeleteUser()
    {
        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->login()
                ->visit(route('admin.users'))
                ->waitFor($this->tableRow(1))
                ->within($this->tableRow(1), function (Browser $browser) {
                    $browser->within(new TableActions, function (Browser $browser) {
                        $browser->clickNthChild(2);
                    });
                })
                ->waitFor((new SweetAlertModal)->selector())
                ->within(new SweetAlertModal, function (Browser $browser) {
                    $browser->assertIsDelete(trans('admin/users/general.confirm_delete.title'))->confirm();
                })
                ->waitForReload()
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/users/general.notifications.deleted')
                    );
                });
        });

        $this->assertNull(User::find($user->getKey()));
    }


    /**
     * Test for creating user.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testFormCreateUser()
    {
        /** @var \App\Models\User $user */
        $user = factory(User::class)->make();
        /** @var \App\Models\Entrust\Role $role */
        $role = factory(Role::class)->create();

        $this->browse(function (Browser $browser) use ($user, $role) {
            $tabSelector = ".content .tabbable > .nav > li";

            $browser->login()
                ->resize(1280, 1280)
                ->visit(route('admin.users.create'))
                ->with('.page-title', function (Browser $browser) {
                    $browser->assertSee(trans('admin/users/general.header_title'))
                        ->assertSee(trans('admin/users/general.descriptions.create'));
                })
                ->waitUntil('!$.active')
                ->assertSeeIn("$tabSelector.active", trans('admin/users/form.tabs.details'))
                // Check fields for default values:
                ->assertChecked('enabled')
                // Fill values
                ->type('firstname', $user->firstname)
                ->type('lastname', $user->lastname)
                ->type('username', $user->username)
                ->type('email', $user->email)
                ->type('password', "Password1")
                ->type('password_confirmation', "Password1")
                ->click('input[name="enabled"] + .switchery')
                //// Switch to roles tab
                ->click("$tabSelector > a[href='#tab_roles']")
                ->with('#tbl-roles > tbody > tr:nth-child(2)', function (Browser $browser) use ($role) {
                    $browser->assertNotChecked('> td:nth-child(1) > input')
                        ->assertSeeIn('> td:nth-child(2)', $role->display_name)
                        ->click('> td:nth-child(1) > input + .switchery');
                })
                // Submit
                ->press(trans('admin/users/form.btn_create'))
                ->assertUrlIs(route('admin.users'))
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/users/general.notifications.created')
                    );
                });
        });

        $newUser = User::latest()->first();

        $this->assertNotNull($newUser);
        $this->assertEquals($user->firstname, $newUser->firstname);
        $this->assertEquals($user->lastname, $newUser->lastname);
        $this->assertEquals($user->username, $newUser->username);
        $this->assertEquals($user->email, $newUser->email);
        $this->assertFalse($newUser->enabled);
        $this->assertTrue(\Hash::check('Password1', $newUser->password));

        $this->assertEquals($newUser->roles->first()->getKey(), $role->getKey());
    }


    /**
     * Test for editing a user.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testFormEditUser()
    {
        /** @var \App\Models\Entrust\Role $role */
        $role = factory(Role::class)->create();

        /** @var \App\Models\User $user */
        $user = factory(User::class)->create([
            'enabled' => false
        ]);

        /** @var \App\Models\User $newValues */
        $newValues = factory(User::class)->make();

        $this->browse(function (Browser $browser) use ($user, $newValues, $role) {
            $tabSelector = ".content .tabbable > .nav > li";

            $browser->login()
                ->visit(route('admin.users.edit', $user->getKey()))
                ->with('.page-title', function (Browser $browser) {
                    $browser->assertSee(trans('admin/users/general.header_title'))
                        ->assertSee(trans('admin/users/general.descriptions.edit'));
                })
                ->assertSeeIn("$tabSelector.active", trans('admin/users/form.tabs.details'))
                // Check fields for default values:
                ->assertInputValue('firstname', $user->firstname)
                ->assertInputValue('lastname', $user->lastname)
                ->assertInputValue('username', $user->username)
                ->assertInputValue('email', $user->email)
                ->assertInputValue('password', "")
                ->assertInputValue('password_confirmation', "")
                ->assertNotChecked('enabled')
                // Fill new values
                ->type('firstname', $newValues->firstname)
                ->type('lastname', $newValues->lastname)
                ->type('username', $newValues->username)
                ->type('email', $newValues->email)
                ->type('password', "Password2")
                ->type('password_confirmation', "Password2")
                ->click('input[name="enabled"] + .switchery')
                //// Switch to roles tab
                ->click("$tabSelector > a[href='#tab_roles']")
                ->with('#tbl-roles > tbody > tr:nth-child(2)', function (Browser $browser) use ($role) {
                    $browser->assertNotChecked('> td:nth-child(1) > input')
                        ->assertSeeIn('> td:nth-child(2)', $role->display_name)
                        ->click('> td:nth-child(1) > input + .switchery');
                })
                // Submit
                ->press(trans('admin/users/form.btn_update'))
                ->assertUrlIs(route('admin.users'))
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/users/general.notifications.updated')
                    );
                });
        });

        $user->refresh();

        $this->assertEquals($user->firstname, $newValues->firstname);
        $this->assertEquals($user->lastname, $newValues->lastname);
        $this->assertEquals($user->username, $newValues->username);
        $this->assertEquals($user->email, $newValues->email);
        $this->assertTrue($user->enabled);
        $this->assertTrue(\Hash::check('Password2', $user->password));

        $this->assertEquals($user->roles->first()->getKey(), $role->getKey());
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
