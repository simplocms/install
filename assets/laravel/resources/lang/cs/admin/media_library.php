<?php

return [

    'header_title' => 'Knihovna médií',

    'root_directory' => 'Kořenový adresář',

    'directories' => [
        'prompt_create_text' => 'Zadejte prosím název složky:',
        'default_folder_name' => 'Nová složka',

        'btn_rename' => 'Přejmenovat',
        'prompt_rename_text' => 'Zadejte nový název složky:',

        'btn_delete' => 'Smazat',
        'confirm_delete' => [
            'title' => 'Smazat složku',
            'text' => 'Opravdu si přejete smazat složku ":name" a všechen její obsah?',
            'confirm' => 'Smazat',
            'cancel' => 'Zrušit'
        ],
    ],

    'search_title' => 'Vyhledávání ":phrase"',

    'sort_options' => [
        'name-ASC' => 'Seřadit podle názvu (A-Z)',
        'name-DESC' => 'Seřadit podle názvu (Z-A)',
        'updated_at-ASC' => 'Poslední změna (od nejstarší)',
        'updated_at-DESC' => 'Poslední změna (od nejnovější)',
    ],

    'search_placeholder' => 'Hledat v souborech a složkách',

    'navbar' => [
        'btn_create_folder' => 'Nová složka',
        'btn_select_files' => 'Nahrát soubory',
        'btn_delete_files' => 'Smazat vybrané soubory',
    ],

    'subdirectories_text' => 'Podadresáře',
    'files_text' => 'Soubory',

    'file' => [
        'uploading' => 'Nahrávám',
        'upload_failed' => 'Nahrávání selhalo.',
        'btn_upload_again' => 'Zkusit znovu',
        'btn_cancel_upload' => 'Zrušit nahrávání',
        'btn_detail' => 'Zobrazit detail',
        'btn_select' => 'Vybrat soubor',
        'btn_rename' => 'Přejmenovat',
        'btn_override' => 'Nahradit souborem',
        'btn_delete' => 'Smazat',
        'btn_save_description' => 'Uložit',
        'btn_change_resolution' => 'Změnit rozlišení',
        'btn_rotate_left_90' => 'Otočit vlevo o 90°',
        'btn_rotate_right_90' => 'Otočit vpravo o 90°',

        'confirm_delete' => [
            'title' => 'Smazat soubor|Smazat vybrané soubory',
            'text' => 'Opravdu si přejete smazat soubor ":name"?|Opravdu si přejete smazat vybrané soubory (celkem :count)?',
            'confirm' => 'Smazat',
            'cancel' => 'Zrušit'
        ],

        'size' => 'Velikost',
        'resolution' => 'Rozměry',
        'last_change_at' => 'Poslední změna',
        'url' => 'URL',
        'copy_url' => 'Kopírovat URL',
        'description' => 'Popis',
        'actions' => 'Akce',
    ],

    'resize_modal' => [
        'title' => 'Změna rozlišení',
        'info_text' => 'Poměr stran obrázku je vždy zachován.',
        'label' => 'Nové rozlišení',
        'btn_save' => 'Uložit změny',
        'btn_cancel' => 'Zavřít',
    ],

    'file_drop_text' => 'Přetáhněte soubory pro upload',

    'file_selector' => [
        'image_preview' => 'Náhled obrázku',
        'accepted' => 'Akceptováno',
        'no_file_selected' => 'Žádný soubor nevybrán',
        'video_not_supported' => 'Váš proohlížeč nepodporuje video.',
        'video_support_notice' => 'Nejlepší podpory prohlížečích dosáhnete formáty MP4, OGG a WebM.',

        'btn_change_image' => 'Změnit obrázek',
        'btn_select_image' => 'Vybrat obrázek',
        'btn_change_file' => 'Změnit soubor',
        'btn_select_file' => 'Vybrat soubor',
        'btn_remove_image' => 'Odebrat obrázek',
        'btn_remove_file' => 'Odebrat soubor',
    ],

    'validation_messages' => [
        'invalid_image' => 'Zvolený soubor není podporovaný obrázek.',
        'invalid_file' => 'Vybrán nepovolený soubor.',
    ],

    'cache_driver_warning' => "Cache driver je nastaven na hodnotu 'array', nahrávání souborů větších než :size nebude fungovat správně.",


];
