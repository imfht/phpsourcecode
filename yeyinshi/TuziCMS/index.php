<?php
/*******************************************************************************
* [TuziCMS] 兔子CMS
* @Copyright (C) 2014-2015  http://tuzicms.com   All rights reserved.
* @Team  Yejiao.net
* @Author: 秦大侠 QQ:176881336
* @Licence http://www.tuzicms.com/license.txt
*******************************************************************************/

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');
//判断是否安装
if(file_exists("./Install/") && !file_exists("./Install/install.lock")){
	header('Location:Install/index.php');
	exit(); 
}
define('APP_DEBUG',True); // 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_PATH','./App/'); // 定义应用目录
require './Core/Think.php'; //加载框架

