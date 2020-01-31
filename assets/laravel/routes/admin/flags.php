<?php

Route::group(['prefix' => 'article-flags'], function ($route) {
    // index
    Route::get('/', [
        'as' => 'admin.article_flags.index',
        'uses' => 'ArticleFlagsController@index'
    ]);

    // forms
    Route::get('create', [
        'as' => 'admin.article_flags.create',
        'uses' => 'ArticleFlagsController@create'
    ]);
    Route::get('{article_flag}/edit', [
        'as' => 'admin.article_flags.edit',
        'uses' => 'ArticleFlagsController@edit'
    ]);

    // actions
    Route::post('create', [
        'as' => 'admin.article_flags.store',
        'uses' => 'ArticleFlagsController@store'
    ]);
    Route::post('{article_flag}/update', [
        'as' => 'admin.article_flags.update',
        'uses' => 'ArticleFlagsController@update'
    ]);
    Route::delete('{article_flag}/delete', [
        'as' => 'admin.article_flags.delete',
        'uses' => 'ArticleFlagsController@delete'
    ]);
});