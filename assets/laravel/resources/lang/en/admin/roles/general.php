<?php

return [

    'header_title' => 'Roles',

    'descriptions' => [
        'index' => 'overview',
        'create' => 'create new role',
        'edit' => 'edit role',
    ],

    'notifications' => [
        'created' => 'Role successfully created.',
        'updated' => 'Role successfully updated.',
        'deleted' => 'Role successfully deleted.',
        'protected_role' => 'This role cannot be modified.',
        'enabled' => 'Role successfully enabled.',
        'disabled' => 'Role successfully disabled.',
    ],

    'index' => [
        'table_columns' => [
            'name' => 'Role name',
            'description' => 'Description',
            'toggle' => 'Enable/Disable',
        ],
        'btn_edit' => 'Edit',
        'btn_delete' => 'Delete',
        'btn_create' => 'Create new role',
        'title_enable' => 'Enable',
        'title_disable' => 'Disable',
    ],

    'confirm_delete' => [
        'title' => 'Delete role',
        'text' => 'Do you really want to delete this role?',
        'confirm' => 'Delete',
        'cancel' => 'Cancel'
    ],

];
