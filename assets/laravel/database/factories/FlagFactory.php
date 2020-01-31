<?php

use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(\App\Models\Article\Flag::class, function (Faker $faker) {
    return [
        'name' => $name = $faker->word,
        'url' => $name,
        'use_tags' => false,
        'use_grid_editor' => false,
        'seo_title' => $faker->realText(20),
        'seo_description' => $faker->realText(100),
        'seo_index' => true,
        'seo_follow' => true,
        'seo_sitemap' => true,
        'open_graph' => [
            'title' => $faker->realText(30),
            'type' => 'website',
            'url' => $faker->url,
            'description' => $faker->realText(100),
        ],
        'language_id' => function () {
            return \App\Models\Web\Language::findDefault()->getKey();
        }
    ];
});
