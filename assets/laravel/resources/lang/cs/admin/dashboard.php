<?php

return [
    'btn_authorize_ga' => 'Authorize Google Analytics',

    'titles' => [
        'index' => 'Dashboard',
        'authorization' => 'Autorizace',
        'profile_list' => 'Výběr profilu',
    ],

    'descriptions' => [
        'index' => 'přehled',
        'authorization' => 'přístup k Google Analytics',
        'profile_list' => 'zvolte profil pro zobrazení na nástěnce',
    ],

    'notifications' => [
        'ga_profile_not_selected' => 'Nepodařilo se vybrat profil',
        'no_ga_permission' => 'Nemáte oprávnění spravovat nástěnku',
        'invalid_ga_token' => 'Kód se nepodařilo ověřit. Zkuste to prosím znovu',
        'ga_disconnected' => 'Google Analytics byl odpojen',
    ],

    'graphs' => [
        'title' => 'Návštěvnost webu',
        'visits_count' => 'Návštěvnost',
        'users_count' => 'Uživatelé',
        'pageviews' => 'Zobrazení stránek',
        'views_per_session' => 'Počet stránek na 1 návštěvu',
        'bouncer_rate' => 'Míra okamžitého opuštění',
        'organic_searches_count' => 'Organických vyhledání',
        'new_visitors' => 'Noví návštěvníci',
        'returning_visitors' => 'Vracející se návštěvníci',

        'buttons' => [
            'settings' => 'Nastavení',
            'disconnect_ga' => 'Odpojit Google Analytics',
            'switch_ga_profile' => 'Přepnout GA profil',
        ],
    ],

    'authorization' => [
        'title' => 'Google Analytics - Autorizace',
        'text_for_link' => 'Nejprve je potřeba pomocí tohoto odkazu',
        'token_link_text' => 'získat kód',
        'label_token' => 'Kód: ',
        'btn_authorize' => 'Autorizovat'
    ],

    'choose_profile' => [
        'title' => 'Vyberte profil',
        'help_text' => 'Vyberte profil, jehož data chcete vidět na nástěnce',
        'include_measuring_code' => 'Přidat do stránky měřící kód',
    ],
];
