<?php
namespace Core\Library;

use Core\Config;
use Library\PHPMailer\PHPMailer;

/**
 * Class Email
 * @package Core\Library
 */
class Email
{
    public static function send($toMail, $name, $title, $body, $isHtml = true)
    {
        $emailSettings = Config::get('emailSettings', array());
        $obEmail = new PHPMailer();
        if ($emailSettings['type'] == 'smtp') {
            $obEmail->isSMTP();
            $obEmail->Host = $emailSettings['smtp_host'];
            $obEmail->SMTPAuth = true;
            $obEmail->Username = $emailSettings['smtp_user_name'];
            $obEmail->Password = $emailSettings['smtp_password'];
            if ($emailSettings['smtp_secure']) {
                $obEmail->SMTPSecure = 'tls';
            }
            $obEmail->Port = 587;
        } elseif ($emailSettings['type'] == 'mail') {
            $obEmail->isMail();
        }
        $obEmail->setFrom($emailSettings['from_email'], $emailSettings['from_name']);
        $obEmail->addAddress($toMail, $name); // Add a recipient
        $obEmail->addReplyTo($emailSettings['reply_to_email'], $emailSettings['reply_to_name']);
        $obEmail->isHTML($isHtml);
        $obEmail->Subject = $title;
        $obEmail->Body = $body;
        $obEmail->AltBody = $body;
        if ($obEmail->send() === false && $obEmail->ErrorInfo) {
            //echo 'Message could not be sent.';
            //echo 'Mailer Error: ' . $mail->ErrorInfo;
            return array(
                'state' => false,
                'manage' => $obEmail->ErrorInfo
            );
        } else {
            return array(
                'state' => true,
                'manage' => '发送成功'
            );
        }
    }
}
