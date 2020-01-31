<?php

return [

    'tabs' => [
        'general' => 'Základní informace',
        'grid' => 'Grid',
        'seo' => 'SEO',
        'state' => 'Stav',
        'og' => 'OpenGraph',
        'category' => 'Kategorie',
        'photogallery' => 'Fotogalerie'
    ],

    'category_tab' => [
        'no_categories' => 'Nebyly vytvořeny žádné kategorie.',
    ],

    // this structure is required for javascript form
    'seo_tab' => [
        'inputs' => [
            'title' => [
                'info' => 'je titulek stránky v prohlížeči. Pokud pole nevyplníte, automaticky se použije nadpis článku.'
            ],
            'description' => [
                'info' => 'je text, který je zobrazen u popisku článku ve výsledku vyhledávání. Pokud pole nevyplníte, automaticky bude použito prvních 320 znaků perexu.'
            ]
        ]
    ],

    'labels' => [
        'title' => 'Titulek',
        'url' => 'URL',
        'tags' => 'Tagy',
        'perex' => 'Perex',
        'user_id' => 'Autor',
        'text' => 'Text',
        'image_id' => 'Obrázek',
        'video_id' => 'Video',
    ],

    'btn_update' => 'Upravit',
    'btn_create' => 'Vytvořit',
    'btn_cancel' => 'Zrušit',
    'btn_preview' => 'Náhled',

    'messages' => [
        'title.required' => 'Zadejte prosím titulek.',
        'title.max' => 'Maximální délka titulku je :max znaků.',
        'url.required' => 'Zadejte prosím URL.',
        'url.max' => 'Maximální délka URL je :max znaků.',
        'perex.required' => 'Zadej te prosím perex.',
        'image_id.required' => 'Vyberte prosím obrázek článku.',
        'categories.required' => 'Vyberte prosím alespoň jednu kategorii.',
        'categories.*.exists' => 'Vybraná kategorie neexistuje.',
        'user_id.required' => 'Vyberte prosím autora.',
        'user_id.exists' => 'Vybraný uživatel neexistuje.',
    ],

];
