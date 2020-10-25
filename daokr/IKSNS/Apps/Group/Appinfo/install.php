<?php
/**
 * 安装小组应用
 * @author 小麦  <810578553@qq.com>
 * @version IKPHP1.5.4
 */
if(!defined('IN_IK')) exit();
// 头文件设置
header('Content-Type:text/html;charset=utf-8;');
// 安装SQL文件
$sql_file = APP_PATH.'/Group/Appinfo/install.sql';
// 执行sql文件 Model.class.php小麦新增了一个方法
$res = D('')->executeSqlFile($sql_file);
// 错误处理
if(!empty($res)) {
	echo $res['error_code'];
	echo '<br />';
	echo $res['error_sql'];
	// 清除已导入的数据
	include_once(APP_PATH.'/Group/Appinfo/uninstall.php');
	exit;
}