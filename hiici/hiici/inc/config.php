<?php

$config = array(
	'manager' => array(5),	//超级管理员

	'mysql_server' => 'localhost', 	//mysql配置
	'mysql_user' => 'root', 	//正式部署时，建议不要使用root连接数据库，且删除test数据库
	'mysql_pwd' => '123', 
	'mysql_db' => 'hiici', 

	'mail_host' => 'smtp.hiici.com', 	//邮箱配置
	'mail_username' => 'postmaster@hiici.com', 
	'mail_password' => '123', 
	'mail_from' => 'postmaster@hiici.com', 
	'mail_fromname' => '衡阳搜索HIICI', 

	's_url' => true,	//伪静态url标识
	'n_u_pay' => 0,		//新用户获赠
	't_u_pay' => 0,		//事件获赠

	'alipay_partner' => '123', 	//合作身份者id，以2088开头的16位纯数字
	'alipay_key' => '123', 	//安全检验码，以数字和字母组成的32位字符

	'qq_appid' => '123', 	//qq合作身份者id
	'qq_appkey' => '123', 	
	'qq_callback' => 'http://hy.hiici.com/user-qq_callback.htm', 	

	'OSS_ACCESS_ID' => '',		//alioss 如果未开启该项服务 该变量请留空
	'OSS_ACCESS_KEY' => '123',
	'OSS_ENDPOINT' => 'oss-cn-hangzhou.aliyuncs.com',
	'OSS_BUCKET' => '',
	'OSS_URL' => 'oss.hiici.com',		//如果自定义了OSS_URL
);
