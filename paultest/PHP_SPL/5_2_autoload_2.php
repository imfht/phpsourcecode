<?php
/**
 * 如果spl_autoload_register函数和__autoload函数同时存在的时候，原来的 __autoload()方法将不会再调用
 */

/**
 * 定义__autoload函数，可以自动完成类的装载
 * @param $class_name
 */
function __autoload($class_name)
{
    echo "__autoload class:" . $class_name . PHP_EOL;

    // 装载类
    require_once('5_1_Class/' . $class_name . ".php");
}

/**
 * 定义一个用来替换__autoload函数的类文件装载函数
 * 需要使用spl_autoload_register('classLoader')来实现自动装载
 * @param $class_name
 */
function classLoader($class_name)
{
    echo "classLoader() load class:" . $class_name . PHP_EOL;

    // 装载类
    require_once('5_1_Class/' . $class_name . ".php");
}

// 传入定义好的类文件装载函数来实现自动装载
spl_autoload_register('classLoader');

new Test();
/*
classLoader() load class:Test
加载Test.php的Test，这是初始化
 */