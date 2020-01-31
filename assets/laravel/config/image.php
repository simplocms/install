<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Image Driver
    |--------------------------------------------------------------------------
    |
    | Intervention Image supports "GD Library" and "Imagick" to process images
    | internally. You may choose one of them according to your PHP
    | configuration. By default PHP's "GD Library" implementation is used.
    |
    | Supported: "gd", "imagick"
    |
    */

    'driver' => extension_loaded('imagick') ? 'imagick' : 'gd',

    /*
     * Array of MIME types of images that can be processed by GD library.
     */
    'gd_processable_types' => [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp'
    ],

    /*
     * Array of MIME types of images that can be processed by Imagick library.
     */
    'imagick_processable_types' => [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
        'image/tiff', 'image/bmp', 'image/x-icon', 'image/vnd.adobe.photoshop',
        'image/x-ms-bmp', 'image/svg+xml'
    ],

    /*
     * Array of MIME types of images that can used within <img /> element on the web.
     */
    'selectable_image_mime_types' => [
        'image/png', 'image/jpeg', 'image/gif', 'image/bmp', 'image/webp',
        'image/x-icon', 'image/x-ms-bmp', 'image/svg+xml'
    ],

];
