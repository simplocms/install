<?php

return [
    'header_title' => 'Přesměrování',

    'descriptions' => [
        'index' => 'přehled',
        'create' => 'vytvořit nové přesměrování',
        'bulk_create' => 'hromadné vytvoření přesměrování',
        'edit' => 'upravit přesměrování',
    ],

    'notifications' => [
        'created' => 'Přesměrování úspěšně vytvořeno.',
        'updated' => 'Přesměrování úspěšně upraveno.',
        'deleted' => 'Přesměrování úspěšně smazáno.',
        'bulk_created' => 'Všechna přesměrování úspěšně vytvořena.',
    ],

    'index' => [
        'table_columns' => [
            'source' => 'Zdrojová URL',
            'target' => 'Cílová URL',
            'code' => 'Stavový kód',
            'author' => 'Vytvořil',
        ],
        'btn_edit' => 'Upravit',
        'btn_delete' => 'Smazat',
        'btn_create' => 'Vytvořit nové přesměrování',
        'btn_export' => 'Export',
        'btn_bulk_create' => 'Hromadné vytvoření',

        'author_system' => 'Systém'
    ],

    'confirm_delete' => [
        'title' => 'Smazat přesměrování',
        'text' => 'Opravdu si přejete smazat toto přesměrování?',
        'confirm' => 'Smazat',
        'cancel' => 'Zrušit'
    ],
];
