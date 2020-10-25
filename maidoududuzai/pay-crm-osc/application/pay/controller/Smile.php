<?php

namespace app\pay\controller;

use \think\Db;
use \app\common\Pay;
use \app\common\PayAction;

class Smile extends Auth
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
	 * 初始化刷脸SDK(D2)
	 * @param String $SN 公共参数
	 * @param String $version 公共参数
	 * @param String $person_id 公共参数
	 * @param String $person_token 公共参数
	 */
	public function initSmile()
	{
		PayAction::log(request()->method() . ' ' . request()->url());
		PayAction::log(input('post.'));
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		$config = Pay::config('alipay');
		$merchantId = Db::name('merchant_alipay')->where('merchant_id', '=', $this->merchant['merchant_id'])->value('user_id');
		$data = [
			'partnerId' => $config['sys_service_provider_id'],
			'appId' => $config['appId'],
			'merchantId' => $merchantId,
		];
		PayAction::log($data);
		return make_json(1, 'ok', $data);
	}
	
	/**
	 * 人脸初始化唤起zim(D2)
	 * @param String $SN 公共参数
	 * @param String $version 公共参数
	 * @param String $person_id 公共参数
	 * @param String $person_token 公共参数
	 * @param String $zimmetainfo 必填参数
	 */
	public function zolozAuthenticationCustomerSmilepayInitialize()
	{
		PayAction::log(request()->method() . ' ' . request()->url());
		PayAction::log(input('post.'));
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		$zimmetainfo = input('post.zimmetainfo/s');
		if(empty($zimmetainfo)) {
			return make_json(0, '缺少参数 [zimmetainfo]');
		}
		$AopSdk = new \app\common\AopSdk('ZolozAuthenticationCustomerSmilepayInitialize');
		$BizContent = ['zimmetainfo' => $zimmetainfo];
		$res = $AopSdk->execute($BizContent, null, $this->merchant['app_auth_token']);
		PayAction::log($res);
		return JSON($res);
	}

}

