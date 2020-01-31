<?php

namespace Tests\Browser;

use App\Models\User;
use Tests\Browser\Components\UserNavBarControl;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class AuthTest extends DuskTestCase
{
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        Browser::$userResolver = function () {
            return factory(User::class)->create([
                'password' => \Hash::make('Password1')
            ]);
        };
    }


    /**
     * Test for log in.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testLogIn()
    {
        $this->browse(function (Browser $browser) {
            /** @var \App\Models\User $user */
            $user = call_user_func(Browser::$userResolver);

            $browser->visit('/admin')
                ->assertPathIs('/admin/login')
                ->assertSee(trans('auth.login_form.title'))
                ->type('input[type="text"]', $user->username)
                ->type('input[type="password"]', 'Password1')
                ->click('button[type="submit"]')
                ->assertPathIs('/admin')
                ->assertSee(trans('admin/dashboard.titles.index'))
                ->within(new UserNavBarControl, function (Browser $browser) use ($user) {
                    $browser->assertSeeIn('@username', $user->username);
                });
        });
    }


    /**
     * Test for logout.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testLogOut()
    {
        $this->browse(function (Browser $browser) {
            // First logout button in navbar.
            $browser->login()
                ->visit('/admin')
                ->assertPathIs('/admin')
                ->assertSee(trans('admin/dashboard.titles.index'))
                ->within(new UserNavBarControl, function (Browser $browser) {
                    $browser->click('@toggle')
                        ->assertVisible('@dropdown')
                        ->click('@logout')
                        ->waitForLocation('/admin/login');
                });

            // Second logout button in sidebar menu.
            $browser->login()
                ->visit('/admin')
                ->assertPathIs('/admin')
                ->assertSee(trans('admin/dashboard.titles.index'))
                ->click('.sidebar-content > .sidebar-user > .category-content .icons-list > li > a')
                ->waitForLocation('/admin/login');
        });
    }
}
