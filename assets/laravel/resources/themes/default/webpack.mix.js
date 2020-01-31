/* global process */

const mix = require('laravel-mix');
const ImageminPlugin = require('imagemin-webpack-plugin').default;
const CopyWebpackPlugin = require('copy-webpack-plugin');
const imageminMozjpeg = require('imagemin-mozjpeg');
const path = require('path');

let envConfig = require('dotenv').config({path: path.resolve(process.cwd(), '../../../.env')});


mix.webpackConfig({
    plugins: [
        new CopyWebpackPlugin([{
            from: 'src/images',
            to: 'images', 
        }]),
        new ImageminPlugin({
            disable: process.env.NODE_ENV !== 'production', // Disable during development
            pngquant: {
                quality: '95-100',
            },
            jpegtran: null,
            plugins: [
                imageminMozjpeg({
                    quality: 80,
                    progressive: true
                })
            ],

            test: /\.(jpe?g|png|gif|svg)$/i
        }),
    ],
});

mix.js('src/js/app.js', 'dist/js')
    .sass('src/scss/main.scss', 'dist/css')
    .setPublicPath('dist')
    .copy('src/fonts', 'dist/fonts')
    .browserSync({
        proxy: envConfig.parsed.APP_URL ? `${envConfig.parsed.APP_URL}/--html` : 'localhost/--html',
        notify: false,
        files: [
            'view/**/*.php',
            '_static/**/*.php',
            'dist/js/**/*.js',
            'dist/css/**/*.css'
        ],
    });

mix.version();
mix.sourceMaps(false, 'source-map');
