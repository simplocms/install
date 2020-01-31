<?php

Route::group(['prefix' => 'widgets'], function () {
    Route::get('/', [
        'as' => 'admin.widgets.index',
        'uses' => 'WidgetsController@index',
    ]);
    Route::post('/', [
        'as' => 'admin.widgets.store',
        'uses' => 'WidgetsController@store',
    ]);

    Route::get('create', [
        'as' => 'admin.widgets.create',
        'uses' => 'WidgetsController@create',
    ]);

    Route::get('{widget}/edit', [
        'as' => 'admin.widgets.edit',
        'uses' => 'WidgetsController@edit',
    ]);
    Route::post('{widget}/edit', [
        'as' => 'admin.widgets.update',
        'uses' => 'WidgetsController@update',
    ]);

    Route::delete('{widget}/delete', [
        'as' => 'admin.widgets.delete',
        'uses' => 'WidgetsController@delete',
    ]);

    // Modules
    Route::group(['prefix' => 'entities'], function () {
        Route::get('edit/{entity?}', [
            'as' => 'admin.widgets.modules.edit',
            'uses' => 'ModulesController@edit',
        ]);

        Route::post('validate-and-preview/{entity?}', [
            'as' => 'admin.widgets.modules.validateAndPreview',
            'uses' => 'ModulesController@validateAndPreview',
        ]);

        Route::post('load',[
            'as' => 'admin.widgets.modules.load',
            'uses' => 'ModulesController@loadContents',
        ]);
    });
});