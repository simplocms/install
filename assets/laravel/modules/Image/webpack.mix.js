let mix = require('laravel-mix');

mix.setPublicPath('Dist')
    .js('Assets/js/configuration.js', 'Dist/configuration.js')
    .version();
