<?php
$c_main = 'index';
//首页引导文件
include './library/inc.php';

setUrlBack();
// 首页初始值
$current_channel_location = '';

//读取指定的频道模型
include 'tpl.php';
