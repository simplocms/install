<?php

return [

    'tabs' => [
        'details' => 'Basic information',
        'role' => 'Roles',
    ],

    'labels' => [
        'username' => 'Username',
        'firstname' => 'Name',
        'lastname' => 'Surname',
        'email' => 'Email',
        'enabled' => 'Is enabled',

        'password' => 'Password',
        'password_confirmation' => 'Password confirmation',
    ],

    'role_columns' => [
        'name' => 'Role name',
        'description' => 'Description'
    ],

    'btn_update' => 'Update',
    'btn_create' => 'Create',
    'btn_cancel' => 'Cancel',

    'messages' => [
        'firstname.required' => 'Please enter name of the user.',
        'firstname.max' => 'Maximal length of the name is :max characters.',
        'lastname.required' => 'Please enter surname of the user.',
        'lastname.max' => 'Maximal length of the surname is :max characters.',
        'username.required' => 'Please enter username of the user.',
        'username.max' => 'Maximal length of the username is :max characters.',
        'username.unique' => 'This username is already used by another user.',
        'email.required' => 'Please enter email of the user.',
        'email.email' => 'Invalid email.',
        'email.unique' => 'This email is already used by another user.',
        'role.*.exists' => 'Selected role does not exist anymore. Try refreshing the page.',
        'password.required' => 'Please enter password for this user.',
        'password.min' => 'Minimal length of the password is :min characters.',
        'password.confirmed' => 'Password confirmation does not match entered password.',
    ],

];
