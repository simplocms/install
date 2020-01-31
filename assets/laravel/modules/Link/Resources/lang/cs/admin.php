<?php

return [

    'grideditor_title' => 'Odkaz',

    'grid_editor_form' => [
        'labels' => [
            'text' => 'Text',
            'custom_url' => 'Vlastní URL',
            'model_type' => 'Odkaz na',
            'view' => 'Šablona',
            'attributes' => 'HTML atributy',
        ],
        'btn_add_attribute' => 'Přidat atribut',
        'model_types' => [
            'page' => 'Stránka',
            'article' => 'Článek',
            'photogallery' => 'Fotogalerie',
        ],
        'default_view' => 'Výchozí'
    ],

    'preview' => [
        'title' => 'Odkaz',
        'text' => 'Text',
    ],

    'validation_messages' => [
        'text.required' => 'Zadejte prosím text odkazu.',
        'url.required_if' => 'Zadejte prosím vlastní url.',
        'model_type.required_without' => 'Zadejte prosím na co chcete odkazovat.',
        'model_type.in' => 'Neplatný typ odkazu.',

        // page
        'page_id.required_if' => 'Vyberte prosím stránku, na kterou chcete odkazovat.',
        'page_id.exists' => 'Vybraná stránka již neexistuje.',

        // article
        'article_id.required_if' => 'Vyberte prosím článek, na který chcete odkazovat.',
        'article_id.exists' => 'Vybraný článek již neexistuje.',

        // photogallery
        'photogallery_id.required_if' => 'Vyberte prosím fotogalerii, na kterou chcete odkazovat.',
        'photogallery_id.exists' => 'Vybraná fotogalerie již neexistuje.',
    ]

];
