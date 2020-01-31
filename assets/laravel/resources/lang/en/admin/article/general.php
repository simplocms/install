<?php

return [

    'descriptions' => [
        'index' => 'overview',
        'create' => 'create new item',
        'edit' => 'edit item',
    ],

    'notifications' => [
        'created' => 'Item successfully created!',
        'updated' => 'Item successfully updated!',
        'deleted' => 'Item successfully deleted!',
        'duplicated' => 'Item successfully duplicated!',
    ],

    'index' => [
        'table_columns' => [
            'title' => 'Title',
            'publish_at' => 'Publish at',
            'unpublish_at' => 'Unpublish at',
            'status' => 'Status',
            'author' => 'Author',
        ],
        'btn_preview' => 'Preview',
        'btn_edit' => 'Edit',
        'btn_duplicate' => 'Duplicate',
        'btn_delete' => 'Delete',
        'btn_create' => 'Create new item'
    ],

    'status' => [
        'published' => 'Published',
        'unpublished' => 'Unpublished'
    ],

    'confirm_delete' => [
        'title' => 'Delete item',
        'text' => 'Do you really want to delete this item?',
        'confirm' => 'Delete',
        'cancel' => 'Cancel'
    ],

    'frontweb_toolbar' => [
        'btn_create' => 'New article',
        'btn_edit' => 'Edit article',
    ],

];
