<?php

use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Article\Article::class, function (Faker $faker) {
    return [
        'title' => $name = $faker->realText(40),
        'perex' => $faker->realText(150),
        'text' => $faker->realText(500),
        'url' => $name,
        'seo_title' => $faker->realText(20),
        'seo_index' => true,
        'seo_follow' => true,
        'seo_sitemap' => true,
        'seo_description' => $faker->realText(100),
        'open_graph' => [
            'title' => $faker->realText(30),
            'type' => 'article',
            'url' => $faker->url,
            'description' => $faker->realText(100),
        ],
        'publish_at' => \Carbon\Carbon::now()->minute(0)->second(0),
        'unpublish_at' => null,
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

$factory->define(App\Models\Article\Content::class, function (Faker $faker) {
    return [
        'content' => null,
        'is_active' => true,
        'article_id' => function () {
            return factory(App\Models\Article\Article::class)->create()->getKey();
        },
        'author_user_id' => function () {
            return factory(\App\Models\User::class)->create()->getKey();
        },
    ];
});
