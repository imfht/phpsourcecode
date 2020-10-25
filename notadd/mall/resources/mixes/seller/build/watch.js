require('./check-versions')();

process.env.NODE_ENV = 'production';

var buildWebpackConfig = require('./webpack.prod.conf');
var chalk = require('chalk');
var config = require('../config');
var merge = require('webpack-merge');
var path = require('path');
var rm = require('rimraf');
var shell = require('shelljs');
var webpack = require('webpack');
var webpackConfig = merge(buildWebpackConfig, {
    plugins: [
        new webpack.ProgressPlugin(),
    ],
    watch: true,
});

rm(path.join(config.build.assetsRoot, config.build.assetsSubDirectory), err => {
    if (err) throw err;
    webpack(webpackConfig, function (err, stats) {
        if (err) throw err;
        console.log('\n');
        process.stdout.write(stats.toString({
            colors: true,
            modules: true,
            children: false,
            chunks: false,
            chunkModules: false,
        }) + '\n');
        var assetsPath = path.join(__dirname, '../../../../../../public/assets/mall/seller');

        console.log(chalk.cyan('  Moving files to path ' + assetsPath + '\n'));

        shell.rm('-rf', assetsPath);
        shell.mkdir('-p', assetsPath);
        shell.config.silent = true;
        shell.cp('-R', path.join(__dirname, '../dist/assets/mall/seller/css'), assetsPath);
        shell.cp('-R', path.join(__dirname, '../dist/assets/mall/seller/img'), assetsPath);
        shell.cp('-R', path.join(__dirname, '../dist/assets/mall/seller/js'), assetsPath);
        shell.config.silent = false;

        console.log(chalk.cyan(`  Build completed at ${(new Date()).toLocaleString()}.`));
        console.log(chalk.cyan('  Watching ...\n'));
    });
});