<?php
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package 服务
*/

defined('INPOP') or exit('Access Denied');

include_once("config.php"); //加载配置

class mnsService extends mns implements _mns{

	public static $_instance; //用于单例模式
	public $mailService;
	public $smService;
	public $weixinService;
	
	//实例化(单例模式)
    public static function getInstance(){
        if(null === self::$_instance) self::$_instance = new self();
        return self::$_instance;
    }

	//获取消息服务详情
	static function getInfo($mnsid = 0){
		$id = (int)$mnsid;
		if($id < 1) return false;
		self::getInstance();
		$info = self::$_instance->getOne($id);
		$return = $info;
		return $return;
	}
	
	//获取消息服务列表
	static function doList($sql = '', $page = 1, $pagesize = PAGE_SIZE){
		//$sql where 里面的东西
		$offset = ($page - 1) * $pagesize;
		self::getInstance();
		$return = self::$_instance->getList($sql, '', $offset, $pagesize);
		return $return;
	}

	//添加消息服务
	static function doAdd($mnsArray = ''){
		self::getInstance();
		$time = time();
		$return = self::$_instance->add($mnsArray);		
		return $return;
	}

	//更新消息服务
	static function doUpdate($mnsid = 0,$mnsArray = ''){
		if($mnsid < 1) return false;
		self::getInstance();
		$return = self::$_instance->editBy($mnsArray, "mnsid=".$mnsid);
		return $return;	
	}

	//获取消息服务总数
	static function doCount(){
		return true;
	}

	//获取消息日志详情
	static function getMnsLogInfo($mnslogid = 0){
		if($mnslogid < 1) return false;
		$_mnsLog = new mnslog();
		$return = $_mnsLog->getOne($mnslogid);
		return $return;
	
	}
	//添加消息日志
	static function addMnsLog($mnsLogArray = ''){
		$_mnsLog = new mnslog();
		$return = $_mnsLog->add($mnsLogArray);
		return $return;
	
	}
	//更新消息日志
	static function updateMnsLog($mnslogid = 0,$mnsLogArray = ''){
		if($mnslogid < 1) return false;
		$_mnsLog = new mnslog();
		$return = $_mnsLog->editBy($mnsLogArray, "mnslogid=".$mnslogid);
		return $return;	
	
	}
	//获取消息日志列表
	static function getMnsLogList($sql = '', $page = 1, $pagesize = PAGE_SIZE){
		$offset = ($page - 1) * $pagesize;
		$_mnsLog = new mnslog();
		$return = $_mnsLog->getList($sql, '', $offset, $pagesize);
		return $return;	
	}

	//初始化邮件服务
	static function initMail($from = "", $fromName = "", $ext = "",$mailType = "jmail"){
		if(!is_array($ext)) return false;
		if($mailType == "jmail"){
			$mailServerUserName = $ext['mailServerUserName'];
			$mailServerPassword = $ext['mailServerPassword'];
			$smtpServer = $ext['smtpServer'];
			self::$_instance->mailService = new jmail($from, $fromName, $mailServerUserName, $mailServerPassword, $smtpServer);
			return true;
		}
		if($mailType == "sendcloud"){
			$apiKey = $ext['apiKey'];
			$apiUser = $ext['apiUser'];
			self::$_instance->mailService = new sendcloud($from, $fromName, $apiKey, $apiUser);
			return true;
		}
	}

	//发送邮件
	static function sendMail($Recipient = "", $Subject = "", $Body = "", $template = ""){
		$return = self::$_instance->mailService->sendMail($Recipient, $Subject, $Body, $template);
		return $return;
	}

	//初始化短信服务
	static function initSM($apiKey = "", $smChannel = "yunpian"){
		self::$_instance->smService = new yunpian($apiKey);
	}

	//发送短信
	static function sendSM($mobile = "", $sendData = ""){
		$return = self::$_instance->smService->doSend($sendData, $mobile);
		return $return;
	}

	//初始化微信服务
	static function initWeixin(){
		return true;
	}

	//发送微信
	static function sendWeixin(){
		return true;
	}

	//格式化内容
	static function formatString($input = ""){
		if(!$input) return false;
		$return = str_replace('[#', '{$sourceData[', $input);
		$return = str_replace('#]', ']}', $return);
		return $return;
	}

}

?>