<?php

return [
    'header_title' => 'Redirects',

    'descriptions' => [
        'index' => 'overview',
        'create' => 'create new redirect',
        'bulk_create' => 'bulk create redirects',
        'edit' => 'edit redirect',
    ],

    'notifications' => [
        'created' => 'Redirect successfully created.',
        'updated' => 'Redirect successfully updated.',
        'deleted' => 'Redirect successfully deleted.',
        'bulk_created' => 'Redirects successfully created.',
    ],

    'index' => [
        'table_columns' => [
            'source' => 'Redirect source',
            'target' => 'Redirect target',
            'code' => 'Status code',
            'author' => 'Created by',
        ],
        'btn_edit' => 'Edit',
        'btn_delete' => 'Delete',
        'btn_create' => 'Create new redirect',
        'btn_export' => 'Export',
        'btn_bulk_create' => 'Bulk create',

        'author_system' => 'System'
    ],

    'confirm_delete' => [
        'title' => 'Delete redirect',
        'text' => 'Do you really want to delete this redirect?',
        'confirm' => 'Delete',
        'cancel' => 'Cancel'
    ],
];
