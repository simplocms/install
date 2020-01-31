<?php

// Authentification
Route::group( [ 'namespace' => 'Auth' ] ,function () {
    Route::post('logout', [
        'as' => 'admin.auth.logout',
        'uses' => 'LoginController@logout'
    ]);

    // login
    Route::get('login', [
        'as' => 'admin.auth.login',
        'uses' => 'LoginController@showLoginForm'
    ]);

    Route::post('login', [
        'uses' => 'LoginController@login'
    ]);

    Route::group([ 'prefix' => 'password' ], function (){
        // forgot password
        Route::post('email', [
            'as' => 'admin.password.email',
            'uses' => 'ForgotPasswordController@sendResetLinkEmail'
        ]);

        // forgot password
        Route::get('forgot', [
            'as' => 'admin.password.forgot',
            'uses' => 'ForgotPasswordController@showLinkRequestForm'
        ]);

        Route::post('forgot', [
            'uses' => 'ForgotPasswordController@sendResetLinkEmail'
        ]);

        // reset password
        Route::get('reset/{token}', [
            'as' => 'password.reset',
            'uses' => 'ResetPasswordController@showResetForm'
        ]);

        Route::post('reset/{token}', [
            'uses' => 'ResetPasswordController@reset'
        ]);

    });
});