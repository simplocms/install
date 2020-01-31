<?php

return [

    'grideditor_title' => 'Seznam článků',

    'grid_editor_form' => [
        'no_views' => 'Pro tento modul nejsou k dispozici žádné šablony!',
        'labels' => [
            'view' => 'Šablona',
            'category_ids' => 'Vypsat kategorie',
            'tag_ids' => 'Vypsat tagy',
            'sort_type' => 'Řadit dle',
            'limit' => 'Limit (pro zobrazení všech zadejte 0)',
        ],

        'all_categories' => 'Všechny kategorie',
        'all_tags' => 'Všechny tagy',

        'messages' => [
            'view.required' => 'Zvolte prosím šablonu, pomocí které bude list vypsán.',
            'view.in' => 'Neplatná šablona. Prosím, vyberte jinou.',
            'category_ids.array' => 'ERR: Vstup musí být pole.',
            'category_ids_exists' => 'Některá z vybraných kategorií již neexistuje.',
            'flag_ids.array' => 'ERR: Vstup musí být pole.',
            'flag_ids_exists' => 'Některý z vybraných tagů již neexistuje.',
            'sort_type.required' => 'Vyberte prosím způsob řazení.',
            'sort_type.in' => 'Neplatný způsob řazení. Vyberte prosím jiný.',
            'limit.int' => 'ERR: Musí být číslo',
            'limit.min' => 'Limit musí být větší než 0. V případě, že limit nechcete, zadejte 0.',
        ]
    ],

    'view_not_exist' => 'Šablona ":name" neexistuje!',

    'preview' => [
        'not_found' => 'Šablona nenalezena!',
        'limit' => '{0} všechny články|{1} :count článek|[2,4] :count články|[5,*] :count článků',
        'labels' => [
            'view' => 'Šablona',
            'category_ids' => 'Z kategorií',
            'tag_ids' => 'S tagy',
            'sort_type' => 'Řadit dle',
            'limit' => 'Vypsat',
        ],
    ],

    'sort_types' => [
        'title' => 'Titulku',
        'publish_date' => 'Datumu zveřejnení (nejnovější)',
        'random' => 'Náhodně',
    ]

];
