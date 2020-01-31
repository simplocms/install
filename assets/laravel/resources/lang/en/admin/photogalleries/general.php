<?php

return [

    'header_title' => 'Photogalleries',

    'descriptions' => [
        'index' => 'overview',
        'create' => 'create new photogallery',
        'edit' => 'edit photogallery',
    ],

    'notifications' => [
        'created' => 'Photogallery successfully created.',
        'updated' => 'Photogallery successfully updated.',
        'deleted' => 'Photogallery successfully deleted.',
    ],

    'index' => [
        'table_columns' => [
            'name' => 'Photogallery name',
            'publish_at' => 'Publish at',
            'unpublish_at' => 'Unpublish at',
            'author' => 'Author',
            'status' => 'Status',
        ],
        'btn_preview' => 'Preview',
        'btn_edit' => 'Edit',
        'btn_delete' => 'Delete',
        'btn_create' => 'Create new photogallery'
    ],

    'status' => [
        'published' => 'Published',
        'unpublished' => 'Unpublished'
    ],

    'confirm_delete' => [
        'title' => 'Delete photogallery',
        'text' => 'Do you really want to delete this photogallery?',
        'confirm' => 'Delete',
        'cancel' => 'Cancel'
    ],

    'frontweb_toolbar' => [
        'btn_create' => 'New photogallery',
        'btn_edit' => 'Edit photogallery',
    ],

];
