<?php

use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Entrust\Role::class, function (Faker $faker) {
    return [
        'display_name' => $name = $faker->jobTitle,
        'name' => \App\Models\Entrust\Role::createFriendlyName($name),
        'description' => $faker->realText(40),
        'enabled' => true
    ];
});