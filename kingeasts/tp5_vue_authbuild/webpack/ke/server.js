const ora = require('ora')
const path = require('path')
const env = new (require('./env'))(path.resolve(__dirname, '../../.env'))
const { exec } = require('child_process')

const spinner = ora('building for production...')
spinner.start()

class Ke
{
    constructor () {
        this.fs = require('fs')
        this.glob = require('glob')
        this.webpack = require('webpack')
        this.utils = require('../utils.js')
        this.HtmlWebpackPlugin = require('html-webpack-plugin')
        this.CopyWebpackPlugin = require('copy-webpack-plugin')
        this.ExtractTextPlugin = require('extract-text-webpack-plugin')
        this.FriendlyErrorsPlugin = require('friendly-errors-webpack-plugin')
        const { VueLoaderPlugin } = require('vue-loader')
        this.VueLoaderPlugin = VueLoaderPlugin
        this.VueConfig = require('../vue.config.js')
        this.AssetWebpackPlugin = require('assets-webpack-plugin')
        this.CleanWebpackPlugin = require('clean-webpack-plugin')
        this.isDebug = process.env.NODE_ENV === 'dev'
    }

    init () {
        return new Promise((resolve, reject) => {
            this.fs.access('./manifest.json', (err) => {
                if(err){
                    return reject('请先执行npm run dll或yarn dll')
                }
                this.options = {}

                this.entrys = {}
                let basename, tmp, pathname

                this.glob.sync('./resources/modules/**/*.entry.js').forEach((entry) => {
                    // console.log(path.parse(entry))
                    let file = entry.split('/').splice(3).join('/')
                    let tmp = file.split('.')
                    tmp.splice(-2)
                    this.entrys[tmp.join('.')] = entry;
                });

                let emptyObj = true
                for (let key in this.entrys) {
                    emptyObj = false
                    break
                }
                if (emptyObj) {
                    console.log('没有在modules找到*.entry.js入口文件')
                    return reject('没有在modules找到*.entry.js入口文件')
                }
                this.manifest = require('../../manifest.json')
                resolve()
            })
        })

    }


    build () {
        this.init()
            .then(() => {
                this.options = {
                    devtool: this.isDebug ? 'cheap-module-eval-source-map' : 'source-map',
                    entry: this.entrys,
                    mode: this.isDebug ? 'development' : 'production',
                    output: {
                        path: path.join(__dirname, '../../public/vueStatic/', (env.get('APP_DEBUG') ? '.bin/' : '')),
                        publicPath: '/vueStatic/' + (env.get('APP_DEBUG') ? '.bin/' : ''),
                        filename: '[name].[chunkHash:8].js',
                        chunkFilename: '[name].[chunkhash:8].js'
                    },
                    externals: {
                    },
                    resolve: {
                        extensions: ['.js', '.vue', '.json'],
                        alias: {
                            'vue$': 'vue/dist/vue.js',
                            '~': path.join(__dirname, '../../resources')
                        }
                    },
                    module: {
                        rules: [
                            {
                                test: /\.js$/,
                                use: ['babel-loader'],
                                exclude: /node_modules/
                            },
                            {
                                test: /\.vue$/,
                                loader: 'vue-loader',
                                options: this.VueConfig
                            },
                            {
                                test: /\.(mp4|webm|ogg|mp3|wav|flac|aac)(\?.*)?$/,
                                loader: 'url-loader',
                                options: {
                                    limit: 10000,
                                    name: 'media/[name].[hash:7].[ext]'
                                }
                            },
                            {
                                test: /\.(png|jpg|gif)$/,
                                use: [{
                                    loader: 'url-loader',
                                    options: {
                                        limit: 10000,  //8k一下的转义为base64
                                        name: 'images/[name].[hash:7].[ext]'
                                    }
                                }]
                            },
                            {
                                test: /\.(woff2?|eot|ttf|otf)(\?.*)?$/,
                                loader: 'url-loader',
                                options: {
                                    limit: 10000,
                                    name: 'fonts/[name].[hash:7].[ext]'
                                }
                            }
                        ].concat(this.utils.styleLoaders({
                            sourceMap: !this.isDebug,
                            extract: !this.isDebug,
                            usePostCSS: true
                        }))
                    },
                    node: {
                        // prevent webpack from injecting useless setImmediate polyfill because Vue
                        // source contains it (although only uses it if it's native).
                        setImmediate: false,
                        // prevent webpack from injecting mocks to Node native modules
                        // that does not make sense for the client
                        dgram: 'empty',
                        fs: 'empty',
                        net: 'empty',
                        tls: 'empty',
                        child_process: 'empty'
                    },
                    plugins: [
                        new this.VueLoaderPlugin(),
                        new this.webpack.DllReferencePlugin({
                            ...this.manifest
                        }),
                        new this.CleanWebpackPlugin(['vueStatic/']),
                        new this.AssetWebpackPlugin({
                            filename: 'assets.json'
                        })
                    ],
                    optimization: {
                        minimize: !this.isDebug
                    }
                }

                if (this.isDebug) {
                    this.options.watchOptions = {
                        ignored: /node_modules/
                    }
                } else {
                    this.options.plugins.push(new this.ExtractTextPlugin({
                        filename: 'css/[name].[chunkhash:7].css',
                        allChunks: true
                    }))
                }
                // webpack --watch --hide-modules --config webpack/ke/server.js --progress --info-verbosity verbose --display=errors-only",
                //     "start": "cross-env NODE_ENV=dev PLATFORM=web npx webpack --watch --hide-modules --config webpack/ke/server.js --progress --info-verbosity verbose --display=errors-only",
                //    "build": "cross-env NODE_ENV=prop PLATFORM=web npx webpack --hide-modules --config webpack/webpack.config.js --progress --bail",
                const webpack = this.webpack({
                    ...this.options
                })
                if (this.isDebug) {
                    webpack.watch({}, (err, stats) => {
                        if (stats.hasErrors()) {
                            console.log(stats.toString())
                        } else {
                            let time = stats.endTime - stats.startTime
                            console.log('compile success', time + 'ms')
                        }
                        spinner.stop()
                    })
                } else {
                    webpack.run((err, stats) => {
                        let time = stats.endTime - stats.startTime
                        console.log('build success', time + 'ms')
                        spinner.stop()
                    })
                }
            })
            .catch((err) => {
                console.error(err)
                spinner.stop()
            })

    }



}

const ke = new Ke()
ke.build()
