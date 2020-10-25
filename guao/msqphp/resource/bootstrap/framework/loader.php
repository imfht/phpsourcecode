<?php declare(strict_types = 1);

/**
 * loader静态类储存一个数组
 * 包括所有通过自动加载加载的文件(框架自带或者composer[需要改动代码])
 * 以实现智能加载
 * 所以在载入自动加载类前先载入对应文件
 *
 */

//是否使用composer自动加载
const COMPOSER_AUTOLOAD = false;

$framework_path = \msqphp\Environment::getPath('framework');

if (COMPOSER_AUTOLOAD) {
    /*
    需要修改compose\ClassLoader.php 中函数为
    function includeFile($file)
    {
        include $file;
        \msqphp\core\loader\SimpleLoader::addClasses($file);
    }
    */
    require $framework_path . 'core/loader/BaseTrait.php';
    require $framework_path . 'core/loader/AiloadTrait.php';
    require $framework_path . 'core/loader/SimpleLoader.php';
    //使用则载入composer自动加载类
    require \msqphp\Environment::getPath('root') . 'vendor/autoload.php';
} else {
    /*
      使用框架本身的自动加载。
      加载方式将命名空间转换为目录再加载，大概是psr4的框架专用简化版。
      支持空间映射：
          app    -》application
          test   -》test
          msqphp -》framework（框架路径） || library/msqphp/framework(图书馆路径)
    */
    //载入文件
    require $framework_path . 'core/loader/BaseTrait.php';
    require $framework_path . 'core/loader/AutoloadTrait.php';
    require $framework_path . 'core/loader/AiloadTrait.php';
    require $framework_path . 'core/loader/Loader.php';
    \msqphp\core\loader\Loader::register();
}

unset($framework_path);