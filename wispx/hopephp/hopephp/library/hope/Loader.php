<?php

// +----------------------------------------------------------------------
// | HopePHP
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.wispx.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: WispX <i@wispx.cn>
// +----------------------------------------------------------------------

// [ 自动加载类 ]

namespace hope;

class Loader
{
    /**
     * 注册自动加载
     * @param null $autoload
     */
    public static function register($autoload = null)
    {
        spl_autoload_register($autoload ?: 'hope\\Loader::autoload', true, true);
    }

    /**
     * 加载类
     * @param $class
     */
    public static function autoload($class)
    {
        $file = LIB_PATH . self::findFile($class);
        file_exists($file) && require $file;
    }

    /**
     * 拼装文件地址
     * @param $class
     * @return string
     */
    private static function findFile($class)
    {
        $namespace = substr($class, 0, strpos($class, '\\'));
        $filePath = substr($class, strlen($namespace)) . EXT;
        return strtr($namespace . $filePath, '\\', DIRECTORY_SEPARATOR);
    }
}