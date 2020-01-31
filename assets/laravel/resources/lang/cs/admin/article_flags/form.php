<?php

return [

    'tabs' => [
        'details' => 'Základní informace',
        'seo' => 'SEO',
        'og' => 'OpenGraph',
    ],

    // this structure is required for javascript form
    'seo_tab' => [
        'inputs' => [
            'title' => [
                'info' => 'je titulek stránky v prohlížeči. Pokud pole nevyplníte, automaticky se použije název.'
            ]
        ]
    ],

    'labels' => [
        'name' => 'Název',
        'url' => 'URL',
        'description' => 'Popis',
        'use_tags' => 'Používat tagy',
        'use_grid_editor' => 'Používat Grid Editor',
        'should_bound_articles_to_category' => 'Zobrazit kategorii v url a drobečkové navigaci článků',
    ],

    'btn_update' => 'Uložit změny',
    'btn_create' => 'Vytvořit',
    'btn_cancel' => 'Zrušit',

    'messages' => [
        'name.required' => 'Zadejte prosím název.',
        'name.max' => 'Maximální délka názvu je :max znaků.',
        'url.required' => 'Zadejte prosím URL.',
        'url.max' => 'Maximální délka URL je :max znaků.',
        'description.max' => 'Maximální délka popisku je :max znaků.',
    ],

];
