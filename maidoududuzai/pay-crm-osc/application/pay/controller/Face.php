<?php

namespace app\pay\controller;

use \think\Db;
use \app\common\Pay;
use \app\common\PayAction;

class Face extends Auth
{

	public function __construct()
	{

		parent::__construct();

	}

	/**
	 * 商户交易号
	 */
	public function index($prefix = 'P')
	{
		PayAction::log(request()->method() . ' ' . request()->url());
		PayAction::log(input('post.'));
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		if(!preg_match('/^[A-Z]{1,2}$/', $prefix)) {
			return make_json(0, '前缀格式错误');
		}
		$length = strlen($prefix);
		$out_trade_no = preg_replace('/\d{' . $length . '}$/', '', get_order_number($prefix));
		$contents = ['out_trade_no' => $out_trade_no];
		PayAction::log(JSON($contents));
		return make_json(1, 'ok', $contents);
	}

	/**
	 * 支付接口
	 * @param String $trade_type
	 * @param String $biz_type
	 * @param String $out_trade_no
	 * @param Float $total_amount
	 * @param String $auth_code
	 * @param String $card_no 可选 会员卡号，会员充值、余额支付必填
	 */
	public function pay()
	{
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		return (new Cash)->pay();
	}

	/**
	 * 查询接口
	 * @param String $out_trade_no
	 */
	public function query()
	{
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		return (new Cash)->query();
	}

	/**
	 * 退款接口
	 * @param String $out_trade_no
	 */
	public function refund()
	{
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		return (new Cash)->refund();
	}

	/**
	 * 广告接口
	 * @param String $SN
	 */
	public function slider()
	{
		return (new Cash)->slider();
	}

	/**
	 * 交易列表
	 * @param Int $page
	 * @param String $trade_gate
	 * @param String $time_create `2011-01-02 ~ 2012-02-03`
	 */
	public function order()
	{
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		return (new Cash)->order();
	}

	/**
	 * 获取人脸凭证
	 * @param String $SN 公共参数
	 * @param String $version 公共参数
	 * @param String $person_id 公共参数
	 * @param String $person_token 公共参数
	 * @param String $rawdata 必填参数
	 */
	public function initFace()
	{
		PayAction::log(request()->method() . ' ' . request()->url());
		PayAction::log(input('post.'));
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		$rawdata = input('post.rawdata/s');
		if(empty($rawdata)) {
			return make_json(0, '缺少参数 [rawdata]');
		}
		$this->errMsg = Index::check_merchant('weixin', $this->merchant);
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		$BizContent = [
			'version' => '1',
			'rawdata' => $rawdata,
			'now' => _time(),
			'device_id' => $this->device['SN'],
			'store_id' => $this->person['store_id'],
			'store_name' => $this->person['store_name'],
		];
		$url = 'https://payapp.weixin.qq.com/face/get_wxpayface_authinfo';
		$res = PayAction::gate('weixin')->merchant($this->merchant)->index(['url' => $url, 'BizContent' => $BizContent]);
		if(isset($res->contents->nonce_str)) { unset($res->contents->nonce_str); }
		if(isset($res->contents->sign)) { unset($res->contents->sign); }
		if($res->status == 0) {
			return make_json(0, $res->message, $res->contents);
		} else {
			$res->contents->sub_mch_id = $this->merchant['sub_mch_id'];
			$res->contents->store_id = $BizContent['store_id'];
			$res->contents->store_name = $BizContent['store_name'];
			return make_json(1, 'ok', $res->contents);
		}
	}

}

