<?php

/**
 * @name Bootstrap
 * @author root
 * @desc   所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * @see    http://www.php.net/manual/en/class.yaf-bootstrap-abstract.php
 * 这些方法, 都接受一个参数:Yaf\Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends Yaf\Bootstrap_Abstract {

    /**
     * 配置文件全局挂载
     */
    public function _initConfig() {
        $arrConfig = Yaf\Application::app()->getConfig();
        Yaf\Registry::set('config', $arrConfig);
    }

    /**
     * 设置系统常量
     */
    public function _initConstant() {
        define('DS', DIRECTORY_SEPARATOR);
        define('APP_DIR', Yaf\Registry::get('config')->application->directory);
        define('ROOT_DIR', dirname(APP_DIR));
        define('VIEW_DIR', APP_DIR . DS . 'views' . DS);
        define('DATA_DIR', APP_DIR . DS . 'public' . DS . 'data');
    }

    /**
     * 全局函数库的加载
     */
    public function _initCommonFunction() {
        Yaf\Loader::import(Yaf\Registry::get('config')->application->directory . '/library/common/Functions.php');
    }

    /**
     * 基本登录信息
     */
    public function _initUser() {
        if (user() && user()['id']) {
            define('UID', user()['id']);
        }
        if (company() && company()['id']) {
            define('CID', company()['id']);
        }
    }

    /**
     * 注册一个插件
     *
     * @param \Yaf\Dispatcher $dispatcher
     */
    public function _initPlugin(Yaf\Dispatcher $dispatcher) {
        $Config = Yaf\Registry::get('config');
        //开启xhprof性能追踪
        if (isset($Config->debug) && $Config->debug && isset($Config->xhprof->dir) && $Config->xhprof->dir) {
            $dispatcher->registerPlugin(new XhprofPlugin());
        }
    }

    /**
     * 设置跨域访问
     */
    public function _initOrigin() {
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
        //获取允许跨域访问的配置
        $allowOrigin = Yaf\Registry::get('config')->get('origin');
        if ($origin && $allowOrigin && strpos($allowOrigin, $origin) !== false) {
            header('Access-Control-Allow-Origin:' . $origin);
            header('Access-Control-Allow-Methods:POST,GET,DELETE,PUT,OPTIONS');
            header('Access-Control-Allow-Credentials:true');//跨域cookie
            header('Access-Control-Allow-Headers:x-requested-with,content-type');
        }
    }

    public function _initRoute(Yaf\Dispatcher $dispatcher) {
        //在这里注册自己的路由协议,默认使用简单路由
    }

    public function _initView(Yaf\Dispatcher $dispatcher) {
        //在这里注册自己的view控制器，例如smarty,firekylin
    }
}
