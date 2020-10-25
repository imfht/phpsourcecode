<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/5/1
 * Time: 下午4:25
 */


spl_autoload_register(function($class)
{
    // e.g. "inhere\gearman\examples\jobs\TestJob"
    if (0 === strpos($class,'inhere\\gearman\\examples\\')) {
        $path = str_replace('\\', '/', substr($class, strlen('inhere\\gearman\\examples\\')));
        $file = __DIR__ . "/{$path}.php";

        if (is_file($file)) {
            include $file;
        }
    // e.g. "inhere\gearman\Helper"
    } elseif (0 === strpos($class,'inhere\\gearman\\')) {
        $path = str_replace('\\', '/', substr($class, strlen('inhere\\gearman\\')));
        $file = dirname(__DIR__) . "/src/{$path}.php";

        if (is_file($file)) {
            include $file;
        }
    }
});
