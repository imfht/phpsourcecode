<?php
// +----------------------------------------------------------------------
// | TpAndVue
// +----------------------------------------------------------------------
// | Author: King east <1207877378@qq.com>
// +----------------------------------------------------------------------


namespace app\common\command;


use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\facade\App;

class VueCommand extends Command
{
    protected function configure()
    {
        $this->setName('vue')
            ->addArgument('name', Argument::OPTIONAL, '模块名');
    }

    protected function execute(Input $input, Output $output)
    {
        $name = trim($input->getArgument('name'));
        $name = $name ?: 'index';

        $path  = App::getAppPath() . $name;
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        if (!is_dir($path . '/controller')) {
            mkdir($path . '/controller', 0755, true);
        }

        // index.html
        $content = <<<html
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>{$name}</title>
</head>
<body>
<div id="app"></div>
</body>
</html>
html;
        file_put_contents($path . '/index.html', $content);

        // index.js
        $content = <<<js
import 'babel-polyfill'
import Vue from 'vue'
import router from './utils/router.js'
import Layout from './layout.vue'
import './utils/http.js'

Vue.config.productionTip = false

new Vue({
    el: '#app',
    router,
    render: h => h(Layout)
})

js;
        file_put_contents($path . '/index.js', $content);

        // layout.vue
        $content = <<<content
<style scoped>
</style>
<template>
  <router-view/>
</template>

<script>
export default {}
</script>

content;
        file_put_contents($path . '/layout.vue', $content);

        // utils/http.js
        $content = <<<content
import Vue from 'vue'
import axios from 'axios'

axios.defaults.baseURL = '/api'

Vue.prototype.\$http = axios

export default axios

content;
        if (!is_dir($path . '/utils')) mkdir($path . '/utils', 0755, true);
        file_put_contents($path . '/utils/http.js', $content);

        // utils/router.js
        $content = <<<content
import Vue from 'vue'
import VueRouter from 'vue-router'
import KeRouter from '~/utils/vue-ke-router'
import Index from '../routes/index'

Vue.use(VueRouter)


// 顶级路由
const route = new KeRouter('{$name}')

Index(route)


const r = new VueRouter({
    // 如需要使用history模式请去掉下面前面的//
    mode: 'history',

    // history模式开启下必须设置 /模块名/
    base: '/{$name}/',
    routes: route.data
})

export default r

content;
        if (!is_dir($path . '/utils')) mkdir($path . '/utils', 0755, true);
        file_put_contents($path . '/utils/router.js', $content);


        // routes/index.js
        $content = <<<content
export default (route) => {

    route.reg(['index', '/'], 'index').meta({ title: '首页' })

}

content;
        if (!is_dir($path . '/routes')) mkdir($path . '/routes', 0755, true);
        file_put_contents($path . '/routes/index.js', $content);


        $output->writeln("created vue-module:{$name}");
        $output->writeln('success!');
    }

}