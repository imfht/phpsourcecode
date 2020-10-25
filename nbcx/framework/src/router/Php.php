<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\router;

use nb\Config;
use nb\Request;
use nb\router\assist\Parser;

/**
 * Native
 *
 * @package nb\router
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/11/29
 */
class Php extends Driver {

    /**
     * 当前路由名称
     *
     * @access public
     * @var string
     */
    public $current;

    //组件默认配置
    protected $config = [
        'name'=>'default', //路由名称，影响路由缓存文件的命名
        'default'=>false,//是否关闭默认路由，true 是，false 不关闭
        'match'=>[]
    ];


    /**
     * 路由必须解析
     * 如果没有解析,将根据参数重新解析
     */
    public function mustAnalyse() {
        if($this->controller) {
            return true;
        }
        $url = $this->url;
        switch (count($url)) {
            case 0:
            case 1:
                break;
            case 2:
                $this->controller = $url[1];
                $this->analyseAction($url[1]);
                break;
            case 3:
                $this->controller = $url[1];
                $this->analyseFunction($url[2]);
                break;
            case 4:
                if($this->module) {
                    $this->folder = $url[1];
                    $this->controller = $url[2];
                    $this->analyseFunction($url[3]);
                }
                else {
                    $module = Config::$o->module_register;
                    if(is_array($module) && in_array($url[1],$module)) {
                        $this->module = $url[1];
                    }
                    else {
                        $this->folder = $url[1];
                    }
                    $this->controller = $url[2];
                    $this->analyseFunction($url[3]);
                }
                break;
            default:
                if($this->module) {
                    $this->folder = $url[1];
                    $this->controller = $url[2];
                    $this->analyseFunction($url[3]);
                }
                else {
                    $this->module = $url[1];
                    $this->folder = $url[2];
                    $this->controller = $url[3];
                    $this->analyseFunction($url[4]);
                }
                break;
        }
        return $this;
    }

    /**
     * 路由正则解析
     *
     * @return void
     * @throws Exception
     */
    private function regular() {
        /** 获取PATHINFO */
        $pathInfo = $this->pathInfo;//$this->getPathInfo();
        foreach ($this->routingTable as $key => $route) {
            if (!preg_match($route['regx'], $pathInfo, $matches)) {
                continue;
            }
            $this->current = $key;
            /** 载入参数 */
            $params = NULL;
            if (!empty($route['params'])) {
                unset($matches[0]);
                $params = array_combine($route['params'], $matches);
                $req = Request::driver();
                $req->get = array_merge($req->get,$params);
                $req->request = array_merge($req->request,$params);
            }
            $this->pathInfo = '/'.$route['action'];
            return true;
        }
        return false;
    }

    /**
     * 获取配置的原始路由
     * @return array
     */
    protected function _routes() {

        if(!isset($this->config['match'])) {
            return null;
        }
        $match = $this->config['match'];
        if(is_string($match)) {
            return include $match;
        }
        return $match;
    }

    /**
     * 将url拆解成节点数组
     * @return array|void
     * @throws Exception
     */
    protected function _url() {
        if($this->routingTable) {
            $url = $this->regular();
            if($url===false && $this->default) {
                $this->controller = false;
                return;
            }
        }
        $url = $this->pathInfo;

        ($url == '/' || empty($url)) and $url = '/' . $sys = Config::$o->default_index;
        Config::$o->default_ext and $url = str_replace(
            Config::$o->default_ext, '', $url
        );
        $url = str_replace('//', '/', $url);
        return explode('/', $url);
    }

    /**
     * 解析后的路由表
     * @return array|mixed
     */
    protected function _routingTable() {

        $module = $this->module;

        $name = $module.'-router-'.$this->config['name'];

        $file = Config::$o->path_temp.$name;

        //如果存在路由缓存
        if( is_file($file) ) {
            return include($file);
        }

        if($this->routes) {
            /** 解析路由配置 */
            $parser = new Parser($this->routes);
            $routingTable = $parser->parse();
            /** 缓存路由 */
            efile($routingTable,$name);
            return $routingTable;
        }
    }

    /**
     * 获取是否关闭默认路由解析
     *
     * @return bool|true true 关闭， false 不关闭
     */
    protected function _default() {
        return isset($this->config['default'])?$this->config['default']:false;
    }

    /**
     * 获取当前请求的PATHINFO
     *
     * @access public
     * @return string
     */
    public function _pathInfo() {
        return Request::driver()->pathinfo;
    }

    /**
     * 设置当前访问的控制器方法
     *
     * @param $name
     * @return mixed|null
     */
    public function ___function($name) {
        if($name) {
            return $name;
        }
        return Config::$o->default_func;
    }

    /**
     * 路由反解析函数
     *
     * @param string $name 路由配置表名称
     * @param array $value 路由填充值
     * @param string $prefix 最终合成路径的前缀
     * @return string
     */
    public function url($name, array $value = NULL, $prefix = NULL) {
        $route = $this->routingTable[$name];

        if(!$route) {
            return '';
        }
        //交换数组键值
        $pattern = [];
        foreach ($route['params'] as $row) {
            $pattern[$row] = isset($value[$row]) ? $value[$row] : '{' . $row . '}';
        }

        $path = vsprintf($route['format'], $pattern);
        if($prefix) {
            $path = (0 === strpos($path, './')) ? substr($path, 2) : $path;
            return rtrim($prefix, '/') . '/' . str_replace('//', '/', ltrim($path, '/'));
        }
        return $path;
    }

    /**
     * 设置路由器默认配置
     *
     * @access public
     * @param mixed $routes 配置信息
     * @return void
     */
    //public function ___routes($routes) {
    //    if (isset($routes[0])) {
    //        $this->routingTable = $routes[0];
    //    }
    //    else {
    //        /** 解析路由配置 */
    //        $parser = new Parser($routes);
    //        $this->routingTable = $parser->parse();
    //    }
    //}

    /**
     * 清除路由缓存
     * @param string $module
     */
    public function clear($module=''){
        $file = Config::getx('path_temp').$module.DS.'_router.php';
        if(is_file($file)) {
            unlink($file);
        }
    }

    /**
     * 获取路由信息
     *
     * @param string $routeName 路由名称
     * @static
     * @access public
     * @return mixed
     */
    //public function get($routeName) {
    //    return isset($this->routingTable[$routeName]) ? $this->routingTable[$routeName] : NULL;
    //}


    /**
     * 解析指定的控制器
     * 其中以‘-’来分割参数
     */
    private function analyseAction($controller) {
        $urlParam = explode('-', $controller);
        switch (count($urlParam)) {
            case 0:
                break;
            case 1:
                $this->controller = $urlParam[0];
                break;
            case 2:
                $this->controller = $urlParam[0];
                $_REQUEST['_1'] = $_GET['_1'] = $urlParam[1];
                break;
            default:
                $this->controller = $urlParam[0];
                foreach ($urlParam as $k => $v) {
                    if ($k === 0) {
                        continue;
                    }
                    $_REQUEST['_' . $k] = $_GET['_' . $k] = $v;
                }
                break;
        }
    }

    /**
     * 解析指定的方法
     * 其中以‘-’来分割参数
     */
    private function analyseFunction($function) {
        $urlParam = explode("-", $function);
        switch (count($urlParam)) {
            case 0:
                break;
            case 1:
                $this->function = $urlParam[0];
                break;
            case 2:
                $this->function = $urlParam[0];
                $_REQUEST['_1'] = $_GET['_1'] = $urlParam[1];
                break;
            default:
                $this->function = $urlParam[0];
                foreach ($urlParam as $k => $v) {
                    if ($k === 0) {
                        continue;
                    }
                    $_REQUEST['_' . $k] = $_GET['_' . $k] = $v;
                }
                break;
        }
    }
}