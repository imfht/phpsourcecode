<?php
/*
* 对 PHPmailer 使用的一个简单封装
* @class name mail
* @author xuhm
* @email huming17@126.com
*/

/*
	* 调用示例,SMTP地址及配置认证信息存储于DZF  $_G
	* require SITE_ROOT.'./source/lib/mail/class_mail.php';
	* $mail = new mail;
	* $mail->addAddress('info@512cs.com', 'Huming Xu');
	* $mail->Subject = 'Here is the subject';
	* $mail->Body    = 'This is the message body';
	* //$mail->addAttachment('c:/temp/11-10-00.zip', 'new_name.zip');  //附件可选
	* 
	* if(!$mail->send())
	* {
	*    echo 'There was an error sending the message';
	*    exit;
	* }else{
	* 	echo 'Message was sent successfully';
	* }
*/


require SITE_ROOT.'./source/lib/mail/phpmailer/PHPMailerAutoload.php';

class mail extends PHPMailer {
    // Set default variables for all new objects
    public $From     = 'info@512cs.com';
    public $FromName = '512cs.com';
    public $Mailer   = 'smtp'; //Alternative to isSMTP()
    public $WordWrap = 75;
	public $Host = "smtp.exmail.qq.com";//Set the hostname of the mail server
	public $Port = 25; //Set the SMTP port number - likely to be 25, 465 or 587
	public $SMTPAuth = true;//Whether to use SMTP authentication
	public $Username = "info@512cs.com";//Username to use for SMTP authentication
	public $Password = "";//Password to use for SMTP authentication
	
    // Replace the default debug output function
    protected function edebug($msg) {
        print('My Site Error');
        print('Description:');
        printf('%s', $msg);
        exit;
    }

    //Extend the send function
    public function send() {
    	//DEBUG 使用DZF框架 全局变量初始化邮件服务器设置 start
    	global $_G;
    	$_G['setting']['server_mail'][1]['from'] = 'info@512cs.com';
    	$_G['setting']['server_mail'][1]['fromname'] = '512cs.com';
    	$_G['setting']['server_mail'][1]['host'] = 'smtp.exmail.qq.com';
    	$_G['setting']['server_mail'][1]['port'] = 25;
    	$_G['setting']['server_mail'][1]['username'] = "info@512cs.com";
    	$_G['setting']['server_mail'][1]['password'] = "huming17zy";
    	print_r($_G['setting']);
    	$this->From = $_G['setting']['server_mail'][1]['from'];
    	$this->FromName = $_G['setting']['server_mail'][1]['fromname'];
    	$this->Host = $_G['setting']['server_mail'][1]['host'];
    	$this->Port = $_G['setting']['server_mail'][1]['port'];
    	$this->Username = $_G['setting']['server_mail'][1]['username'];
    	$this->Password = $_G['setting']['server_mail'][1]['password'];
		//DEBUG 使用全局变量初始化邮件服务器设置 end
		
        return parent::send();
    }

    // Create an additional function
    public function do_something($something) {
        // Place your new code here
    }
}