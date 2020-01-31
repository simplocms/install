<?php

use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\User::class, function (Faker $faker) {
    return [
        'firstname' => $faker->firstName,
        'lastname' => $faker->lastName,
        'username' => $faker->userName,
        'email' => $faker->email,
        'password' => Hash::make($faker->password),
        'enabled' => true,
        'position' => $faker->jobTitle,
        'about' => $faker->text(200),
        'protected' => false
    ];
});
