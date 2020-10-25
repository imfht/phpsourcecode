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

/**
 * Swoole Http Server 模式下的路由处理
 *
 * @package nb\router
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2017/11/29
 */
class Http extends Php {

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