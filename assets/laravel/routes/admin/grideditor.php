<?php

Route::group(['prefix' => 'grideditor/modules'], function () {
    Route::get('edit/{entity?}', [
        'as' => 'admin.grideditor.modules.edit',
        'uses' => 'ModulesController@edit',
    ]);

    Route::post('validate-and-preview/{entity?}', [
        'as' => 'admin.grideditor.modules.validateAndPreview',
        'uses' => 'ModulesController@validateAndPreview',
    ]);

    Route::get('entity/{entity?}', [
        'as' => 'admin.grideditor.modules.entity',
        'uses' => 'ModulesController@entity',
    ]);

    Route::get('previews',[
        'as' => 'admin.grideditor.modules.previews',
        'uses' => 'ModulesController@loadPreviews',
    ]);
});