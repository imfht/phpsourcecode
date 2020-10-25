<?php
/**
 * Mail：Printemps Framework 发件文件
 * Printemps Framework : Mail.pri.php
 * 2015 Printemps Framework
 */
class Printemps_Mail{
	/* SMTP 部分 */
	/**
	 * 初始化 smtp class
	 * @var object
	 */
	public $smtp;
	/**
	 * Smtp Server Info
	 */
	private $sServer;			//SMTP服务器
	private $sPort;				//SMTP服务器端口
	private $sender;			//发件邮箱
	private $sUser;				//SMTP用户名
	private $sPwd;				//SMTP密码
	private $ssl;				//是否使用SSL加密
	/**
	 * Init email content and receiver before sending
	 */
	private $sendTo;			//发送给谁
	private $mailTitle;			//邮件标题
	private $mailContent;			//右键内容
	private $mailType;			//邮件类型，TXT或者HTML
	/**
	 * And.....system param
	 */
	private $method;

	function __construct($method){
		$this->method = $method;
	}

	/** SMTP 设置 */
	//SMTP主机
	public function server($server){
		$this->sServer = $server;
	}
	//SMTP端口
	public function port($port){
		$this->sPort = $port;
	}
	//发送者邮箱
	public function sender($emailAddress){
		$this->sender = $emailAddress;
	}
	//SMTP用户
	public function user($user){
		$this->sUser = $user;
	}
	//SMTP密码
	public function password($password){
		$this->sPwd = $password;
	}
	//是否通过SSL
	public function ssl($openSSL){
		$this->ssl = $openSSL;
		if(empty($this->sPort)){
			$this->ssl ? $this->sPort = 465 : $this->sPort = 25;
		}
	}
	//发送给
	public function sendTo($toAddress){
		$this->sendTo = $toAddress;
	}
	//邮件标题
	public function title($title){
		$this->mailTitle = $title;
	}
	//邮件内容
	public function content($content){
		if(empty($this->mailType)){
			if(preg_match("(<html>|<div>|<p>|<font>)", $content))
				$this->mailType = 'HTML';
			else
				$this->mailType = 'TXT';
		}
		$this->mailContent = $content;
		return $this->mailType;
	}
	//邮件内容种类
	public function contentType($type){
		$this->mailType = $type;
	}
	//快速设定
	public function fastset($s,$p,$u,$m,$who,$to,$t,$c){
		$this->sServer = $s;
		$this->sPort = $p;
		$this->sUser = $u;
		$this->sPwd = $m;
		$this->sender = $who;
		$this->sendTo = $to;
		$this->mailTitle = $t;
		$this->mailContent = $this->content($c);
	}
	/*立即发送*/
	public function sendnow(){
		if($this->method == 'smtp'){
			@$this->smtp = new smtp($this->sServer,$this->sPort,true,$this->sUser,$this->sPwd);
			@$this->smtp->sendmail($this->sendTo,$this->sender,$this->mailTitle,$this->mailContent,$this->mailType);
		}
	}
}