<?php

namespace Library;

// 定义Workerman根目录
if(!defined('APP_ROOT_DIR')) {
    define('APP_ROOT_DIR', realpath(__DIR__  . '/../'));
}

/**
 * 自动加载类
 * @author moxiaobai<mlkom@live.com>
 */

class Autoloader{

    /**
     * 根据命名空间加载文件
     * @param string $name
     * @return boolean
     */
    public static function loadByNamespace($name) {
        // 相对路径
        $classPath = str_replace('\\', DIRECTORY_SEPARATOR ,$name);

        // 先尝试在应用目录寻找文件
        $classFile = APP_ROOT_DIR . '/' . $classPath.'.php';

        // 找到文件
        if(is_file($classFile)) {
            // 加载
            require_once($classFile);
            if(class_exists($name, false)) {
                return true;
            }
        }
        return false;
    }
}

// 设置类自动加载回调函数
spl_autoload_register('\Library\Autoloader::loadByNamespace');