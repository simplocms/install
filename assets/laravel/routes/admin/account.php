<?php

Route::group(['prefix' => 'account'], function () {
    Route::get('/edit', [
        'as' => 'admin.account.edit',
        'uses' => 'AccountController@edit',
    ]);

    Route::post('/edit', [
        'uses' => 'AccountController@update',
    ]);

    Route::post('/password-change', [
        'as' => 'admin.account.password.change',
        'uses' => 'AccountController@changePassword',
    ]);
});