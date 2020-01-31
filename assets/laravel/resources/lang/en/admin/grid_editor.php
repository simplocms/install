<?php

return [

    'sizes' => [
        'xl' => 'Desktop',
        'lg' => 'Laptop',
        'md' => 'Tablet',
        'sm' => 'Tablet',
        'xs' => 'Mobile',
    ],

    'modes' => [
        'layout' => 'Edit layout',
        'content' => 'Edit content'
    ],

    'version_text' => 'Version :index',

    'version_change_confirm' => [
        'title' => 'Switch version',
        'text' => 'All unsaved changes will be lost when switching version. Do you really want to continue?',
        'confirm' => 'Confirm',
        'cancel' => 'Cancel',
    ],

    'content_buttons' => [
        'add_container' => 'Add row',
        'add_row' => 'Add row without container',
        'add_module' => 'Add module',
    ],

    'content_controls' => [
        'move' => 'Move',
        'settings' => 'Settings',
        'delete' => 'Delete',
        'duplicate' => 'Duplicate',
    ],

    'remove_confirmation' => [
        'confirm' => 'Delete',
        'cancel' => 'Cancel'
    ],

    // CONTENT ITEMS //

    'container' => [
        'title' => 'Container',
        'btn_add_row' => 'Add row',
        'btn_add_module' => 'Add module',
        'remove_title' => 'Delete container',
        'remove_text' => 'Do you really want to delete this container with all its content? This action is permanent!',
    ],

    'row' => [
        'title' => 'Row',
        'btn_add_column' => 'Add column',
        'remove_title' => 'Delete row',
        'remove_text' => 'Do you really want to delete this row with all its content? This action is permanent!'
    ],

    'column' => [
        'title' => 'Column',
        'btn_add_row' => 'Add row',
        'btn_add_module' => 'Add module',
        'remove_title' => 'Delete column',
        'remove_text' => 'Do you really want to delete this column with all its content? This action is permanent!',
    ],

    'module' => [
        'loader' => 'Loading...',
        'btn_edit' => 'Edit module',
        'btn_remove' => 'Delete',
        'btn_duplicate' => 'Duplicate',
        'remove_title' => 'Delete module',
        'remove_text' => 'Do you really want to delete this module? This action is permanent!',
    ],

    // MODALS //

    'container_settings_modal' => [
        'title' => 'Container settings',
        'legend' => 'Container',
        'labels' => [
            'class' => 'Class name',
            'id' => 'Element ID',
            'tag' => 'Element of the container',
            'bg' => 'Background color',
            'fluid' => 'Extend to full screen width',
            'active' => 'Deactivate container',
            'wrap' => 'Wrap container'
        ],
        'wrap_help_text' => 'Insert HTML structure of wrapper element. Content of the container will be included on the place of :code.'
    ],

    'row_settings_modal' => [
        'title' => 'Row settings',
        'legend' => 'Row',
        'labels' => [
            'class' => 'Class name',
            'id' => 'Element ID',
            'tag' => 'Element of the row',
            'bg' => 'Background color',
            'active' => 'Deactivate row',
        ]
    ],

    'column_settings_modal' => [
        'title' => 'Column settings',
        'legend' => 'Column',
        'labels' => [
            'class' => 'Class name',
            'id' => 'Element ID',
            'tag' => 'Element of the column',
            'bg' => 'Background color',
            'active' => 'Deactivate column',
        ]
    ],

    'row_layouts_modal' => [
        'title' => 'Add row',
        'layout_label' => ':count column|:count columns'
    ],

    'modules_modal' => [
        'title' => 'Add module',
    ],

    'settings_modal' => [
        'btn_save' => 'Save changes',
        'btn_cancel' => 'Close',
        'attributes' => [
            'legend' => 'Attributes settings',
            'label_name' => 'Attribute name',
            'label_value' => 'Attribute value',
            'btn_add' => 'Add attribute',
        ]
    ]

];
