<?php

/**
 * 类名：WechatMessageCommon
 * 作者：mqycn
 * 博客：http://www.miaoqiyuan.cn
 * 源码：https://gitee.com/mqycn/WechatMessage
 * 说明：微信消息处理核心类，使用时直接 继承此类，重写 on[事件] 即可，使用 $this->textMessage 可以输出回复
 */
abstract class WechatMessageCommon {

	protected $fromUsername;
	protected $toUsername;
	protected $msgId;
	protected $msgTime;

	/**
	 * 验证消息事件
	 */
	protected function onValid($request) {}

	/**
	 * 原始消息事件
	 */
	protected function onMessage($request) {}

	/**
	 * 回复事件
	 */
	protected function onSendMessage($body) {}

	/**
	 * 文字消息事件
	 */
	protected function onTextMessage($content) {
		return $this->onOtherMessage('文字消息', array($content));
	}

	/**
	 * 图片消息事件
	 */
	protected function onImageMessage($image, $media_id) {
		return $this->onOtherMessage('图片消息', array($image, $media_id));
	}

	/**
	 * 语音消息事件
	 */
	protected function onVoiceMessage($media_id, $format, $to_text) {
		return $this->onOtherMessage('语音消息', array($media_id, $format, $to_text));
	}

	/**
	 * 视频消息事件
	 */
	protected function onVideoMessage($media_id, $media_thumb_id) {
		return $this->onOtherMessage('视频消息', array($media_id, $media_thumb_id));
	}

	/**
	 * 分享事件
	 */
	protected function onLinkMessage($title, $desc, $url) {
		return $this->onOtherMessage('分享消息', array($title, $desc, $url));
	}

	/**
	 * 上传事件
	 */
	protected function onFileMessage($file_name, $desc, $file_key, $file_md5, $file_size) {
		return $this->onOtherMessage('文件上传', array($file_name, $desc, $file_key, $file_md5, $file_size));
	}

	/**
	 * 位置信息事件
	 */
	protected function onLocationMessage($address, $lat, $lng, $scale) {
		return $this->onOtherMessage('位置信息', array($address, $lat, $lng, $scale));
	}

	/**
	 * 其他消息事件
	 */
	protected function onOtherMessage($event_type, $argument = array()) {
		return $this->textMessage('暂时不支持的消息' . $event_type . '，参数（' . join(', ', $argument) . ')');
	}

	/**
	 * 订阅事件
	 */
	protected function onSubscribeEvent() {
		return $this->textMessage('您好，请问有什么可以帮助您？');
	}

	/**
	 * 客服事件
	 */
	protected function onUserEnterTempsessionEvent() {
		return $this->textMessage('您好，请问有什么可以帮助您？');
	}

	/**
	 * 其他消息事件
	 */
	protected function onOtherEvent($event_type, $argument = array()) {
		return $this->textMessage('暂时不支持的事件' . $event_type . '，参数（' . join(', ', $argument) . ')');
	}

	/**
	 * 处理一个请求
	 */
	public function auto() {
		if (!isset($HTTP_RAW_POST_DATA)) {
			$HTTP_RAW_POST_DATA = file_get_contents('php://input');
		}
		if (!empty($HTTP_RAW_POST_DATA)) {
			return $this->answer($HTTP_RAW_POST_DATA);
		} else {
			return $this->valid();
		}
	}

	/**
	 * 验证
	 */
	public function valid() {
		$this->onValid($_SERVER['QUERY_STRING']);
		return isset($_GET["echostr"]) ? $_GET["echostr"] : '';
	}

	/**
	 * 自动应答
	 */
	public function answer($post_raw = false) {
		if ($post_raw === false) {
			if (!isset($HTTP_RAW_POST_DATA)) {
				$HTTP_RAW_POST_DATA = file_get_contents('php://input');
			}
			$post_raw = $HTTP_RAW_POST_DATA;
		}
		$this->onMessage($post_raw);
		if (empty($post_raw)) {
			die("Error");
		} else {
			$message_request = simplexml_load_string($post_raw, 'SimpleXMLElement', LIBXML_NOCDATA);

			//提取公共参数
			$this->fromUsername = trim($message_request->FromUserName);
			$this->toUsername = trim($message_request->ToUserName);
			$this->msgId = (int) $message_request->MsgId;
			$this->msgTime = (int) $message_request->CreateTime;

			switch (trim($message_request->MsgType)) {
			case 'text':
				return $this->onTextMessage($message_request->Content);
			case 'image':
				return $this->onImageMessage($message_request->PicUrl, $message_request->MediaId);
			case 'voice':
				return $this->onVoiceMessage($message_request->MediaId, $message_request->Format, $message_request->Recognition);
			case 'video':
				return $this->onVideoMessage($message_request->MediaId, $message_request->ThumbMediaId);
			case 'link':
				return $this->onLinkMessage($message_request->Title, $message_request->Description, $message_request->Url);
			case 'file':
				return $this->onFileMessage($message_request->Title, $message_request->Description, $message_request->FileKey, $message_request->FileMd5, $message_request->FileTotalLen);
			case 'location':
				return $this->onLocationMessage($message_request->Label, $message_request->Location_X, $message_request->Location_Y, $message_request->Scale);
			case 'event':
				return $this->onEvent($message_request);
			default:
				return $this->onOtherMessage($message_request->Event);
			}
		}
	}

	/**
	 * 事件
	 */
	protected function onEvent($message_request) {
		switch (trim($message_request->Event)) {
		case "subscribe":
			return $this->onSubscribeEvent();
		case "user_enter_tempsession":
			return $this->onUserEnterTempsessionEvent();
		default:
			return $this->onOtherEvent($message_request->Event);
		}
	}

	/**
	 * 回复消息
	 */
	protected function sendMessage($message_body) {
		$tpl = "<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime>%s</xml>";
		$body = sprintf($tpl, $this->fromUsername, $this->toUsername, time(), $message_body);
		$this->onSendMessage($body);
		return $body;
	}

	/**
	 * 回复文本信息
	 */
	protected function textMessage($content) {
		return $this->sendMessage("<MsgType><![CDATA[text]]></MsgType><Content><![CDATA[${content}]]></Content>");
	}

	/**
	 * 回复图片消息
	 */
	protected function imageMessage($media_id) {
		return $this->sendMessage("<MsgType><![CDATA[image]]></MsgType><Image><MediaId><![CDATA[${media_id}]]></MediaId></Image>");
	}

	/**
	 * 回复语音消息
	 */
	protected function voiceMessage($media_id) {
		return $this->sendMessage("<MsgType><![CDATA[voice]]></MsgType><Voice><MediaId><![CDATA[${media_id}]]></MediaId></Voice>");
	}

	/**
	 * 回复视频消息
	 */
	protected function videoMessage($media_id, $title = '', $desc = '') {
		return $this->sendMessage("<MsgType><![CDATA[video]]></MsgType><Video><MediaId><![CDATA[${media_id}]]></MediaId><Title><![CDATA[${title}]]></Title><Description>< ![CDATA[${desc}] ]></Description></Video>");
	}

	/**
	 * 回复分享信息
	 */
	protected function linkMessage($articles = array()) {
		$content = "<MsgType><![CDATA[news]]></MsgType><ArticleCount>" . count($articles) . "</ArticleCount><Articles>";
		foreach ($articles as $article) {
			list($title, $url, $image, $desc) = $this->linkMessageArticleInfo($article);
			$content .= "<item><Title><![CDATA[${title}]]></Title><Description><![CDATA[${desc}]]></Description><PicUrl><![CDATA[${image}]]></PicUrl><Url><![CDATA[${url}]]></Url></item>";
		}
		$content .= "</Articles>";
		return $this->sendMessage($content);
	}

	/**
	 * 获取分享信息记录
	 */
	protected function linkMessageArticleInfo($article) {
		if (!is_array($article)) {
			$article = array();
		}
		$title = isset($article['title']) ? $article['title'] : '';
		$url = isset($article['url']) ? $article['url'] : '';
		$image = isset($article['image']) ? $article['image'] : '';
		$desc = isset($article['desc']) ? $article['desc'] : '';
		return array($title, $url, $image, $desc);
	}

	/**
	 * 生成单条分享信息记录
	 */
	protected function linkMessageArticleItem($title, $url, $image, $desc) {
		return array(
			'title' => $title,
			'url' => $url,
			'image' => $image,
			'desc' => $desc,
		);
	}
}