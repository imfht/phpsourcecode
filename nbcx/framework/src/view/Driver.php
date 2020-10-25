<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\view;

/**
 * Driver
 *
 * 模板引擎
 * 支持XML标签和普通标签的模板解析
 * 编译型模板引擎 支持动态缓存
 *
 * @package nb\view
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2017/11/29
 */
abstract class Driver {


    /**
     * 模板变量赋值
     * @access public
     * @param mixed $name
     * @param mixed $value
     * @return void
     */
    abstract public function assign($name, $value = '');

    /**
     * 模板引擎配置项
     * @access public
     * @param array|string $config
     * @return void|array
     */
    //abstract public function config($config,$value=null);

    /**
     * 渲染模板文件
     * @access public
     * @param string $template 模板文件
     * @param array $vars 模板变量
     * @param array $config 模板参数
     * @return void
     */
    abstract public function fetch($template='', $vars = [], $config = []);

    /**
     * 渲染模板内容
     * @access public
     * @param string $content 模板内容
     * @param array $vars 模板变量
     * @param array $config 模板参数
     * @return void
     */
    abstract public function display($template='', $vars = [], $config = []);



    /**
     * 返回编译后模版文件路径
     * @param $template
     * @param array $config
     * @return string
     */
    //abstract public function path($template='',$config = []);


    /**
     * 模板引擎参数赋值
     * @access public
     * @param mixed $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        $this->config[$name] = $value;
    }

    /**
     * 读取传给模版的变量
     * 自动判断是否存在
     * @param $name
     */
    public function __get($name) {
        // TODO: Implement __get() method.
        if(isset($this->data[$name])) {
            return $this->data[$name];
        }
        return '';
    }
}
