<?php

Route::group(['middleware' => 'web', 'prefix' => 'admin/module/image', 'namespace' => 'Modules\Image\Http\Controllers'], function()
{
    Route::get('create', [
        'as' => 'module.image.create',
        'uses' => 'ModuleController@create',
    ]);

    Route::post('validate-and-preview', [
        'uses' => 'ModuleController@validateAndPreview',
    ]);
});
