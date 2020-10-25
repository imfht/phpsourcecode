<?php

/**
 * =============================================================================
 *  [YTF] (C)2015-2099 Yuantuan Inc.
 *  This content is released under the Apache License, Version 2.0 (the "License");
 *  Licensed    http://www.apache.org/licenses/LICENSE-2.0
 *  Link        http://www.ytframework.cn
 * =============================================================================
 *  @author     Tangqian<tanufo@126.com> 
 *  @version    $Id: App.class.php 103 2016-04-25 08:42:09Z lixiaohui $
 *  @created    2015-10-10
 *  
 * =============================================================================                   
 */

namespace core;

use core\Router;
use core\Config;

class App
{

    protected static $router;

    /**
     * APP运行入口
     * @param type $uri  $_SERVER['REQUEST_URI'];
     */
    public static function run($uri)
    {

        self::$router = new Router($uri);

        define('CONTROLLER', self::$router->getController());
        define('ACTION', self::$router->getAction());
        
        /**
         * 安全处理
         */
        $controller_class = ucfirst(self::$router->getController()) . 'Controller';
        //APP_C
        preg_match('/^[_a-zA-Z0-9]\w+$/', $controller_class) ? true : Exceptions::show404();

        $method = 'action' . ucfirst(self::$router->getAction());
        //APP_A
        preg_match('/^[_a-zA-Z0-9]\w+$/', $method) ? true : Exceptions::show404();

        $controller_class = 'controllers' . DS . $controller_class;

        /**
         * 模块优先级处理
         */
        $module = Config::get('modules');
        if (isset($module[self::$router->getModule()])) {

            $has_module = $module[self::$router->getModule()];
            define('MODULE', self::$router->getModule());


            //子模块目录（一般用于后台）
            if (self::$router->getModuleChild()) {
                define('MODULE_CHILD', self::$router->getModuleChild());

                //APP_MODULE_CHILD
                preg_match('/^[_a-zA-Z0-9]\w+$/', MODULE_CHILD) ? true : Exceptions::show404();
                $controller_class = MODULE_CHILD . DS . $controller_class;
            }

            //别名处理
            if (is_array($has_module) && isset($has_module['module'])) {
                define('MODULE_DIR', 'modules' . DS . $has_module['module'] . DS);
                $controller_class = MODULE_DIR . $controller_class;
            } else {
                define('MODULE_DIR', 'modules' . DS . self::$router->getModule() . DS);
                $controller_class = MODULE_DIR . $controller_class;
            }
        }

        if (!is_file(ROOT . DS . $controller_class . '.class.php')) {
            Exceptions::show404();
        }

        $controller_class = str_replace(DS, '\\', $controller_class);


        //运行
        /**
         * 是否开启自动SESSION
         */
        if (ini_get(SESSION_AUTO_START) === false) {
            if (SESSION_AUTO_START === true) {
                /**
                 * session丢失处理
                 */
                if (Request::get('request', session_name())) {
                    session_id(Request::get('request', session_name()));
                }
                session_start();
            }
        }
        $controller = new $controller_class();

        if (method_exists($controller, $method)) {
            /**
             * DO it
             * 引入模块下的common config functions文件
             */
            if (defined('MODULE_DIR')) {
                $fileName = ['config.php', 'functions.php'];
                foreach ($fileName as $k => $v) {
                    if (defined('MODULE_CHILD')) {
                        $file = ROOT . DS . MODULE_DIR . MODULE_CHILD . DS . 'common' . DS . $v;
                    } else {
                        $file = ROOT . DS . MODULE_DIR . 'common' . DS . $v;
                    }
                    if (is_file($file)) {
                        $return = require($file);
                        if (is_array($return)) {
                            Config::set('M_C', $return);
                        }
                    }
                }
            }
            self::methodWork($controller->$method());
        } else {
            Log::set('Method ' . $method . ' of class ' . $controller_class . ' does not exist.');
            Exceptions::show404();
        }
    }

    /**
     * 路由获取
     * @return obj
     */
    public static function getRouter()
    {
        return self::$router;
    }

    /**
     * controller处理后的返回数据处理输出
     * @param type $param
     * @return type
     */
    public static function methodWork($param)
    {
        if (!$param) {
            return;
        }
        if (!empty($param['ajax'])) {
            self::methodAjax($param);
            return;
        }
        //有模板配置对模板进行解析处理
        if (!empty($param['tpl'])) {
            $view = new View();
            //分配模板变量
            if (isset($param['data']) && is_array($param['data'])) {
                $view->assign($param['data']);
            }
            //解析展现
            $view->display($param['tpl'] . '.tpl');
        }
    }

    /**
     * ajax输出处理
     * @param type $param
     */
    public static function methodAjax($param)
    {
        header('Content-type: application/json');
        //ajax输出配置
        //status---状态,data传回的数据,msg提示信息,page分页信息
        $data = ['status', 'msg', 'data'];
        foreach ($data as $value) {
            $output[$value] = isset($param[$value]) ? $param[$value] : '';
        }
        echo json_encode($output);
    }

}
