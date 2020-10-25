<?php
/**
 * spl_autoload_register函数
 * 类自动加载函数
 */

// 注册并返回spl_autoload函数使用的默认文件扩展名，可以有多个，先找第一个，如没有才找接下来的后缀
spl_autoload_extensions('.php, .class.php');

// 加载类的路径
set_include_path(get_include_path() . PATH_SEPARATOR . "5_1_Class/");

// 让类的自动加载生效
spl_autoload_register();

// 调用自动加载的类
new Test();
// 加载Test.php的Test，这是初始化