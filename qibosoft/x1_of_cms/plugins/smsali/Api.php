<?php
/**
 * 阿里云发送短信接口
 * Author: SuiFeng 87211061@qq.com
 * Copyright (c) 2017-2020 http://www.qibo168.com All rights reserved.
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * Date: 2017/11/29
 */

namespace plugins\smsali;

class Api{
	private $accessKeyId;
	private $accessKeySecret;
	function __construct($accessKeyId, $accessKeySecret) {
		$this->accessKeyId = $accessKeyId;
		$this->accessKeySecret = $accessKeySecret;
	}
	/**
	 * 发送短信
	 *
	 * @param string $signName 必填, 短信签名参考：https://dysms.console.aliyun.com/dysms.htm#/sign 短信签名页
	 * @param string $templateCode 必填, 短信模板Code参考：https://dysms.console.aliyun.com/dysms.htm#/template短信模板页
	 * @param string $phoneNumbers 必填, 短信接收号码
	 * @param array|null $templateParam 选填, 假如模板中存在变量需要替换则为必填项  Array("code"=>"12345", "product"=>"阿里通信"))
	 */
	public function sendSms($signName, $templateCode, $phoneNumbers, $templateParam = null) {
		$params = [
			"RegionId" => "cn-hangzhou",
			"Action" => "SendSms",
			"Version" => "2017-05-25",
			"PhoneNumbers" => $phoneNumbers,
			"SignName" => $signName,
			"TemplateCode" => $templateCode,
		];
		// 可选，设置模板参数
		if($templateParam) {
			$params['TemplateParam'] = json_encode($templateParam);
		}
		$content = $this->request(
			$this->accessKeyId,
			$this->accessKeySecret,
			"dysmsapi.aliyuncs.com",
			$params
		);
		return $this->object2array($content);
	}
	/**
	 * 生成签名并发起请求
	 *
	 * @param $accessKeyId string AccessKeyId (https://ak-console.aliyun.com/)
	 * @param $accessKeySecret string AccessKeySecret
	 * @param $domain string API接口所在域名
	 * @param $params array API具体参数
	 * @return bool|\stdClass 返回API接口调用结果，当发生错误时返回false
	 */
	public function request($accessKeyId, $accessKeySecret, $domain, $params) {
		$apiParams = array_merge([
			"SignatureMethod" => "HMAC-SHA1",
			"SignatureNonce" => uniqid(mt_rand(0, 0xffff), true),
			"SignatureVersion" => "1.0",
			"AccessKeyId" => $accessKeyId,
			"Timestamp" => gmdate("Y-m-d\TH:i:s\Z"),
			"Format" => "JSON",
		], $params);
		ksort($apiParams);
		$sortedQueryStringTmp = "";
		foreach($apiParams as $key => $value) {
			$sortedQueryStringTmp .= "&" . $this->encode($key) . "=" . $this->encode($value);
		}
		$stringToSign = "GET&%2F&" . $this->encode(substr($sortedQueryStringTmp, 1));
		$sign = base64_encode(hash_hmac("sha1", $stringToSign, $accessKeySecret . "&", true));
		$signature = $this->encode($sign);
		$url = "http://{$domain}/?Signature={$signature}{$sortedQueryStringTmp}";
		try {
			$content = $this->fetchContent($url);
			return json_decode($content);
		} catch(\Exception $e) {
			return false;
		}
	}
	private function encode($str) {
		$res = urlencode($str);
		$res = preg_replace("/\+/", "%20", $res);
		$res = preg_replace("/\*/", "%2A", $res);
		$res = preg_replace("/%7E/", "~", $res);
		return $res;
	}
	private function fetchContent($url) {
		if(function_exists("curl_init")) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				"x-sdk-client" => "php/2.0.0"
			]);
			$rtn = curl_exec($ch);
			if($rtn === false) {
				trigger_error("[CURL_" . curl_errno($ch) . "]: " . curl_error($ch), E_USER_ERROR);
			}
			curl_close($ch);
			return $rtn;
		}
		$context = stream_context_create([
			"http" => [
				"method" => "GET",
				"header" => ["x-sdk-client: php/2.0.0"],
			]
		]);
		return file_get_contents($url, false, $context);
	}
	private function object2array(&$object) {
             $object =  json_decode( json_encode( $object),true);
             return  $object;
    }
}