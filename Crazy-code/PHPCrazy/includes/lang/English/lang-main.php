<?php
/*
*	Package:		PHPCrazy
*	Link:			http://zhangyun.org/
*	Author: 		Crazy <mailzhangyun@qq.com>
*	Copyright:		2014-2015 Crazy
*	License:		Please read the LICENSE file.
*/

if (!isset($lang) || empty($lang) || !is_array($lang)) {
	$lang = array();
}

$lang = array_merge($lang, 

	array(

		'问候 用户名'		=> 'Hello, %s ！',
		'游客 随机数'		=> 'Anonymous%d',
		'用户名 用户中心'	=> '%s - User center',

		'首页'				=> 'Index',
		'后台'				=> 'Administer',
		'登录'				=> 'Login',
		'注册'				=> 'Register',
		'注销'				=> 'Unset',
		'选择'				=> 'Select',
		'提交'				=> 'Submit',

		'注册成功'			=> 'Register succesed',

		'ID'				=> 'ID',
		'注册时间'			=> 'Registration date',
		'帐号'				=> 'Account',
		'邮箱 ID 用户名'	=> 'Email / ID / Username',
		'密码'				=> 'Password',
		'输入密码'			=> 'Please enter the password',
		'用户名'			=> 'Username',
		'输入用户名'		=> 'Please enter a username',
		'输入用户名说明'	=> '1至12字符,不能为全数字',
		'输入邮箱说明'		=> '当忘记密码时您可以通过电子邮件找回密码',
		'输入密码说明'		=> '建议不要把密码设置过于简单',
		'确认密码'			=> '确认密码',
		'输入确认密码说明'	=> '请再次输入密码',
		'验证码'			=> 'Verification code',
		'输入验证码 说明'	=> '点击图片可以刷新验证码, 请计算出上图的结果, 如果结果为负数用 - （负号）来表示, 例如：-1',
		'验证码不正确'		=> '验证码不正确',
		'完成注册'			=> '完成注册',
		'新密码'			=> 'New password',
		'确认新密码'		=> '确认新密码',

		'搜索用户'			=> 'Find Users',
		'搜索结果'			=> 'Search results',

		'确认'				=> 'Confirm',
		'保存'				=> 'Save',
		'返回上级'			=> 'Back',

		'SQL'				=> 'SQL',
		'行'				=> 'Line',
		'文件'				=> 'File',

		'修改账号信息'				=> '修改账号信息',
		'邮箱'						=> 'Email',
		'找回密码'					=> 'back password',
		'发送邮件'					=> 'Sendmail',

		'输入邮箱地址'				=> '请输入Email地址',

		'激活邮件发送 邮箱 说明' 	=> '系统已将激活链接发送到您的邮箱 %s,请注意查收！',

		'SQL错误' 			=> 'SQL Error',
		'SQL执行出错'		=> 'SQL执行出错',
		'错误'				=> 'Error',
		'提示'				=> 'Prompt',
		'模版 文件 不存在' 	=> '模版 %s 不存在',
		'模版 文件 无法打开'=> '模版 %s 无法打开',
		'用户中心' 			=> 'User center',
		'密码不能为空'		=> '密码不能为空',
		'账号不能为空'		=> '账号不能为空',
		'密码错误'			=> 'Password error',
		'帐号不存在'		=> '帐号不存在',
		'用户不存在'		=> '没有此用户',
		'重置密码'			=> 'Reset password',
		'您的新密码'		=> 'Your new password',
		'密码重置成功'		=> 'Password reset successful',
		'输入密码不一样'	=> '两次输入的密码不一样',
		'激活链接无效'		=> '激活链接无效',
		'非法操作'			=> '非法操作',
		'忘记密码'			=> '忘记密码',

		'！用户名不能为空'		=> 'Sorry, the user name can not be empty',
		'！用户名不能为全数字'	=> '对不起，用户名不能为全数字',
		'！用户名带有非法字符'	=> '对不起，用户名带有非法字符',
		'！用户名太长'			=> '对不起，用户名不能超过12个字符',
		'！用户名已存在'		=> 'Sorry, the username has',


		'邮箱未注册'		=> '该Email还没有注册',
		'邮箱已注册'		=> 'Email already exists',
		'邮箱无效'			=> 'Invalid Email',

		'注册'				=> 'Register',

		'开启Email发送'		=> '请开启Email发送功能',

		'是'				=> 'Yes',
		'否'				=> 'No',

		'开启'				=> 'Open',
		'关闭'				=> 'Close',

		'开'				=> 'Open',
		'关'				=> 'Close',

		'刷新'				=> 'Refresh',

		'用户列表'			=> 'User list',
		'权限'				=> 'Auth',
		'权限不足'			=> '权限不足',
		'用户资料已保存'	=> '用户资料已保存',
		'关键词为空'		=> 'Keyword is empty',

		'版权所有 年 作者'	=> 'CopyRight©%s %s.',

		'Auth'			=> 
			array(
				ANONYMOU 		=> 'Anonymous',
				USER 			=> 'User',
				ADMIN 			=> 'Admin',
				MASTER   		=> 'Master'
			),

		// 时区
		// 详情请参考 http://php.net/manual/zh/timezones.others.php
		'timezone' 			=>
			array(
				'PRC' 				=> 'China',
				'America/New_York' 	=> 'New York',
			)
	)
);