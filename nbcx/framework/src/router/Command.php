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

/**
 * Command
 *
 * @package nb\router
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/11/28
 */
class Command extends Driver {

    /**
     * 当前路由名称
     *
     * @access public
     * @var string
     */
    public $current;

    public function _pathinfo() {
        $url = Config::$o->argv;
        if(isset($url[1])) {
            return $url[1];
        }
        return '';
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


    /**
     * 路由反解析函数
     *
     * @param string $name 路由配置表名称
     * @param array $value 路由填充值
     * @param string $prefix 最终合成路径的前缀
     * @return string
     */
    //public function url($name, array $value = NULL, $prefix = NULL) {
    //    return null;
    //}

    /**
     * 手动填写url解析路径
     *
     * 暂时不用
     *
     * @access public
     * @param string $pathInfo 全路径
     * @param mixed $parameter 输入参数
     * @return mixed
     * @throws Exception
     */
    //public function match($pathInfo, $parameter = NULL) {
    //    return null;
        //在CLI模式下，不需要url
    //}

    protected function _folder_default(){
        return Config::$o->folder_console;
    }

}