<?php

use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Widget\Widget::class, function (Faker $faker) {
    return [
        'id' => str_replace('-', '_', $faker->slug(3)),
        'name' => $faker->realText(40),
        'author_user_id' => function () {
            return factory(\App\Models\User::class)->create()->getKey();
        }
    ];
});

$factory->define(App\Models\Widget\Content::class, function (Faker $faker) {
    return [
        'content' => json_encode([
            [
                "type" => "container",
                "content" => [
                    [
                        "type" => "row",
                        "content" => [
                            [
                                "type" => "column",
                                "content" => [],
                                "size" => ["xl" => 12, "lg" => 12, "md" => 12, "sm" => 12, "col" => 12]
                            ]
                        ]
                    ]
                ],
            ]
        ]),
        'widget_id' => function () {
            return factory(App\Models\Widget\Widget::class)->create()->getKey();
        },
        'language_id' => function () {
            return \App\Models\Web\Language::findDefault()->getKey();
        }
    ];
});
