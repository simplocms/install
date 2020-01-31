<?php

return [

    'tabs' => [
        'details' => 'Basic information',
        'grid' => 'Grid',
        'seo' => 'SEO',
        'planning' => 'Planning',
        'og' => 'OpenGraph',
    ],

    'planning_tab' => [
        'title' => 'Planning',
        'help_text' => 'Publishing pages can be scheduled. The page will appear on the site since <kbd>published</kbd> date. If the field <kbd>unpublished</kbd> is filled, the page will be hidden from the site from this date.',
    ],

    // this structure is required for javascript form
    'seo_tab' => [
        'inputs' => [
            'title' => [
                'info' => 'is the page title in the browser. If you do not fill this field, the name of the page will be automatically used.'
            ],
        ]
    ],

    'labels' => [
        'name' => 'Name',
        'url' => 'URL slug',
        'view' => 'View',
        'parent_id' => 'Parent page',
        'is_homepage' => 'Is homepage',
        'published' => 'Published on the web',
        'publish_at' => 'Publish at',
        'unpublish_at' => 'Unpublish at',
        'set_unpublish_at' => 'Set page publishing',
    ],

    'placeholders' => [
        'view' => 'Default',
        'parent_id' => 'None',
        'publish_at_date' => 'Select date',
        'publish_at_time' => 'Select time',
        'unpublish_at_date' => 'Select date',
        'unpublish_at_time' => 'Select time',
    ],

    'btn_save' => 'Save',
    'btn_save_finish' => 'Save and finish',
    'btn_create' => 'Create',
    'btn_cancel' => 'Cancel',

    'testing_variant_switch_confirm' => 'Form contains unsaved changes. Do you really want to switch variant?',

    'messages' => [
        'name.required' => 'Please enter name of the page.',
        'name.max' => 'Maximal length of the name is :max characters.',
        'url.required' => 'Please enter the URL slug.',
        'url.max' => 'Maximal length of the URL slug is :max characters.',
        'parent_id.exists' => 'Selected parent page no longer exists.',
        'testing_b_id.exists' => 'This page cannot be used, because it does not exist, or is already used for testing.',
    ],

];
