<?php
/*
	
	修复 - 搜索引擎部分代码不支持PHP5.3的问题
	修复 - 搜索页面显示摘要 导致泄露文章隐藏内容
	找回密码 优化
	过滤关键字 发帖 注册


*/
if(version_compare(PHP_VERSION,'5.3.0','<'))die('You Need PHP Version > 5.3.0 !  You PHP Version = ' . PHP_VERSION);

define('HYBBS_V'			,'1.5.36');
define('INDEX_PATH' 		, str_replace('\\', '/', dirname(__FILE__)).'/');
define('DEBUG'      		,(is_file(INDEX_PATH . 'DEBUG'))?false:true);
define('PLUGIN_ON'  		,true);
define('PLUGIN_ON_FILE'		,true);
define('PLUGIN_MORE_LANG_ON',true);

require INDEX_PATH . 'HY/HY.php';