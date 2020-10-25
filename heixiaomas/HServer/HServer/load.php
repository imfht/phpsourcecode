<?php
/**
 * Created by PhpStorm.
 * User: hxm
 * Date: 2019/9/20
 * Time: 14:13
 */


require_once __DIR__ . '/util/Autoloader.php';

/**
 * 核心自动加载
 */
Autoloader::RegisterPsr4("HServer", __DIR__ . "/../HServer");

/**
 * 自动加载APP
 */
function load($path)
{
    //1、首先先读取文件夹
    $temp = scandir($path);
    //遍历文件夹
    foreach ($temp as $v) {
        $a = $path . '/' . $v;
        if (is_dir($a)) {//如果是文件夹则执行

            if ($v == '.' || $v == '..') {//判断是否为系统隐藏的文件.和..  如果是则跳过否则就继续往下走，防止无限循环再这里。
                continue;
            }
            Autoloader::RegisterDir($a);
            load($a);//因为是文件夹所以再次调用自己这个函数，把这个文件夹下的文件遍历出来
        }
    }
}

load(__DIR__ . "/../app");
