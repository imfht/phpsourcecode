<?php
error_reporting(E_ALL);

require './PHPMailer/PHPMailerAutoload.php';

$mail = new PHPMailer;

$mail->isSMTP();

//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
$mail->SMTPDebug = 2;
$mail->Debugoutput = 'html';

$mail->Host = "smtp.qq.com";
$mail->Port = 25;

$mail->SMTPAuth = true;
$mail->Username = "1275782053@qq.com";
$mail->Password = "abshanghai0301";

$mail->CharSet = 'utf-8';
$mail->Encoding = "base64";

$mail->setFrom('1275782053@qq.com', '上海通耀信息科技有限公司 市场部');
$mail->addReplyTo('1275782053@qq.com', '上海通耀信息科技有限公司 市场部');
$mail->addAddress('liy@tyyouxi.com', '尊敬的《数码宝贝:圣十字军》用户');

//$mail->Subject = iconv("UTF-8", "GB2312", '《数码宝贝:圣十字军》微信活动获奖信息通知');
$mail->Subject = '《数码宝贝:圣十字军》微信活动获奖信息通知';

$body = file_get_contents('contents.html');
$body = str_replace('__code__', '123456', $body);
//$body = iconv("UTF-8", "GB2312", $body);

$mail->msgHTML($body);
$mail->AltBody = 'This is a plain-text message body';

@$mail->send();

/*
if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    echo "Message sent!";
}
*/

/*
$html = <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
亲爱的玩家，您好！<br/>
恭喜您通过参与《数码宝贝：圣十字军》微信活动获得至尊数码礼包（数码宝贝正版手办），<br/>
至尊礼包码：XXXXXXXX<br/>
上海通耀科技有限公司感谢您的参与，谢谢<br/>
</body>
</html>
EOF;
*/