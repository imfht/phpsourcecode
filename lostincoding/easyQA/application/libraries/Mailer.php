<?php

require_once 'PHPMailer/PHPMailerAutoload.php';

/*发送邮件*/

class Mailer
{
    private $now = null;

    //构造方法
    public function __construct()
    {
        $this->now = date('Y-m-d H:i:s');
    }

    //发送邮件
    public function post($params)
    {
        //Create a new PHPMailer instance
        $mail = new PHPMailer;
        if (isset($params['SMTPSecure']) && !empty($params['SMTPSecure'])) {
            $mail->SMTPSecure = $params['SMTPSecure'];
        }
        //设置编码
        $mail->CharSet = $params['CharSet'];
        //Tell PHPMailer to use SMTP
        $mail->isSMTP();
        //Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        $mail->SMTPDebug = $params['SMTPDebug'];
        //Ask for HTML-friendly debug output
        $mail->Debugoutput = 'html';
        //Set the hostname of the mail server
        $mail->Host = $params['Host'];
        //Set the SMTP port number - likely to be 25, 465 or 587
        if (isset($params['Port']) && !empty($params['Port'])) {
            $mail->Port = $params['Port'];
        } else {
            $mail->Port = 25;
        }
        //Whether to use SMTP authentication
        $mail->SMTPAuth = true;
        //Username to use for SMTP authentication
        $mail->Username = $params['Username'];
        //Password to use for SMTP authentication
        $mail->Password = $params['Password'];
        //Set who the message is to be sent from
        $mail->setFrom($params['From'][0], $params['From'][1]);
        //Set an alternative reply-to address
        $mail->addReplyTo($params['From'][0], $params['From'][1]);
        //Set who the message is to be sent to
        $mail->addAddress($params['Address'][0], $params['Address'][1]);
        //Set the subject line
        $mail->Subject = $params['Subject'];
        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        $mail->msgHTML($params['msgHTML']);
        //Replace the plain text body with one created manually
        //$mail->AltBody = 'This is a plain-text message body';

        //send the message, check for errors
        if ($params['SMTPDebug'] != 0) {
            var_dump($mail->ErrorInfo);
        }
        return $mail->send();
    }
}
