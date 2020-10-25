<?php
/**
 * spl_autoload装载类
 * 系统可以通过显式调用spl_autoload函数自动查找文件名来装载类，参数为类的名称来重启类文件的自动查找（装载）
 *
 * 注意：当使用 spl_autoload函数的时候，require函数会失去作用了
 */

/**
 * 定义一个用来替换__autoload函数的类文件装载函数
 * 需要使用spl_autoload_register('classLoader')来实现自动装载
 * @param $class_name
 */
function classLoader($class_name)
{
    echo "classLoader() load class:" . $class_name . PHP_EOL;

    // 装载类
    // require_once('5_1_Class/' . $class_name . ".php");
    set_include_path('5_1_Class/');
    spl_autoload($class_name);
}

// 传入定义好的类文件装载函数来实现自动装载
spl_autoload_register('classLoader');

new Test();
/*
classLoader() load class:Test
加载Test.php的Test，这是初始化
 */