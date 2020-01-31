let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix scripts for pages, forms and components.
 |--------------------------------------------------------------------------
 */

mix.js('resources/assets/js/bootstrap.js', 'public/js')
    .js('resources/assets/js/admin.js', 'public/js')
    .sass('resources/assets/sass/errors.scss', 'public/css')
    .sass('resources/assets/sass/admin.scss', 'public/css')

    /*
     * FRONTWEB TOOLBAR
     */
    .js('resources/assets/js/frontweb-toolbar/index.js', 'public/js/cms-toolbar.frontweb.js')

    /*
     * THEME DEVELOPMENT
     */
    .sass('resources/assets/sass/pages/theme-development.scss', 'public/css')

    /*
     * FORMS
     */
    .js('resources/assets/js/forms/articles.js', 'public/js/articles.form.js')
    .js('resources/assets/js/forms/photogallery.js', 'public/js/photogallery.form.js')
    .js('resources/assets/js/forms/categories.js', 'public/js/categories.form.js')
    .js('resources/assets/js/forms/pages.js', 'public/js/pages.form.js')
    .js('resources/assets/js/forms/widgets.js', 'public/js/widgets.form.js')
    .js('resources/assets/js/forms/article-flags.js', 'public/js/article-flags.form.js')
    .js('resources/assets/js/forms/users.js', 'public/js/users.form.js')
    .js('resources/assets/js/forms/roles.js', 'public/js/roles.form.js')
    .js('resources/assets/js/forms/languages.js', 'public/js/languages.form.js')
    .js('resources/assets/js/forms/universal-modules/index.js', 'public/js/universalmodule.form.js')
    .js('resources/assets/js/forms/universal-modules/grid-editor.js', 'public/js/universalmodule.ge-form.js')
    .js('resources/assets/js/forms/settings.js', 'public/js/settings.form.js')
    .js('resources/assets/js/forms/redirects/form.js', 'public/js/redirects.form.js')
    .js('resources/assets/js/forms/redirects/bulk_create_form.js', 'public/js/redirects-bulk-create.form.js')

    /**
     * PAGES
     */
    .js('resources/assets/js/pages/login.js', 'public/js/login.page.js')
    .js('resources/assets/js/pages/menu/menu.js', 'public/js/menu.page.js')
    .js('resources/assets/js/pages/account.js', 'public/js/account.page.js')
    .js('resources/assets/js/pages/dashboard/index.js', 'public/js/dashboard.page.js')
    .js('resources/assets/js/pages/dashboard/profiles-list.js', 'public/js/dashboard.profiles-list.page.js')
    .js('resources/assets/js/pages/languages.js', 'public/js/languages.page.js')
    .js('resources/assets/js/pages/modules.js', 'public/js/modules.page.js')
    .js('resources/assets/js/pages/pages-index.js', 'public/js/pages-index.page.js')

    /**
     * COMPONENTS
     */

    // Grid editor
    .js('resources/assets/js/grid-editor/index.js', 'public/js/grideditor.js')


    // Media Library
    .js('resources/assets/js/media-library/index.js', 'public/js/media-library.js')

    // Color picker
    .js('resources/assets/js/color-picker/plugin.js', 'public/js/color-picker.js')

    .version();

/*
 |--------------------------------------------------------------------------
 | Mix scripts for plugins.
 |--------------------------------------------------------------------------
 */
mix
    // CKeditor 5
    .copy('resources/assets/lib/ckeditor/dist/main.js', 'public/plugin/js/ckeditor.js')
    .copy('resources/assets/lib/ckeditor/dist/translations/*.js', 'public/js/localizations/ckeditor/')

    // tagsinput
    .js('resources/assets/lib/bootstrap-tagsinput/bootstrap-tagsinput.js', 'public/plugin/js')

    // maxlength
    .js('resources/assets/lib/bootstrap-maxlength/bootstrap-maxlength.js', 'public/plugin/js')

    // switchery
    .copy('resources/assets/lib/switchery/switchery.min.js', 'public/plugin/js/switchery.js')

    // jquery-ui
    .js('resources/assets/lib/jquery-ui/jquery-ui.full.js', 'public/plugin/js/jquery-ui.js')

    // typeahead
    .copy('resources/assets/lib/typeahead/bootstrap-typeahead.js', 'public/plugin/js/typeahead.js')

    // Fancytree
    .combine([
        'resources/assets/lib/jquery-ui/jquery-ui.js',
        'resources/assets/lib/fancytree/jquery.fancytree-all.js'
    ], 'public/plugin/js/fancytree.js')

    // Plugin: pickadate
    .combine([
        'resources/assets/lib/pickadate/picker.js',
        'resources/assets/lib/pickadate/picker.date.js',
        'resources/assets/lib/pickadate/picker.time.js',
        'resources/assets/lib/pickadate/legacy.js'
    ], 'public/plugin/js/pickadate.js')
    .styles('resources/assets/lib/pickadate/themes/classic.css', 'public/plugin/css/pickadate.css')

    // beautify
    .combine([
        'resources/assets/lib/beautify/beautify.js',
        'resources/assets/lib/beautify/beautify-html.js'
    ], 'public/plugin/js/beautify.js');


    mix.sourceMaps();
