<?php

Route::group(['prefix' => 'media-library'], function () {
    Route::get('/', [
        'as' => 'admin.media',
        'uses' => 'MediaLibraryController@showMediaLibrary'
    ]);

    // GET: Directory tree
    Route::get('tree', [
        'as' => 'admin.media.tree',
        'uses' => 'MediaLibraryController@getDirectoryTree',
    ]);

    // GET: File detail
    Route::get('files/{file}', [
        'as' => 'admin.media.files.detail',
        'uses' => 'MediaLibraryController@getFileDetail',
    ]);

    // GET: Directory contents
    Route::get('directories/contents/{directory?}', [
        'as' => 'admin.media.directories.contents',
        'uses' => 'MediaLibraryController@getDirectoryContents',
    ]);

    // GET: search
    Route::get('search', [
        'as' => 'admin.media.search',
        'uses' => 'MediaLibraryController@getSearchContent',
    ]);

    // POST: Upload and store file
    Route::post('upload/{directory?}', [
        'as' => 'admin.media.directories.upload',
        'uses' => 'MediaLibraryController@uploadAndStoreFile',
    ]);

    // DELETE: Cancel upload
    Route::delete('upload/{directory?}', [
        'uses' => 'MediaLibraryController@cancelUpload',
    ]);

    // POST: Upload and override file
    Route::post('files/{file}/override', [
        'as' => 'admin.media.files.override',
        'uses' => 'MediaLibraryController@uploadAndOverrideFile',
    ]);

    // POST: Create directory
    Route::post('directories/{directory?}', [
        'as' => 'admin.media.directories.create',
        'uses' => 'MediaLibraryController@storeDirectory',
    ]);

    // PUT: Update file
    Route::put('files/{file}', [
        'as' => 'admin.media.files.update',
        'uses' => 'MediaLibraryController@updateFile',
    ]);

    // PUT: Update directory
    Route::put('directories/{directory}', [
        'as' => 'admin.media.directories.update',
        'uses' => 'MediaLibraryController@updateDirectory',
    ]);

    // DELETE: Delete files
    Route::delete('files/{files}', [
        'as' => 'admin.media.files.delete',
        'uses' => 'MediaLibraryController@deleteFiles',
    ]);

    // DELETE: Delete files
    Route::delete('directories/{directory}', [
        'as' => 'admin.media.directories.delete',
        'uses' => 'MediaLibraryController@deleteDirectory',
    ]);
});
