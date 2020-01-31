<?php

namespace Tests\Browser;

use App\Models\Entrust\Permission;
use App\Models\Entrust\Role;
use App\Models\User;
use Tests\Browser\Components\JGrowl;
use Tests\Browser\Components\SweetAlertModal;
use Tests\Browser\Components\TableActions;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class RolesTest extends DuskTestCase
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
                'roles-create', 'roles-edit', 'roles-delete'
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
        Permission::getQuery()->delete();

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
        /** @var \App\Models\Entrust\Role $protectedRole */
        $protectedRole = factory(Role::class)->create([
            'name' => 'administrator',
            'protected' => true
        ]);

        /** @var \App\Models\Entrust\Role $role */
        $role = factory(Role::class)->create([
            'enabled' => false
        ]);

        // Full permissions
        $this->browse(function (Browser $browser) use ($protectedRole, $role) {
            $browser->login()
                ->visit(route('admin.roles'))
                ->assertUrlIs(route('admin.roles'))
                ->with('.page-title', function (Browser $browser) {
                    $browser->assertSee(trans('admin/roles/general.header_title'))
                        ->assertSee(trans('admin/roles/general.descriptions.index'));
                })
                ->assertSeeIn('.content > a.btn', trans('admin/roles/general.index.btn_create'))
                ->assertSeeIn('.breadcrumb-elements', trans('admin/roles/general.index.btn_create'))
                ->waitFor($this->tableRow(1))
                ->within($this->tableRow(1), function (Browser $browser) use ($protectedRole) {
                    $browser
                        ->assertVisible('> td:nth-child(1) > span')
                        ->assertSeeIn('> td:nth-child(2)', $protectedRole->display_name)
                        ->assertSeeIn('> td:nth-child(3)', $protectedRole->description)
                        ->assertMissing('> td:nth-child(4) > ul');
                })
                ->within($this->tableRow(2), function (Browser $browser) use ($role) {
                    $browser
                        ->assertVisible(
                            '> td:nth-child(1) > a[title="' .
                            trans('admin/roles/general.index.title_enable') .
                            '"]'
                        )
                        ->assertSeeIn('> td:nth-child(2)', $role->display_name)
                        ->assertSeeIn('> td:nth-child(3)', $role->description)
                        ->assertVisible('> td:nth-child(4) > ul')
                        ->within(new TableActions, function (Browser $browser) {
                            $browser->click('@toggle')
                                ->assertSeeIn(
                                    '@item:nth-child(1)', trans('admin/roles/general.index.btn_edit')
                                )
                                ->assertSeeIn(
                                    '@item:nth-child(2)', trans('admin/roles/general.index.btn_delete')
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
        $user = factory(User::class)->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit(route('admin.roles'))
                ->assertUrlIs(route('admin.roles'))
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
        $user = $this->createUserWithPermissions(['roles-create']);

        factory(Role::class)->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit(route('admin.roles'))
                ->with('.page-title', function (Browser $browser) {
                    $browser->assertSee(trans('admin/roles/general.header_title'))
                        ->assertSee(trans('admin/roles/general.descriptions.index'));
                })
                ->assertSeeIn('.content > a.btn', trans('admin/roles/general.index.btn_create'))
                ->assertSeeIn('.breadcrumb-elements', trans('admin/roles/general.index.btn_create'))
                ->waitFor($this->tableRow(1))
                ->within($this->tableRow(1), function (Browser $browser) {
                    $browser->assertMissing('> td:nth-child(4)');
                });
        });
    }


    /**
     * Test for deactivating a role.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testDeactivateRole()
    {
        /** @var \App\Models\Entrust\Role $role */
        $role = factory(Role::class)->create();

        $this->browse(function (Browser $browser) use ($role) {
            $browser->login()
                ->visit(route('admin.roles'))
                ->waitFor($this->tableRow(1))
                ->waitForReload(function (Browser $browser): void {
                    $browser->click($this->tableRow(1) . ' > td:nth-child(1) > a');
                })
                ->waitFor($this->tableRow(1))
                ->assertVisible(
                    $this->tableRow(1) . ' > td:nth-child(1) > a[title="' .
                    trans('admin/roles/general.index.title_enable') . '"]'
                )
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/roles/general.notifications.disabled')
                    );
                });
        });

        $role->refresh();
        $this->assertFalse($role->enabled);
    }


    /**
     * Test activate a role.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testActivateRole()
    {
        /** @var \App\Models\Entrust\Role $role */
        $role = factory(Role::class)->create([
            'enabled' => false
        ]);

        $this->browse(function (Browser $browser) use ($role) {
            $browser->login()
                ->visit(route('admin.roles'))
                ->waitFor($this->tableRow(1))
                ->click($this->tableRow(1) . ' > td:nth-child(1) > a')
                ->waitForReload()
                ->waitFor($this->tableRow(1))
                ->assertVisible(
                    $this->tableRow(1) . ' > td:nth-child(1) > a[title="' .
                    trans('admin/roles/general.index.title_disable') . '"]'
                )
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/roles/general.notifications.enabled')
                    );
                });
        });

        $role->refresh();
        $this->assertTrue($role->enabled);
    }


    /**
     * Test deactivate protected role.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testDeactivateProtectedRole()
    {
        /** @var \App\Models\Entrust\Role $role */
        $role = factory(Role::class)->create([
            'name' => 'programmer',
            'protected' => true
        ]);

        $this->browse(function (Browser $browser) use ($role) {
            $browser->login()
                ->visit(route('admin.roles'))
                ->waitFor($this->tableRow(1))
                ->assertMissing($this->tableRow(1) . ' > td:nth-child(1) > a');
        });

        $role->refresh();
        $this->assertTrue($role->enabled);
    }


    /**
     * Test button to create role.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testButtonCreateRole()
    {
        $this->browse(function (Browser $browser) {
            $browser->login()
                ->visit(route('admin.roles'))
                ->clickLink(trans('admin/roles/general.index.btn_create'), '.content > a.btn')
                ->assertUrlIs(route('admin.roles.create'));
        });
    }


    /**
     * Test action to edit a role.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testActionEditRole()
    {
        /** @var \App\Models\Entrust\Role $role */
        $role = factory(Role::class)->create();

        $this->browse(function (Browser $browser) use ($role) {
            $browser->login()
                ->visit(route('admin.roles'))
                ->waitFor($this->tableRow(1))
                ->within($this->tableRow(1), function (Browser $browser) use ($role) {
                    $browser
                        ->within(new TableActions, function (Browser $browser) {
                            $browser->clickNthChild(1);
                        });
                })
                ->assertUrlIs(route('admin.roles.edit', $role->getKey()));
        });
    }


    /**
     * Test action to delete a role.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testActionDeleteRole()
    {
        /** @var \App\Models\Entrust\Role $role */
        $role = factory(Role::class)->create();

        $this->browse(function (Browser $browser) use ($role) {
            $browser->login()
                ->visit(route('admin.roles'))
                ->waitFor($this->tableRow(1))
                ->within($this->tableRow(1), function (Browser $browser) {
                    $browser->within(new TableActions, function (Browser $browser) {
                        $browser->clickNthChild(2);
                    });
                })
                ->waitFor((new SweetAlertModal)->selector())
                ->within(new SweetAlertModal, function (Browser $browser) {
                    $browser->assertIsDelete(trans('admin/roles/general.confirm_delete.title'))->confirm();
                })
                ->waitForReload()
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/roles/general.notifications.deleted')
                    );
                });
        });

        $this->assertNull(Role::find($role->getKey()));
    }


    /**
     * Test for creating role.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testFormCreateRole()
    {
        /** @var \App\Models\Entrust\Role $role */
        $role = factory(Role::class)->make();

        $this->browse(function (Browser $browser) use ($role) {
            $browser->login()
                ->resize(1280, 1280)
                ->visit(route('admin.roles.create'))
                ->with('.page-title', function (Browser $browser) {
                    $browser->assertSee(trans('admin/roles/general.header_title'))
                        ->assertSee(trans('admin/roles/general.descriptions.create'));
                })
                ->waitUntil('!$.active')
                // Check fields for default values:
                ->assertChecked('enabled');

            // Verify all permissions are unchecked.
            $groups = config('permissions.groups', []);
            foreach ($groups as $group) {
                foreach ($group['areas'] as $area) {
                    foreach (array_keys($group['permissions']) as $permission) {
                        $browser->assertNotChecked("{$area}[{$permission}]");
                    }
                }
            }

            // Fill values
            $browser->type('display_name', $role->display_name)
                ->type('description', $role->description)
                ->check('articles[all]')
                ->check('article-categories[show]')
                ->check('article-flags[create]')
                ->check('widgets[delete]')
                ->check('pages[edit]')
                // Submit
                ->press(trans('admin/roles/form.btn_create'))
                ->assertUrlIs(route('admin.roles'))
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/roles/general.notifications.created')
                    );
                });
        });

        $newRole = Role::latest()->first();

        $this->assertNotNull($newRole);
        $this->assertEquals($role->display_name, $newRole->display_name);
        $this->assertEquals($role->description, $newRole->description);

        // has all these permissions
        $this->assertTrue($newRole->hasPermission([
            'articles-all', 'article-categories-show', 'article-flags-create', 'widgets-delete', 'pages-edit'
        ], true));

        // has neither of these permissions
        $this->assertFalse($newRole->hasPermission([
            'articles-show', 'articles-create', 'articles-edit', 'articles-delete',
            'article-categories-create', 'article-categories-edit', 'article-categories-delete',
            'article-flags-edit', 'article-flags-delete',
            'widgets-create', 'widgets-edit', 'pages-create', 'pages-delete'
        ]));
    }


    /**
     * Test for editing a user.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testFormEditRole()
    {
        /** @var \App\Models\Entrust\Role $role */
        $role = factory(Role::class)->create();
        $role->saveNamedPermissions(['articles-all', 'article-flags-edit']);

        /** @var \App\Models\Entrust\Role $newValues */
        $newValues = factory(Role::class)->make();

        $this->browse(function (Browser $browser) use ($role, $newValues) {
            $browser->login()
                ->resize(1280, 1280)
                ->visit(route('admin.roles.edit', $role->getKey()))
                ->with('.page-title', function (Browser $browser) {
                    $browser->assertSee(trans('admin/roles/general.header_title'))
                        ->assertSee(trans('admin/roles/general.descriptions.edit'));
                })
                // Check fields for default values:
                ->assertInputValue('display_name', $role->display_name)
                ->assertInputValue('description', $role->description)
                ->assertChecked('enabled');

            // Verify default values of permissions.
            $groups = config('permissions.groups', []);
            foreach ($groups as $group) {
                foreach ($group['areas'] as $area) {
                    foreach (array_keys($group['permissions']) as $permission) {
                        if ($area === 'articles' || ($area === 'article-flags' && ($permission === 'edit' || $permission === 'show'))) {
                            $browser->assertChecked("{$area}[{$permission}]");
                        } else {
                            $browser->assertNotChecked("{$area}[{$permission}]");
                        }
                    }
                }
            }

            // Fill new values
            $browser->type('display_name', $newValues->display_name)
                ->type('description', $newValues->description)
                ->click('input[name="enabled"] + .switchery')
                ->uncheck('articles[all]')
                ->check('article-categories[show]')
                // Submit
                ->press(trans('admin/roles/form.btn_update'))
                ->assertUrlIs(route('admin.roles'))
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/roles/general.notifications.updated')
                    );
                });
        });

        $role->refresh();

        $this->assertEquals($newValues->display_name, $role->display_name);
        $this->assertEquals($newValues->description, $role->description);

        // has all these permissions
        $this->assertTrue($role->hasPermission([
            'article-categories-show', 'article-flags-edit'
        ], true));

        // has all these permissions
        $this->assertFalse($role->hasPermission('articles-all'));

        // has neither of these permissions
        $this->assertFalse($role->hasPermission([
            'articles-show', 'articles-create', 'articles-edit', 'articles-delete',
            'article-categories-create', 'article-categories-edit', 'article-categories-delete',
            'article-flags-show', 'article-flags-create', 'article-flags-delete'
        ]));
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
