<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2020 http://zswin.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: zswin.cn
// +----------------------------------------------------------------------
function asyn_sendmail($mail,$type) 
     {  
$domain=$_SERVER[HTTP_HOST];

$url=U('Home/Home/send_mail',array('to'=>$mail,'type'=>$type));

$par=time();

$header="POST $url HTTP/1.0\r\n";

$header.="Content-Type: application/x-www-form-urlencoded\r\n";

$header.="Content-Length: ".strlen($par)."\r\n\r\n";

$fp=@fsockopen ($domain,80,$errno,$errstr,30);

fputs($fp,$header.$par);

fclose($fp);


}  

/**
 * 系统邮件发送函数
 * @param string $to 接收邮件者邮箱
 * @param string $name 接收邮件者名称
 * @param string $subject 邮件主题
 * @param string $body 邮件内容
 * @param string $attachment 附件列表
 * @茉莉清茶 57143976@qq.com
 */
function send_mail($to = '', $subject = '', $body = '', $name = '', $attachment = null)
{
    if (is_sae()) {
        return sae_mail($to, $subject, $body, $name);
    } else {
        return send_mail_local($to, $subject, $body, $name, $attachment);
    }
}

/**
 * SAE邮件发送函数
 * @param string $to 接收邮件者邮箱
 * @param string $name 接收邮件者名称
 * @param string $subject 邮件主题
 * @param string $body 邮件内容
 * @param string $attachment 附件列表
 * @茉莉清茶 57143976@qq.com
 */
function sae_mail($to = '', $subject = '', $body = '', $name = '')
{
    if ($to == '') {
        $to = C('MAIL_SMTP_CE'); //邮件地址为空时，默认使用后台默认邮件测试地址
    }
    if ($name == '') {
        $name = C('WEB_SITE'); //发送者名称为空时，默认使用网站名称
    }
    if ($subject == '') {
        $subject = C('WEB_SITE_TITLE'); //邮件主题为空时，默认使用网站标题
    }
    if ($body == '') {
        $body = C('WEB_SITE_DESCRIPTION'); //邮件内容为空时，默认使用网站描述
    }
    $mail = new SaeMail();
    $mail->setOpt(array(
        'from' => C('MAIL_SMTP_USER'),
        'to' => $to,
        'smtp_host' => C('MAIL_SMTP_HOST'),
        'smtp_username' => C('MAIL_SMTP_USER'),
        'smtp_password' => C('MAIL_SMTP_PASS'),
        'subject' => $subject,
        'content' => $body,
        'content_type' => 'HTML'
    ));

    $ret = $mail->send();
    return $ret ? true : $mail->errmsg(); //返回错误信息
}

function is_sae()
{
    return function_exists('sae_debug');
}

/**
 * 用常规方式发送邮件。
 */
function send_mail_local($to = '', $subject = '', $body = '', $name = '', $attachment = null)
{
    $from_email = C('MAIL_SMTP_USER');
    $from_name = C('WEB_SITE');
    $reply_email = '';
    $reply_name = '';

    require_once('./ThinkPHP/Library/Vendor/PHPMailer/phpmailer.class.php');
    $mail = new PHPMailer; //实例化PHPMailer
    $mail->CharSet = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    

     $mail->IsSMTP(); // 设定使用SMTP服务
     $mail->SMTPDebug = 0; // 关闭SMTP调试功能
    // 1 = errors and messages
    // 2 = messages only
    
    
    $mail->SMTPAuth = true; // 启用 SMTP 验证功能
    $mail->SMTPSecure = ''; // 使用安全协议
    $mail->Host = C('MAIL_SMTP_HOST'); // SMTP 服务器
    $mail->Port = C('MAIL_SMTP_PORT'); // SMTP服务器的端口号
    $mail->Username = C('MAIL_SMTP_USER'); // SMTP服务器用户名
    $mail->Password = C('MAIL_SMTP_PASS'); // SMTP服务器密码

   
    
    
    
 
    
    
    
    
    $mail->SetFrom($from_email, $from_name);
    $replyEmail = $reply_email ? $reply_email : $from_email;
    $replyName = $reply_name ? $reply_name : $from_name;
    if ($to == '') {
        $to = C('MAIL_SMTP_CE'); //邮件地址为空时，默认使用后台默认邮件测试地址
    }
    if ($name == '') {
        $name = C('WEB_SITE'); //发送者名称为空时，默认使用网站名称
    }
    if ($subject == '') {
        $subject = C('WEB_SITE_TITLE'); //邮件主题为空时，默认使用网站标题
    }
    if ($body == '') {
        $body = C('WEB_SITE_DESCRIPTION'); //邮件内容为空时，默认使用网站描述
    }
    $mail->AddReplyTo($replyEmail, $replyName);
    $mail->Subject = $subject;
    $mail->MsgHTML($body); //解析
    $mail->AddAddress($to, $name);
    if (is_array($attachment)) { // 添加附件
        foreach ($attachment as $file) {
            is_file($file) && $mail->AddAttachment($file);
        }
    }
    return $mail->Send() ? true : $mail->ErrorInfo; //返回错误信息
}