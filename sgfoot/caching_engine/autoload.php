<?php
header('Content-type:text/html;charset=utf-8');
/**
 * 注册自动加载函数
 * User: freelife2020@163.com
 * Date: 2018/3/27
 * Time: 9:33
 */
if (PHP_VERSION_ID <= 50300) {
    throw new Exception('The PHP version must be greater than or equal to 5.3.0');
}
spl_autoload_register(function ($className) {
    $namespace = 'SgIoc\\Cache';
    if (strpos($className, $namespace) === 0) {
        $fileName = str_replace($namespace, '', $className);
        $fileName = str_replace('\\', '/', __DIR__ . '/src' . $fileName . '.php');
        if (is_file($fileName)) {
            require_once($fileName);
        }
    }
});