<?php
/**
 * 小组版本信息
 * @author 小麦 <810578553@qq.com>
 * @version IKPHP 1.5.4
 */

if(!defined('IN_IK')) exit();

return array(
	// 应用名称 [必填]
	'NAME'						=> '小组',	
	// 应用简介 [必填]
	'DESCRIPTION'				=> '小组是一个大家一起讨论热门话题的地方',
	// 托管类型 [必填]（0:本地应用，1:远程应用）
	'HOST_TYPE'					=> '0',
	// 前台入口 [必填]（格式：Action/act）
	'APP_ENTRY'					=> 'index/index',
	// 为空
	'ICON_URL'					=> APP_PATH.'Group/Appinfo/icon_app.png',
	// 为空
	'LARGE_ICON_URL'			=> APP_PATH.'Group/Appinfo/icon_app_large.png',
	// 版本号 [必填]
	'VERSION'			=> '1.5.4',
	// 后台入口 [选填]
	'ADMIN_ENTRY'				=> 'group/admin/index',
	// 统计入口 [选填]（格式：Model/method）
	'STATISTICS_ENTRY'			=> 'statistics/index',
	// 开发者
	'AUTHOR_NAME'				=> '爱客开源',
	// 开发者个人网址
	'AUTHOR_URL'				=> 'http://www.ikphp.com',	

);
