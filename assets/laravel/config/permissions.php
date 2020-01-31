<?php

return [

    'groups' => [
        1 => [
            'permissions' => [
                'show' => [],
                'create' => ['show'],
                'edit' => ['show'],
                'delete' => ['show'],
                'all' => ['show', 'create', 'edit', 'delete']
            ],
            'areas' => [
                'articles', 'article-categories', 'pages',
                'photogalleries', 'menu', 'users', 'languages', 'widgets',
                'article-flags', 'roles', 'redirects'
            ]
        ],
        2 => [
            'permissions' => [
                'show' => [],
                'toggle' => ['show'],
                'install' => ['show'],
                'all' => ['show', 'toggle', 'install']
            ],
            'areas' => ['modules']
        ],
        3 => [
            'permissions' => [
                'edit-layout' => ['edit-layout']
            ],
            'areas' => ['ge']
        ]
    ],

];
