const path = require('path')
const webpack = require('webpack')
const package = require('../package.json')

let vendors = []
for (let key in package.dependencies) {
    vendors.push(key)
}

module.exports = {
    mode: 'production',
    entry: {
        vendors: vendors
    },
    output: {
        path: path.resolve(__dirname, '../public'),
        filename: '[name].js',
        library: '[name]'
    },
    plugins: [
        new webpack.DllPlugin({
            path: path.resolve(__dirname, '../', 'manifest.json'),
            name: '[name]'
        })
    ]
}
