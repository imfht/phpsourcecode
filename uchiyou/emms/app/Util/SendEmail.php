<?php
namespace App\Util;
use Illuminate\Support\Facades\Mail;

class SendEmail{
	/*
	 * 使用 laravel 自带的 email。不成功，错误信息如下
	 * laravel but got code "530", with message "530 Authentication required
	 * 
	 */
	public static function sendEmailTest(){
		$uid = 'uid123';
		$code = 'activeCode';
		$data = ['uid'=>$uid, 'activationcode'=>$code];
		Mail::send('email/activemail', $data, function($message)
		{
			$message->to('uchiyou@sina.com')->subject('欢迎注册我们的网站，请激活您的账号！');
		});
	}

	/*
	 * 使用 PHPMailer 发送邮件--已成功
	 */
	public static function testPHPMailer(){
		$mail = new \PHPMailer();
		//$mail->SMTPDebug = 3;                               // Enable verbose debug output
		
		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->Host = 'smtp.qq.com';  // Specify main and backup SMTP servers
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Username = '1373918920@qq.com';                 // SMTP username
		$mail->Password = 'ligrtchzoisvhjif';                           // SMTP password
		$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
		$mail->Port = 465;                                    // TCP port to connect to
		
		$mail->setFrom('1373918920@qq.com', 'Mailer');
		$mail->addAddress('uchiyou@sina.com', 'U');     // Add a recipient
		//$mail->addAddress('ellen@example.com');               // Name is optional
		//$mail->addReplyTo('uchiyou@sina.com', 'Information');
		//$mail->addCC('cc@example.com');
		//$mail->addBCC('bcc@example.com');
		
	//	$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
	//	$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
		$mail->isHTML(true);                                  // Set email format to HTML
		
		$mail->Subject = 'Here is the subject';
		$mail->Body    = 'This is the HTML message body <b>in bold!</b>';
		$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
		
		if(!$mail->send()) {
			return 'Message could not be sent.'.
			 'Mailer Error: ' . $mail->ErrorInfo;
		} else {
			return 'Message has been sent';
		}
	}
	public static function postPHPMailer($to,$subject = '',$body = ''){
		//Author:Jiucool WebSite: http://www.jiucool.com
		//$to 表示收件人地址 $subject 表示邮件标题 $body表示邮件正文
		//error_reporting(E_ALL);
		error_reporting(E_STRICT);
		date_default_timezone_set('Asia/Shanghai');//设定时区东八区
		$mail             = new \PHPMailer(); //new一个PHPMailer对象出来
	//	$body            = eregi_replace("[\]",'',$body); //对邮件内容进行必要的过滤
		$mail->CharSet ="GBK";//设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
		$mail->IsSMTP(); // 设定使用SMTP服务
		$mail->SMTPDebug  = 1;                     // 启用SMTP调试功能
		// 1 = errors and messages
		// 2 = messages only
		$mail->SMTPAuth   = true;                  // 启用 SMTP 验证功能
		$mail->SMTPSecure = "ssl";                 // 安全协议，可以注释掉
		$mail->Host       = 'stmp.sina.com';      // SMTP 服务器
		$mail->Port       = 465;                   // SMTP服务器的端口号
		$mail->Username   = 'uchiyou@sina.com';  // SMTP服务器用户名，PS：我乱打的
		$mail->Password   = 'zhouyou1373918';            // SMTP服务器密码
		$mail->SetFrom('uchiyou@sina.com', '发件人');
		$mail->AddReplyTo('uchiyou@sina.com','收件人');
		$mail->Subject    = $subject;
		$mail->AltBody    = 'To view the message, please use an HTML compatible email viewer!'; // optional, comment out and test
		$mail->MsgHTML($body);
		$mail->AddAddress($to);
		//$mail->AddAttachment("images/phpmailer.gif");      // attachment
		//$mail->AddAttachment("images/phpmailer_mini.gif"); // attachment
		if(!$mail->Send()) {
			echo 'Mailer Error: ' . $mail->ErrorInfo;
		} else {
			//        echo "Message sent!恭喜，邮件发送成功！";
		}
	}
	
}