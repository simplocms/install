<?php

Route::group(['prefix' => 'dashboard'], function () {
    Route::get('/', [
        'as' => 'admin.dashboard',
        'uses' => 'DashboardController@index'
    ]);

    // get chart data
    Route::get('/chart-data', [
        'as' => 'admin.dashboard.chartData',
        'uses' => 'DashboardController@getChartData'
    ]);

    // log off from ga
    Route::post('/off', [
        'as' => 'admin.dashboard.off',
        'uses' => 'DashboardController@logOff'
    ]);

    // authorization
    Route::get('/authorization', [
        'as' => 'admin.dashboard.authorization',
        'uses' => 'DashboardController@showAuthorizationForm'
    ]);
    Route::post('/authorization', [
        'uses' => 'DashboardController@saveGAToken',
    ]);

    // choose profile
    Route::get('/profiles', [
        'as' => 'admin.dashboard.profiles',
        'uses' => 'DashboardController@showProfilesList'
    ]);
    Route::post('/profiles', [
        'uses' => 'DashboardController@saveSelectedProfile',
    ]);
});