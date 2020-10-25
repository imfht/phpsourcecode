<?php
/*
*	Package:		PHPCrazy.QQConnect
*	Link:			http://git.oschina.net/Crazy-code/PHPCrazy.QQConnect
*	Author: 		Crazy <mailzhangyun@qq.com>
*	Copyright:		2014-2015 Crazy
*	License:		Please read the LICENSE file.
*/

include Lang('qqconnect');

if (isset($setModule)) {

	return $Module['Settings'][L('QQC 设置')] = 'qqconnect';

}

LoadFunc('misc');

$PageTitle = sprintf(L('模块名 后台'), L('QQC 设置'));

$submit = isset($_POST['submit']) ? true : false;

if ($submit) {
	
	$post_list = array('qqc_appid', 'qqc_appkey', 'qqc_scope');

	$PostVars = array();

	foreach ($post_list as $post_name) {
		
		$PostVars[$post_name] = isset($_POST[$post_name]) ? $_POST[$post_name] : '';

		UpConfig($post_name, $PostVars[$post_name]);

	}

	$sql = 'SELECT config_name, config_value FROM ' . CONFIG_TABLE;

	$result = $PDO->query($sql);

	while($row = $result->fetch(PDO::FETCH_ASSOC)) {

		$C[$row['config_name']] = $row['config_value'];
	
	}

	$error = array();

	$error[] = L('QQC 保存成功');
}

include T('qqconnect', true);

AppEnd();

?>