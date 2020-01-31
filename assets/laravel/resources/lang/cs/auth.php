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

    'failed' => 'Přihlašovací údaje neodpovídají.',
    'throttle' => 'Příliš mnoho pokusů. Zkuste to znovu za :seconds sekund.',

    'admin_lock' => [
        'help_text' => 'Zadejte heslo pro odemknutí účtu.',
        'password_placeholder' => 'Vaše heslo',
        'btn_unlock' => 'Odemknout'
    ],

    'login_form' => [
        'title' => 'Přihlášení k účtu',
        'subtitle' => 'Vyplňte své přihlašovací údaje',
        'remember_label' => 'Zapamatovat',
        'forgotten_password' => 'Zapomenuté heslo',
        'username_placeholder' => 'Přihlašovací jméno',
        'password_placeholder' => 'Heslo',
        'btn_login' => 'Přihlásit se',
        'cookie_consent' => 'Přihlášením souhlasíte s ukládáním souborů cookie.',

        'messages' => [
            'username.required' => 'Zadejte prosím uživatelské jméno.',
            'password.required' => 'Zadejte prosím heslo.',
        ]
    ],

    'password_form' => [
        'title' => 'Zapomenuté heslo',
        'subtitle' => 'Na zadaný email budou zaslány instrukce pro obnovení hesla',
        'email_placeholder' => 'E-mail',
        'btn_submit' => 'Obnovit heslo',
        'btn_login' => 'Přihlásit se',
    ],

    'reset_form' => [
        'title' => 'Reset hesla',
        'subtitle' => 'Zadejte email a nové heslo k účtu',
        'email_placeholder' => 'E-mail',
        'password_placeholder' => 'Heslo',
        'password_confirmation_placeholder' => 'Heslo znovu pro potvrzení',
        'btn_submit' => 'Resetovat heslo',
        'btn_login' => 'Přihlásit se',

        'messages' => [
            'password.confirmed' => 'Zadaná hesla se neshodují.',
            'password.min' => 'Heslo musí mít alespoň :min znaků.'
        ],

        'notification_reset' => 'Heslo k vašemu účtu bylo změněno.'
    ]

];
