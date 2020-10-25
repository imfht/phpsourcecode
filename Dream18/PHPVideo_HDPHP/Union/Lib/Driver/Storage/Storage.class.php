<?php
// .-----------------------------------------------------------------------------------
// |  Software: [HDPHP framework]
// |   Version: 2013.01
// |      Site: http://www.hdphp.com
// |-----------------------------------------------------------------------------------
// |    Author: 向军 <2300071698@qq.com>
// | Copyright (c) 2012-2013, http://houdunwang.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
// |   License: http://www.apache.org/licenses/LICENSE-2.0
// '-----------------------------------------------------------------------------------
/**
 * 储存工厂类
 * @author hdxj <houdunwangxj@gmail.com>
 */
final class Storage
{
    //处理程序
    static private $handler = null;

    static public function init($Driver = 'File')
    {
        if (is_null(self::$handler)) {
            self::connect($Driver);
        }
        return self::$handler;
    }

    //驱动连接
    static public function connect($Driver = '')
    {
        $Driver = empty($Driver) ? C('STORAGE_DRIVER') : $Driver;
        $class = $Driver . 'Storage';
        self::$handler = new $class;
    }

    //调用驱动方法
    public function __call($method, $args)
    {
        if (method_exists(self::$handler, $method)) {
            return call_user_func_array(array(self::$handler, $method), $args);
        }
    }

}
