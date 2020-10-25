<?php
require_once DIR_SYSTEM . 'mail/swift_required.php';
require_once DIR_SYSTEM . 'mail/swift_init.php';

final class Mail {
   protected $to;
   protected $from;
   protected $sender;
   protected $subject;
   protected $text;
   protected $html;
   protected $attachments = array();
   public $protocol = 'mail';
   public $smtp_owner;
   public $hostname;
   public $username;
   public $password;
   public $port = 25;
   public $timeout = 5;
   public $newline = "\n";
   public $crlf = "\r\n";
   public $verp = FALSE;
   public $parameter = '';

   
   public function setTo($to) {
      $this->to = $to;
   }

   public function setBcc($bcc) {
      $this->bcc = $bcc;
   }

   public function setFrom($from) {
      $this->from = $from;
   }

   public function addheader($header, $value) {
      $this->headers[$header] = $value;
   }

   public function setSender($sender) {
      $this->sender = html_entity_decode($sender, ENT_COMPAT, 'UTF-8');
   }

   public function setSubject($subject) {
      $this->subject = html_entity_decode($subject, ENT_COMPAT, 'UTF-8');
   }

   public function setText($text) {
      $this->text = $text;
   }

   public function setHtml($html) {
      $this->html = $html;
   }

   public function addAttachment($file, $filename = '') {
      if (!$filename) {
         $filename = basename($file);
      }

      $this->attachments[] = array(
         'filename' => $filename,
         'file'     => $file
      );
   }

   public function send() {
   	 // wont send mail if there is no mail setting.
	 if ($this->protocol == '0') {
		return 0;
	 }
   	
  	 if (!$this->to) {
         exit('Error: E-Mail to required!');
      }

      if (!$this->from) {
         exit('Error: E-Mail from required!');
      }

      if (!$this->sender) {
         exit('Error: E-Mail sender required!');
      }

      if (!$this->subject) {
         exit('Error: E-Mail subject required!');
      }

      if ((!$this->text) && (!$this->html)) {
         exit('Error: E-Mail message required!');
      }

      if ($this->protocol == 'mail') {
		// 使用本地的SMTP服务发送邮件
      	  $transport=Swift_SmtpTransport::newInstance();
          $message = Swift_Message::newInstance();
          $mailer = Swift_Mailer::newInstance($transport);
      }else{
      	// 使用外部的SMTP服务发送邮件
          $transport = Swift_SmtpTransport::newInstance($this->hostname,$this->port);
          $transport->setUsername( $this->username);
          $transport->setPassword($this->password);
          $mailer = Swift_Mailer::newInstance($transport);
          $message = Swift_Message::newInstance();
          $message->setSender($this->username,$this->sender);
      }

      if (is_array($this->to)) {
          $message->setTo($this->to);
      } else {
          $message->setTo(explode(',',$this->to));
      }

      $message->setSubject($this->subject);
      
      if(isset($this->sender))
         $message->setFrom(array($this->from => $this->sender));
      else
         $message->setFrom(array($this->from => ''));

      $message->setFormat('multipart/mixed');
      $message->setReplyTo($this->from,$this->sender);

        $message->setCharset('utf-8');

        foreach ($this->attachments as $attachment) {
         if (file_exists($attachment['file'])) {
            $message->attach(Swift_Attachment::fromPath($attachment['file'], 'image/jpeg')->setFilename( basename($attachment['filename'])));
         }
      }

      if (!$this->html) {
         $mail_body=$this->text;
      } else {
         $mail_body=$this->html;
      }


      if (!$this->html) {
         $message->setBody($mail_body,'text/plain');
      } else {
         $message->setBody($mail_body,'text/html' );

      }

      try{
      	$mailer->send($message);
      }
      catch (Swift_ConnectionException $e){
           echo 'There was a problem communicating with SMTP: ' . $e->getMessage();
      }

   }
}
?>
