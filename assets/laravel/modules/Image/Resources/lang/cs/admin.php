<?php

return [

    'grideditor_title' => 'Obrázek',

    'grid_editor_form' => [
        'labels' => [
            'is_sized' => 'Velikost obrázku',
            'resolution' => 'Zadejte rozlišení',
            'alt' => 'Alternativní text k obrázku (co je na obrázku)',
            'img_class' => 'Třída <img> elementu',
        ],
        'size_options' => [
            'automatic' => 'Automaticky',
            'manual' => 'Zadat manuálně',
        ],
    ],

    'validation_messages' => [
        'image_id.required' => 'Vyberte prosím obrázek.',
        'alt.required' => 'Zadejte prosím alternativní text obrázku.',
        'is_sized.required' => 'Zvolte velikost obrázku.',

        'width.required' => 'Zadejte prosím šířku obrázku.',
        'width.min' => 'Minimální šířka obrázku je :min px.',
        'width.integer' => 'Šířka musí být celé číslo.',

        'height.required' => 'Zadejte prosím výšku obrázku.',
        'height.min' => 'Minimální výška obrázku je :min px.',
        'height.integer' => 'Výška musí být celé číslo.',
    ]

];
