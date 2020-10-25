<?php
/*
 * @varsion		Winner权限管理系统 3.0var
 * @package		程序设计深圳市九五时代科技有限公司设计开发
 * @copyright	Copyright (c) 2010 - 2015, d-winner, Inc.
 * @link		http://www.d-winner.com
 */

//PHPMailer发送邮件插件实例化类
class MailPublic extends Action {
	//PHPMailer操作
	public $mailObj;
	public function set($subject = '',$body = '',$m_cfg){
		date_default_timezone_set('Asia/Shanghai');							//设定时区东八区
		import('ORG.Net.PHPMailer.PHPMailerAutoload');
		//import('ORG.Net.PHPMailer.smtp');
		$mail = new PHPMailer; 	
		$this->mailObj = $mail;										//new一个PHPMailer对象出来
		$body = preg_replace("/\\*/",'',$body); 							//对邮件内容进行必要的过滤
		$mail->CharSet ="UTF-8";										//设定邮件编码，默认ISO-8859-1
		$mail->IsSMTP(); 													// 设定使用SMTP服务
		$mail->SMTPDebug  = 1; 
		$mail->SetLanguage('zh_cn');                  							// 启用SMTP调试功能
		$mail->IsError = 1;																// 1 = errors and messages
																			// 2 = messages only
		$mail->SMTPAuth = true; 											// 启用 SMTP 验证功能
		if($m_cfg['ssl']){
			$ssl = true;
		}else{
			$ssl = false;
		}           	    	 					
		$mail->SMTPSecure = $ssl;                 							// 安全协议
		$mail->Host = $m_cfg['smtp'];      								// SMTP 服务器
		$mail->Port = $m_cfg['port'];                   					// SMTP服务器的端口号
		$mail->Username = $m_cfg['email'];  									// SMTP服务器用户名
		$mail->Password = $m_cfg['pwd'];            						// SMTP服务器密码
		$mail->SetFrom($m_cfg['email'], $m_cfg['username']);
		$mail->AddReplyTo($m_cfg['email'],$m_cfg['username']);
		$mail->Subject = $subject;
		$mail->AltBody = ''; 		
		$mail->MsgHTML($body);
		//$mail->AddAttachment("images/phpmailer.gif");      // attachment 
		if(!$mail->Send()) {
			return false;
		} else {
			return true;
		}
	}
}