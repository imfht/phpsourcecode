const path = require('path');

const PATHS = {
    source: path.join(__dirname, 'app'),
    build: path.join(__dirname, 'src')
};
const VueLoaderPlugin = require('vue-loader/lib/plugin');
const HtmlWebpackPlugin = require('html-webpack-plugin');
module.exports = {
    entry:{
        build:PATHS.source+'/app.js',//vue基础文件包
        echarts:PATHS.source+'/echarts.js',//数据统计
        echartsThem:PATHS.source+'/echarts/echartsThem.js'
    },
    output: {
        path: PATHS.build,
        filename: '[name].js'
    },
    performance: {
        hints:'warning',
        //入口起点的最大体积
        maxEntrypointSize: 50000000,
        //生成文件的最大体积
        maxAssetSize: 30000000,
        //只给出 js 文件的性能提示
        assetFilter: function(assetFilename) {
            return assetFilename.endsWith('.js');
        }
    },
    module: {
        rules: [
            {
                test: /.vue$/,
                loader: 'vue-loader'
            }
        ]
    },
    resolve: {
        extensions: [
          '.vue', '.js'
        ],
        modules: ["node_modules"],
        alias: {
          vue: 'vue/dist/vue.min.js',
          components: path.resolve(__dirname + '/src/components/'),
          '@': path.resolve('src')
        }
    },
    plugins:[
        new HtmlWebpackPlugin(),
        new VueLoaderPlugin()
    ],
    devServer: {
        historyApiFallback: {
          index: `src/App.vue`
        },
        host: '127.0.0.1',
        disableHostCheck: true
    }
};
