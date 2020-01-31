let mix = require('laravel-mix');

mix.webpackConfig({
    resolve: {
        alias: {
            '#':  path.resolve(__dirname + '/../../resources/assets')
        }
    }
});

mix.setPublicPath('Dist')
    .js('Assets/js/configuration.js', 'Dist/configuration.js')
    .version();
