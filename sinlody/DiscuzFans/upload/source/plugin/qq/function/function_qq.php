<?php

/**
 * 维清 [ Discuz!应用专家，深圳市维清互联科技有限公司旗下Discuz!开发团队 ]
 *
 * Copyright (c) 2011-2099 http://www.wikin.cn All rights reserved.
 *
 * Author: wikin <wikin@wikin.cn>
 *
 * $Id: function_qqlogin.php 2015-5-13 15:24:06Z $
 */
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function getErrorMessage($errroCode) {

	return lang('plugin/qq', sprintf('login_error_code_%d', $errroCode));
}

function save_avatar($url) {

	$dir = DISCUZ_ROOT . "/data/cache/avatarTmp/";

	dmkdir($dir);

	$filename = $dir . '/' . date('His') . strtolower(random(16)) . '.jpg';

	$img = dfsockopen($url, 0, array(), '', false);
	file_put_contents($filename, $img);

	return $filename;
}

?>