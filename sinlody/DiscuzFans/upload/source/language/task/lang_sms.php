<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: lang_sms.php 27449 2012-02-01 05:32:35Z pmonkey_w $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$lang = array
(
	'sms_name' => '验证手机号码任务',
	'sms_desc' => '验证手机号码获得相应的奖励。',
	'sms_view' => '<strong>请按照以下的说明来参与本任务：</strong>
		<ul>
		<li><a href="home.php?mod=spacecp&ac=profile&op=password" target="_blank">新窗口打开账号设置页面</a></li>
		<li>在新打开的设置页面中，将自己的手机号真实填写(新填写的手机号需要先保存)，并点击“重新接收验证短信”链接</li>
		<li>几分钟后，系统会给您发送一条短信，收到短信后，请将短信里的验证码填写到当前页面即可</li>
		</ul>',
);

?>