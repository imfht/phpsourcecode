<?php

require_once dirname(__FILE__) . '/../../src/WechatMessageCommon.php';

/**
 * 类名：WechatMessageApp
 * 作者：mqycn
 * 博客：http://www.miaoqiyuan.cn
 * 源码：https://gitee.com/mqycn/WechatMessage
 * 说明：微信消息处理（例子）
 */
class WechatMessageApp extends WechatMessageCommon {
	protected function onMessage($request) {
		$this->addLog("message", "Request: ${request}");
	}
	protected function onSendMessage($body) {
		$this->addLog("message", "Response: ${body}");
	}
	protected function onValid($request) {
		$this->addLog("valid", $request);
	}
	protected function onSubscribeEvent() {
		$msg = "你好，非常感谢您的订阅。\n\n";
		return $this->textMessage($msg);
	}
	protected function onTextMessage($content) {
		return $this->textMessage("[自动回复]${content}");
	}
	protected function onImageMessage($image, $media_id) {
		return $this->imageMessage($media_id);
	}
	private function addLog($type, $content) {
		$content = date("Y-m-d H:i:s") . "	" . $content . "\n";
		file_put_contents(dirname(__FILE__) . "/log/${type}_" . date("Y-m-d") . ".log", $content, FILE_APPEND);
	}
}
