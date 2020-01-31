<?php

return [

    'grideditor_title' => 'Articles list',

    'grid_editor_form' => [
        'no_views' => 'No views are available for this module!',
        'labels' => [
            'view' => 'View',
            'category_ids' => 'For categories',
            'tag_ids' => 'For tags',
            'sort_type' => 'Order by',
            'limit' => 'Limit (insert 0 for all)',
        ],

        'all_categories' => 'All categories',
        'all_tags' => 'All tags',

        'messages' => [
            'view.required' => 'Please select view for rendering list of articles.',
            'view.in' => 'Invalid view. Please select different one.',
            'category_ids.array' => 'ERR: Input must be array.',
            'category_ids_exists' => 'Some of selected categories does not exist.',
            'flag_ids.array' => 'ERR: Input must be array.',
            'flag_ids_exists' => 'Some of selected tags does not exist.',
            'sort_type.required' => 'Please select type of ordering.',
            'sort_type.in' => 'Invalid type. Please select different one.',
            'limit.int' => 'Please enter number.',
            'limit.min' => 'Limit must be greater than 0. Insert 0 if you do not want any limit.',
        ]
    ],

    'view_not_exist' => 'View ":name" does not exist!',

    'preview' => [
        'not_found' => 'View does not exist!',
        'limit' => '{0} all articles|{1} :count article|[2,*] :count articles',
        'labels' => [
            'view' => 'View',
            'category_ids' => 'For categories',
            'tag_ids' => 'For tags',
            'sort_type' => 'Order by',
            'limit' => 'Show',
        ],
    ],

    'sort_types' => [
        'title' => 'Title',
        'publish_date' => 'Publish date (newest)',
        'random' => 'Random',
    ]

];
