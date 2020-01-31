<?php

return [

    'header_title' => 'Media Library',

    'root_directory' => 'Root',

    'directories' => [
        'prompt_create_text' => 'Enter name of the directory:',
        'default_folder_name' => 'New Folder',

        'btn_rename' => 'Rename',
        'prompt_rename_text' => 'Enter new name of the directory:',

        'btn_delete' => 'Delete',
        'confirm_delete' => [
            'title' => 'Delete directory',
            'text' => 'Do you really want to delete directory ":name" and all its content?',
            'confirm' => 'Delete',
            'cancel' => 'Cancel'
        ],
    ],

    'search_title' => 'Searching ":phrase"',

    'sort_options' => [
        'name-ASC' => 'Sort by name (A-Z)',
        'name-DESC' => 'Sort by name (Z-A)',
        'updated_at-ASC' => 'Last change (from oldest)',
        'updated_at-DESC' => 'Last change (from recent)',
    ],

    'search_placeholder' => 'Search in files and folders',

    'navbar' => [
        'btn_create_folder' => 'New directory',
        'btn_select_files' => 'Upload files',
        'btn_delete_files' => 'Delete selected files',
    ],

    'subdirectories_text' => 'Subdirectories',
    'files_text' => 'Files',

    'file' => [
        'uploading' => 'Uploading',
        'upload_failed' => 'Upload failed.',
        'btn_upload_again' => 'Try again',
        'btn_cancel_upload' => 'Cancel upload',
        'btn_detail' => 'Show detail',
        'btn_select' => 'Select file',
        'btn_rename' => 'Rename',
        'btn_override' => 'Replace with file',
        'btn_delete' => 'Delete',
        'btn_save_description' => 'Save',
        'btn_change_resolution' => 'Change resolution',
        'btn_rotate_left_90' => 'Rotate 90° left',
        'btn_rotate_right_90' => 'Rotate 90° right',

        'confirm_delete' => [
            'title' => 'Delete file|Delete selected files',
            'text' => 'Do you really want to delete file ":name"?|Do you really want to delete selected files (total :count)?',
            'confirm' => 'Delete',
            'cancel' => 'Cancel'
        ],

        'size' => 'Size',
        'resolution' => 'Resolution',
        'last_change_at' => 'Last change',
        'url' => 'URL',
        'copy_url' => 'Copy URL',
        'description' => 'Description',
        'actions' => 'Actions',
    ],

    'resize_modal' => [
        'title' => 'Resolution change',
        'info_text' => 'Aspect ratio of the image is always preserved.',
        'label' => 'New resolution',
        'btn_save' => 'Save changes',
        'btn_cancel' => 'Close',
    ],

    'file_drop_text' => 'Drop files for upload',

    'file_selector' => [
        'image_preview' => 'Image preview',
        'accepted' => 'Accepted',
        'no_file_selected' => 'No file selected',
        'video_not_supported' => 'Your browser does not support the video tag.',
        'video_support_notice' => 'Best support across browsers you will get with MP4 video format, followed by OGG and WebM.',

        'btn_change_image' => 'Change image',
        'btn_select_image' => 'Select image',
        'btn_change_file' => 'Change file',
        'btn_select_file' => 'Select file',
        'btn_remove_image' => 'Remove image',
        'btn_remove_file' => 'Remove file',
    ],

    'validation_messages' => [
        'invalid_image' => 'Selected file is not supported image.',
        'invalid_file' => 'Selected file type is not allowed.',
    ],

    'cache_driver_warning' => "Cache driver is set to 'array', uploading files larger than :size wont work correctly.",

];
