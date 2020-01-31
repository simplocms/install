<?php

use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Page\Page::class, function (Faker $faker) {
    return [
        'name' => $name = $faker->text(40),
        'published' => true,
        'parent_id' => null,
        'url' => $name,
        'view' => null,
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
        'publish_at' => \Carbon\Carbon::now()->minute(0)->second(0),
        'unpublish_at' => null,
        'is_homepage' => false,
        'image_id' => null,
        'language_id' => function () {
            return \App\Models\Web\Language::findDefault()->getKey();
        }
    ];
});

$factory->define(App\Models\Page\Content::class, function (Faker $faker) {
    return [
        'content' => null,
        'is_active' => true,
        'page_id' => function () {
            return factory(App\Models\Page\Page::class)->create()->getKey();
        },
        'author_user_id' => function () {
            return factory(\App\Models\User::class)->create()->getKey();
        },
    ];
});
