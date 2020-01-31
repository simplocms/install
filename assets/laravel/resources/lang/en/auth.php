<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'These credentials do not match our records.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',

    'admin_lock' => [
        'help_text' => 'Enter password for unlocking your account.',
        'password_placeholder' => 'Your password',
        'btn_unlock' => 'Unlock'
    ],

    'login_form' => [
        'title' => 'Sign in',
        'subtitle' => 'Fill in your credentials',
        'remember_label' => 'Remember',
        'forgotten_password' => 'Forgotten password',
        'username_placeholder' => 'Username',
        'password_placeholder' => 'Password',
        'btn_login' => 'Sign in',
        'cookie_consent' => 'By signing in, you agree to storing cookies.',

        'messages' => [
            'username.required' => 'Please enter your username.',
            'password.required' => 'Please enter your password.',
        ]
    ],

    'password_form' => [
        'title' => 'Forgotten password',
        'subtitle' => 'Password recovery instructions will be sent to your email',
        'email_placeholder' => 'E-mail',
        'btn_submit' => 'Reset password',
        'btn_login' => 'Sign in',
    ],

    'reset_form' => [
        'title' => 'Reset password',
        'subtitle' => 'Enter an email and a new password for your account',
        'email_placeholder' => 'E-mail',
        'password_placeholder' => 'Password',
        'password_confirmation_placeholder' => 'Password confirmation',
        'btn_submit' => 'Reset password',
        'btn_login' => 'Sign in',

        'messages' => [
            'password.confirmed' => 'The entered passwords do not match.',
            'password.min' => 'The password must have at least: min characters.'
        ],

        'notification_reset' => 'The password for your account has been changed.'
    ]

];
