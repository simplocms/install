<?php

return [

    'descriptions' => [
        'index' => 'přehled kategorií',
        'create' => 'vytvořit kategorii',
        'edit' => 'upravit kategorii',
    ],

    'notifications' => [
        'created' => 'Kategorie byla úspěšně vytvořena!',
        'updated' => 'Kategorie byla úspěšně uložena!',
        'deleted' => 'Kategorie byla úspěšně smazána!',
    ],

    'index' => [
        'table_columns' => [
            'name' => 'Název kategorie',
            'status' => 'Stav',
            'created' => 'Vytvořeno',
            'author' => 'Autor',
            'action' => 'Akce',
        ],
        'btn_preview' => 'Náhled',
        'btn_edit' => 'Upravit',
        'btn_delete' => 'Smazat',
        'btn_create' => 'Vytvořit novou kategorii'
    ],

    'status' => [
        'published' => 'Publikována',
        'unpublished' => 'Nepublikována'
    ],

    'confirm_delete' => [
        'title' => 'Smazat kategorii',
        'text' => 'Opravdu si přejete smaat kategorii včetně všech podřazených kategorií?',
        'confirm' => 'Smazat',
        'cancel' => 'Zrušit'
    ],

    'frontweb_toolbar' => [
        'btn_create' => 'Nová kategorie',
        'btn_edit' => 'Upravit kategorii',
    ],

];
