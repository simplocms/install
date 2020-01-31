<?php

Route::group(['prefix' => 'redirects', 'as' => 'admin.redirects.'], function () {
    // GET: index with table
    Route::get('/', [
        'as' => 'index',
        'uses' => 'RedirectsController@index',
    ]);

    // POST: store redirect
    Route::post('/', [
        'as' => 'store',
        'uses' => 'RedirectsController@store',
    ]);

    // GET: create form
    Route::get('create', [
        'as' => 'create',
        'uses' => 'RedirectsController@create',
    ]);

    // GET: edit form
    Route::get('{redirect}/edit', [
        'as' => 'edit',
        'uses' => 'RedirectsController@edit',
    ])->where('redirect', '(.*)');

    // PUT: update redirect
    Route::put('{redirect}/edit', [
        'uses' => 'RedirectsController@update',
    ])->where('redirect', '(.*)');

    // DELETE: delete redirect
    Route::delete('{redirect}/delete', [
        'as' => 'delete',
        'uses' => 'RedirectsController@delete',
    ])->where('redirect', '(.*)');

    // GET: export redirects
    Route::get('export', [
        'as' => 'export',
        'uses' => 'RedirectsController@export',
    ]);

    // GET: import example
    Route::get('import-example', [
        'as' => 'import_example',
        'uses' => 'RedirectsController@importExample',
    ]);

    // GET: bulk create form
    Route::get('bulk-create', [
        'as' => 'bulk_create',
        'uses' => 'RedirectsController@bulkCreate',
    ]);

    // POST: bulk store redirects
    Route::post('bulk-create', [
        'uses' => 'RedirectsController@bulkStore',
    ]);
});
