<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2017/3/26 0026
 * Time: 16:06
 */

$prefix = 'inhere\\redis';

spl_autoload_register(function($name) use ($prefix) {
    if (false === strpos($name, $prefix)) {
        return false;
    }
    $path = str_replace([$prefix, '\\'], ['', DIRECTORY_SEPARATOR] ,$name);

    if(is_file($file = dirname(__DIR__) . "/src/$path.php")) {
        require_once $file;

        if( class_exists($name, false) ) {
            return true;
        }
    }

    return false;
});
