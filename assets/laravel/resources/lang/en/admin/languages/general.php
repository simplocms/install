<?php

return [

    'header_title' => 'Languages',

    'descriptions' => [
        'index' => 'overview',
        'create' => 'create new language',
        'edit' => 'edit language',
    ],

    'notifications' => [
        'created' => 'Language successfully created.',
        'updated' => 'Language successfully updated.',
        'deleted' => 'Language successfully deleted.',
        'protected_default' => 'Default language cannot be deleted.',
        'enabled' => 'Language successfully enabled.',
        'disabled' => 'Language successfully disabled.',
        'default' => 'Language ":name" successfully set as default',
        'settings_updated' => 'The language settings successfully updated.'
    ],

    'index' => [
        'table_columns' => [
            'name' => 'Name',
            'code' => 'Code',
            'toggle' => 'Enable/Disable',
            'flag' => 'Flag',
            'default' => 'Default',
            'actions' => 'Actions',
        ],
        'btn_edit' => 'Edit',
        'btn_delete' => 'Delete',
        'btn_create' => 'Add language',
        'btn_set_default' => 'Set as default',
        'title_enable' => 'Enable',
        'title_disable' => 'Disable',
    ],

    'status' => [
        'enabled' => 'Enabled',
        'disabled' => 'Disabled',
        'default' => 'Default',
    ],

    'confirm_delete' => [
        'title' => 'Delete language',
        'text' => 'Do you really want to delete this language?',
        'confirm' => 'Delete',
        'cancel' => 'Cancel'
    ],

    'settings' => [
        'title' => 'Settings',
        'help_text' => 'If multiple languages are used:',
        'option_directory' => 'Language set by directory',
        'show_default' => 'Do not show the default language in the address',
        'option_subdomain' => 'Language set by subdomain. E.g. ',
        'option_domain' => 'Language set by another domain',
        'example_domain' => 'example.com',
        'btn_save' => 'Save changes'
    ],

];

