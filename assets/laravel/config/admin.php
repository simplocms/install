<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Default upload directory
	|--------------------------------------------------------------------------
	*/

	'path_upload' => 'media/upload',

	/*
	|--------------------------------------------------------------------------
	| Default admin title
	|--------------------------------------------------------------------------
	*/

	'title' => '[ admin ]',

	/*
	 |--------------------------------------------------------------------------
	 | Types of displaying language code in url
	 |--------------------------------------------------------------------------
	 */
	'language_url' => [
		'directory' => 1,
		'subdomain' => 2,
		'domain' => 3,
	],

	/*
	|--------------------------------------------------------------------------
	| Copyright text for admin footer
	|--------------------------------------------------------------------------
	*/
	'copyright' => '&copy; ' . date('Y') . '. Admin v:version by <a href="https://www.simplo.cz/" target="_blank">SIMPLO</a>',

	/*
	|--------------------------------------------------------------------------
	| Simple page url
	|--------------------------------------------------------------------------
	| When true, pages have simple single-level url address. When false,
	| url address of the page is based on hierarchic dependency on other pages.
	|
	*/
	'simple_page_url' => env('SIMPLE_PAGE_URL', true),

	/*
	|--------------------------------------------------------------------------
	| Grid Editor configuration.
	|--------------------------------------------------------------------------
	*/
	'grideditor' => [
		'allowed_tags' => [
			'div', 'article', 'aside', 'details', 'figcaption', 'figure', 'footer', 'header',
			'main', 'mark', 'nav', 'section', 'summary', 'time'
		]
	],

    /*
     * Site icons - sizes that should be generated
     * https://realfavicongenerator.net/faq
     */
	'icon_sizes' => [
		'main' => [16, 32, 48], // combined into favicon.ico
		'png_favicons' => [32, 48],
		'apple_touch' => [57, 60, 72, 76, 114, 120, 144, 152, 180],
		'android_chrome' => [36, 48, 72, 96, 144, 192, 256, 384, 512],
		'ms_tile' => [70, 144, 150, 310, [310, 150]],
	],

    /*
     * Limit for maximal menu depth.
     */
    'max_menu_depth' => env('MAX_MENU_DEPTH', 2),

    /*
     * Administration menu structure.
     */
    'menu_structure' => \App\Structures\AdminMenu\Structure::make([
        \App\Structures\AdminMenu\Item::dashboard(),
        \App\Structures\AdminMenu\Group::articlesAndCategoriesOfAllFlags(),
        \App\Structures\AdminMenu\Item::pages(),
        \App\Structures\AdminMenu\Item::photogalleries(),
        \App\Structures\AdminMenu\Item::menu(),
        \App\Structures\AdminMenu\Group::allUniversalModules(),
        \App\Structures\AdminMenu\Group::allModules(),
        \App\Structures\AdminMenu\Item::mediaLibrary(),
        \App\Structures\AdminMenu\Item::widgets(),
        \App\Structures\AdminMenu\Group::usersAndRoles(),
        \App\Structures\AdminMenu\Group::settings(),
    ])

];
