<?php
namespace Org\Convert;

class Dever
{
	private $config;
	public function __construct($config)
	{
		$this->config = $config;
	}

	/**
	 * 查看文件
	 */
	public function get($key, $id, $url, $img, $host, $file_type, $return_url = false)
	{
		$url = $this->curl($url, $key, $id, false, false);

		if ($return_url) {
			return $url;
		}

		$css = str_replace('.html.jpg', '.css', $this->img($img));
		
		$html = '<div id="document"></div>';

		$html .= '<link rel="stylesheet" href="'.$css.'">
<script src="'.$this->config['site'].'static/document.js"></script><script>var option={};option.type=1;option.document="'.$url.'";</script>';

		return $html;
	}

	/**
	 * 转换文件
	 */
	public function upload($file, $id, $file_type, $title, $format, $local)
	{
		return $this->curl('main/convert', $file, $id, $file_type);
	}

	/**
	 * 下载文件
	 */
	public function download($key, $id)
	{
		return $this->curl('main/down', $key, $id, false, false);
	}

	/**
	 * 查看文件截图
	 */
	public function img($url)
	{
		$site = $this->config['site'];
        if (strstr($url, '/files/') && $site) {
        	return $site . $url;
            //return $site . str_replace('/files/', 'files/', $url);
        }
        return $url;
	}

	/**
	 * 文件购买授权
	 */
	public function auth($key, $id)
	{
		return $this->curl('main/auth', $key, $id);
	}

	/**
	 * 请求转换接口
	 */
	function curl($api, $file, $file_id = false, $file_type = false, $state = true)
	{
		$url = $this->config['site'];
		$appid = $this->config['appid'];
		$appsecret = $this->config['appsecret'];
		$timestamp = time();
		$nonce = substr(md5(microtime()), rand(10, 15));
		$uid = $this->config['uid'];

		if (!$uid) {
			$uid = -1;
		}

		$param = $this->signature($appid, $appsecret, $timestamp, $nonce, $file, $file_id, $uid);
		if ($file_type) {
			$param['file_type'] = $file_type;
		}

		$url = $url . $api . '?' . http_build_query($param);

		if ($state == true) {
			$http = new \Http();
			$data = $http->curl($url);

			$data = json_decode($data, true);

			return $data;
		}

		return $url;
	}

	public function signature($appid, $appsecret, $timestamp, $nonce, $file, $file_id, $uid)
	{
		if (!$appsecret) {
			$appsecret = $this->config['appsecret'];
		}
		$signature = sha1($appid . '&' . $appsecret . '&' . $timestamp . '&' . $nonce . '&' . $file . '&' . $file_id . '&' . $uid);

		return array
		(
			'signature' => $signature,
			'appid' => $appid,
			'timestamp' => $timestamp,
			'nonce' => $nonce,
			'file' => $file,
			'file_id' => $file_id,
			'uid' => $uid,
		);
	}
}