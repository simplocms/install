<?php

return [

    'header_title' => 'Moduly',

    'descriptions' => [
        'index' => 'správa modulů',
    ],

    'notifications' => [
        'enabled' => 'Modul ":name" úspěšně povolen.',
        'disabled' => 'Modul ":name" úspěšně zakázán.',
    ],

    'index' => [
        'table_columns' => [
            'name' => 'Název modulu',
            'status' => 'Stav',
            'installation' => 'Instalace',
        ],
        'btn_install' => 'Nainstalovat',
        'btn_uninstall' => 'Odinstalovat',
    ],

    'status' => [
        'enabled' => 'Povolen',
        'disabled' => 'Zakázán',
    ],

    'confirm_uninstall' => [
        'title' => 'Odinstalace modulu',
        'text' => 'Opravdu si přejete tento modul odinstalovat? Budou zároveň nenávratně odstraněna všechna jeho data a použití.',
        'confirm' => 'Ano, odinstalovat!',
        'cancel' => 'Zrušit'
    ],

];
