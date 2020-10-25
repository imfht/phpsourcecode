<?php
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package 消息服务服务接口
*/

defined('INPOP') or exit('Access Denied');

//消息服务接口
interface _mns{
	//获取消息服务详情
	static function getInfo();
	//添加消息服务
	static function doAdd();
	//更新消息服务
	static function doUpdate();
	//获取消息服务列表
	static function doList();
	//获取消息服务总数
	static function doCount();
	//获取消息日志详情
	static function getMnsLogInfo();
	//添加消息日志
	static function addMnsLog();
	//更新消息日志
	static function updateMnsLog();
	//获取消息日志列表
	static function getMnsLogList();
	//初始化邮件服务
	static function initMail();
	//发送邮件
	static function sendMail();
	//初始化短信服务
	static function initSM();
	//发送短信
	static function sendSM();
	//初始化微信服务
	static function initWeixin();
	//发送微信
	static function sendWeixin();
	//格式化内容
	static function formatString();
}

?>