<?php

return [

    'sizes' => [
        'xl' => 'Desktop',
        'lg' => 'Laptop',
        'md' => 'Tablet',
        'sm' => 'Tablet',
        'xs' => 'Mobil',
    ],

    'modes' => [
        'layout' => 'Upravit šablonu',
        'content' => 'Upravit obsah'
    ],

    'version_text' => 'Verze :index',

    'version_change_confirm' => [
        'title' => 'Přepnout verzi',
        'text' => 'Přepnutím verze budou ztraceny všechny neuložené změny. Opravdu chcete pokračovat?',
        'confirm' => 'Přepnout verzi',
        'cancel' => 'Zrušit',
    ],

    'content_buttons' => [
        'add_container' => 'Přidat řádek',
        'add_row' => 'Přidat řádek bez kontejneru',
        'add_module' => 'Přidat modul',
    ],

    'content_controls' => [
        'move' => 'Přesunout',
        'settings' => 'Nastavení',
        'delete' => 'Smazat',
        'duplicate' => 'Duplikovat',
    ],

    'remove_confirmation' => [
        'confirm' => 'Smazat',
        'cancel' => 'Zrušit'
    ],

    // CONTENT ITEMS //

    'container' => [
        'title' => 'Kontejner',
        'btn_add_row' => 'Přidat řádek',
        'btn_add_module' => 'Přidat modul',
        'remove_title' => 'Smazat kontejner',
        'remove_text' => 'Opravdu si přejete smazat kontejner i s jeho obsahem? Tato akce je nevratná!',
    ],

    'row' => [
        'title' => 'Řádek',
        'btn_add_column' => 'Přidat sloupec',
        'remove_title' => 'Smazat řádek',
        'remove_text' => 'Opravdu si přejete smazat řádek i s jeho obsahem? Tato akce je nevratná!'
    ],

    'column' => [
        'title' => 'Sloupec',
        'btn_add_row' => 'Přidat řádek',
        'btn_add_module' => 'Přidat modul',
        'remove_title' => 'Smazat sloupec',
        'remove_text' => 'Opravdu si přejete smazat sloupec i s jeho obsahem? Tato akce je nevratná!',
    ],

    'module' => [
        'loader' => 'Načítám...',
        'btn_edit' => 'Upravit modul',
        'btn_remove' => 'Smazat',
        'btn_duplicate' => 'Duplikovat',
        'remove_title' => 'Smazat modul',
        'remove_text' => 'Opravdu si přejete smazat tento modul? Tato akce je nevratná!',
    ],

    // MODALS //

    'container_settings_modal' => [
        'title' => 'Nastavení kontejneru',
        'legend' => 'Kontejner',
        'labels' => [
            'class' => 'Název třídy',
            'id' => 'ID elementu',
            'tag' => 'Element kontejneru',
            'bg' => 'Barva pozadí',
            'fluid' => 'Rozšířit na celou šířku stránky',
            'active' => 'Deaktivovat kontejner',
            'wrap' => 'Obalit kontejner'
        ],
        'wrap_help_text' => 'Vložte HTML strukturu obalovacího elementu. Obsah kontejneru se vloží pomocí :code.'
    ],

    'row_settings_modal' => [
        'title' => 'Nastavení řádku',
        'legend' => 'Řádek',
        'labels' => [
            'class' => 'Název třídy',
            'id' => 'ID elementu',
            'tag' => 'Element řádku',
            'bg' => 'Barva pozadí',
            'active' => 'Deaktivovat řádek',
        ]
    ],

    'column_settings_modal' => [
        'title' => 'Nastavení sloupce',
        'legend' => 'Sloupec',
        'labels' => [
            'class' => 'Název třídy',
            'id' => 'ID elementu',
            'tag' => 'Element sloupce',
            'bg' => 'Barva pozadí',
            'active' => 'Deaktivovat sloupec',
        ]
    ],

    'row_layouts_modal' => [
        'title' => 'Přidat řádek',
        'layout_label' => '{1} :count sloupec|[2,4] :count sloupce|[5,*] :count sloupců'
    ],

    'modules_modal' => [
        'title' => 'Přidat modul',
    ],

    'settings_modal' => [
        'btn_save' => 'Uložit změny',
        'btn_cancel' => 'Zavřít',
        'attributes' => [
            'legend' => 'Nastavení atributů',
            'label_name' => 'Název atributu',
            'label_value' => 'Hodnota atributu',
            'btn_add' => 'Přidat atribut',
        ]
    ]

];
