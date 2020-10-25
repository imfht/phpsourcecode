<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
/**
 * 邮箱配置
 */
//邮件发送协议:mail, sendmail, smtp  默认值：mail
$config['protocol'] = 'smtp';
//smtp服务器地址 无默认值
$config['smtp_host'] = SITE_ADMIN_EMAIL_SMTP;
//邮件发送人
$config['username'] = SITE_ADMIN_EMAIL_USERNAME;
//邮件标题
$config['title'] = SITE_ADMIN_EMAIL_TITLE;
//smtp用户账号 无默认值
$config['smtp_user'] = SITE_ADMIN_EMAIL_USER;
//smtp用户密码 无默认值
$config['smtp_pass'] = SITE_ADMIN_EMAIL_PASSWORD;
//smtp端口 默认值：25
$config['smtp_port'] = SITE_ADMIN_EMAIL_PORT;
//smtp超时设置(单位：秒) 默认值:5
$config['smtp_timeout'] = 5;
//开启自动换行TRUE 或 FALSE (布尔值) 默认值:TRUE
$config['wordwrap'] = TRUE;
//自动换行时每行的最大字符数 默认值:76
$config['wrapchars'] = 76;
//邮件类型:text 或 html.发送 HTML 邮件比如是完整的网页,请确认网页中是否有相对路径的链接和图片地址，它们在邮件中不能正确显示 默认值:text
$config['mailtype'] = 'html';
//字符集(utf-8, iso-8859-1 等) 默认值:utf-8
$config['charset'] = 'utf-8';
//是否验证邮件地址TRUE 或 FALSE (布尔值) 默认值:FALSE
$config['validate'] = TRUE;
//Email 优先级:1, 2, 3, 4, 5. 1 = 最高. 5 = 最低. 3 = 正常 默认值:3
$config['priority'] = 1;
//换行符:\n	"\r\n" or "\n" or "\r"	(使用 "\r\n" to 以遵守RFC 822) 默认值:\n
$config['crlf'] = "\\r\\n";
//换行符:"\r\n" or "\n" or "\r"	 (使用 "\r\n" to 以遵守RFC 822) 默认值:\n
$config['newline'] = "\\r\\n";
//启用批量暗送模式 TRUE or FALSE (布尔值) 默认值:FALSE
$config['bcc_batch_mode'] = FALSE;
//批量暗送的邮件数 默认值:200
$config['bcc_batch_size'] = 200;




/* End of file email.php */
/* Location: ./application/config/email.php */
