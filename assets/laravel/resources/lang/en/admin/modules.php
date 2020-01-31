<?php

return [

    'header_title' => 'Modules',

    'descriptions' => [
        'index' => 'module management',
    ],

    'notifications' => [
        'enabled' => 'Module ":name" successfully enabled.',
        'disabled' => 'Module ":name" successfully disabled.',
    ],

    'index' => [
        'table_columns' => [
            'name' => 'Name',
            'status' => 'Status',
            'installation' => 'Installation',
        ],
        'btn_install' => 'Install',
        'btn_uninstall' => 'Uninstall',
    ],

    'status' => [
        'enabled' => 'Enabled',
        'disabled' => 'Disabled',
    ],

    'confirm_uninstall' => [
        'title' => 'Uninstall module',
        'text' => 'Are you sure you want to uninstall this module? All data and uses will be permanently removed.',
        'confirm' => 'Yes, uninstall!',
        'cancel' => 'Cancel'
    ],

];
