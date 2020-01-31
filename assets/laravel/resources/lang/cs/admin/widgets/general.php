<?php

return [

    'header_title' => 'Widgety',

    'descriptions' => [
        'index' => 'přehled',
        'create' => 'tvorba nového widgetu',
        'edit' => 'úprava widgetu',
    ],

    'notifications' => [
        'created' => 'Widget byl úspěšně vytvořen.',
        'updated' => 'Widget byl úspěšně uložen.',
        'deleted' => 'Widget byl úspěšně smazán.',
    ],

    'index' => [
        'table_columns' => [
            'name' => 'Název widgetu',
            'id' => 'Identifikátor',
        ],
        'btn_edit' => 'Upravit',
        'btn_delete' => 'Smazat',
        'btn_create' => 'Vytvořit nový widget'
    ],

    'confirm_delete' => [
        'title' => 'Smazat widget',
        'text' => 'Opravdu si přejete smazat tento widget?',
        'confirm' => 'Smazat',
        'cancel' => 'Zrušit'
    ],

    'no_language_fallback' => [
        'known_language' => 'Widget :id nemá pro jazyk ":language" žádný obsah!',
        'unknown_language' => 'Widget ":id" nemá pro tento jazyk žádný obsah!'
    ],

    'no_widget_fallback' => 'Widget s identifikátorem ":id" neexistuje!',

];
