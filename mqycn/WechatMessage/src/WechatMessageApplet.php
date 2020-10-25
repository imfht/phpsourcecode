<?php

require_once dirname(__FILE__) . '/WechatMessageServices.php';

/**
 * 类名：WechatMessageApplet
 * 作者：mqycn
 * 博客：http://www.miaoqiyuan.cn
 * 源码：https://gitee.com/mqycn/WechatMessage
 * 说明：微信小程序消息处理核心类
 */

abstract class WechatMessageApplet extends WechatMessageCommon {

	/**
	 * 客户服务接口提交
	 */
	protected function onSendServicesMessage($body) {}

	/**
	 * 客户服务接口返回
	 */
	protected function onResultServicesMessage($body) {}

	/**
	 * 上传信息
	 */
	protected function sendMessage($message_body) {
		$req = $this->messageToJson($message_body);
		$this->onSendServicesMessage($req);
		$rest = $this->api("message/custom/send", $req);
		$this->onResultServicesMessage(json_encode($rest));
		if ($rest['errcode'] === 0) {
			return 'success';
		} else {
			return 'fail';
		}
	}

	/**
	 * 转换消息类型
	 */
	protected function messageToJson($message_body) {
		$message = simplexml_load_string("<xml>${message_body}</xml>", 'SimpleXMLElement', LIBXML_NOCDATA);
		$data = array(
			'touser' => $this->fromUsername,
			'msgtype' => trim($message->MsgType),
		);
		switch (trim($message->MsgType)) {
		case "text":
			$data['text'] = array(
				"content" => trim($message->Content),
			);
			break;
		case "image":
			$data['image'] = array(
				"media_id" => trim($message->Image->MediaId),
			);
			break;
		case "voice":
			$data['voice'] = array(
				"media_id" => trim($message->Voice->MediaId),
			);
			break;
		case "video":
			$data['video'] = array(
				"media_id" => trim($message->Video->MediaId),
				"title" => trim($message->Video->Title),
				"description" => trim($message->Video->Description),
			);
			break;
		case "news":
			$data['news'] = array(
				"articles" => array(),
			);
			break;
		default:
			$data['msgtype'] = 'text';
			$data['text'] = array(
				"content" => "消息(" . $message->MsgType . ")转换未实现",
			);
		}
		if (version_compare(PHP_VERSION, '5.4.0', '<')) {
			$str = json_encode($data);
			function fun($matchs) {
				return iconv('UCS-2BE', 'UTF-8', pack('H4', $matchs[1]));
			};
			$str = preg_replace_callback("#\\\u([0-9a-f]{4})#i", fun, $str);
			return $str;
		} else {
			return json_encode($data, JSON_UNESCAPED_UNICODE);
		}
	}
}