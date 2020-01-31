<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Web\Language;
use Tests\Browser\Components\LanguageSwitch;
use Tests\Browser\Components\NavBar;
use Tests\Browser\Components\UserNavBarControl;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class AdminUITest extends DuskTestCase
{
    /**
     * Test for navigation bar on the top of the page.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testNavBar()
    {
        $this->browse(function (Browser $browser) {
            /** @var \App\Models\User $user */
            $user = factory(User::class)->create();
            $languageId = \Session::get('language', null);

            if ($languageId) {
                $language = Language::enabled()->where('id', $languageId)->first();
            } else {
                $language = Language::findDefault();
            }

            $browser->loginAs($user)
                ->visit('/admin')
                ->assertPathIs('/admin')
                ->within(new NavBar, function (Browser $browser) {
                    $browser
                        ->assertVisible('@brand')
                        ->assertVisible('@menu-toggle')
                        ->assertVisible('@public-link')
                        ->assertVisible('@language-switch')
                        ->assertVisible('@user-control');
                })
                ->within(new LanguageSwitch, function (Browser $browser) use ($language) {
                    $browser
                        ->assertVisible('@flag')
                        ->assertVisible('@toggle')
                        ->assertVisible('@name')
                        ->assertSeeIn('@name', $language->name)
                        ->click('@toggle')
                        ->assertVisible('@dropdown')
                        ->assertVisible('@language');

                    foreach (Language::enabled()->get() as $enabledLanguage) {
                        $browser->assertSeeIn('@language', $enabledLanguage->name);
                    }
                })
                ->within(new UserNavBarControl, function (Browser $browser) use ($user) {
                    $browser
                        ->assertVisible('@image')
                        ->assertVisible('@toggle')
                        ->assertVisible('@username')
                        ->assertSeeIn('@username', $user->username)
                        ->click('@toggle')
                        ->assertVisible('@dropdown')
                        ->assertVisible('@settings')
                        ->assertVisible('@logout')
                        ->assertVisible('@name')
                        ->assertSeeIn('@name', $user->name);
                });
        });
    }
}
