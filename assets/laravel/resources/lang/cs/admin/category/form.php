<?php

return [

    'tabs' => [
        'info' => 'Základní informace',
        'seo' => 'SEO',
        'og' => 'OpenGraph',
    ],

    // this structure is required for javascript form
    'seo_tab' => [
        'inputs' => [
            'title' => [
                'info' => 'je titulek stránky v prohlížeči. Pokud pole nevyplníte, automaticky se použije název kategorie.'
            ],
        ]
    ],

    'labels' => [
        'name' => 'Název',
        'url' => 'URL',
        'description' => 'Popis',
        'parent_id' => 'Nadřazená kategorie',
        'show' => 'Zobrazení na webových stránkách',
    ],

    'default_parent_category' => 'Žádná',

    'btn_update' => 'Upravit',
    'btn_create' => 'Vytvořit',
    'btn_cancel' => 'Zrušit',

    'messages' => [
        'name.required' => 'Zadejte prosím název kategorie.',
        'name.max' => 'Maximální délka názvu je :max znaků.',
        'url.required' => 'Zadejte prosím URL kategorie.',
        'url.max' => 'Maximální délka URL je :max znaků.',
        'parent_id.exists' => 'Vybraná kategorie neexistuje.',
        'description.max' => 'Maximální délka popisku je :max znaků.',
    ],

];
