<?php 
/*
Plugin Name: Smtp email
Plugin URI: https://eyaslife.com
Description: 开启smtp发送email
Usage: 
Version: 1.2
Author: Pedro Eyas
Author URI: https://eyaslife.com
*/
//使用smtp发送邮件，这里是QQ邮箱，你可以参照你使用的邮箱具体设置SMTP
if(!function_exists('ey_mail_setup')):
function ey_mail_setup( $phpmailer ) {
	$phpmailer->FromName = 'service@wonya.com'; //发件人
	$phpmailer->Host = 'smtp.qq.com'; //修改为你使用的SMTP服务器
	$phpmailer->Port = 465; //SMTP端口
	$phpmailer->Username = '893521870@qq.com'; //邮箱账户   
	$phpmailer->Password = 'enter your password here'; //邮箱密码
	$phpmailer->From = '893521870@qq.com'; //你的邮箱   
	$phpmailer->SMTPAuth = true;   
	$phpmailer->SMTPSecure = 'ssl'; //tls or ssl （port=25留空，465为ssl）
	$phpmailer->IsSMTP();
}
add_action('phpmailer_init', 'mail_smtp');
endif;