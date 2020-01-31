<?php
// UniversalModule
Route::group(['prefix' => 'universal-module'], function () {
    Route::get('previews',[
        'as' => 'admin.universalmodule.previews',
        'uses' => 'UniversalModuleController@loadPreviews',
    ]);

    Route::get('new-form/{prefix}', [
        'as' => 'admin.universalmodule.newForm',
        'uses' => 'UniversalModuleController@showCreateForm',
    ]);

    Route::get('edit-form/{id?}', [
        'as' => 'admin.universalmodule.editForm',
        'uses' => 'UniversalModuleController@showEditForm',
    ]);

    Route::get('/{prefix}', [
        'as' => 'admin.universalmodule.index',
        'uses' => 'UniversalModuleController@index'
    ]);

    Route::post('validate-and-preview', [
        'as' => 'admin.universalmodule.validateAndPreview',
        'uses' => 'UniversalModuleController@validateAndPreview',
    ]);

    Route::get('{prefix}/create', [
        'as' => 'admin.universalmodule.create',
        'uses' => 'UniversalModuleController@create',
    ]);

    Route::post('{prefix}/store', [
        'as' => 'admin.universalmodule.store',
        'uses' => 'UniversalModuleController@store',
    ]);

    Route::get('{prefix}/{item}/edit', [
        'as' => 'admin.universalmodule.edit',
        'uses' => 'UniversalModuleController@edit',
    ]);

    Route::post('{prefix}/{item}/update', [
        'as' => 'admin.universalmodule.update',
        'uses' => 'UniversalModuleController@update',
    ]);

    Route::delete('{prefix}/{item}/delete', [
        'as' => 'admin.universalmodule.delete',
        'uses' => 'UniversalModuleController@delete',
    ]);

    Route::post('{prefix}/{item}/toggle', [
        'as' => 'admin.universalmodule.toggle',
        'uses' => 'UniversalModuleController@toggle',
    ]);

});
