<?php

use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Photogallery\Photogallery::class, function (Faker $faker) {
    return [
        'title' => $name = $faker->realText(40),
        'text' => $faker->realText(500),
        'url' => $name,
        'seo_title' => $faker->realText(20),
        'seo_index' => true,
        'seo_follow' => true,
        'seo_sitemap' => true,
        'seo_description' => $faker->realText(100),
        'open_graph' => [
            'title' => $faker->realText(30),
            'type' => 'website',
            'url' => $faker->url,
            'description' => $faker->realText(100),
        ],
        'publish_at' => \Carbon\Carbon::now()->minute(0)->second(0),
        'unpublish_at' => null,
        'language_id' => function () {
            return \App\Models\Web\Language::findDefault()->getKey();
        },
        'user_id' =>function () {
            return factory(\App\Models\User::class)->create()->getKey();
        }
    ];
});
