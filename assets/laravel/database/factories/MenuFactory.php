<?php

use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(\App\Models\Menu\Menu::class, function (Faker $faker) {
    return [
        'name' => $faker->realText(20),
        'language_id' => function () {
            return \App\Models\Web\Language::findDefault()->getKey();
        }
    ];
});

$factory->define(\App\Models\Menu\Item::class, function (Faker $faker) {
    return [
        'name' => $faker->realText(25),
        'menu_id' => function (array $item) {
            $attributes = [];

            if (isset($item['language_id'])) {
                $attributes['language_id'] = $item['language_id'];
            }

            return factory(\App\Models\Menu\Menu::class)->create($attributes)->getKey();
        },
        'language_id' => function (array $item) {
            if (isset($item['menu_id'])) {
                $menu = \App\Models\Menu\Menu::find($item['menu_id']);
                if ($menu) {
                    return $menu->language_id;
                }
            }

            return \App\Models\Web\Language::findDefault()->getKey();
        },
        'class' => $faker->word,
        'page_id' => function (array $item) use ($faker) {
            $canSet = !isset($item['category_id']) && !isset($item['url']);
            return $canSet && $faker->boolean ? factory(\App\Models\Page\Page::class)->create()->getKey() : null;
        },
        'category_id' => function (array $item) use ($faker)  {
            $canSet = !isset($item['page_id']) && !isset($item['url']);
            return $canSet && $faker->boolean ? factory(\App\Models\Article\Category::class)->create()->getKey() : null;
        },
        'url' => function (array $item) use ($faker) {
            return !isset($item['page_id']) && !isset($item['category_id']) ? $faker->url : null;
        },
        'open_new_window' => $faker->boolean,
        'parent_id' => null
    ];
});

$factory->state(\App\Models\Menu\Item::class, 'url', function ($faker) {
    return [
        'url' => $faker->url,
        'page_id' => null,
        'category_id' => null
    ];
});
