<?php

return [

    'header_title' => 'Role',

    'descriptions' => [
        'index' => 'přehled',
        'create' => 'tvorba nové role',
        'edit' => 'úprava role',
    ],

    'notifications' => [
        'created' => 'Role úspěšně vytvořena.',
        'updated' => 'Role úspěšně upravena.',
        'deleted' => 'Role úspěšně smazána.',
        'protected_role' => 'Tuto roli nelze modifikovat.',
        'enabled' => 'Role úspěšně aktivována.',
        'disabled' => 'Role úspěšně deaktivována.',
    ],

    'index' => [
        'table_columns' => [
            'name' => 'Název role',
            'description' => 'Popis',
            'toggle' => 'Aktivace/Deaktivace',
        ],
        'btn_edit' => 'Upravit',
        'btn_delete' => 'Smazat',
        'btn_create' => 'Vytvořit novou roli',
        'title_enable' => 'Aktivovat',
        'title_disable' => 'Deaktivovat',
    ],

    'confirm_delete' => [
        'title' => 'Smazat roli',
        'text' => 'Opravdu si přejete smazat tuto roli?',
        'confirm' => 'Smazat',
        'cancel' => 'Zrušit'
    ],

];
