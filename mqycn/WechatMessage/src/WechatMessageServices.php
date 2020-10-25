<?php

require_once dirname(__FILE__) . '/WechatMessageCommon.php';

/**
 * 类名：WechatMessageServices
 * 作者：mqycn
 * 博客：http://www.miaoqiyuan.cn
 * 源码：https://gitee.com/mqycn/WechatMessage
 * 说明：微信消息处理核心类（必须为已认证的订阅号或服务号）
 */

abstract class WechatMessageServices extends WechatMessageCommon {
	protected $appId;
	protected $appSecret;
	protected $cachePath; //缓存保存路径

	public function __construct($app_id, $app_secret, $cache_path = '') {
		if ($cache_path != '') {
			$cache_path .= "/";
		}
		$this->appId = $app_id;
		$this->appSecret = $app_secret;
		$this->cachePath = $cache_path;
	}

	/**
	 * 下载媒体文件
	 */
	protected function downloadMedia($media_id, $media_type, $save_path = '/data') {
		$file_name = $save_path . '/' . md5($media_id) . $media_type;
		file_put_contents($file_name, $this->httpGet($this->apiUrl("media/get", array("media_id" => $media_id))));
		return array("file" => $file_name, "size" => filesize($file_name), "media_id" => $media_id, "mediaHash" => md5($media_id));
	}

	/**
	 * 上传媒体文件
	 */
	protected function uploadMedia($file_name, $media_type) {
		if (version_compare(PHP_VERSION, '5.5.0', '>=')) {
			$_file = new CURLFile($file_name);
		} else {
			$_file = "@" . $file_name;
		}
		return $this->api("media/upload", array("media" => $_file), array("type" => $media_type));
	}

	/**
	 * 调用微信 API 接口
	 */
	protected function api($action = 'getcallbackip', $POST = '', $query_addon = '') {
		$res = $this->httpGet($this->apiUrl($action, $query_addon), $POST);
		return json_decode($res, true);
	}

	/**
	 * 获取微信 API 接口 的URL
	 */
	protected function apiUrl($action, $query_addon = '') {
		if ($query_addon != '') {
			if (is_array($query_addon)) {
				$query_addon = $this->builderQuery($query_addon);
			}
			$query_addon = "&" . $query_addon;
		}
		return "https://api.weixin.qq.com/cgi-bin/" . $action . "?access_token=" . $this->getAccessToken() . $query_addon;
	}

	/**
	 * 数组生成URL请求字符串
	 */
	protected function builderQuery($arr, $encode = true) {
		$str = "";
		foreach ($arr as $key => $val) {
			$str .= "&" . $key . "=" . ($encode ? urlencode($val) : $val);
		}
		if ($str != "") {
			$str = substr($str, 1);
		}
		return $str;
	}

	/**
	 * 返回 HTTP请求结果
	 * $post不为空是，使用POST方式提交
	 */
	protected function httpGet($url, $post = '') {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 500);
		// 如果出错请参考：http://www.miaoqiyuan.cn/p/curl-cacert
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl, CURLOPT_URL, $url);
		if ($post !== "") {
			curl_setopt($curl, CURLOPT_POST, 1); //设置为POST方式
			curl_setopt($curl, CURLOPT_POSTFIELDS, $post); //POST数据
		}
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			"Content-Type" => "application/javascript",
		));
		$res = curl_exec($curl) or die(curl_error($curl));
		curl_close($curl);
		return $res;
	}

	/**
	 * 获取AccessToken
	 */
	protected function getAccessToken() {
		$data = $this->getCacheItem("access_token");
		if ($data == "") {
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $this->appId . "&secret=" . $this->appSecret;
			$res = json_decode($this->httpGet($url), true);
			$data = $this->setCacheItem("access_token", $res);
		}
		return $data;
	}

	/**
	 * 设置缓存的项目
	 */
	private function setCacheItem($cache_name, $data) {
		if (isset($data[$cache_name])) {
			$arr = array(
				"expire_time" => time() + 2000,
				$cache_name => $data[$cache_name],
			);
			file_put_contents($this->getCacheFile($cache_name), "<?php\nreturn " . var_export($arr, true) . ";");
			return $arr[$cache_name];
		} else {
			return "";
		}
	}

	/**
	 * 获取缓存的项目
	 */
	protected function getCacheItem($cache_name) {
		$file_name = $this->getCacheFile($cache_name);
		if (is_file($file_name)) {
			$data = require $file_name;
		} else {
			$data = array();
		}
		if (!isset($data[$cache_name]) || !isset($data['expire_time']) || $data['expire_time'] < time()) {
			return "";
		} else {
			return $data[$cache_name];
		}
	}

	/**
	 * 缓存文件存储路径
	 */
	protected function getCacheFile($cache_name) {
		return $this->cachePath . $this->appId . '_' . $cache_name . ".php";
	}

}