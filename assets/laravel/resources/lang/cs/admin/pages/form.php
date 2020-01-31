<?php

return [

    'tabs' => [
        'details' => 'Základní informace',
        'grid' => 'Grid',
        'seo' => 'SEO',
        'planning' => 'Plánování',
        'og' => 'OpenGraph',
    ],

    'planning_tab' => [
        'title' => 'Plánování',
        'help_text' => 'Publikaci stránek je možné naplánovat. Stránka zobrazí na webu od data <kbd>publikovat od</kbd>. Pokud je zadané pole <kbd>publikovat do</kbd>, tak se stránka skryje z webu od tohoto data.',
    ],

    // this structure is required for javascript form
    'seo_tab' => [
        'inputs' => [
            'title' => [
                'info' => 'je titulek stránky v prohlížeči. Pokud pole nevyplníte, automaticky se použije název stránky.'
            ],
        ]
    ],

    'labels' => [
        'name' => 'Název',
        'url' => 'URL',
        'view' => 'Šablona',
        'parent_id' => 'Nadřazená stránka',
        'is_homepage' => 'Úvodní stránka',
        'published' => 'Publikováno na webu',
        'publish_at' => 'Publikovat od',
        'unpublish_at' => 'Publikovat do',
        'set_unpublish_at' => 'Zvolit odpublikování stránky',
    ],

    'placeholders' => [
        'view' => 'Výchozí',
        'parent_id' => 'Žádná',
        'publish_at_date' => 'Zvolte datum',
        'publish_at_time' => 'Zvolte čas',
        'unpublish_at_date' => 'Zvolte datum',
        'unpublish_at_time' => 'Zvolte čas',
    ],

    'btn_save' => 'Uložit',
    'btn_save_finish' => 'Uložit a dokončit',
    'btn_create' => 'Vytvořit',
    'btn_cancel' => 'Zrušit',

    'testing_variant_switch_confirm' => 'Formulář obsahuje neuložené změny. Opravdu si přejete přepnout variantu?',

    'messages' => [
        'name.required' => 'Zadejte prosím název stránky.',
        'name.max' => 'Maximální délka názvu je :max znaků.',
        'url.required' => 'Zadejte prosím URL stránky.',
        'url.max' => 'Maximální délka URL je :max znaků.',
        'parent_id.exists' => 'Vybraná nadřazená stránka neexistuje.',
        'testing_b_id.exists' => 'Tuto stránku nelze vybrat, protože neexistuje, nebo je již pro testování použita.',
    ],

];
