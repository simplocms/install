<?php

return [

    'tabs' => [
        'general' => 'Basic information',
        'grid' => 'Grid',
        'seo' => 'SEO',
        'state' => 'State',
        'og' => 'OpenGraph',
        'category' => 'Category',
        'photogallery' => 'Photogallery'
    ],

    'category_tab' => [
        'no_categories' => 'No categories were created.',
    ],

    // this structure is required for javascript form
    'seo_tab' => [
        'inputs' => [
            'title' => [
                'info' => 'is the page title in the browser. If you do not fill this field, an article title will be automatically used.'
            ],
            'description' => [
                'info' => 'is the text that is displayed next to the page title in the search result. If you do not fill this field, an article perex will be automatically used.'
            ]
        ]
    ],

    'labels' => [
        'title' => 'Title',
        'url' => 'URL',
        'tags' => 'Tags',
        'perex' => 'Perex',
        'user_id' => 'Author',
        'text' => 'Text',
        'image_id' => 'Image',
        'video_id' => 'Video',
    ],

    'btn_update' => 'Update',
    'btn_create' => 'Create',
    'btn_cancel' => 'Cancel',
    'btn_preview' => 'Preview',

    'messages' => [
        'title.required' => 'Please enter the title.',
        'title.max' => 'Maximal length of the title is :max characters.',
        'url.required' => 'Please enter the URL slug.',
        'url.max' => 'Maximal length of the URL slug is :max characters.',
        'perex.required' => 'Please enter the perex.',
        'image_id.required' => 'Please provide image of article.',
        'categories.required' => 'Please select at least one category.',
        'categories.*.exists' => 'Selected category no longer exists.',
        'user_id.required' => 'Please select author.',
        'user_id.exists' => 'Selected user no longer exists.',
    ],

];
