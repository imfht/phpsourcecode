<?php

/**
 * Wikin! [ Discuz!应用专家，维清互联旗下最新品牌 ]
 *
 * Copyright (c) 2011-2099 http://www.wikin.cn All rights reserved.
 *
 * Author: wikin <wikin@wikin.cn>
 *
 * $Id: qqlogin_config.php 2015-5-13 15:28:10Z $
 */
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if (empty($_G['uid'])) {
	showmessage('to_login', '', array(), array('showmsg' => true, 'login' => 1));
}

dheader("Location:home.php?mod=spacecp&ac=plugin&id=qq:spacecp");
?>