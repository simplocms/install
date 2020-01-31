<?php

return [

    'header_title' => 'Stránky',

    'descriptions' => [
        'index' => 'přehled',
        'create' => 'tvorba nové stránky',
        'edit' => 'úprava stránky',
    ],

    'notifications' => [
        'created' => 'Stránka byla úspěšně vytvořena.',
        'updated' => 'Stránka byla úspěšně upravena.',
        'deleted' => 'Stránka byla úspěšně smazána.',
        'duplicated' => 'Stránka byla úspěšně zduplikována.',
        'ab_test_created' => 'A/B test úspěšně vytvořen.',
        'ab_test_stopped' => 'A/B test úspěšně ukončen.',
        'ab_test_already_created' => 'A/B test byl pro tuto stránku již vytvořen.',
        'ab_test_not_available' => 'A/B test není k dispozici pro tuto stránku.',
    ],

    'index' => [
        'table_columns' => [
            'name' => 'Název stránky',
            'publish_at' => 'Publikováno od',
            'unpublish_at' => 'Odpublikováno',
            'status' => 'Stav',
        ],
        'btn_preview' => 'Náhled',
        'btn_edit' => 'Upravit',
        'btn_duplicate' => 'Duplikovat',
        'btn_ab_test_start' => 'Vytvořit A/B test',
        'btn_ab_test_stop' => 'Ukončit A/B test',
        'btn_delete' => 'Smazat',
        'btn_create' => 'Vytvořit novou stránku',
        'ab_testing_stop' => [
            'text' => 'Jakou testovací variantu si přejete ponechat?',
            'keep_a' => 'Variantu A',
            'keep_b' => 'Variantu B',
            'keep_both' => 'Obě varianty',
        ],
    ],

    'status' => [
        'published' => 'Publikováno',
        'unpublished' => 'Nepublikováno'
    ],

    'confirm_delete' => [
        'title' => 'Smazat stránku',
        'text' => 'Opravdu si přejete smazat tuto stránku a všechny její podřazené stránky?',
        'confirm' => 'Smazat',
        'cancel' => 'Zrušit'
    ],

    'frontweb_toolbar' => [
        'btn_create' => 'Nová stránka',
        'btn_edit' => 'Upravit stránku',
    ],

    'page_duplicate_name_suffix' => 'Kopie',

];
