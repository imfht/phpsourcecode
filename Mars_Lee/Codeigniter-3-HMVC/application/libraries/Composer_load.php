<?php

/**
 * Created by PhpStorm.
 * User: li
 * Date: 15-11-19
 * Time: 下午9:51
 */
class Composer_load
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('ClassLoader' === $class) {
            require APPPATH . 'libraries/ClassLoader.php';
        }
    }

    static function get_load(){
        if (null !== self::$loader) {
            return self::$loader;
        }
        spl_autoload_register(array('Composer_load', 'loadClassLoader'), true, true);
        self::$loader = $loader = new ClassLoader();
        spl_autoload_unregister(array('Composer_load', 'loadClassLoader'));

        $map = require COMPOSERDIR . '/autoload_namespaces.php';
        foreach ($map as $namespace => $path) {
            $loader->set($namespace, $path);
        }
        $map = require COMPOSERDIR . '/autoload_psr4.php';
        foreach ($map as $namespace => $path) {
            $loader->setPsr4($namespace, $path);
        }

        $classMap = require COMPOSERDIR . '/autoload_classmap.php';
        if ($classMap) {
            $loader->addClassMap($classMap);
        }

        $loader->register(true);

        return $loader;
    }
}
return Composer_load::get_load();