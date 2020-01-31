<?php

return [

    'header_title' => 'Widgets',

    'descriptions' => [
        'index' => 'overview',
        'create' => 'create new widget',
        'edit' => 'edit widget',
    ],

    'notifications' => [
        'created' => 'Widget successfully created.',
        'updated' => 'Widget successfully updated.',
        'deleted' => 'Widget successfully deleted.',
    ],

    'index' => [
        'table_columns' => [
            'name' => 'Widget name',
            'id' => 'Identifier',
        ],
        'btn_edit' => 'Edit',
        'btn_delete' => 'Delete',
        'btn_create' => 'Create new widget'
    ],

    'confirm_delete' => [
        'title' => 'Delete widget',
        'text' => 'Do you really want to delete this widget?',
        'confirm' => 'Delete',
        'cancel' => 'Cancel'
    ],

    'no_language_fallback' => [
        'known_language' => 'Widget :id does not have any content for language ":language"!',
        'unknown_language' => 'Widget ":id" does not have any content for this language!'
    ],

    'no_widget_fallback' => 'Widget with identifier ":id" does not exist!',

];
