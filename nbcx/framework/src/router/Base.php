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
use nb\Pool;
use nb\Request;

/**
 * Command
 *
 * @package nb\router
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/8/7
 */
class Base extends Driver {

    /**
     * 当前路由名称
     *
     * @access public
     * @var string
     */
    public $current;

    public function _pathinfo() {
        return Request::driver()->pathinfo;
    }

    /**
     * 路由必须解析
     * 如果没有解析,将根据参数重新解析
     */
    public function mustAnalyse(){
        if($this->controller) {
            return true;
        }
        $url = $this->pathinfo;

        $sys = Config::$o;
        if(!$url) {
            $url = $sys->default_index;
        }
        $url = explode('/', $url);
        switch (count($url)) {
            case 0:
            case 1:
                $this->controller = $url[0];
                break;
            case 2:
                $this->controller = $url[0];
                $this->function = $url[1];
                break;
            default:
                $this->module = $url[0];
                $this->controller = $url[1];
                $this->function = $url[2];
                break;
        }
        return $this;
    }

    protected function _folder_default(){
        return Config::$o->folder_controller;
    }

    protected function _class() {
        $module = $this->module;
        $zone = '';
        if($module) {
            $zone = Config::$o->folder_module.'\\'.$module.'\\';
        }
        if($this->folder) {
            //path:app/folder/controller/Class
            //path:app/controller/folder/Class
            //url:app/folder/Class
            $class =  "{$zone}controller\\{$this->folder}\\{$this->controller}";
        }
        else {
            //path:app/controller/Class
            //url:app/Class
            $class =  "{$zone}controller\\{$this->controller}";
        }
        if(class_exists($class)) {
            return $class;
        }
        return $this->load()?$class:false;
    }

    protected function load() {
        $conf = Config::$o;
        $folder_controller = $this->folder_default;
        $path = __APP__;
        if($this->module) {
            $path .= $conf->folder_module.'/'.$this->module.'/';
        }
        else {
            $path .=($conf->folder_app?$conf->folder_app.'/':'');
        }
        if($this->folder) {
            //folder\filename
            $tmp = $path .$this->folder.'/';
            if (!is_dir($tmp) && $folder_controller) {
                //controller\folder\filename
                $tmp = $path.$folder_controller.'/'.$this->folder.'/';
            }
            $path  = $tmp;
        }
        else {
            $path  .= $folder_controller.'/';
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