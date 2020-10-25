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

	return $Module['Settings'][L('邮件设置')] = 'mail';

}

$PageTitle = sprintf(L('模块名 后台'), L('邮件设置'));

$submit = isset($_POST['submit']) ? true : false;

if ($submit) {
	
	$post_list = array(
		'send_mail', 'smtp', 'system_mail',
		'smtp_secure', 'smtp_host', 'smtp_port', 'smtp_username',
		'smtp_password'
	);

	$PostVars = array();

	LoadFunc('misc');

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

	$error[] = L('邮件设置保存成功');
}

$send_mail_on = ($GLOBALS['C']['send_mail']) ? ' selected="selected"' : '';
$send_mail_off = (!$GLOBALS['C']['send_mail']) ? ' selected="selected"' : '';

$smtp_on = ($GLOBALS['C']['smtp']) ? ' selected="selected"' : '';
$smtp_off = (!$GLOBALS['C']['smtp']) ? ' selected="selected"' : '';

$smtp_secure_on = ($GLOBALS['C']['smtp_secure']) ? ' selected="selected"' : '';
$smtp_secure_off = (!$GLOBALS['C']['smtp_secure']) ? ' selected="selected"' : '';

include T('mail');

AppEnd();

?>