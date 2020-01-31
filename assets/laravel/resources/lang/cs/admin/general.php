<?php

return [
    'notifications' => [
        'language_switch_disabled' => 'Tento jazyk není povolen!',
        'language_changed' => 'Jazyk byl změněn',
        'validation_failed' => 'Nepodařilo se uložit změny.',
    ],

    'flash_level' => [
        'danger' => 'Chyba!',
        'warning' => 'Varování',
        'info' => 'Informace',
        'success' => 'Úspěch'
    ],

    'photogallery' => [
        'title' => 'Fotografie',
        'btn_select_photos' => 'Vybrat fotografie',
        'empty_table_text' => 'Nenahrány žádné fotografie.',

        'table_columns' => [
            'preview' => 'Náhled',
            'description' => 'Popisek',
            'author' => 'Autor',
            'info' => 'Informace',
            'action' => 'Akce',
        ],

        'table_row' => [
            'uploaded_at' => 'Vloženo',
            'size' => 'Velikost',
            'resolution' => 'Rozlišení',
            'btn_remove' => 'Odebrat',
            'edit_description_title' => 'Popisek fotografie',
            'edit_author_title' => 'Autor fotografie',
            'empty_description_text' => 'Nezadán',
            'empty_author_text' => 'Nezadán',
        ],
    ],

    'seo' => [
        'title' => 'SEO vlastnosti',
        'help_text' => 'Prosím vyplňte následující pole, které umožňují vyhledávačům lépe nalézt webovou stránku.',

        'inputs' => [
            'title' => [
                'label' => 'SEO title',
                'info' => 'je nadpis stránky v prohlížeči.'
            ],
            'description' => [
                'label' => 'SEO description',
                'info' => 'je text, který je zobrazen u popisku stránky ve výsledku vyhledávání.'
            ],
            'index' => [
                'label' => 'Index',
                'info' => 'určuje, zda má být stránka indexována vyhledávači.'
            ],
            'follow' => [
                'label' => 'Follow',
                'info' => 'určuje, zda mají být odkazy na stránce zohledněny pro počítání odkazových ranků.'
            ],
            'sitemap' => [
                'label' => 'Zobrazit na sitemapě'
            ]
        ]
    ],

    'open_graph' => [
        'title' => 'OpenGraph tagy',
        'help_text' => 'Protokol OpenGraph umožňuje, aby se jakákoli webová stránka lépe prezentovala při sdílení na sociálních sítích.',

        'inputs' => [
            'title' => [
                'label' => 'Titulek (og:title)',
                'info' => 'název stránky, který se má zobrazovat v grafu, např. "The Rock".'
            ],
            'description' => [
                'label' => 'Popis (og:description)',
                'info' => 'jedna až dvě věty pro popis vaší stránky.'
            ],
            'type' => [
                'label' => 'Typ (og:type)',
                'info' => 'typ objektu, např. "video.movie". V závislosti na zadaném typu mohou být vyžadovány i jiné vlastnosti.'
            ],
            'url' => [
                'label' => 'Trvalá URL (og:url)',
                'info' => 'kanonická URL adresa stránky, která bude použita jako trvalý identifikátor v grafu, např. "http://www.imdb.com/title/tt0117500/".'
            ],
            'image' => [
                'label' => 'Obrázek (og:image)',
                'info' => 'obrázek, který by měl reprezentovat vaši stránku v grafu.'
            ],
        ]
    ],

    'twitter' => [
        'help_text' => 'Přezdívka, jméno vašeho účtu (user name na Twitteru), tedy něco takového jako <kbd>@BBCNews</kbd> či <kbd>@seznam_cz</kbd>, je unikátní jméno pod kterým jste na Twitteru známi. Zadejte včetně <code>@</code>.',
        'account_validation' => 'Neplatný formát názvu Twitter účtu.',
    ],

    'publishing_states_component' => [
        'title' => 'Publikovat',
        'toggle_text' => 'Změnit datum a čas',
        'label_publish_at' => 'Publikovat od',
        'label_unpublish_at' => 'Publikovat do',
        'placeholder_date' => 'Zvolte datum',
        'placeholder_time' => 'Zvolte čas',
        'label_set_unpublish_at' => 'Zvolit datum odpublikování',
        'btn_set_current_time' => 'Vložit aktuální čas',
        'since_text' => 'od',
        'until_text' => 'do'
    ],

    'publishing_states' => [
        'published' => 'Publikováno',
        'unpublished' => 'Nepublikováno',
        'concept' => 'Koncept',
    ],
];
