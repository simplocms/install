<?php

Route::group(['middleware' => 'web', 'prefix' => 'admin', 'namespace' => 'Admin'], function () {

    // Root
    Route::get('/', [
        'as' => 'admin',
        'uses' => 'DashboardController@index'
    ]);

    // Ping
    Route::get('ping', [
        'as' => 'admin.ping',
        'uses' => 'AdminController@ping'
    ]);

    // Turn off maintenance mode
    Route::post('maintenance-off', [
        'as' => 'admin.maintenance.off',
        'uses' => 'AdminController@turnOffMaintenanceMode'
    ]);

    // Language switch
    Route::get('switch/{language}', [
        'as' => 'admin.switch',
        'uses' => 'AdminController@switchLanguage'
    ]);

    foreach (\File::files(base_path('routes/admin')) as $file) {
        require $file;
    }
});

// Theme Development
Route::group(['middleware' => 'web', 'prefix' => '--html'], function () {
    Route::get('/', [
        'as' => 'theme_development.index',
        'uses' => 'ThemeDevelopmentController@showList'
    ]);

    Route::get('page/{name}', [
        'as' => 'theme_development.page',
        'uses' => 'ThemeDevelopmentController@showPage'
    ]);

    Route::get('component/{name}', [
        'as' => 'theme_development.component',
        'uses' => 'ThemeDevelopmentController@showComponent'
    ]);
});


// Resources routes
require 'resources.php';

// Homepage
Route::get('/{url?}', [
    'as' => 'homepage',
    'uses' => 'MainController@index',
    'middleware' => ['web', 'front_web']
])->where('url', '(.*)');
