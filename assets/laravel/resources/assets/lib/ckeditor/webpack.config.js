const CKEditorWebpackPlugin = require('@ckeditor/ckeditor5-dev-webpack-plugin');
const UglifyJsPlugin = require('uglifyjs-webpack-plugin');
const {styles} = require('@ckeditor/ckeditor5-dev-utils');

module.exports = {
    plugins: [
        new CKEditorWebpackPlugin({
            // See https://docs.ckeditor.com/ckeditor5/latest/features/ui-language.html
            language: 'en',
            additionalLanguages: ['cs'],
            strict: true // build process should fail if an error occurs
        })
    ],

    module: {
        rules: [
            {
                test: /\.js$/,
                use: [
                    {
                        loader: 'babel-loader',
                        options: {
                            presets: [require('babel-preset-env')]
                        }
                    }
                ]
            },
            {
                // Or /ckeditor5-[^/]+\/theme\/icons\/[^/]+\.svg$/ if you want to limit this loader
                // to CKEditor 5 icons only.
                test: /\.svg$/,

                use: ['raw-loader']
            },
            {
                // Or /ckeditor5-[^/]+\/theme\/[\w-/]+\.css$/ if you want to limit this loader
                // to CKEditor 5 theme only.
                test: /\.css$/,
                use: [
                    {
                        loader: 'style-loader',
                        options: {
                            singleton: true
                        }
                    },
                    {
                        loader: 'postcss-loader',
                        options: styles.getPostCssConfig({
                            themeImporter: {
                                themePath: require.resolve('@ckeditor/ckeditor5-theme-lark'),
                            },
                            minify: true
                        })
                    },
                ]
            }
        ]
    },

    optimization: {
        minimizer: [
            new UglifyJsPlugin({
                uglifyOptions: {
                    ecma: 5,
                    warnings: false,
                    compress: true,
                    output: {
                        comments: false,
                        beautify: false,
                    },
                    toplevel: false,
                    nameCache: null,
                    ie8: false,
                    keep_classnames: undefined,
                    keep_fnames: false,
                    safari10: false,
                }
            })
        ]
    }
};
