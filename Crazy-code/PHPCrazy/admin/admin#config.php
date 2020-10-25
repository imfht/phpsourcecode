<?php
/*
*	Package:		PHPCrazy
*	Link:			http://zhangyun.org/
*	Author: 		Crazy <mailzhangyun@qq.com>
*	Copyright:		2014-2015 Crazy
*	License:		Please read the LICENSE file.
*/

if (!defined('IN_PHPCRAZY') || !defined('IN_ADMIN')) exit;

if (isset($setModule)) {

	return $Module['Settings'][L('常规设置')] = 'config';
}

LoadFunc('misc');

$PageTitle = sprintf(L('模块名 后台'), L('常规设置'));

$submit = isset($_POST['submit']) ? true : false;

if ($submit) {
	
	$post_list = array(
		'sitename', 'timezone', 'date_var',
		'lang', 'http_secure', 'author', 'keywords',
		'description', 'theme'
	);

	$PostVars = array();

	foreach ($post_list as $post_name) {
		
		$PostVars[$post_name] = isset($_POST[$post_name]) ? $_POST[$post_name] : '';

		UpConfig($post_name, $PostVars[$post_name]);

	}

	// 从数据库中读取出网站的设置

	$sql = 'SELECT config_name, config_value FROM ' . CONFIG_TABLE;

	$result = $PDO->query($sql);

	while($row = $result->fetch(PDO::FETCH_ASSOC)) {

		$C[$row['config_name']] = $row['config_value'];
	
	}

	$error = array();

	$error[] = L('网站设置保存成功');
}

$http_secure_on = ($GLOBALS['C']['http_secure']) ? ' selected="selected"' : '';
$http_secure_off = (!$GLOBALS['C']['http_secure']) ? ' selected="selected"' : '';

include T('config');

AppEnd();

?>