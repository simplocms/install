<?php

return [

    'descriptions' => [
        'index' => 'categories',
        'create' => 'create category',
        'edit' => 'edit category',
    ],

    'notifications' => [
        'created' => 'Category successfully created!',
        'updated' => 'Category successfully updated!',
        'deleted' => 'Category successfully deleted!',
    ],

    'index' => [
        'table_columns' => [
            'name' => 'Category name',
            'status' => 'Status',
            'created' => 'Created at',
            'author' => 'Author',
            'action' => 'Action',
        ],
        'btn_preview' => 'Preview',
        'btn_edit' => 'Edit',
        'btn_delete' => 'Delete',
        'btn_create' => 'Create new category'
    ],

    'status' => [
        'published' => 'Published',
        'unpublished' => 'Unpublished'
    ],

    'confirm_delete' => [
        'title' => 'Delete category',
        'text' => 'Do you really want to delete this category and all its descendant categories?',
        'confirm' => 'Delete',
        'cancel' => 'Cancel'
    ],

    'frontweb_toolbar' => [
        'btn_create' => 'New category',
        'btn_edit' => 'Edit category',
    ],

];
