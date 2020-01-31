<?php

return [

    'tabs' => [
        'details' => 'Základní informace',
    ],

    'labels' => [
        'name' => 'Název',
        'country_code' => 'Kód země (ISO Alpha-2 nebo Alpha-3)',
        'language_code' => 'Kód jazyka (ISO 639-1 nebo ISO 639-2)',
        'domain' => 'Doména jazyka',
        'enabled' => 'Aktivován',
    ],

    'placeholders' => [
        'domain' => 'např. mujweb.cz'
    ],

    'btn_update' => 'Uložit změny',
    'btn_create' => 'Vytvořit',
    'btn_cancel' => 'Zrušit',

    'messages' => [
        'name.required' => 'Zadejte prosím název jazyka.',
        'name.max' => 'Mazimální délka názvu je :max znaků.',
        'country_code.required' => 'Zadejte prosím kód země.',
        'country_code.max' => 'Maximální délka kódu země je :max znaků.',
        'language_code.required' => 'Zadejte prosím kód jazyka.',
        'language_code.max' => 'Maximální délka kódu jazyka je :max znaků.',
        'language_code.unique' => 'Tento kód již používá jiný jazyk.',
        'domain.max' => 'Maximální délka domény je :max znaků.',
    ],

];
