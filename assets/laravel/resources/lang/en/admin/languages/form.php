<?php

return [

    'tabs' => [
        'details' => 'Basic information',
    ],

    'labels' => [
        'name' => 'Name',
        'country_code' => 'Country code (ISO Alpha-2 or Alpha-3)',
        'language_code' => 'Language code (ISO 639-1 or ISO 639-2)',
        'domain' => 'Domain for language',
        'enabled' => 'Enabled',
    ],

    'placeholders' => [
        'domain' => 'e.g. example.com'
    ],

    'btn_update' => 'Update',
    'btn_create' => 'Create',
    'btn_cancel' => 'Cancel',

    'messages' => [
        'name.required' => 'Please enter name of the language.',
        'name.max' => 'Maximal length of the name is :max characters.',
        'country_code.required' => 'Please enter country code of the language.',
        'country_code.max' => 'Maximal length of the country code is :max characters.',
        'language_code.required' => 'Please enter language code of the language.',
        'language_code.max' => 'Maximal length of the language code is :max characters.',
        'language_code.unique' => 'This language code is already used by another language.',
        'domain.max' => 'Maximal length of the domain is :max characters.',
    ],

];
