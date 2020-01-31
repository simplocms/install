<?php

return [

    'header_title' => 'Jazyky',

    'descriptions' => [
        'index' => 'přehled',
        'create' => 'přidání jazyka',
        'edit' => 'úprava jazyka',
    ],

    'notifications' => [
        'created' => 'Jazyk úspěšně vytvořen.',
        'updated' => 'Jazyk úspěšně upraven.',
        'deleted' => 'Jazyk úspěšně smazán.',
        'protected_default' => 'Výchozí jazyk nelze smazat.',
        'enabled' => 'Jazyk úspěšně aktivován.',
        'disabled' => 'Jazyk úspěšně deaktivován.',
        'default' => 'Jazyk ":name" úspěšně nastaven jako výchozí',
        'settings_updated' => 'Nastavení jazyků bylo uloženo.'
    ],

    'index' => [
        'table_columns' => [
            'name' => 'Název',
            'code' => 'Kód',
            'toggle' => 'Stav',
            'flag' => 'Ikona',
            'default' => 'Výchozí',
            'actions' => 'Akce',
        ],
        'btn_edit' => 'Upravit',
        'btn_delete' => 'Smazat',
        'btn_create' => 'Přidat jazyk',
        'btn_set_default' => 'Nastavit jako výchozí',
        'title_enable' => 'Aktivovat',
        'title_disable' => 'Deaktivovat',
    ],

    'status' => [
        'enabled' => 'Aktivován',
        'disabled' => 'Deaktivován',
        'default' => 'Výchozí',
    ],

    'confirm_delete' => [
        'title' => 'Smazat jazyk',
        'text' => 'Opravdu si přejete smazat tento jazyk se všemi jeho stránkami, články a dalšími návaznostmi?',
        'confirm' => 'Smazat',
        'cancel' => 'Zrušit'
    ],

    'settings' => [
        'title' => 'Nastavení',
        'help_text' => 'Pokud je používáno více jazyků:',
        'option_directory' => 'Jazyk nastavený podle adresáře',
        'show_default' => 'Nezobrazovat výchozí jazyk v adrese',
        'option_subdomain' => 'Jazyk nastavený podle subdomény. Např. ',
        'option_domain' => 'Jazyk nastavený podle jiné domény',
        'example_domain' => 'mujweb.cz',
        'btn_save' => 'Uložit změny'
    ],

];
