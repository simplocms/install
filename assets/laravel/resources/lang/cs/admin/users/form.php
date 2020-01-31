<?php

return [

    'tabs' => [
        'details' => 'Základní informace',
        'role' => 'Role',
    ],

    'labels' => [
        'username' => 'Přihlašovací jméno',
        'firstname' => 'Křestní jméno',
        'lastname' => 'Příjmení',
        'email' => 'Email',
        'enabled' => 'Aktivován',

        'password' => 'Heslo',
        'password_confirmation' => 'Heslo pro ověření',
    ],

    'role_columns' => [
        'name' => 'Název role',
        'description' => 'Popis'
    ],

    'btn_update' => 'Upravit',
    'btn_create' => 'Vytvořit',
    'btn_cancel' => 'Zrušit',

    'messages' => [
        'firstname.required' => 'Zadejte prosím křestní jméno uživatele.',
        'firstname.max' => 'Maximální délka křestního jména je :max znaků.',
        'lastname.required' => 'Zadejte prosím příjmení uživatele.',
        'lastname.max' => 'Maximální délka příjmení je :max znaků.',
        'username.required' => 'Zadejte prosím přihlašovací jméno uživatele.',
        'username.max' => 'Maximální délka přihlašovacího jména je :max znaků.',
        'username.unique' => 'Toto přihlašovací jméno již používá jiný uživatel.',
        'email.required' => 'Zadejte prosím email uživatele.',
        'email.email' => 'Neplatný email.',
        'email.unique' => 'Tento email již používá jiný uživatel.',
        'role.*.exists' => 'Vybraná role již neexistuje. Zkuste obnovit stránku.',
        'password.required' => 'Zadejte prosím heslo uživatele.',
        'password.min' => 'Minimální délka hesla je :min znaků.',
        'password.confirmed' => 'Zadaná hesla se neshodují.',
    ],

];
