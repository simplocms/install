<?php

return [

    'labels' => [
        'from' => 'Source url slug',
        'to' => 'Target url or slug',
        'status_code' => 'Status code',
    ],

    'from_info' => 'Enter <strong>relative</strong> address (url slug). If the address starts with a slash, the system automatically deletes the slash when it is saved.',
    'to_info' => 'Here you can enter either relative address (url slug) or absolute url address of target. If the address starts with a slash, the system automatically deletes the slash when it is saved.',

    'custom_url_option' => 'Any address',

    'status_codes' => [
        301 => '301 Moved Permanently',
        302 => '302 Found',
        307 => '307 Temporary Redirect',
        308 => '308 Permanent Redirect',
    ],

    'btn_save' => 'Save changes',
    'btn_create' => 'Create',
    'btn_cancel' => 'Cancel',

    'messages' => [
        'from.required' => 'Please enter source url slug.',
        'from.max' => 'Maximal length of the source url slug is :max characters.',
        'from.regex' => 'Url slug contains invalid characters.',
        'from_unique' => 'Redirect from this url slug already exist.',
        'from_absolute_url' => 'Please enter an address relative to your site.',
        'to.required' => 'Please enter target url or slug.',
        'to.max' => 'Maximal length of the target is :max characters.',
        'to.regex' => 'Url contains invalid characters.',
        'to_redirect' => 'The entered address is redirected to ":url". Please avoid redirecting to already redirected addresses.',
        'status_code.required' => 'Please select status code for redirect.',
        'status_code.in' => 'Invalid status code.',
    ],

    'bulk_create' => [
        'btn_import' => 'Import from CSV file', // Importovat z CSV souboru
        'btn_clear' => 'Clear table', // Vyprázdnit tabulku
        'btn_import_example' => 'Download example of CSV file for import', // Stáhnout vzor CSV souboru pro import
        'btn_add_row' => 'Add row',
        'btn_save' => 'Create redirects',
        'btn_cancel' => 'Cancel',
        'table_columns' => [
            'from' => 'Redirect source',
            'to' => 'Redirect target',
            'status_code' => 'Status code',
            'actions' => 'Remove',
        ],
        'messages' => [
            'redirects.required' => 'Please enter at least one redirect.',
            'redirects.*from.distinct' => 'This redirect source is duplicated.',
        ],
    ]

];
