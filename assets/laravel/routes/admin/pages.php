<?php

Route::group(['prefix' => 'pages'], function () {
    Route::get('grid', [
        'uses' => 'PagesController@grid',
    ]);

    Route::get('/', [
        'as' => 'admin.pages.index',
        'uses' => 'PagesController@index',
    ]);
    Route::post('/', [
        'as' => 'admin.pages.store',
        'uses' => 'PagesController@store',
    ]);

    Route::get('create', [
        'as' => 'admin.pages.create',
        'uses' => 'PagesController@create',
    ]);

    Route::get('{page}/edit', [
        'as' => 'admin.pages.edit',
        'uses' => 'PagesController@edit',
    ]);
    Route::post('{pageId}/edit', [
        'as' => 'admin.pages.update',
        'uses' => 'PagesController@update',
    ]);

    Route::post('{page}/duplicate', [
        'as' => 'admin.pages.duplicate',
        'uses' => 'PagesController@duplicate',
    ]);

    Route::post('{page}/make-ab-test', [
        'as' => 'admin.pages.make_ab_test',
        'uses' => 'PagesController@makeABTest',
    ]);

    Route::post('{page}/stop-ab-test', [
        'as' => 'admin.pages.stop_ab_test',
        'uses' => 'PagesController@stopABTest',
    ]);

    Route::delete('{page}/delete', [
        'as' => 'admin.pages.delete',
        'uses' => 'PagesController@delete',
    ]);

    Route::get('{pageId}/switch-version/{version?}', [
        'as' => 'admin.pages.switch_version',
        'uses' => 'PagesController@switchVersion',
    ]);

    Route::post('{page}/ab-testing', [
        'as' => 'admin.pages.ab_testing',
        'uses' => 'PagesController@updateABTestCookie',
    ]);
});
