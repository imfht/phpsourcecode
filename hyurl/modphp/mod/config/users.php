<?php
/**
 * 本地用户数据，格式：
 * HTTP 基本认证：用户名:密码:用户组(级别)，密码使用 md5_crypt() 函数生成
 * HTTP 摘要认证：用户名:密码:用户组，密码使用 encrypt() 函数生成，密钥为 config('user.password.encryptKey')
 * 这些用户仅在系统未安装时有效，系统一旦安装，则使用数据库中的账户。
 */
return array(
	'admin:$1$rXslk1ym$BtqFh76NpcH.75gM5Nphf/:5', //admin:12345:5(基本认证)
	//'admin:mp6IoJCgfX90gHiAfX90gHmCgIN5:5', //admin:12345:5(摘要认证)
	);