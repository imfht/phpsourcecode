<?php

/**
 * =============================================================================
 *  [YTF] (C)2015-2099 Yuantuan Inc.
 *  This content is released under the Apache License, Version 2.0 (the "License");
 *  Licensed    http://www.apache.org/licenses/LICENSE-2.0
 *  Link        http://www.ytframework.cn
 * =============================================================================
 *  @author     Tangqian<tanufo@126.com> 
 *  @version    $Id: Router.class.php 105 2016-04-25 09:15:43Z lixiaohui $
 *  @created    2015-10-10
 *  路由
 * =============================================================================                   
 */

namespace core;

class Router
{

    private $uri;
    private $param;
    private $post;
    private $controller;
    private $action;
    private static $module;
    private $child_module;
    private static $module_alias = array();

    public function __construct($uri)
    {
        header('X-Powered-By:YTFramework');

        $this->uri = urldecode(trim($uri, '/'));
        $this->uri = explode('?', $this->uri);
        $default_router = Config::get('router');
        $this->controller = $default_router['controller'];
        $this->action = $default_router['action'];
        if (!empty($this->uri[1])) {
            parse_str($this->uri[1], $this->param);
        }

        $router = explode('/', $this->uri[0]);
        if (count($router)) {
            //弹出脚本名

            if (Config::get('showScriptName')) {
                array_shift($router);
            }
            //分组
            if (current($router)) {
                $modules = Config::get('modules');
                if (!empty($modules)) {
                    if (isset($modules[$router[0]])) {
                        self::$module = $router[0];
                        array_shift($router);
                        //子模块目录
                        if (!empty($modules[self::$module]['has_child_module'])) {
                            $this->child_module = $router[0];
                            array_shift($router);
                        }
                    }
                }
            }

            //controller
            if (current($router)) {
                if (strpos($router[0], '-')) {
                    $tem = [];
                    $controller_tem = explode('-', $router[0]);
                    foreach ($controller_tem as $v) {
                        $tem[] = ucfirst($v);
                    }
                    $this->controller = implode('', $tem);
                } else {
                    $this->controller = $router[0];
                }
                array_shift($router);
            }
            //action
            if (current($router)) {
                if (strpos($router[0], '-')) {
                    $tem = [];
                    $action_tem = explode('-', $router[0]);
                    foreach ($action_tem as $v) {
                        $tem[] = ucfirst($v);
                    }
                    $this->action = implode('', $tem);
                } else {
                    $this->action = $router[0];
                }

                array_shift($router);
            }
        }
        define('TRUE_MODULE', $this->getTrueModule());
        Request::set('request', $_REQUEST);
        Request::set('get', $this->param);
        Request::set('post', $_POST);
    }

    /**
     * 获取当前控制器名称
     * @return string   当前控制器名
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * 获取当前模块下面名子模块名
     * @return string   当前子模块名
     */
    public function getModuleChild()
    {
        return $this->child_module;
    }

    /**
     * 获取当前模块名
     * @return string   当前模块分组名
     */
    public function getModule()
    {
        return self::$module;
    }

    public function getTrueModule()
    {
        return self::$module . ($this->child_module ? '/' : '') . $this->child_module;
    }

    /**
     * 获取当前操作方法名称
     * @return string   当前操作方法名称
     */
    public function getAction()
    {
        return $this->action;
    }

    /*
     * 重定向
     */

    public static function redirect($url = '')
    {
        if (strpos($url, 'http') === false) {
            $url = $url;
        }
        header('Location: ' . $url);
        exit;
    }

    /**
     * $uri参数可以是字符串,数组形式
     * url生成
     * 返回除了域名以外的路径及参数字符串
     */
    public static function createUrl($uri = '', $param = [])
    {
        if (empty($uri)) {
            return '';
        }
        if (is_array($uri)) {
            $uri = $uri[0];
        }
        $temp = explode('/', $uri);
        $module = TRUE_MODULE;
        $controller = CONTROLLER;
        switch (count($temp)) {
            case 1:
                $action = $temp[0];
                break;
            case 2:
                $controller = $temp[0];
                $action = $temp[1];
                break;
            case 3:
                $module = $temp[0];
                if (empty(self::$module_alias)) {
                    self::$module_alias = \ytf_getModuleAlias(Config::get('modules'));
                }
                $m = explode('>', $module);
                if (isset(self::$module_alias[$m[0]])) {
                    $m[0] = self::$module_alias[$m[0]];
                }
                $module = join('/', $m);
                $controller = $temp[1];
                $action = $temp[2];
                break;
        }
        $module = $module ? $module . '/' : '';
        $index = Config::get('showScriptName') ? 'index.php/' : '';
        $result = '/' .$index. $module . $controller . '/' . $action;
        if (!empty($param)) {
            $result.='?' . http_build_query($param);
        }
        return $result;
    }

}
