<?php
namespace app\common\util;
use email\PHPMailer;
use email\phpmailerException;
use email\SMTP;

class Email{
    
    private $obj = null;
    
    public function send($email,$title,$content){
        $result = $this->phpmailer($email,$title,$content);
        return $result;
    }
    
    private function phpmailer($email,$title,$content){
        if ($this->obj===null) {
            $this->obj = new PHPMailer();
        }
        $mail             = $this->obj;
        
        $mail->IsSMTP(); // telling the class to use SMTP
        
        $mail->CharSet = 'UTF-8'; //UTF-8设置邮件的字符编码，这很重要，不然中文乱码
        
        //$mail->AddReplyTo("2244484@qq.com","mckee");//回复地址
        $mail->FromName   = config('webdb.webname');
        
        $mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
        // 1 = errors and messages
        // 2 = messages only
        $mail->SMTPAuth   = true;                  // enable SMTP authentication
        $mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
        $mail->Host       = config('webdb.MailServer');      // sets GMAIL as the SMTP server
        $mail->Port       = config('webdb.MailPort');                   // set the SMTP port for the GMAIL server
        $mail->Username   = config('webdb.MailId');  // GMAIL username
        $mail->Password   = config('webdb.MailPw');            // GMAIL password
        
        $mail->From = config('webdb.MailId');
        
        $mail->Subject    = $title;
        
        $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // 当邮件不支持html时备用显示，可以省略
        
        $mail->MsgHTML($content);
        
        $mail->AddAddress($email, "");
        //$mail->AddAddress("2211141@qq.com", "John Doe");
        //$mail->IsHTML(true);
        
        if(!$mail->Send()) {
            return $mail->ErrorInfo;
        } else {
            return true;
        }
    }
    

}