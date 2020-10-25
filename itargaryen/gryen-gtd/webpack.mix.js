const fs = require('fs');
const mix  = require('laravel-mix');
const PKG = require('./package.json');

const DIST_PATH = 'public/dist';
const DIST_PATH_WITH_VERSION = `${DIST_PATH}/${PKG.version}`;
const JS_PATH = './resources/assets/js'; // JS 源文件根目录
const JS_MODULES = fs.readdirSync(JS_PATH); // JS 模块

/* CSS 处理 */
mix.sass('resources/assets/sass/lib.scss', `${DIST_PATH_WITH_VERSION}/css/lib.css`)
    .sass('resources/assets/sass/app.scss', `${DIST_PATH_WITH_VERSION}/css/app.css`)
    .sass('resources/assets/sass/dashboard.scss', `${DIST_PATH_WITH_VERSION}/css/dashboard.css`);

/* JS 处理 */
for (let module of JS_MODULES) {
    if (module !== 'helpers' && module !== '.DS_Store') {
        mix.js(`${JS_PATH}/${module}/index.js`, `${DIST_PATH_WITH_VERSION}/js/${module}.bundle.js`);
    }
}

/* Vue 处理 */
mix.js('resources/vue/index.js', `${DIST_PATH_WITH_VERSION}/js/vue.bundle.js`);
mix.js('resources/vue/dashboard.js', `${DIST_PATH_WITH_VERSION}/js/dashboard.bundle.js`);


/* 公共库抽离 */
mix.extract(['jquery'], `${DIST_PATH_WITH_VERSION}/js/jquery.bundle.js`);
mix.extract(['axios'], `${DIST_PATH_WITH_VERSION}/js/axios.bundle.js`);

mix.sourceMaps(false);
mix.autoload({
    jquery: ['$', 'window.jQuery', 'jQuery'],
    vue: ['window.Vue', 'Vue'],
    axios: ['window.axios', 'axios']
});
