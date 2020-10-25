<?php 
namespace Weixin\Controller;
use Common\Controller\BaseController;

/**
 * 运行在公众号连接的回调模式下
 * 对接微信服务器，响应来自其推送的普通消息或操作事件
 */
class ServiceController extends BaseController
{
	protected function _initialize()
	{
		if( I('get.encrypt_type')=='raw' OR I('get.encrypt_type')==NULL ){
			define('ENCRYPTED', false);
		}else{
			define('ENCRYPTED', I('get.encrypt_type'));
		}

		$this->loadSettings(); //加载系统配置信息到C('settings')中
	}

	/**
	 * 监听微信公众平台之前，进行消息真实性验证
	 * 验证算法参照官方开发文档
	 */
	public function _before_listen()
	{
		$tmpArr = array(C('settings.weixin_Token'), I('get.timestamp'), I('get.nonce'));
        sort($tmpArr, SORT_STRING);
		$tmpStr = sha1( implode($tmpArr) );
		
		/* 签名不匹配， 抛出异常， 终止请求 */
		if( $tmpStr != I('get.signature') )
			throw new \Think\Exception("消息签名不匹配，伪造微信消息，不予处理");
	}

	/**
	 * 监听微信公众平台推送的普通消息或操作事件
	 */
	public function listen()
	{
		if( I('get.echostr')==NULL ){ //非服务器接入请求
			$rawMsg = file_get_contents("php://input");

			if( ENCRYPTED ){ //当前消息被加密了, 处于加密模式或者兼容模式
				$realMsg = decryptMsg($rawMsg, I('get.msg_signature'), I('get.timestamp'), I('get.nonce'));
			}else{ //消息未加密, 处于明文模式
				$realMsg = $rawMsg;
			}

			$msgType = getMsgField('MsgType', 'string', $realMsg); //取得请求消息的类型

			$this->_dispatch($msgType, $realMsg);
		}else{ //公众平台保存设定后发起的服务器接入验证请求
			$this->_weixinConnect(I('get.echostr'));
		}
	}



	/**
	 * 针对不同消息事件类型，调度请求到特定的消息事件处理器中
	 * @param string $msgType  消息类型
	 * @param string $realMsg  解密后的消息体
	 */
	private function _dispatch($msgType, $realMsg)
	{
		switch ($msgType) {
			case 'text':
			case 'image':
			case 'voice':
			case 'video':
			case 'location':
			case 'link':
							$ctlName = 'Ordinary';
							$actionName = strtolower($msgType);
							R("Weixin/$ctlName/$actionName", array($realMsg), 'Event');
							break;
			
			case 'event':
							$eventType = getMsgField('Event', 'string', $realMsg); //取得消息的事件类型
							$this->_dispatchEvent($eventType, $realMsg);
							break;
			
			default: 
				echo '';die;
		}		
	}

	/**
	 * 针对不同的事件类型，调度请求到特定的事件处理器中
	 * @param string $eventType  事件类型
	 * @param string $realMsg  解密后的消息体
	 */
	private function _dispatchEvent($eventType, $realMsg)
	{
		switch ($eventType) {
			case 'subscribe':
			case 'unsubscribe':
			case 'SCAN':
			case 'LOCATION':
			case 'CLICK':
			case 'VIEW':
							$ctlName = 'Opration';
							$actionName = strtolower($eventType);
							R("Weixin/$ctlName/$actionName", array($realMsg), 'Event');
							break;
			
			default: 
				echo '';die;
		}		
	}

	/**
	 * 公众平台每次保存设定都会发起一次服务器接入验证请求，正确响应回显子串后才能正常接入公众平台
	 */
	private function _weixinConnect($echoStr)
	{
		echo $echoStr;		//接入成功
		exit;
	}	

}
?>