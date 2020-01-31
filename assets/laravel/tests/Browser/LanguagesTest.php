<?php

namespace Tests\Browser;

use App\Models\Web\Language;
use App\Services\Settings\Settings;
use Tests\Browser\Components\JGrowl;
use Tests\Browser\Components\SweetAlertModal;
use Tests\Browser\Components\TableActions;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class LanguagesTest extends DuskTestCase
{
    /**
     * Selector for table row.
     *
     * @var string
     */
    private $tableRowSelector = '.content table > tbody > tr';

    /** @var \App\Services\Settings\Settings */
    private $settings;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $this->settings = new Settings;

        Browser::$userResolver = function () {
            return $this->createUserWithPermissions(['languages-create', 'languages-edit', 'languages-delete']);
        };
    }


    /**
     * Test for index page of languages.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testIndexPage()
    {
        // Full permissions
        $this->browse(function (Browser $browser) {
            $czechLanguage = Language::findByUrlCode('cs');

            $browser->login()
                ->visit(route('admin.languages.index'))
                ->assertUrlIs(route('admin.languages.index'))
                ->assertSee(trans('admin/languages/general.header_title'))
                ->assertVisible('.content table.table')
                ->assertVisible('#language-settings-form')
                ->assertSee(trans('admin/languages/general.index.btn_create'))
                ->assertSeeIn('#language-settings-form', trans('admin/languages/general.settings.btn_save'))
                ->within($this->tableRow(1), function (Browser $browser) use ($czechLanguage) {
                    $browser->assertSeeIn(
                        '> td:nth-child(1) > a > span',
                        mb_strtoupper(trans('admin/languages/general.status.enabled'))
                    )
                        ->assertSeeIn('> td:nth-child(3)', $czechLanguage->name)
                        ->assertSeeIn('> td:nth-child(4)', $czechLanguage->language_code)
                        ->assertSeeIn(
                            '> td:nth-child(5)',
                            mb_strtoupper(trans('admin/languages/general.status.default'))
                        )
                        ->within(new TableActions, function (Browser $browser) {
                            $browser->click('@toggle')
                                ->assertSeeIn(
                                    '@item:nth-child(1)',
                                    trans('admin/languages/general.index.btn_edit')
                                )
                                ->assertSeeIn(
                                    '@item:nth-child(2)',
                                    trans('admin/languages/general.index.btn_delete')
                                )
                                ->assertSeeIn(
                                    '@item:nth-child(3)',
                                    trans('admin/languages/general.index.btn_set_default')
                                );
                        });
                });
        });

        // No permissions
        $this->browse(function (Browser $browser) {
            /** @var \App\Models\User $user */
            $user = factory(\App\Models\User::class)->create();

            $browser->loginAs($user)
                ->visit(route('admin.languages.index'))
                ->assertUrlIs(route('admin.languages.index'))
                ->assertSee('403');
        });

        // Permissions to create languages
        $this->browse(function (Browser $browser) {
            /** @var \App\Models\User $user */
            $user = $this->createUserWithPermissions(['languages-create']);

            $browser->loginAs($user)
                ->visit(route('admin.languages.index'))
                ->assertSee(trans('admin/languages/general.header_title'))
                ->assertSee(trans('admin/languages/general.index.btn_create'))
                ->assertDontSeeIn('#language-settings-form', trans('admin/languages/general.settings.btn_save'))
                ->within($this->tableRow(1), function (Browser $browser) {
                    $browser->assertMissing('> td:nth-child(1) > a')
                        ->assertMissing('> td:nth-child(6)');
                });
        });
    }


    /**
     * Test for enabling and disabling language.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testEnableAndDisableLanguage()
    {
        $englishLanguage = (new Language)->where('language_code', 'en')->first();

        // Test table containing english language.
        $this->browse(function (Browser $browser) use ($englishLanguage) {
            $browser->login()
                ->visit(route('admin.languages.index'))
                ->within($this->tableRow(2), function (Browser $browser) use ($englishLanguage) {
                    $browser->assertSeeIn(
                        '> td:nth-child(1)',
                        mb_strtoupper(trans('admin/languages/general.status.disabled'))
                    )
                        ->assertSeeIn('> td:nth-child(3)', $englishLanguage->name)
                        ->assertSeeIn('> td:nth-child(4)', $englishLanguage->language_code);
                });
        });

        // Test enabling language.
        $this->browse(function (Browser $browser) use ($englishLanguage) {
            $browser->login()
                ->visit(route('admin.languages.index'))
                ->click($this->tableRow(2) . ' > td:nth-child(1) > a')
                ->waitForReload()
                ->assertSeeIn(
                    $this->tableRow(2) . ' > td:nth-child(1)',
                    mb_strtoupper(trans('admin/languages/general.status.enabled'))
                )
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) use ($englishLanguage) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/languages/general.notifications.enabled')
                    );
                });

            $englishLanguage->refresh();
            $this->assertTrue($englishLanguage->enabled);
        });

        // Test disabling language.
        $this->browse(function (Browser $browser) use ($englishLanguage) {
            $browser->login()
                ->visit(route('admin.languages.index'))
                ->click($this->tableRow(2) . ' > td:nth-child(1) > a')
                ->waitForReload()
                ->assertSeeIn(
                    $this->tableRow(2) . ' > td:nth-child(1)',
                    mb_strtoupper(trans('admin/languages/general.status.disabled'))
                )
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) use ($englishLanguage) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/languages/general.notifications.disabled')
                    );
                });

            $englishLanguage->refresh();
            $this->assertFalse($englishLanguage->enabled);
        });
    }


    /**
     * Test for changing default language.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testChangeDefaultLanguage()
    {
        $czechLanguage = Language::findByUrlCode('cs');
        $englishLanguage = (new Language)->where('language_code', 'en')->first();

        // Initialize values
        $czechLanguage->update(['enabled' => true, 'default' => true]);
        $englishLanguage->update(['enabled' => true, 'default' => false]);

        // Test set default language to english.
        $this->browse(function (Browser $browser) use ($englishLanguage) {
            $browser->login()
                ->visit(route('admin.languages.index'))
                ->within($this->tableRow(2), function (Browser $browser) use ($englishLanguage) {
                    $browser
                        ->within(new TableActions, function (Browser $browser) use ($englishLanguage) {
                            $browser->click('@toggle')
                                ->click('@item:nth-child(3)');
                        })
                        ->waitForReload()
                        ->assertSeeIn(
                            '> td:nth-child(5)',
                            mb_strtoupper(trans('admin/languages/general.status.default'))
                        );
                })
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) use ($englishLanguage) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/languages/general.notifications.default', [
                            'name' => $englishLanguage->name
                        ])
                    );
                });
        });

        $czechLanguage->refresh();
        $englishLanguage->refresh();
        $this->assertTrue($czechLanguage->enabled);
        $this->assertFalse($czechLanguage->default);
        $this->assertTrue($englishLanguage->enabled);
        $this->assertTrue($englishLanguage->default);

        // Test disabling default language (english).
        $this->browse(function (Browser $browser) use ($englishLanguage) {
            $browser->login()
                ->visit(route('admin.languages.index'))
                ->click($this->tableRow(2) . ' > td:nth-child(1) > a')
                ->waitForReload()
                ->within($this->tableRow(1), function (Browser $browser) {
                    $browser->assertSeeIn(
                        '> td:nth-child(1)',
                        mb_strtoupper(trans('admin/languages/general.status.enabled'))
                    );
                    $browser->assertSeeIn(
                        '> td:nth-child(5)',
                        mb_strtoupper(trans('admin/languages/general.status.default'))
                    );
                })
                ->within($this->tableRow(2), function (Browser $browser) {
                    $browser->assertSeeIn(
                        '> td:nth-child(1)',
                        mb_strtoupper(trans('admin/languages/general.status.disabled'))
                    );
                    $browser->assertDontSeeIn(
                        '> td:nth-child(5)',
                        mb_strtoupper(trans('admin/languages/general.status.default'))
                    );
                })
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) use ($englishLanguage) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/languages/general.notifications.disabled')
                    );
                });
        });

        $czechLanguage->refresh();
        $englishLanguage->refresh();
        $this->assertTrue($czechLanguage->enabled);
        $this->assertTrue($czechLanguage->default);
        $this->assertFalse($englishLanguage->enabled);
        $this->assertFalse($englishLanguage->default);
    }


    /**
     * Test for edit and delete action in table row.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testTableEditDeleteActions()
    {
        // Test action to edit language.
        $this->browse(function (Browser $browser) {
            $browser->login()
                ->visit(route('admin.languages.index'))
                ->within($this->tableRow(1), function (Browser $browser) {
                    $czechLanguage = Language::findByUrlCode('cs');

                    $browser
                        ->within(new TableActions, function (Browser $browser) {
                            $browser->click('@toggle')
                                ->click('@item:nth-child(1)');
                        })
                        ->assertUrlIs(route('admin.languages.edit', $czechLanguage->id));
                });
        });

        /** @var \App\Models\Web\Language $language */
        $language = factory(Language::class)->create();

        // Test action to edit language.
        $this->browse(function (Browser $browser) {
            $browser->login()
                ->visit(route('admin.languages.index'))
                ->within($this->tableRow(3), function (Browser $browser) {
                    $browser->within(new TableActions, function (Browser $browser) {
                        $browser->click('@toggle')
                            ->click('@item:nth-child(2)');
                    });
                })
                ->waitFor((new SweetAlertModal)->selector())
                ->within(new SweetAlertModal, function (Browser $browser) {
                    $browser->assertIsDelete(trans('admin/languages/general.confirm_delete.title'))->confirm();
                })
                ->waitForReload()
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/languages/general.notifications.deleted')
                    );
                });
        });

        $this->assertNull(Language::find($language->id));
    }


    /**
     * Test for creating language.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testCreateLanguage()
    {
        $this->browse(function (Browser $browser) {
            $browser->login()
                ->visit(route('admin.languages.create'))
                ->assertSee(trans('admin/languages/general.descriptions.create'))
                ->assertChecked('enabled')
                // Fill new values
                ->type('name', $name = 'Italšťina')
                ->type('country_code', $countryCode = 'IT')
                ->type('language_code', $languageCode = 'it')
                // Submit
                ->click('#submit-form-button')
                ->assertUrlIs(route('admin.languages.index'))
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/languages/general.notifications.created')
                    );
                });

            $language = Language::findByUrlCode($languageCode);
            $this->assertNotNull($language);
            $this->assertEquals($name, $language->name);
            $this->assertEquals($countryCode, $language->country_code);
            $this->assertTrue($language->enabled);

            $language->forceDelete();
        });
    }


    /**
     * Test for editing language.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testEditLanguage()
    {
        $this->browse(function (Browser $browser) {
            /** @var \App\Models\Web\Language $language */
            $language = factory(Language::class)->create([
                'name' => 'Rušťina',
                'country_code' => 'RU',
                'language_code' => 'ru',
                'enabled' => false
            ]);

            $browser->login()
                ->visit(route('admin.languages.edit', $language->id))
                ->assertSee(trans('admin/languages/general.descriptions.edit'))
                // Check default values
                ->assertInputValue('name', $language->name)
                ->assertInputValue('country_code', $language->country_code)
                ->assertInputValue('language_code', $language->language_code)
                ->assertNotChecked('enabled')
                // Fill new values
                ->type('name', $name = 'Italšťina')
                ->type('country_code', $countryCode = 'IT')
                ->type('language_code', $languageCode = 'it')
                ->click('input[name="enabled"] + .switchery')
                ->assertChecked('enabled')
                // Submit
                ->click('#submit-form-button')
                ->assertUrlIs(route('admin.languages.index'))
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/languages/general.notifications.updated')
                    );
                });

            $language->refresh();

            $this->assertEquals($name, $language->name);
            $this->assertEquals($countryCode, $language->country_code);
            $this->assertEquals($languageCode, $language->language_code);
            $this->assertTrue($language->enabled);

            $language->forceDelete();
        });
    }


    /**
     * Test for editing language.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testEditSettings()
    {
        $this->browse(function (Browser $browser) {
            $this->settings->put('language_display', config('admin.language_url.directory'));
            $this->settings->put('default_language_hidden', false);

            $browser->login()
                ->visit(route('admin.languages.index'))
                // Check default values.
                ->assertRadioSelected('language_display', config('admin.language_url.directory'))
                ->assertVisible('#default-language-input')
                ->assertNotChecked('default_language_hidden')
                // Change settings.
                ->check('default_language_hidden')
                // Submit
                ->click('#btn-submit-settings')
                ->waitForReload()
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/languages/general.notifications.settings_updated')
                    );
                })
                // Check form again.
                ->assertRadioSelected('language_display', config('admin.language_url.directory'))
                ->assertVisible('#default-language-input')
                ->assertChecked('default_language_hidden');

            // There is need to clear the cache, because they are overwritten
            // by another process, while this is still running.
            \Cache::clear();

            $this->assertEquals(
                config('admin.language_url.directory'), $this->settings->getInt('language_display')
            );
            $this->assertTrue($this->settings->getBoolean('default_language_hidden'));
        });

        $this->browse(function (Browser $browser) {
            $this->settings->put('language_display', config('admin.language_url.directory'));
            $this->settings->put('default_language_hidden', false);

            $browser->login()
                ->visit(route('admin.languages.index'))
                // Check default values.
                ->assertRadioSelected('language_display', config('admin.language_url.directory'))
                ->assertVisible('#default-language-input')
                ->assertNotChecked('default_language_hidden')
                // Change settings.
                ->radio('language_display', config('admin.language_url.subdomain'))
                ->assertMissing('#default-language-input')
                // Submit
                ->click('#btn-submit-settings')
                ->waitForReload()
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/languages/general.notifications.settings_updated')
                    );
                })
                // Check form again.
                ->assertRadioSelected('language_display', config('admin.language_url.subdomain'))
                ->assertMissing('#default-language-input');

            // There is need to clear the cache, because they are overwritten
            // by another process, while this is still running.
            \Cache::clear();

            $this->assertEquals(
                config('admin.language_url.subdomain'), $this->settings->getInt('language_display')
            );
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
