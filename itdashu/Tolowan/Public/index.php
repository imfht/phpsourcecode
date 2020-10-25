<?php
use Phalcon\Loader;

/**
 * Very simple MVC structure
 */

    ini_set("display_errors", "On");
    error_reporting(E_ALL | E_STRICT);
require_once __DIR__ . '/../Web/site.php';
if (!isset($site[$_SERVER['HTTP_HOST']])) {
    header('HTTP/1.1 404 Not Found');
    header("status: 404 Not Found");
} else {

    spl_autoload_register(function ($className) {
        $classNamePath = str_replace('\\', '/', $className);
        $the_path = __DIR__ . '/../' . $classNamePath . '.php';
        if (file_exists($the_path)) {
            require $the_path;
            return true;
        }
        return false;
    });

    $loader = new Phalcon\Loader();
    $namespaces = array(
        'Models' => __DIR__ . '/../Web/'.ucfirst($site[$_SERVER['HTTP_HOST']]).'/Models',
    );
    $loader->registerNamespaces($namespaces);
    $loader->register();
    //加载网站所属类别
    include __DIR__ . '/../Web/' . $site[$_SERVER['HTTP_HOST']] . '/config/type.php';
    // 注册服务
    include __DIR__ . '/../Core/bootstrap/' . $settings . '.php';
}
