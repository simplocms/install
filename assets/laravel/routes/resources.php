<?php

Route::group(['middleware' => 'resources'], function () {

    // Robots
    Route::get('robots.txt', 'SiteController@robots');

    // Sitemap
    Route::get('sitemap/index.xml', [
        'as' => 'sitemap.index',
        'uses' => 'SiteController@sitemapIndex'
    ]);

    // Image sitemap
    Route::get('sitemap/images-{page}.xml', [
        'as' => 'sitemap.images',
        'uses' => 'SiteController@imageSitemap'
    ]);

    // Sitemap
    Route::get('sitemap/{languageCode}.xml', [
        'as' => 'sitemap',
        'uses' => 'SiteController@sitemap'
    ]);

    // RSS feed
    Route::get('feed/{languageCode}-rss.xml', [
        'as' => 'feed.rss',
        'uses' => 'SiteController@rssFeed'
    ]);

    // site.webmanifest
    Route::get('{languageCode}/site.webmanifest', [
        'as' => 'site.webmanifest',
        'uses' => 'SiteController@webManifest'
    ]);

    // site.webmanifest
    Route::get('browserconfig.xml', [
        'as' => 'site.browserconfig',
        'uses' => 'SiteController@browserConfig'
    ]);

    // Media
    Route::get('media/{path}', [
        'as' => 'media',
        'uses' => 'SiteController@media',
    ])->where('path', '(.*)');
});
