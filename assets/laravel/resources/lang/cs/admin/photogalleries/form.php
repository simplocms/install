<?php

return [

    'tabs' => [
        'details' => 'Základní informace',
        'photogallery' => 'Fotogalerie',
        'seo' => 'SEO',
        'planning' => 'Plánování',
        'og' => 'OpenGraph',
    ],

    'planning_tab' => [
        'title' => 'Plánování',
        'help_text' => 'Publikaci fotogalerií je možné naplánovat. Fotogalerie se zobrazí na webu od data <kbd>publikovat od</kbd>. Pokud je zadané pole <kbd>publikovat do</kbd>, tak se fotogalerie skryje z webu od tohoto data.',
    ],

    // this structure is required for javascript form
    'seo_tab' => [
        'inputs' => [
            'title' => [
                'info' => 'je titulek stránky v prohlížeči. Pokud pole nevyplníte, automaticky se použije titulek fotogalerie.'
            ]
        ]
    ],

    'labels' => [
        'title' => 'Titulek',
        'url' => 'URL',
        'text' => 'Text',
        'sort' => 'Řazení',

        'publish_at' => 'Publikovat od',
        'unpublish_at' => 'Publikovat do',
    ],

    'placeholders' => [
        'publish_at_date' => 'Zvolte datum',
        'publish_at_time' => 'Zvolte čas',
        'unpublish_at_date' => 'Zvolte datum',
        'unpublish_at_time' => 'Zvolte čas',
    ],

    'btn_update' => 'Uložit změny',
    'btn_create' => 'Vytvořit',
    'btn_cancel' => 'Zrušit',

    'messages' => [
        'title.required' => 'Zadejte prosím titulek.',
        'title.max' => 'Maximální délka titulku je :max znaků.',
        'url.required' => 'Zadejte prosím URL.',
        'url.max' => 'Maximální délka URL je :max znaků.',
        'sort.numeric' => 'Řazení musí být reprezentováno číslem.',
        'sort.min' => 'Minimální povolená hodnota je :min.',
    ],

];
