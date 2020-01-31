<?php

use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Web\Language::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'country_code' => $faker->countryCode,
        'language_code' => $faker->languageCode,
        'enabled' => true,
        'default' => false,
        'domain' => null
    ];
});
