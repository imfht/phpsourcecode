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

use nb\Access;
use nb\Config;
use nb\Pool;

/**
 * Driver
 *
 * @package nb\router
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/11/29
 */
abstract class Driver extends Access {

    /**
     * read
     *
     * @var module
     * @var controller
     * @var function
     * @var namespace
     * @var folder
     */

    protected $config=[];

    /**
     * 路由驱动构造函数
     *
     * Driver constructor.
     * @throws \ReflectionException
     */
    public function __construct($config=[]) {
        $this->config = array_merge($this->config, $config);

        //路由解析前的回调函数
        //可以重定路由，可以修改路由配置等
        Pool::object('nb\\event\\Framework')->router($this);
    }

    /**
     * 动态修改组件配置和获取对应配置
     *
     * @access public
     * @param array|string $config
     * @return string|void|array
     */
    public function config($config) {

        if (is_array($config)) {
            $this->config = array_merge($this->config, $config);
        }
        elseif (isset($this->config[$config])) {
            return $this->config[$config];
        }
        else {
            return;
        }
    }

    /**
     * 调度器在戳发redirect事件后，将调用此函数
     * 此函数应该保证控制器是有值的
     */
    abstract public function mustAnalyse();

    /**
     * 返回控制器的默认目录
     * @return mixed
     */
    protected function _folder_default(){
        return Config::$o->folder_controller;
    }

    /**
     * 获取路由指向的控制器函数
     * @return string
     */
    public function _function() {
        return Config::$o->default_func;
    }

    /**
     * 获取控制器完整类，并自动加载
     * @return bool|string
     */
    protected function _class() {
        $conf = Config::$o;
        $folder_controller = $this->folder_default;

        $path = __APP__;
        if($this->module) {
            $path .= $conf->folder_module.'/'.$this->module.'/';
            $class = $conf->folder_module.'\\'.$this->module.'\\';
        }
        else {
            $path .=($conf->folder_app?$conf->folder_app.'/':'');
            $class = '';
        }
        if($this->folder) {
            //folder\filename
            $tmp = $path .$this->folder.'/';
            if (!is_dir($tmp) && $folder_controller) {
                //controller\folder\filename
                $tmp = $path.$folder_controller.'/'.$this->folder.'/';
            }
            $path  = $tmp;
            $class .= "controller\\{$this->folder}\\{$this->controller}";
        }
        else {
            $path  .= $folder_controller.'/';
            $class .=  "controller\\{$this->controller}";
        }
        $file = $path.$this->controller.'.php';
        $auto = glob($path.'*.php');
        foreach ($auto as $v) {
            if (strcasecmp($v, $file) == 0) {
                include_once $v;
                return class_exists($class)?$class:false;
            }
        }
        return false;
    }

    protected function load() {
        $folder_controller = $this->folder_default;
        $conf = Config::$o;
        //$controller = $type=='command'?$conf->folder_console:$conf->folder_controller;

        $path = __APP__;
        if($this->module) {
            $path .= $conf->folder_module.'/'.$this->module.'/';
        }
        else {
            $app  = $conf->folder_app?$conf->folder_app.'/':'';
            $path .=$app;
        }
        if($this->folder) {
            //folder\filename
            $tmp = $path .$this->folder.'/';
            if (!is_dir($tmp) && $folder_controller) {
                //controller\folder\filename
                $tmp = $path.$folder_controller.'/'.$this->folder.'/';
            }
            $path = $tmp;
        }
        else {
            $path .= $folder_controller.'/';
        }
        $file = $path.$this->controller.'.php';
        $auto = glob($path.'*.php');

        foreach ($auto as $v) {
            if (strcasecmp($v, $file) == 0) {
                include_once $v;
                return true;
            }
        }
        return false;
    }

}