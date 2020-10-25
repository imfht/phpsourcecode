<?php

namespace Common\Lib\Api;

class WxChat {

	/**
	 * 微信推送过来的数据或响应数据
	 * @var array
	 */
	private $data = array();

	/**
	 * 主动发送的数据
	 * @var array
	 */
	private $send = array();

	/**
	 * 获取微信推送的数据
	 * @return array 转换为数组后的数据
	 */
	public function request() {
		$this->auth() || exit;
		if (IS_GET) {
			exit($_GET['echostr']);
		} else {
			$xml = new \SimpleXMLElement(file_get_contents("php://input"));
			$xml || exit;
			foreach ($xml as $key => $value) {
				$this->data[$key] = strval($value);
			}
		}
		return $this->data;
	}

	/**
	 * * 被动响应微信发送的信息（自动回复）
	 * @param  string $to      接收用户名
	 * @param  string $from    发送者用户名
	 * @param  array  $content 回复信息，文本信息为string类型
	 * @param  string $type    消息类型
	 * @param  string $flag    是否新标刚接受到的信息
	 * @return string          XML字符串
	 */
	public function response($content, $type = 'text', $flag = 0) {
		/* 基础数据 */
		$this->data = array(
			'ToUserName'	 => $this->data['FromUserName'],
			'FromUserName'	 => $this->data['ToUserName'],
			'CreateTime'	 => NOW_TIME,
			'MsgType'		 => $type,
		);

		/* 添加类型数据 */
		$this->$type($content);

		/* 添加状态 */
		$this->data['FuncFlag'] = $flag;

		/* 转换数据为XML */
		$xml = new \SimpleXMLElement('<xml></xml>');
		$this->data2xml($xml, $this->data);
		exit($xml->asXML());
	}

	/**
	 * * 主动发送消息
	 *
	 * @param string $content   内容
	 * @param string $openid   	发送者用户名
	 * @param string $type   	类型
	 * @return array 返回的信息
	 */
	public function sendMsg($content, $openid = '', $type = 'text') {
		/* 基础数据 */
		$this->send ['touser'] = $openid;
		$this->send ['msgtype'] = $type;

		/* 添加类型数据 */
		$sendtype = 'send' . $type;
		$this->$sendtype($content);

		/* 发送 */
		$sendjson = jsencode($this->send);
		$restr = $this->send($sendjson);
		return $restr;
	}

	/**
	 * 发送文本消息
	 * 
	 * @param string $content
	 *        	要发送的信息
	 */
	private function sendtext($content) {
		$this->send ['text'] = array(
			'content' => $content
		);
	}

	/**
	 * 发送图片消息
	 * 
	 * @param string $content
	 *        	要发送的信息
	 */
	private function sendimage($content) {
		$this->send ['image'] = array(
			'media_id' => $content
		);
	}

	/**
	 * 发送视频消息
	 * @param  string $video 要发送的信息
	 */
	private function sendvideo($video) {
		list (
				$video ['media_id'],
				$video ['title'],
				$video ['description']
				) = $video;

		$this->send ['video'] = $video;
	}

	/**
	 * 发送语音消息
	 * 
	 * @param string $content
	 *        	要发送的信息
	 */
	private function sendvoice($content) {
		$this->send ['voice'] = array(
			'media_id' => $content
		);
	}

	/**
	 * 发送音乐消息
	 * 
	 * @param string $music
	 *        	要发送的信息
	 */
	private function sendmusic($music) {
		list (
				$music ['title'],
				$music ['description'],
				$music ['musicurl'],
				$music ['hqmusicurl'],
				$music ['thumb_media_id']
				) = $music;

		$this->send ['music'] = $music;
	}

	/**
	 * 发送图文消息
	 * @param  string $news 要回复的图文内容
	 */
	private function sendnews($news) {
		$articles = array();
		foreach ($news as $key => $value) {
			list(
					$articles[$key]['title'],
					$articles[$key]['description'],
					$articles[$key]['url'],
					$articles[$key]['picurl']
					) = $value;
			if ($key >= 9) {
				break;
			} //最多只允许10调新闻
		}
		$this->send['articles'] = $articles;
	}

	/**
	 * * 获取微信用户的基本资料
	 * 
	 * @param string $openid   	发送者用户名
	 * @return array 用户资料
	 */
	public function user($openid = '') {
		if ($openid) {
			header("Content-type: text/html; charset=utf-8");
			$url = 'https://api.weixin.qq.com/cgi-bin/user/info';
			$params = array();
			$params ['access_token'] = $this->getToken();
			$params ['openid'] = $openid;
			$httpstr = http($url, $params);
			$harr = json_decode($httpstr, true);
			return $harr;
		} else {
			return false;
		}
	}

	/**
	 * 生成菜单
	 * @param  string $data 菜单的str
	 * @return string  返回的结果；
	 */
	public function setMenu($data = NULL) {
		$access_token = $this->getToken();
		$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$access_token}";
		$menustr = http($url, $data, 'POST', array("Content-type: text/html; charset=utf-8"), true);
		return $menustr;
	}

	/**
	 * 回复文本信息
	 * @param  string $content 要回复的信息
	 */
	private function text($content) {
		$this->data['Content'] = $content;
	}

	/**
	 * 回复音乐信息
	 * @param  string $music 要回复的音乐
	 */
	private function music($music) {
		list(
				$music['Title'],
				$music['Description'],
				$music['MusicUrl'],
				$music['HQMusicUrl']
				) = $music;
		$this->data['Music'] = $music;
	}

	/**
	 * 回复图文信息
	 * @param  string $news 要回复的图文内容
	 */
	private function news($news) {
		$articles = array();
		foreach ($news as $key => $value) {
			list(
					$articles[$key]['Title'],
					$articles[$key]['Description'],
					$articles[$key]['PicUrl'],
					$articles[$key]['Url']
					) = $value;
			if ($key >= 9) {
				break;
			} //最多只允许10调新闻
		}
		$this->data['ArticleCount'] = count($articles);
		$this->data['Articles'] = $articles;
	}

	/**
	 * 主动发送的信息
	 * @param  string $data    json数据
	 * @return string          微信返回信息
	 */
	private function send($data = NULL) {
		$access_token = $this->getToken();
		$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$access_token}";
		$restr = http($url, $data, 'POST', array("Content-type: text/html; charset=utf-8"), true);
		return $restr;
	}

	/**
	 * 数据XML编码
	 * @param  object $xml  XML对象
	 * @param  mixed  $data 数据
	 * @param  string $item 数字索引时的节点名称
	 * @return string
	 */
	private function data2xml($xml, $data, $item = 'item') {
		foreach ($data as $key => $value) {
			/* 指定默认的数字key */
			is_numeric($key) && $key = $item;

			/* 添加子元素 */
			if (is_array($value) || is_object($value)) {
				$child = $xml->addChild($key);
				$this->data2xml($child, $value, $item);
			} else {
				if (is_numeric($value)) {
					$child = $xml->addChild($key, $value);
				} else {
					$child = $xml->addChild($key);
					$node = dom_import_simplexml($child);
					$node->appendChild($node->ownerDocument->createCDATASection($value));
				}
			}
		}
	}

	/**
	 * 对数据进行签名认证，确保是微信发送的数据
	 * @param  string $token 微信开放平台设置的TOKEN
	 * @return boolean       true-签名正确，false-签名错误
	 */
	private function auth() {
		/* 获取数据 */
		$data = array($_GET['timestamp'], $_GET['nonce'], C('WECHAT_TOKEN'));
		$sign = $_GET['signature'];
		/* 对数据进行字典排序 */
		sort($data);
		/* 生成签名 */
		$signature = sha1(implode($data));
		return $signature === $sign;
	}

	/**
	 * 获取保存的accesstoken
	 */
	private function getToken() {
		static $stoken = null;
		// 从缓存获取ACCESS_TOKEN
		is_null($stoken) && $stoken = S('WX_S_TOKEN');
		if (is_array($stoken)) {
			$nowtime = time();
			// 判断缓存里面的TOKEN保存了多久
			$difftime = $nowtime - $stoken ['tokentime'];
			// TOKEN有效时间7200 判断超过7000就重新获取;
			if ($difftime > 7000) {
				// 去微信获取最新ACCESS_TOKEN
				$accesstoken = $this->getAcessToken();
				$stoken ['tokentime'] = time();
				$stoken ['token'] = $accesstoken;
				// 放进缓存
				S('WX_S_TOKEN', $stoken);
			} else {
				$accesstoken = $stoken ['token'];
			}
		} else {
			// 去微信获取最新ACCESS_TOKEN
			$accesstoken = $this->getAcessToken();
			$stoken ['tokentime'] = time();
			$stoken ['token'] = $accesstoken;
			S('WX_S_TOKEN', $stoken); // 放进缓存
		}
		return $accesstoken;
	}

	/**
	 * 重新从微信获取accesstoken
	 */
	private function getAcessToken() {
		$token = C('WECHAT_TOKEN');
		$appid = C('WECHAT_APPID');
		$appsecret = C('WECHAT_APPSECRET');
		$url = 'https://api.weixin.qq.com/cgi-bin/token';
		$params = array();
		$params ['grant_type'] = 'client_credential';
		$params ['appid'] = $appid;
		$params ['secret'] = $appsecret;
		$httpstr = http($url, $params);
		$harr = json_decode($httpstr, true);
		return $harr['access_token'];
	}

}
