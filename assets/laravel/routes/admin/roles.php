<?php

Route::group(['prefix' => 'roles'], function () {
    Route::get('/', [
        'as' => 'admin.roles',
        'uses' => 'RolesController@index',
    ]);

    Route::get('create', [
        'as' => 'admin.roles.create',
        'uses' => 'RolesController@create',
    ]);
    Route::post('create', [
        'as' => 'admin.roles.store',
        'uses' => 'RolesController@store',
    ]);

    Route::get('{role}/edit', [
        'as' => 'admin.roles.edit',
        'uses' => 'RolesController@edit',
    ]);
    Route::post('{role}/edit', [
        'as' => 'admin.roles.update',
        'uses' => 'RolesController@update',
    ]);

    Route::delete('{role}', [
        'as' => 'admin.roles.delete',
        'uses' => 'RolesController@delete',
    ]);

    Route::post('{role}/toggle', [
        'as' => 'admin.roles.toggle',
        'uses' => 'RolesController@toggle',
    ]);
});