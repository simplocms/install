<?php

return [

    'header_title' => 'Pages',

    'descriptions' => [
        'index' => 'overview',
        'create' => 'create new page',
        'edit' => 'edit page',
    ],

    'notifications' => [
        'created' => 'Page successfully created.',
        'updated' => 'Page successfully updated.',
        'deleted' => 'Page successfully deleted.',
        'duplicated' => 'Page successfully duplicated.',
        'ab_test_created' => 'A/B test successfully created.',
        'ab_test_stopped' => 'A/B test successfully stopped.',
        'ab_test_already_created' => 'A/B test was already created for this page.',
        'ab_test_not_available' => 'A/B test is not available for this page.',
    ],

    'index' => [
        'table_columns' => [
            'name' => 'Page name',
            'publish_at' => 'Publish at',
            'unpublish_at' => 'Unpublish at',
            'status' => 'Status',
        ],
        'btn_preview' => 'Preview',
        'btn_edit' => 'Edit',
        'btn_duplicate' => 'Duplicate',
        'btn_ab_test_start' => 'Make A/B test',
        'btn_ab_test_stop' => 'Stop A/B test',
        'btn_delete' => 'Delete',
        'btn_create' => 'Create new page',
        'ab_testing_stop' => [
            'text' => 'Which testing variant you wish to preserve?',
            'keep_a' => 'Variant A',
            'keep_b' => 'Variant B',
            'keep_both' => 'Both variants',
        ],
    ],

    'status' => [
        'published' => 'Published',
        'unpublished' => 'Unpublished'
    ],

    'confirm_delete' => [
        'title' => 'Delete page',
        'text' => 'Do you really want to delete this page?',
        'confirm' => 'Delete',
        'cancel' => 'Cancel'
    ],

    'frontweb_toolbar' => [
        'btn_create' => 'New page',
        'btn_edit' => 'Edit page',
    ],

    'page_duplicate_name_suffix' => 'Copy',

];
