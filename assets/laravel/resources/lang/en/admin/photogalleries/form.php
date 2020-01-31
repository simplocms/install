<?php

return [

    'tabs' => [
        'details' => 'Basic information',
        'photogallery' => 'Photogallery',
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
                'info' => 'is the page title in the browser. If you do not fill this field, photogallery title will be automatically used.'
            ]
        ]
    ],

    'labels' => [
        'title' => 'Title',
        'url' => 'URL',
        'text' => 'Text',
        'sort' => 'Sorting',

        'publish_at' => 'Publish at',
        'unpublish_at' => 'Unpublish at'
    ],

    'placeholders' => [
        'publish_at_date' => 'Select date',
        'publish_at_time' => 'Select time',
        'unpublish_at_date' => 'Select date',
        'unpublish_at_time' => 'Select time',
    ],

    'btn_update' => 'Update',
    'btn_create' => 'Create',
    'btn_cancel' => 'Cancel',

    'messages' => [
        'title.required' => 'Please enter the title.',
        'title.max' => 'Maximal length of the title is :max characters.',
        'url.required' => 'Please enter the URL slug.',
        'url.max' => 'Maximal length of the URL slug is :max characters.',
        'sort.numeric' => 'Sorting order must be number.',
        'sort.min' => 'Minimal value is :min.',
    ],

];
