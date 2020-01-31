<?php

return [

    'tabs' => [
        'info' => 'Basic information',
        'seo' => 'SEO',
        'og' => 'OpenGraph',
    ],

    // this structure is required for javascript form
    'seo_tab' => [
        'inputs' => [
            'title' => [
                'info' => 'is the page title in the browser. If you do not fill this field, category name will be automatically used.'
            ],
        ]
    ],

    'labels' => [
        'name' => 'Name',
        'url' => 'URL',
        'description' => 'Description',
        'parent_id' => 'Parent category',
        'show' => 'View on web pages',
    ],

    'default_parent_category' => 'None',

    'btn_update' => 'Update',
    'btn_create' => 'Create',
    'btn_cancel' => 'Cancel',

    'messages' => [
        'name.required' => 'Please enter name of the category.',
        'name.max' => 'Maximal length of the name is :max characters.',
        'url.required' => 'Please enter URL slug of the category.',
        'url.max' => 'Maximal length of the URL slug is :max characters.',
        'parent_id.exists' => 'Selected category no longer exists.',
        'description.max' => 'Maximal length of description is :max characters.',
    ],

];
