<?php 
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package Jmail调用类
*/

defined('INPOP') or exit('Access Denied');

class jmail{

	public $object;
	public $smtpServer;

	//初始化
    public function __construct($from = "", $fromName = "", $mailServerUserName = "", $mailServerPassword = "", $smtpServer = ""){
		if(!$from) return false;
		if(!$fromName) return false;
		if(!$mailServerUserName) return false;
		if(!$mailServerPassword) return false;
		if(!$smtpServer) return false;

		$object = new COM("JMail.Message")or die("can not load Jmail");  
		//屏蔽例外错误，静默处理  
		$object->silent = true;  
		//编码必须设置，否则中文会乱码
		$object->charset = "gb2312";
		$object->ContentTransferEncoding = "base64";
		$object->Encoding = "base64";
		//设置邮件形式为HTML  
		$object->contentType = 'text/html';
		//发信人邮件地址和名称，能自定义，可以和邮件发送账号不同  
		$object->From = $from;
		//$object->FromName = iconv('utf-8', 'gb2312//IGNORE', $fromName); 
		$object->FromName = mb_convert_encoding($fromName, 'gb2312', 'utf-8'); 
		//发信邮件账号和密码  
		$object->MailServerUserName = $mailServerUserName;  
		$object->MailServerPassword = $mailServerPassword;
		//SMTP发信服务器地址
		$this->smtpServer = $smtpServer;
		$this->object = $object;
    }
	
	//发送邮件
	public function sendMail($Recipient, $Subject, $Body, $template){
		if(!$Recipient) return false;
		if(!$Subject) return false;
		if(!$Body) return false;
		$Subject .= "                            |"; 
		$Body = '<!DOCTYPE html><html lang="zh"><head><meta charset="gb2312"></head><body>'.$Body.'<hr></body></html>';
		//添加多个邮件接受者  
		$this->object->AddRecipient($Recipient);
		//系统发送都抄送管理员
		$this->object->AddRecipient("wang.jianning@jieandata.com");
		//邮件主题和正文信息  
		//$this->object->Subject = iconv('utf-8', 'gb2312//IGNORE', $Subject); 
		$this->object->Subject = mb_convert_encoding($Subject, 'gb2312', 'utf-8'); 
		//$this->object->Body = iconv('utf-8', 'gb2312//IGNORE', $Body);   
		$this->object->Body = mb_convert_encoding($Body, 'gb2312', 'utf-8');  
		try{  
			//发送的时候附带SMTP发信服务器地址  
			$done = $this->object->Send($this->smtpServer);
			if($done){
				$return = "success";
			}else{
				$return = "failure";
			}
		}catch(Exception $e){  
			$return = $e->GetMessage();  
		}
		return $return;
	}

}

?>