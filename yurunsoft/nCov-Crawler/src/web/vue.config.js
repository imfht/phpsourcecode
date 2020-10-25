const vuxLoader = require('vux-loader')

module.exports = {
  publicPath: process.env.NODE_ENV === 'production'? '/': '/',
  configureWebpack: config => {
    vuxLoader.merge(config, {
      plugins: ['vux-ui', 'duplicate-style']
    })
  },
  devServer: {
    port: 8081
  }
}
