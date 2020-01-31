<?php

use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(\App\Models\Article\Category::class, function (Faker $faker) {
    return [
        'name' => $name = $faker->realText(20),
        'show' => true,
        'parent_id' => null,
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
        'flag_id' => function () {
            return factory(\App\Models\Article\Flag::class)->create()->getKey();
        },
        'language_id' => function (array $category) {
            $flag = \App\Models\Article\Flag::find($category['flag_id']);
            return $flag->language_id;
        },
        'user_id' =>function () {
            return factory(\App\Models\User::class)->create()->getKey();
        }
    ];
});
