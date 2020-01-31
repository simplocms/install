<?php

Route::group(['prefix' => 'users'], function () {
    Route::get('/', [
        'as' => 'admin.users',
        'uses' => 'UsersController@index',
    ]);

    // Create
    Route::get('create', [
        'as' => 'admin.users.create',
        'uses' => 'UsersController@create'
    ]);
    Route::post('create', [
        'as' => 'admin.users.store',
        'uses' => 'UsersController@store',
    ]);

    // Edit
    Route::get('{user}/edit', [
        'as' => 'admin.users.edit',
        'uses' => 'UsersController@edit',
    ]);

    Route::post('{user}/edit', [
        'as' => 'admin.users.update',
        'uses' => 'UsersController@update',
    ]);

    // Toggle
    Route::post('{user}/toggle', [
        'as' => 'admin.users.toggle',
        'uses' => 'UsersController@toggle',
    ]);

    // Delete
    Route::delete('{user}', [
        'as' => 'admin.users.delete',
        'uses' => 'UsersController@delete',
    ]);
});