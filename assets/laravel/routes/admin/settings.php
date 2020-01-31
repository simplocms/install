<?php

Route::group(['prefix' => 'settings'], function () {
    Route::get('/',[
        'as' => 'admin.settings',
        'uses' => 'SettingsController@index',
    ]);

    Route::post('switch-theme/{name}',[
        'as' => 'admin.settings.switch_theme',
        'uses' => 'SettingsController@switchTheme',
    ]);

    Route::put('/',[
        'as' => 'admin.settings.update',
        'uses' => 'SettingsController@update',
    ]);
});
