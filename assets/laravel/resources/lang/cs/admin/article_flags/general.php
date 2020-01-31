<?php

return [

    'header_title' => 'Druhy článků',

    'descriptions' => [
        'index' => 'přehled',
        'create' => 'tvorba nového druhu článků',
        'edit' => 'úprava druhu článků',
    ],

    'notifications' => [
        'created' => 'Druh článků úspěšně vytvořen.',
        'updated' => 'Druh článků úspěšně upraven.',
        'deleted' => 'Druh článků úspěšně smazán.',
    ],

    'index' => [
        'table_columns' => [
            'name' => 'Název druhu článků',
            'url' => 'URL',
            'author' => 'Autor',
        ],
        'btn_edit' => 'Upravit',
        'btn_delete' => 'Smazat',
        'btn_create' => 'Vytvořit nový druh článků',
        'default_author' => 'Systém'
    ],

    'confirm_delete' => [
        'title' => 'Smazat druh článků',
        'text' => 'Opravdu si přejete smazat druh článků spolu s jeho články a kategoriemi?',
        'confirm' => 'Smazat',
        'cancel' => 'Zrušit'
    ],

];
