<?php

Route::group(['prefix' => 'menu'], function () {
    Route::get('/', [
        'as' => 'admin.menu',
        'uses' => 'MenuController@index'
    ]);

    Route::get('pages-tree', [
        'as' => 'admin.menu.pages',
        'uses' => 'MenuController@pagesTree'
    ]);

    Route::get('categories-tree', [
        'as' => 'admin.menu.categories',
        'uses' => 'MenuController@categoriesTree'
    ]);

    Route::post('/', [
        'as' => 'admin.menu.store',
        'uses' => 'MenuController@store'
    ]);

    Route::post('ulozit', [
        'as' => 'admin.menu.update',
        'uses' => 'MenuController@update'
    ]);

    Route::delete('/', [
        'as' => 'admin.menu.delete',
        'uses' => 'MenuController@delete'
    ]);
});