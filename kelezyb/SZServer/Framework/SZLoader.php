<?php
namespace Framework;

define('DS', DIRECTORY_SEPARATOR);

/**
 * 类自动加载器
 * @package Framework
 */
class SZLoader {
    public static function init() {
        spl_autoload_register(array('Framework\SZLoader', 'autoload'));
    }

    public static function autoload($class) {
        $newclass = ROOT_PATH . DS . str_replace('\\', '/', $class);

        $newfile = $newclass . '.php';

        if (is_readable($newfile)) {
            include $newfile;
        } else {
            throw new \Exception(sprintf('class %s not found.', $class));
        }
    }
}