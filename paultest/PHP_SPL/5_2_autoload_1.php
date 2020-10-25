<?php
/**
 * autoload装载类
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

new Test();
/**
__autoload class:Test
加载Test.php的Test，这是初始化
 */