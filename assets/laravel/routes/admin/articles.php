<?php

Route::group(['prefix' => 'articles/{flag}'], function () {
    // GET: articles table
    Route::get('/', [
        'as' => 'admin.articles.index',
        'uses' => 'ArticlesController@index',
    ]);

    // GET: create article form
    Route::get('create', [
        'as' => 'admin.articles.create',
        'uses' => 'ArticlesController@create',
    ]);

    // GET: edit article form
    Route::get('{article}/edit', [
        'as' => 'admin.articles.edit',
        'uses' => 'ArticlesController@edit',
    ]);

    // POST: store article
    Route::post('/', [
        'as' => 'admin.articles.store',
        'uses' => 'ArticlesController@store',
    ]);

    // POST: update article
    Route::post('{article}', [
        'as' => 'admin.articles.update',
        'uses' => 'ArticlesController@update',
    ]);

    // DELETE: delete article
    Route::delete('{article}', [
        'as' => 'admin.articles.delete',
        'uses' => 'ArticlesController@delete',
    ]);

    // GET: categories tree
    Route::get(
        'categories-tree/{article?}', [
        'as' => 'admin.articles.categories_tree',
        'uses' => 'ArticlesController@categoriesTree',
    ]);

    // POST: duplicate article
    Route::post('{article}/duplicate', [
        'as' => 'admin.articles.duplicate',
        'uses' => 'ArticlesController@duplicate',
    ]);

    // GET: GridEditor version content
    Route::get('{article}/switch-version/{contentId?}', [
        'as' => 'admin.articles.getVersionContent',
        'uses' => 'ArticlesController@getVersionContent',
    ]);
});
