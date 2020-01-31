<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Support\Str;
use Tests\Browser\Components\JGrowl;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class AccountEditTest extends DuskTestCase
{
    /**
     * Test for editing user's account.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testEditAccount()
    {
        $this->browse(function (Browser $browser) {
            $aboutText = Str::random(1000);
            /** @var \App\Models\User $user */
            $user = factory(User::class)->create();

            $browser->loginAs($user)
                ->visit(route('admin.account.edit', [], false))
                ->assertPathIs(route('admin.account.edit', [], false))
                ->assertSee(trans('admin/account/form.general.title'))
                // Check default values
                ->assertInputValue('firstname', $user->firstname)
                ->assertInputValue('lastname', $user->lastname)
                ->assertInputValue('email', $user->email)
                ->assertInputValue('username', $user->username)
                ->assertInputValue('position', $user->position)
                ->assertInputValue('about', $user->about)
                // Input new values
                ->type('firstname', $firstname = 'Hlavní')
                ->type('lastname', $lastname = 'Administrátor')
                ->type('email', $email = 'admin@simplo.cz')
                ->type('username', $username = 'admin')
                ->type('position', $position = 'Správce webu')
                ->type('about', $aboutText)
                ->attach('image', base_path('tests/assets/images/black.png'))
                ->click('#form-account-edit button[type="submit"]')
                ->assertPathIs(route('admin.account.edit', [], false))
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/account/general.notifications.saved')
                    );
                });

            $user->refresh();

            $this->assertEquals($firstname, $user->firstname);
            $this->assertEquals($lastname, $user->lastname);
            $this->assertEquals($email, $user->email);
            $this->assertEquals($username, $user->username);
            $this->assertEquals($position, $user->position);
            $this->assertEquals($aboutText, $user->about);
            $this->assertTrue($user->hasCustomImage());
        });
    }


    /**
     * Test for changing user's password.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testChangePassword()
    {
        $this->browse(function (Browser $browser) {
            /** @var \App\Models\User $user */
            $user = factory(User::class)->create([
                'password' => \Hash::make($password = 'heslo123')
            ]);

            $browser->loginAs($user)
                ->visit(route('admin.account.edit', [], false))
                ->assertPathIs(route('admin.account.edit', [], false))
                ->assertSee(trans('admin/account/form.password.title'))
                ->type('password', $password)
                ->type('new_password', $newPassword = 'NoveHeslo123')
                ->type('verify_new_password', $newPassword)
                ->click('#form-password-change button[type="submit"]')
                ->assertPathIs(route('admin.account.edit', [], false))
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/account/general.notifications.password_changed')
                    );
                });

            $user->refresh();

            \Hash::check($newPassword, $user->password);
            $this->assertTrue(\Hash::check($newPassword, $user->password));
        });
    }
}
