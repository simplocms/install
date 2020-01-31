<?php

return [

    'grideditor_title' => 'Link',

    'grid_editor_form' => [
        'labels' => [
            'text' => 'Text',
            'custom_url' => 'Custom URL',
            'model_type' => 'Link to',
            'view' => 'View',
            'attributes' => 'HTML attributes',
        ],
        'btn_add_attribute' => 'Add attribute',
        'model_types' => [
            'page' => 'Page',
            'article' => 'Article',
            'photogallery' => 'Photogallery',
        ],
        'default_view' => 'Default'
    ],

    'preview' => [
        'title' => 'Link',
        'text' => 'Text',
    ],

    'validation_messages' => [
        'text.required' => 'Please enter text of the link.',
        'url.required_if' => 'please enter custom URL.',
        'model_type.required_without' => 'Please select type of the link.',
        'model_type.in' => 'Invalid link type.',

        // page
        'page_id.required_if' => 'Please select the page to link to.',
        'page_id.exists' => 'The selected page no longer exists.',

        // article
        'article_id.required_if' => 'Please select the article to link to.',
        'article_id.exists' => 'The selected article no longer exists.',

        // photogallery
        'photogallery_id.required_if' => 'Please select the photogallery to link to.',
        'photogallery_id.exists' => 'The selected photogallery no longer exists.',
    ]

];
