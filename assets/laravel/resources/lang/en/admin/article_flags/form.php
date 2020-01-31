<?php

return [

    'tabs' => [
        'details' => 'Basic information',
        'seo' => 'SEO',
        'og' => 'OpenGraph',
    ],

    // this structure is required for javascript form
    'seo_tab' => [
        'inputs' => [
            'title' => [
                'info' => 'is the page title in the browser. If you do not fill this field, the name will be automatically used.'
            ]
        ]
    ],

    'labels' => [
        'name' => 'Name',
        'url' => 'URL slug',
        'description' => 'Description',
        'use_tags' => 'Use tags',
        'use_grid_editor' => 'Use Grid Editor',
        'should_bound_articles_to_category' => 'Show category in urls and breadcrumbs of articles',
    ],

    'btn_update' => 'Update',
    'btn_create' => 'Create',
    'btn_cancel' => 'Cancel',

    'messages' => [
        'name.required' => 'Please enter the name.',
        'name.max' => 'Maximal length of the name is :max characters.',
        'url.required' => 'Please enter url slug of the type.',
        'url.max' => 'Maximal length of the url slug is :max characters.',
        'description.max' => 'Maximal length of description is :max characters.',
    ],

];
