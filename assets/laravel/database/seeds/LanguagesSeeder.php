<?php

use Illuminate\Database\Seeder;

class LanguagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Web\Language::create([
            'name' => 'Čeština',
            'enabled' => 1,
            'country_code' => 'CZ',
            'language_code' => 'cs',
            'default' => 1
        ]);

        \App\Models\Web\Language::create([
            'name' => 'English',
            'enabled' => 0,
            'country_code' => 'US',
            'language_code' => 'en',
            'default' => 0
        ]);

        \App\Models\Web\Language::create([
            'name' => 'Deutsch',
            'enabled' => 0,
            'country_code' => 'DE',
            'language_code' => 'de',
            'default' => 0
        ]);

        \App\Structures\Enums\SingletonEnum::settings()->put(
            'language_display', config('admin.language_url.directory')
        );
        \App\Structures\Enums\SingletonEnum::settings()->put('default_language_hidden', 1);
    }
}
