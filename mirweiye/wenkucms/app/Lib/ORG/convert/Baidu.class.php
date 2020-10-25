<?php
namespace Org\Convert;

require 'baidu/BaiduBce.phar';
include_once 'baidu/Auth.php';
use BaiduBce\BceClientConfigOptions;
use BaiduBce\Util\Time;
use BaiduBce\Util\MimeTypes;
use BaiduBce\Http\HttpHeaders;
use BaiduBce\Services\Bos\BosClient;

class Baidu
{
	private $host = 'doc.bj.baidubce.com';
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
		$param['read'] = '';

		# 查看是否购买过
		$data['typeid'] = 1;
        $data['type'] = 1;
        $data['itemid'] = $id;
        $data['uid'] = $this->config['uid'];
        $info = D('itemlog')->where($data)->find();

        $where['id'] = $id;
        $info = D('doc_con')->where($where)->find();
        if (!$info['imgurl']) {
        	$data = $this->curl($key . '?https=false', 'get', $param);
	        if (isset($data['publishInfo']['coverUrl'])) {
	        	D('doc_con')->where($where)->save(array('page' => $data['publishInfo']['pageCount'], 'imgurl' => $data['publishInfo']['coverUrl']));
	        }
        }
        

        if ($info) {
        	$data = $this->curl($key . '?read', 'get', $param);
        } elseif ($file_type == 2) {
        	# public的文档
        	$data['docId'] = $key;
			$data['token'] = 'TOKEN';
			$data['host'] = 'BCEDOC';
        } else {
        	# 未购买
        	$data = $this->curl($key . '?read', 'get', $param);
        }

		$html = '<div id="document"></div>';

		$html .= '<script src="http://static.bcedocument.com/reader/v2/doc_reader_v2.js"></script><script>var option={};option.type=2;option.docId="'.$data['docId'].'";option.token="'.$data['token'].'";option.host="'.$data['host'].'";option.serverHost="http://'.$this->host.'";</script>';

		return $html;
	}

	/**
	 * 转换文件
	 */
	public function upload($file, $id, $file_type, $title, $format, $local)
	{
		$param['title'] = $title;
		$param['format'] = $format;
		if ($file_type == 1) {
			$param['access'] = 'PRIVATE';
		} else {
			$param['access'] = 'PUBLIC';
		}

		$appid = $this->config['appid'];
		$appsecret = $this->config['appsecret'];

		$BOS_TEST_CONFIG =array(
			'credentials' => array('accessKeyId' => $appid,'secretAccessKey' => $appsecret,)
	    );

	    $bucketName = 'cnheneng';	

		$client = new BosClient($BOS_TEST_CONFIG);
		$objectKey = basename($file);
		$fileName = $local;
		$res = $client->putObjectFromFile($bucketName, $objectKey, $fileName);
		if ($fileName && $res) {
			$param['bucket'] = $bucketName;
			$param['object'] = $objectKey;
			$data = $this->curl('?source=bos', 'post', $param);
			if (isset($data['documentId'])) {
				return $data['documentId'];
			}
		}
		return false;
	}

	/**
	 * 下载文件
	 */
	public function download($key, $id)
	{
		$param['download'] = '';
		$param['expireInSeconds'] = 60;
		$data = $this->curl($key . '?download&expireInSeconds=' . $param['expireInSeconds'], 'get', $param);
		return $data['downloadUrl'];
	}

	/**
	 * 文件购买授权
	 */
	public function auth($key, $id)
	{
		$param['read'] = '';
		$data = $this->curl($key . '?read', 'get', $param);
	}

	/**
	 * 查看文件截图
	 */
	public function img($url)
	{
        return $url;
	}

	/**
	 * 请求转换接口
	 */
	function curl($api, $method = 'get', $param = array())
	{

		//$url = $this->config['site'];
		$url = $this->host;
		$appid = $this->config['appid'];
		$appsecret = $this->config['appsecret'];

		$method = strtoupper($method);
		$path = '/v2/document/' . $api;

		$headers = $this->header($appid, $appsecret, $url, $method, $path, $param);

		$url = 'http://' . $url . $path;
		$http = new \Http();
		$data = $http->curl($url, $param, $method, $headers);
		$data = json_decode($data, true);
		if (isset($data['code'])) {
			//print_r($data);die;
		}

		return $data;
	}

	public function header($appid, $appsecret, $url, $method, $path, $params)
	{
		date_default_timezone_set("UTC");
		$timestamp = new \DateTime();
		$expirationInSeconds = 1800;
		$timeStr = $timestamp->format("Y-m-d\TH:i:s\Z");

		if (strstr($path, '?')) {
			$temp = explode('?', $path);
			$path = $temp[0];
			parse_str($temp[1], $params);
		}
		//$path = rtrim($path, '/');
		$authorization = generateAuthorization($appid, $appsecret, $method, $url, $path, $params, $timestamp, $expirationInSeconds);

		$headers = array(
			"Content-Type: application/json",
			"Authorization:{$authorization}",
			"x-bce-date:{$timeStr}",
		);

		return $headers;
	}
}