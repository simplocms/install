<?php

Route::group(['prefix' => 'categories/{flag}'], function ($route) {
    // index
    Route::get('/', [
        'as' => 'admin.categories.index',
        'uses' => 'CategoryController@index',
    ]);

    // forms
    Route::get('create', [
        'as' => 'admin.categories.create',
        'uses' => 'CategoryController@create',
    ]);
    Route::get('{category}/edit', [
        'as' => 'admin.categories.edit',
        'uses' => 'CategoryController@edit',
    ]);

    // actions
    Route::post('create', [
        'as' => 'admin.categories.store',
        'uses' => 'CategoryController@store',
    ]);
    Route::post('{category}/update', [
        'as' => 'admin.categories.update',
        'uses' => 'CategoryController@update',
    ]);
    Route::delete('{category}/delete', [
        'as' => 'admin.categories.delete',
        'uses' => 'CategoryController@delete',
    ]);
});
