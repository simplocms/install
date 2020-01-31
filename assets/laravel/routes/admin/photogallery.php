<?php

Route::group(['prefix' => 'photogalleries'], function () {
    Route::get('/', [
        'as' => 'admin.photogalleries',
        'uses' => 'PhotogalleriesController@index',
    ]);

    Route::get('create', [
        'as' => 'admin.photogalleries.create',
        'uses' => 'PhotogalleriesController@create',
    ]);

    Route::post('upload-photo/{photogallery?}', [
        'as' => 'admin.photogalleries.upload_photo',
        'uses' => 'PhotogalleriesController@uploadPhoto',
    ]);

    Route::get('{photogallery}/edit', [
        'as' => 'admin.photogalleries.edit',
        'uses' => 'PhotogalleriesController@edit',
    ]);

    Route::post('create', [
        'as' => 'admin.photogalleries.store',
        'uses' => 'PhotogalleriesController@store',
    ]);

    Route::post('{photogallery}/edit', [
        'as' => 'admin.photogalleries.update',
        'uses' => 'PhotogalleriesController@update',
    ]);

    Route::delete('{photogallery}', [
        'as' => 'admin.photogalleries.delete',
        'uses' => 'PhotogalleriesController@delete',
    ]);

    // Photo

    Route::get('photo/list/{photogallery?}', [
        'as' => 'admin.photogalleries.photo.list',
        'uses' => 'PhotogalleriesController@photoList',
    ]);

    Route::post('photo/update/{photogallery?}', [
        'as' => 'admin.photogalleries.photo.update',
        'uses' => 'PhotogalleriesController@updatePhoto',
    ]);

    Route::delete('photo/{photo}/delete', [
        'as' => 'admin.photogalleries.photo.delete',
        'uses' => 'PhotogalleriesController@deletePhoto',
    ]);
});