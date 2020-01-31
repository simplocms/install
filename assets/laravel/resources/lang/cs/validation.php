<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attribute musí být přijat.',
    'active_url' => ':attribute není platnou URL adresou.',
    'after' => ':attribute musí být datum po :date.',
    'alpha' => ':attribute může obsahovat pouze písmena.',
    'alpha_dash' => ':attribute může obsahovat pouze písmena, číslice, pomlčky a podtržítka. České znaky (á, é, í, ó, ú, ů, ž, š, č, ř, ď, ť, ň) nejsou podporovány.',
    'alpha_num' => ':attribute může obsahovat pouze písmena a číslice.',
    'array' => ':attribute musí být pole.',
    'before' => ':attribute musí být datum před :date.',
    'between' => [
        'numeric' => ':attribute musí být hodnota mezi :min a :max.',
        'file' => ':attribute musí být větší než :min a menší než :max Kilobytů.',
        'string' => ':attribute musí být delší než :min a kratší než :max znaků.',
        'array' => ':attribute musí obsahovat nejméně :min a nesmí obsahovat více než :max prvků.',
    ],
    'boolean' => ':attribute musí být true nebo false',
    'confirmed' => 'Pole ":attribute" nesouhlasí s potvrzením pole ":attribute"',
    'date' => ':attribute musí být platné datum.',
    'date_format' => ':attribute není platný formát data podle :format.',
    'different' => ':attribute a :other se musí lišit.',
    'digits' => ':attribute musí být :digits pozic dlouhé.',
    'digits_between' => ':attribute musí být dlouhé nejméně :min a nejvíce :max pozic.',
    'email' => ':attribute není platný formát.',
    'filled' => ':attribute musí být vyplněno.',
    'exists' => 'Zvolená hodnota pro :attribute není platná.',
    'image' => ':attribute musí být obrázek.',
    'in' => 'Zvolená hodnota pro :attribute je neplatná.',
    'integer' => ':attribute musí být celé číslo.',
    'ip' => ':attribute musí být platnou IP adresou.',
    'max' => [
        'numeric' => 'Pole ":attribute" musí být nižší než :max.',
        'file' => 'Pole ":attribute" musí být menší než :max Kilobytů.',
        'string' => 'Pole ":attribute" musí být kratší než :max znaků.',
        'array' => 'Pole ":attribute" nesmí obsahovat více než :max prvků.',
    ],
    'mimes' => ':attribute musí být jeden z následujících datových typů :values.',
    'min' => [
        'numeric' => 'Pole ":attribute" musí být větší než :min.',
        'file' => 'Pole ":attribute" musí být větší než :min Kilobytů.',
        'string' => 'Pole ":attribute" musí být delší než :min znaků.',
        'array' => 'Pole ":attribute" musí obsahovat více než :min prvků.',
    ],
    'not_in' => 'Zvolená hodnota pro :attribute je neplatná.',
    'numeric' => ':attribute musí být číslo.',
    'regex' => ':attribute nemá správný formát.',
    'required' => 'Pole ":attribute" je povinné.',
    'required_unless' => 'Pole ":attribute" je povinné, pokud pole :other má hodnotu/y :values.',
    'required_if' => ':attribute musí být vyplněno, pokud :other je :value.',
    'required_with' => ':attribute musí být vyplněno, pokud :values je vyplněno.',
    'required_with_all' => ':attribute musí být vyplněno, pokud :values je zvoleno.',
    'required_without' => ':attribute musí být vyplněno, pokud :values není vyplněno.',
    'required_without_all' => ':attribute musí být vyplněno, pokud není žádné z :values zvoleno.',
    'same' => ':attribute a :other se musí shodovat.',
    'size' => [
        'numeric' => ':attribute musí být přesně :size.',
        'file' => ':attribute musí mít přesně :size Kilobytů.',
        'string' => ':attribute musí být přesně :size znaků dlouhý.',
        'array' => ':attribute musí obsahovat právě :size prvků.',
    ],
    'timezone' => ':attribute musí být platná časová zóna.',
    'unique' => ':attribute musí být unikátní.',
    'url' => 'Formát :attribute je neplatný.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'url' => [
            'required_unless' => 'Pole "URL" je povinné, pokud se nejedná o Úvodní stranu'
        ],
        'model_type' => [
            'required_without' => 'Pole "Odkaz na" musí být vyplněno pokud není vybrána možnost "Vlastní url"'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'name' => "název",
        'title' => 'nadpis',
        'image' => 'obrázek',
        'firstname' => 'jméno',
        'lastname' => 'přijmení',
        'username' => 'přihlašovací jméno',
        'password' => 'heslo',
        'country_code' => 'kód státu',
        'language_code' => 'kód jazyka',
        'code' => 'kód',
        'url' => 'URL',
        'perex' => 'perex',
        'id' => 'indentifikátor',
        'email' => 'email',
        'alt' => 'alternativní text',
        'text' => 'text',
        'view' => 'šablona'
    ],

    /*
    |--------------------------------------------------------------------------
    | Seo Validation Messages
    |--------------------------------------------------------------------------
    */
    'seo' => [
        'seo_title.max' => 'Maximální délka SEO titulku by měla být :max znaků.',
        'seo_description.max' => 'Maximální délka SEO popisku by měla být :max znaků.',
    ],

    /*
    |--------------------------------------------------------------------------
    | OpenGraph Validation Messages
    |--------------------------------------------------------------------------
    */
    'open_graph' => [
        'open_graph.title.max' => 'Maximální délka OpenGraph titulku by měla být :max znaků.',
        'open_graph.description.max' => 'Maximální délka OpenGraph popisku by měla být :max znaků.',
        'open_graph.url.url' => 'Neplatná URL adresa.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback message for ExistsArrayRule
    |--------------------------------------------------------------------------
    */
    'exists_array_rule' => 'Některé záznamy neexistují.',

];
