<?php
/**
* Created by PhpStorm.
* User: caipeichao
* Date: 4/3/14
* Time: 5:15 PM
*/

/**
* 自动缓存
* @param $key
* @param $interval
* @param $func
* @return mixed
*/
function op_cache($key, $func, $interval){
	$result = S($key);
	if (!$result) {
		$result = $func();
		S($key, $result, $interval);
	}
	return $result;
}

/**清理全部缓存
* @auth 陈一枭
*/
function clean_all_cache(){
	$dirname = RUNTIME_PATH;

	//清文件缓存
	$dirs = array($dirname);
	//清理缓存
	foreach ($dirs as $value) {
		rmdirr($value);
	}
	@mkdir($dirname, 0777, true);
}


function rmdirr($dirname){
	if (!file_exists($dirname)) {
		return false;
	}
	if (is_file($dirname) || is_link($dirname)) {
		return unlink($dirname);
	}
	$dir = dir($dirname);
	if ($dir) {
		while (false !== $entry = $dir->read()) {
			if ($entry == '.' || $entry == '..') {
				continue;
			}
			rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);
		}
	}
	$dir->close();
	return rmdir($dirname);
}

/*发送邮件函数*/
function sendMail($to, $title, $content) {
	Vendor('PHPMailer.PHPMailerAutoload');   
	$mail = new PHPMailer(); //实例化
	$mail->IsSMTP(); // 启用SMTP
	$mail->Host=C('MAIL_HOST'); //smtp服务器的名称（这里以QQ邮箱为例）
	$mail->SMTPAuth = C('MAIL_SMTPAUTH'); //启用smtp认证
	$mail->Username = C('MAIL_USERNAME'); //你的邮箱名
	$mail->Password = C('MAIL_PASSWORD') ; //邮箱密码
	$mail->From = C('MAIL_USERNAME'); //发件人地址（也就是你的邮箱地址）
	$mail->FromName = C('MAIL_FROMNAME'); //发件人姓名
	$mail->AddAddress($to,"尊敬的用户");
	$mail->WordWrap = 50; //设置每行字符长度
	$mail->IsHTML(C('MAIL_ISHTML')); // 是否HTML格式邮件
	$mail->CharSet=C('MAIL_CHARSET'); //设置邮件编码
	$mail->Subject =$title; //邮件主题
	$mail->Body = $content; //邮件内容
	$mail->AltBody = "这是一个纯文本的身体在非营利的HTML电子邮件客户端"; //邮件正文不支持HTML的备用显示
	return $mail->Send() ? true : $mail->ErrorInfo;
}