const path = require('path');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

module.exports = (env, options) => {
    const isProd = options.mode === 'production';

    return {
        mode: options.mode,
        devtool: isProd ? false : 'source-map',

        entry: {
            tinymcerte: [
                '@babel/polyfill',
                './src/css/tinymcerte.css',
                './src/js/index.js',
            ],
            browser: './src/js/browser.js'
        },

        output: {
            path: path.resolve(__dirname, './assets/components/tinymcerte/mgr'),
            filename: '[name].min.js',
            clean: {keep: '.gitignore'}
        },

        module: {
            rules: [
                {
                    test: /\.ts$/,
                    use: 'ts-loader',
                    exclude: /node_modules/,
                },
                {
                    test: /\.js$/,
                    exclude: /(node_modules)/,
                    use: {
                        loader: 'babel-loader'
                    }
                },
                {
                    test: /\.(sa|sc|c)ss$/,
                    use: [
                        {
                            loader: MiniCssExtractPlugin.loader
                        },
                        {
                            loader: "css-loader",
                            options: {
                                url: false,
                                sourceMap: true
                            }
                        },
                        {
                            loader: "postcss-loader"
                        },
                    ]
                }
            ]
        },

        resolve: {
            extensions: [ '.ts', '.js' ],
        },

        plugins: [
            new MiniCssExtractPlugin({
                filename: "tinymcerte.css"
            })
        ]
    };
};
