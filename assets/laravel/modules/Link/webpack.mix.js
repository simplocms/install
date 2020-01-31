let mix = require('laravel-mix');

mix.setPublicPath('Dist')
    .js('Assets/js/config.js', 'Dist/config.js')
    .version();
