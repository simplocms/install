<?php

return [

    'grideditor_title' => 'Image',

    'grid_editor_form' => [
        'labels' => [
            'is_sized' => 'Image size',
            'resolution' => 'Resolution',
            'alt' => 'Alternative text for the image (what is in the picture)',
            'img_class' => 'Class of <img> element',
        ],
        'size_options' => [
            'automatic' => 'Automatic',
            'manual' => 'Manual',
        ],
    ],

    'validation_messages' => [
        'image_id.required' => 'Please select the image.',
        'alt.required' => 'Please enter an alternative text for the image.',
        'is_sized.required' => 'Choose the image size.',

        'width.required' => 'Please enter the width of the image.',
        'width.min' => 'The minimum width of the image is :min px.',
        'width.integer' => 'The width must be an integer.',

        'height.required' => 'Please enter the height of the image.',
        'height.min' => 'The minimum image height is :min px.',
        'height.integer' => 'The height must be an integer.',
    ]
];
