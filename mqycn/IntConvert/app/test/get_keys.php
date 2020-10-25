<?php
namespace app\test;
use app\services\IntConvert;

/**
 * 类名：get_keys.php
 * 作者：mqycn
 * 博客：http://www.miaoqiyuan.cn
 * 源码：https://gitee.com/mqycn/IntConvert
 * 说明：生成随机的KeyMap
 */

# 如果使用 TP框架，会自动引入
require '../services/IntConvert.php';
IntConvert::randomKey();
?>