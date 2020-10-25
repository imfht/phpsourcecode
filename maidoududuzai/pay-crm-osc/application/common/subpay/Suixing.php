<?php

namespace app\common\subpay;

use \think\Db;
use \app\common\Pay;
use \app\common\Rsa;

class Suixing {

	const NAME = '随行付';

	const API_URL = 'https://openapi.tianquetech.com';

	//异步URL
	public $notify_url;

	//返回URL
	public $return_url;

	//机构编号
	public $orgId = null;

	//子商户
	public $mno = null;

	//接口密钥
	public $suixing_PublicKey = _DATA_ . 'cert/suixing_PublicKey.txt';
	public $suixing_PrivateKey = _DATA_ . 'cert/suixing_PrivateKey.txt';

	public function __construct()
	{

		$this->notify_url = url('/pay/notify/suixing', null, null, true);
		$this->orgId = model('\app\common\model\Config')->config('suixing_orgId');
		return $this;

	}

	public static function log($content = '')
	{

		Pay::log($content, [request()->module(), request()->controller()]);

	}

	//商户
	public function set_orgId($orgId = '')
	{

		$this->orgId = $orgId;
		return $this;

	}

	//子商户
	public function set_sub_mch($mno = '')
	{

		$this->mno = $mno;
		return $this;

	}

	public function set_PublicKey()
	{

	}

	public function set_PrivateKey()
	{

	}

	//条码支付
	public function pay($reqData = [])
	{
		//return $this->post('Pay', $reqData);
		$res = $this->post('/order/reverseScan', $reqData);
		return $res;
	}

	//统一下单创建
	public function create($reqData = [])
	{
		//return $this->post('Create', $reqData);
	}

	//统一下单预创建
	public function precreate($reqData = [])
	{
		//return $this->post('Precreate', $reqData);
	}

	//查询接口
	public function query($reqData = [])
	{
		//return $this->post('Query', $reqData);
		$reqData['BizContent'] = array_key_replace($reqData['BizContent'], ['out_trade_no' => 'ordNo']);
		$res = $this->post('/query/tradeQuery', $reqData);
		return $res;
	}

	//关闭接口
	public function close($reqData = [])
	{
		//return $this->post('Close', $reqData);
		$reqData['BizContent'] = array_key_replace($reqData['BizContent'], ['out_trade_no' => 'origOrderNo']);
		$res = $this->post('/query/close', $reqData);
		return $res;
	}

	//撤销接口
	public function cancel($reqData = [])
	{
		//return $this->post('Cancel', $reqData);
		$reqData['BizContent'] = array_key_replace($reqData['BizContent'], ['out_trade_no' => 'origOrderNo']);
		$res = $this->post('/query/cancel', $reqData);
		return $res;
	}

	//退款接口
	public function refund($reqData = [])
	{
		//return $this->post('Refund', $reqData);
		$res = $this->post('/order/refund', $reqData);
		return $res;
	}

	//退款查询接口
	public function query_refund($reqData = [])
	{
		//return $this->post('Refund', $reqData);
		$res = $this->post('/query/refundQuery', $reqData);
		return $res;
	}

	//异步通知
	public function notify($reqData = [])
	{
		//return $this->post('Notify', $reqData);
	}

	//请求接口
	public function post($url, $reqData = [])
	{
		$data = [
			'orgId' => $this->orgId,
			'reqData' => [
				'mno' => $this->mno,
			],
			'reqId' => md5(get_rand(16)),
			'signType' => 'RSA',
			'timestamp' => gsdate('YmdHis'),
			'version' => '1.0',
		];
		foreach($reqData['BizContent'] as $key => $value) {
			$data['reqData'][$key] = $value;
		}
		$req = $data;
		$req['sign'] = $this->makeSign($req, Rsa::get(null, $this->suixing_PrivateKey));
		$resp = http()->post(self::API_URL . $url, 30, JSON($req), ['Content-Type' => 'application/json']);
		return $this->get_result($resp);
	}

	//生成签名
	public function makeSign($data, $private_key)
	{
		if(isset($data['sign'])) {
			unset($data['sign']);
		}
		ksort($data);
		$args = [];
		foreach($data as $key => $val) {
			if(!is_array($val)) {
				$args[] = $key . '=' . $val;
			} else {
				$args[] = $key . '=' . JSON($val);
			}
		}
		$data = implode('&', $args);
		//$private_key = chunk_split($private_key, 64, "\n");
		$private_key = "-----BEGIN RSA PRIVATE KEY-----\n" . wordwrap($private_key, 64, "\n", true) . "\n-----END RSA PRIVATE KEY-----";
		$res = openssl_get_privatekey($private_key);
		if($res) {
			openssl_sign($data, $sign, $res);
			openssl_free_key($res);
		} else {
			return null;
		}
		$sign = base64_encode($sign);
		return $sign;
	}

	//验证签名
	public function checkSign($data, $sign, $public_key)
	{
		//$public_key = chunk_split($public_key, 64, "\n");
		$public_key = "-----BEGIN PUBLIC KEY-----\n" . wordwrap($public_key, 64, "\n", true) . "\n-----END PUBLIC KEY-----";
		$res = openssl_get_publickey($public_key);
		if($res) {
			$result = (bool)openssl_verify($data, base64_decode($sign), $res);
			openssl_free_key($res);
		} else {
			return null;
		}
		return $result;
	}

	//获得结果
	public function get_result($resp)
	{
		self::log($resp);
		$resp = ToObject(json_decode($resp));
		if($resp->code !== '0000') {
			$resp = make_return(0, $resp->msg);
		} else {
			if($resp->respData->bizCode === '0000') {
				$resp = make_return(1, 'ok', $resp->respData);
			} else {
				if($resp->respData->bizCode === '2002') {
					$resp = make_return(1, 'ok', $resp->respData);
				} else {
					$resp = make_return(0, $resp->respData->bizMsg, $resp->respData);
				}
			}
		}
		return ToObject($resp);
	}

}

