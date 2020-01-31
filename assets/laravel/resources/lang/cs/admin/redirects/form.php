<?php

return [

    'labels' => [
        'from' => 'Zdrojová URL',
        'to' => 'Cílová URL',
        'status_code' => 'Stavový kód',
    ],

    'from_info' => 'Zde vložte <strong>relativní</strong> adresu (bez domény tzv. url slug). Bude-li adresa začínat lomítkem, systém jej automaticky odstraní při uložení.',
    'to_info' => 'Zde můžete vložit jak relativní adresu (bez domény tzv. url slug), tak absolutní url adresu cíle. Bude-li adresa začínat lomítkem, systém jej automaticky odstraní při uložení.',

    'custom_url_option' => 'Libovolná adresa',

    'status_codes' => [
        301 => '301 Trvale přesunuto',
        302 => '302 Nalezeno',
        307 => '307 Dočasné přesměrování',
        308 => '308 Trvalé přesměrování',
    ],

    'btn_save' => 'Uložit změny',
    'btn_create' => 'Vytvořit',
    'btn_cancel' => 'Zrušit',

    'messages' => [
        'from.required' => 'Vložte prosím zdrojovou URL.',
        'from.max' => 'Maimální délka zdrojvé adresy je :max znaků.',
        'from.regex' => 'Url obsahuje neplatné znaky.',
        'from_unique' => 'Přesměrování z této adresy již existuje.',
        'from_absolute_url' => 'Zadejte prosím adresu relativní k vašemu webu.',
        'to.required' => 'Vložte prosím cílovou URL adresu.',
        'to.max' => 'Maximální délka cílové adresy je :max znaků.',
        'to.regex' => 'Url obsahuje neplatné znaky.',
        'to_redirect' => 'Zadaná adresa je přesměrována na adresu ":url". Vyhněte se prosím přesměrování na již přesměrované adresy.',
        'status_code.required' => 'Vyberte prosím stavový kód přesměrování.',
        'status_code.in' => 'Neplatný stavový kód.',
    ],

    'bulk_create' => [
        'btn_import' => 'Importovat z CSV souboru',
        'btn_clear' => 'Vyprázdnit tabulku',
        'btn_import_example' => 'Stáhnout vzor CSV souboru pro import',
        'btn_add_row' => 'Přidat řádek',
        'btn_save' => 'Vytvořit přesměrování',
        'btn_cancel' => 'Zrušit',
        'table_columns' => [
            'from' => 'Zdrojová adresa',
            'to' => 'Cílová adresa',
            'status_code' => 'Stavový kód',
            'actions' => 'Odstranit',
        ],
        'messages' => [
            'redirects.required' => 'Vložte prosím alespoň jeden řádek.',
            'redirects.*from.distinct' => 'Tato zdrojová adresa je zadána vícekrát.',
        ],
    ]

];
