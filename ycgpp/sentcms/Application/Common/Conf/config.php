<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

/**
 * 注意：该配置文件请使用常量方式定义
 */
if (is_file('./Data/Conf/config.php')){
	return require_once('./Data/Conf/config.php');
}