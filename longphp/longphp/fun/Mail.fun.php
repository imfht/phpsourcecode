<?php
if(!defined('DIR')){
	exit('Please correct access URL.');
}

function send_mail($tomail, $subject, $body, $cc = null){
    require_once DIR_CLASS.'phpmail/class.phpmailer.php';
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Host = 'smtp.php.net';
    $mail->SMTPAuth = true;
    $mail->Username = 'php@php.net';
    $mail->Password = 'php';
    $mail->From = 'php@php.net';
    $mail->FromName = 'php team';
    $mail->CharSet = "UTF-8";
    $mail->Encoding = "base64";
    $tomail = explode(',', $tomail);
    foreach((array)$tomail as $v){
        $v = trim($v);
        $mail->AddAddress($v);
    }
    $mail->AddReplyTo("php@php.net","php.net");
    $mail->IsHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->AltBody = 'text/html';

    if(!empty($cc)){
        $cc = explode(',', $cc);
        foreach((array)$cc as $v){
            $v = trim($v);
            $mail->AddCC($v);
        }
    }

    if($mail->Send()){
        return true;
    }else {
        return false;
    }
}
