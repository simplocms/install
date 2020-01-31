<?php

return [

    // Form for password change //
    'password' => [
        'title' => 'Změna hesla',
        'labels' => [
            'password' => 'Aktuální heslo',
            'new_password' => 'Nové heslo',
            'verify_new_password' => 'Nové heslo pro ověření',
        ],
        'button_submit' => 'Změnit heslo',
        'messages' => [
            'password.required' => 'Vložte prosím své aktuální heslo',
            'new_password.required' => 'Vložte prosím nové heslo.',
            'new_password.min' => 'Heslo musí mít alespoň :min znaků',
            'new_password.regex' => 'Heslo musí být kombinací číslic a malých a velkých písmen.',
            'verify_new_password.required' => 'Vložte nové heslo znovu pro ověření jeho správnosti.',
            'verify_new_password.same' => 'Hesla se neshodují.',
            'invalid_password' => 'Zadejte prosím platné heslo.'
        ],
    ],

    // Form for general changes //
    'general' => [
        'title' => 'Obecná nastavení',
        'labels' => [
            'firstname' => 'Jméno',
            'lastname' => 'Příjmení',
            'email' => 'E-mail',
            'username' => 'Přihlašovací jméno',
            'position' => 'Pracovní pozice',
            'locale' => 'Jazyk administrace',
            'about' => 'Něco o mě',
            'twitter_account' => 'Název twitter účtu',
        ],
        'buttons' => [
            'choose_image' => 'Vybrat obrázek',
            'change_image' => 'Změnit obrázek',
            'remove_image' => 'Odebrat obrázek',
            'submit' => 'Uložit změny',
        ],
    ]

];
