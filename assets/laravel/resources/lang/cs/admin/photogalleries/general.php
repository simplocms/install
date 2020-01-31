<?php

return [

    'header_title' => 'Fotogalerie',

    'descriptions' => [
        'index' => 'přehled',
        'create' => 'tvorba nové fotogalerie',
        'edit' => 'úprava fotogalerie',
    ],

    'notifications' => [
        'created' => 'Fotogalerie byla úspěšně vytvořena.',
        'updated' => 'Fotogalerie byla úspěšně upravena.',
        'deleted' => 'Fotogalerie byla úspěšně smazána.',
    ],

    'index' => [
        'table_columns' => [
            'name' => 'Název fotogalerie',
            'publish_at' => 'Publikováno od',
            'unpublish_at' => 'Odpublikovat',
            'author' => 'Autor',
            'status' => 'Stav',
        ],
        'btn_preview' => 'Náhled',
        'btn_edit' => 'Upravit',
        'btn_delete' => 'Smazat',
        'btn_create' => 'Vytvořit novou fotogalerii'
    ],

    'status' => [
        'published' => 'Publikováno',
        'unpublished' => 'Nepublikováno'
    ],

    'confirm_delete' => [
        'title' => 'Smazat fotogalerii',
        'text' => 'Opravdu si přejete smazat tuto fotogalerii?',
        'confirm' => 'Smazat',
        'cancel' => 'Zrušit'
    ],

    'frontweb_toolbar' => [
        'btn_create' => 'Nová fotogalerie',
        'btn_edit' => 'Upravit fotogalerii',
    ],

];
