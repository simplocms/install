<?php

Route::group(['prefix' => 'modules'], function () {
    Route::get('/', [
        'as' => 'admin.modules',
        'uses' => 'ModulesController@index'
    ]);

    Route::post('{module}/toggle', [
        'as' => 'admin.modules.toggle',
        'uses' => 'ModulesController@toggleEnabled'
    ]);

    Route::post('{name}/install', [
        'as' => 'admin.modules.install',
        'uses' => 'ModulesController@install'
    ]);

    Route::post('{module}/uninstall', [
        'as' => 'admin.modules.uninstall',
        'uses' => 'ModulesController@uninstall'
    ]);
});