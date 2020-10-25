<?php
$ret = array(
	'code' => 'faile',
	'msg' => '',
);

$email = $_GET['email'];
if(!$email) {
	$ret['msg'] = '获取邮件地址失败';
	echo json_encode($ret);
	exit();
}

$key = $_GET['key'];
if(!$key) {
	$ret['msg'] = '获取激活码失败';
	echo json_encode($ret);
	exit();
}

require './PHPMailer/PHPMailerAutoload.php';

$mail = new PHPMailer;

$mail->isSMTP();

//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
$mail->SMTPDebug = 0;
$mail->Debugoutput = 'html';

$mail->Host = "smtp.qq.com";
$mail->Port = 25;

$mail->SMTPAuth = true;
$mail->Username = "1275782053@qq.com";
$mail->Password = "abshanghai0301";

$mail->CharSet = 'utf-8';
$mail->Encoding = "base64";

$mail->setFrom('1275782053@qq.com', '《数码宝贝：圣十字军》运营团队');
$mail->addReplyTo('1275782053@qq.com', '《数码宝贝：圣十字军》运营团队');
$mail->addAddress($email, '尊敬的《数码宝贝:圣十字军》用户');

//$mail->Subject = iconv("UTF-8", "GB2312", '《数码宝贝:圣十字军》微信活动获奖信息通知');
$mail->Subject = '《数码宝贝:圣十字军》微信活动获奖信息通知';

$body = file_get_contents('contents.html');
$body = str_replace('__code__', $key, $body);
//$body = iconv("UTF-8", "GB2312", $body);

$mail->msgHTML($body);
$mail->AltBody = 'This is a plain-text message body';

if(!$mail->send()) {
	$ret['msg'] = '发生激活码邮件失败，请联系运营团队。';
} else {
	$ret['msg'] = '发送激活码邮件成功，请至邮箱查看';
	$ret['code'] = 'success';
}

echo json_encode($ret);

exit();