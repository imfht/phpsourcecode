<?php
/**
 * 我们将用此文件向你展示如何通过 Printemps Framework 自带的邮件类 Prinemps_Mail 来发邮件
 */
class mailController extends Printemps{

	function __construct(){
		parent::__construct();
	}

	function index(){
		/**
		 * 使用 smtp 方式发件，可以分开设置，或者快速设置
		 */
		$mail = new Printemps_Mail('smtp');			//实例化 Printemps_Mail 类
		$mail->server('smtp.gov.cn(wu)');			//设置 SMTP 服务器
		$mail->port('25');					//设置 SMTP 服务器地址，一般是25
		$mail->user('someone@yoursmtp');			//设置 SMTP 用户
		$mail->password('password233666');			//设置 SMTP 密码
		$mail->sendTo('sendto@someone.com');			//设置发件对象
		$mail->sender('someone@yoursmtp');			//设置发件者邮箱
		$mail->title('Printemps Framework SMTP Test Mail');	//设置邮件标题
		$mail->content('<p>Hello gay!</p>');			//设置邮件内容
		$mail->sendnow();					//最后，用sendnow()函数发件吧 : )

		/**
		 * 或者，可以直接使用快速设置函数：$mail->fastset();
		 */
		$mail->fastset('SMTP服务器','端口','用户名','密码','谁发的','发给谁','邮件名','邮件内容');
		$mail->sendnow();

	}
}