<?php

namespace DAO;

/**
 * 数据读取模型抽象类
 *
 * @package DAO
 * @author iceup <sjlinyu@qq.com>
 */
abstract class AbstractModel {

    /**
     * 捕获dao中没有的方法，直接访问mysql中相应的类的方法
     * 
     * @param string $method
     * @param array $args
     * @return mixd
     */
    public function __call($method, $args) {
        $className      = get_class($this);
        $mysqlClassName = '$mysql = ' . str_replace('DAO', '\Mysql', $className) . '::getInstance();';
        eval($mysqlClassName);

        $excutePhp = '$result = $mysql->$method(_args_);';

        $string = '';
        foreach ($args as $key => $arg) {
            $string .= '$args[' . $key . '],';
        }

        $excutePhp = str_replace('_args_', rtrim($string, ','), $excutePhp);

        eval($excutePhp);

        return $result;
    }

}
