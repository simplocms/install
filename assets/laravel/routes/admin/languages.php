<?php

Route::group(['prefix' => 'languages'], function () {
    Route::get('/', [
        'as' => 'admin.languages.index',
        'uses' => 'LanguagesController@index'
    ]);

    // Create
    Route::get('create', [
        'as' => 'admin.languages.create',
        'uses' => 'LanguagesController@create'
    ]);
    Route::post('store', [
        'as' => 'admin.languages.store',
        'uses' => 'LanguagesController@store',
    ]);

    // Edit
    Route::get('{language}/edit', [
        'as' => 'admin.languages.edit',
        'uses' => 'LanguagesController@edit'
    ]);
    Route::post('{language}/update', [
        'as' => 'admin.languages.update',
        'uses' => 'LanguagesController@update',
    ]);

    // Delete
    Route::delete('{language}/delete', [
        'as' => 'admin.languages.delete',
        'uses' => 'LanguagesController@delete'
    ]);

    // Actions
    Route::post('{language}/switch', [
        'as' => 'admin.languages.toggle',
        'uses' => 'LanguagesController@toggleEnabled'
    ]);

    Route::post('{language}/set-default', [
        'as' => 'admin.languages.default',
        'uses' => 'LanguagesController@toggleDefault'
    ]);

    // language settings
    Route::post('settings', [
        'as' => 'admin.languages.settings',
        'uses' => 'LanguagesController@updateSettings'
    ]);
});