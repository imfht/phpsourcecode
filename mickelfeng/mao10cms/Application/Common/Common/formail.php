<?php 

include_once ('class-phpmailer.php');
include_once ('class-smtp.php');

//发送邮件
function mc_mail($to,$subject,$body) {
$mail=new PHPMailer();

// 设置PHPMailer使用SMTP服务器发送Email
$mail->IsSMTP();

// 设置邮件的字符编码，若不指定，则为'UTF-8'
$mail->CharSet='UTF-8';

/*--------------- 邮件函数 ---------------*/

// 添加收件人地址，可以多次使用来添加多个收件人
$mail->AddAddress($to);

// 设置邮件标题
$mail->Subject=$subject;

// 设置邮件正文
$mail->Body=$body;

/*--------------- 邮件函数 ---------------*/

/*--------------- 设置 ---------------*/

// 这部分必须和你的实际账号相同，否则会验证出错。
$mail->From=mc_option('stmp_from');

// 设置发件人名字
$mail->FromName=mc_option('stmp_name');

// 设置SMTP服务器。这里使用网易的SMTP服务器。
$mail->Host=mc_option('stmp_host');

// 设置SMTP服务器端口。
$mail->SMTP_PORT = mc_option('stmp_port');

// 设置为“需要验证”
$mail->SMTPAuth=true;

// 设置用户名和密码，即SMTP服务的用户名和密码。
$mail->Username=mc_option('stmp_username');
$mail->Password=mc_option('stmp_password');

/*--------------- 设置 ---------------*/

// 发送邮件。
$mail->Send();
};
?>