<?php

return [

    // Form for password change //
    'password' => [
        'title' => 'Change password',
        'labels' => [
            'password' => 'Current password',
            'new_password' => 'New password',
            'verify_new_password' => 'Confirm new password',
        ],
        'button_submit' => 'Change password',
        'messages' => [
            'password.required' => 'Please enter your current password',
            'new_password.required' => 'Please enter new password.',
            'new_password.min' => 'The password must be at least :min characters long.',
            'new_password.regex' => 'The password must be a combination of upper and lower case letters and numbers.',
            'verify_new_password.required' => 'Enter the password again to verify the correctness.',
            'verify_new_password.same' => 'Passwords do not match.',
            'invalid_password' => 'Please enter a valid current password.'
        ],
    ],

    // Form for general changes //
    'general' => [
        'title' => 'General settings',
        'labels' => [
            'firstname' => 'Name',
            'lastname' => 'Surname',
            'email' => 'E-mail',
            'username' => 'Login',
            'position' => 'Position name',
            'locale' => 'Admin language',
            'about' => 'About me',
            'twitter_account' => 'Twitter account name',
        ],
        'buttons' => [
            'choose_image' => 'Choose image',
            'change_image' => 'Change image',
            'remove_image' => 'Remove image',
            'submit' => 'Save changes',
        ],
    ]

];
