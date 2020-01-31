<?php

Route::group([
    'middleware' => 'web',
    'prefix' => 'admin/module/articleslist',
    'namespace' => 'Modules\ArticlesList\Http\Controllers'
], function() {

    Route::get('create', [
        'as' => 'module.articleslist.create',
        'uses' => 'ModuleController@create',
    ]);

});
