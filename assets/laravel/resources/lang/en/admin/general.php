<?php

return [
    'notifications' => [
        'language_switch_disabled' => 'This language is not allowed.',
        'language_changed' => 'Language was changed.',
        'validation_failed' => 'Changes could not be saved.',
    ],

    'flash_level' => [
        'danger' => 'Error!',
        'warning' => 'Warning',
        'info' => 'Information',
        'success' => 'Success'
    ],

    'photogallery' => [
        'title' => 'Photos',
        'btn_select_photos' => 'Select photos',
        'empty_table_text' => 'No photos uploaded.',

        'table_columns' => [
            'preview' => 'Preview',
            'description' => 'Description',
            'author' => 'Author',
            'info' => 'Information',
            'action' => 'Action',
        ],

        'table_row' => [
            'uploaded_at' => 'Uploaded at',
            'size' => 'Size',
            'resolution' => 'Resolution',
            'btn_remove' => 'Remove',
            'edit_description_title' => 'Description of the photo',
            'edit_author_title' => 'Author of the photo',
            'empty_description_text' => 'Empty',
            'empty_author_text' => 'Empty',
        ],
    ],

    'seo' => [
        'title' => 'SEO properties',
        'help_text' => 'Please fill in the following fields to improve quantity and quality of traffic to your website through organic search engine results.',

        'inputs' => [
            'title' => [
                'label' => 'SEO title',
                'info' => 'is the page title in the browser.'
            ],
            'description' => [
                'label' => 'SEO description',
                'info' => 'is the text that is displayed next to the page title in the search result.'
            ],
            'index' => [
                'label' => 'Index',
                'info' => 'determines whether the page should be indexed by the search engine.'
            ],
            'follow' => [
                'label' => 'Follow',
                'info' => 'determines whether the links on the page should be taken into consideration for computing ranks.'
            ],
            'sitemap' => [
                'label' => 'Show in sitemap'
            ]
        ]
    ],

    'open_graph' => [
        'title' => 'OpenGraph tags',
        'help_text' => 'The OpenGraph protocol enables any web page to become a rich object in a social graph. For instance, this is used on Facebook to allow any web page to have the same functionality as any other object on Facebook.',

        'inputs' => [
            'title' => [
                'label' => 'Title (og:title)',
                'info' => 'the title of your object as it should appear within the graph, e.g., "The Rock".'
            ],
            'description' => [
                'label' => 'Description (og:description)',
                'info' => 'a one to two sentence description of your object.'
            ],
            'type' => [
                'label' => 'Type (og:type)',
                'info' => 'the type of your object, e.g., "video.movie". Depending on the type you specify, other properties may also be required.'
            ],
            'url' => [
                'label' => 'Permanent URL (og:url)',
                'info' => 'the canonical URL of your object that will be used as its permanent ID in the graph, e.g. "http://www.imdb.com/title/tt0117500/".'
            ],
            'image' => [
                'label' => 'Image (og:image)',
                'info' => 'an image which should represent your object within the graph.'
            ],
        ]
    ],

    'twitter' => [
        'help_text' => 'On Twitter, your username, or handle, is your identity. For example <kbd>@BBCNews</kbd> or <kbd>@Google</kbd> are unique names of twitter accounts. Insert your username including <code>@</code>.',
        'account_validation' => 'Invalid format of twitter account name.',
    ],

    'publishing_states_component' => [
        'title' => 'Publish',
        'toggle_text' => 'Change date and time',
        'label_publish_at' => 'Publish at',
        'label_unpublish_at' => 'Unpublish at',
        'placeholder_date' => 'Select date',
        'placeholder_time' => 'Select time',
        'label_set_unpublish_at' => 'Set unpublishing date',
        'btn_set_current_time' => 'Insert current time',
        'since_text' => 'since',
        'until_text' => 'until'
    ],

    'publishing_states' => [
        'published' => 'Published',
        'unpublished' => 'Not published',
        'concept' => 'Concept',
    ],
];
