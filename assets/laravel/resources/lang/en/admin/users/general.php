<?php
return [

    'header_title' => 'Users',

    'descriptions' => [
        'index' => 'overview',
        'create' => 'create new user',
        'edit' => 'edit user',
    ],

    'notifications' => [
        'created' => 'User successfully created.',
        'updated' => 'User successfully updated.',
        'deleted' => 'User successfully deleted.',
        'protected_user' => 'This user cannot be edited.',
        'enabled' => 'User successfully enabled.',
        'disabled' => 'User successfully disabled.',
    ],

    'index' => [
        'table_columns' => [
            'name' => 'Name',
            'username' => 'Username',
            'toggle' => 'Enable/Disable',
            'email' => 'Email',
        ],
        'btn_edit' => 'Edit',
        'btn_delete' => 'Delete',
        'btn_create' => 'Create new user',
        'title_enable' => 'Enable',
        'title_disable' => 'Disable',
    ],

    'confirm_delete' => [
        'title' => 'Delete user',
        'text' => 'Do you really want to delete this user?',
        'confirm' => 'Delete',
        'cancel' => 'Cancel'
    ],

];

