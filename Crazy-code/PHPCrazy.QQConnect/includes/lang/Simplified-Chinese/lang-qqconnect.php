<?php
/*
*	Package:		PHPCrazy.QQConnect
*	Link:			http://git.oschina.net/Crazy-code/PHPCrazy.QQConnect
*	Author: 		Crazy <mailzhangyun@qq.com>
*	Copyright:		2014-2015 Crazy
*	License:		Please read the LICENSE file.
*/

if (!isset($lang) || empty($lang) || !is_array($lang)) {
	$lang = array();
}

$lang = array_merge($lang, 

	array(
		'无法获取openid' 	=> '无法获取openid',
		'openid 为空'		=> 'openid不存在',
		'第一次登录 说明' 	=> '由于您使用此QQ帐号第一次登录本站，您需要绑定或创建一个帐号',
		'QQC 创建新用户' 	=> '创建新用户',
		'QQC 绑定用户' 		=> '绑定用户',
		'QQC 绑定'			=> '绑定',
		'QQC 您已绑定'		=> '您已绑定QQ登录',
		'QQC 已被绑定'		=> '该QQ已被其他用户绑定',
		'QQC 完成创建'		=> '完成创建',
		'QQC 设置'			=> 'QQ登录设置',
		'QQC appid'			=> 'appid',
		'QQC appkey'		=> 'appkey',
		'QQC scope'			=> 'scope',
		'QQC 信息'			=> 'QQ互联信息',
		'QQC 后台说明'		=> '如果您没有以下这些信息请先到 %s 申请',
		'QQC 保存成功'		=> '保存成功',
		'QQC 绑定'			=> '绑定',
		'QQC 解除'			=> '解除',

	)
);