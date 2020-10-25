<?php

namespace app\pay\controller;

use \think\Db;
use \think\Cookie;
use \app\common\Pay;
use \app\common\PayAction;
use \app\common\PayAlipay;
use \app\common\UserAlipay;

class Alipay
{

	//应用ID
	protected $appId;

	//商户
	protected $merchant;
	protected $app_auth_token;

	//错误信息
	public $errNo = 0;
	public $errMsg = null;

	//初始化
	public function __construct($merchant = [])
	{

		$this->appId = Pay::config('alipay')['appId'];

		$store_id = input('param.store_id');
		$person_id = input('param.person_id');
		$merchant_id = authcode(input('param.merchant_id'), 'DECODE');
		$merchant_id = $merchant_id ? $merchant_id : input('param.merchant_id');
		$this->merchant = Pay::merchant($merchant_id, ['store_id' => $store_id, 'person_id' => $person_id]);

		if(!empty($this->merchant['app_auth_token'])) {
			$this->app_auth_token = $this->merchant['app_auth_token'];
		}

		$this->check($this->merchant);
		$this->PayAlipay = new PayAlipay($this->app_auth_token);

		$this->UserAlipay = new UserAlipay($this->merchant);

	}

	public function check($merchant)
	{

		$this->errMsg = Index::check_merchant('alipay', $merchant);

	}

	//默认方法
	public function index()
	{
	}

	//条码支付
	public function pay()
	{
		if(request()->isPost()) {
			if($this->errMsg) {
				return make_json(0, $this->errMsg);
			}
			//交易金额
			$total_amount = input('post.total_amount/f');
			if(!$total_amount) {
				return make_json(0, '缺少参数 [total_amount]');
			}
			//支付授权码
			$auth_code = input('post.auth_code/s');
			if(!$auth_code) {
				return make_json(0, '缺少参数 [auth_code]');
			}
			//商户交易号
			$out_trade_no = model('Trade')->_insert($this->merchant, 'alipay', 'bar_code', $total_amount);
			$PostData = [
				//商户交易号
				'out_trade_no' => $out_trade_no,
				//支付场景 
				'scene' => 'bar_code',
				//支付授权码
				'auth_code' => $auth_code,
				//订单标题
				'subject' => $this->merchant['merchant_name'],
				//订单总金额
				'total_amount' => $total_amount,
			];
			$res = PayAction::gate('alipay')->merchant($this->merchant)->pay($PostData);
			$res = PayAction::result_filter($res);
			if($res->status == 0) {
				if((isset($res->contents->code) && $res->contents->code == 10003) || (isset($res->contents->err_code) && $res->contents->err_code == 'USERPAYING')) {
					return make_json(1, 'query', $res->contents);
				} else {
					return make_json(0, $res->message, $res->contents);
				}
			}
			return make_json(1, 'query', $res->contents);
		}
	}

	//扫码支付
	public function pay_qrcode()
	{
		$qrcode_id = input('param.qid');
		$qrcode_url = authcode(input('param.qrcode_url'), 'DECODE', $qrcode_id);
		if(!$qrcode_url) {
			echo '<h1>无效的二维码</h1>';
			exit();
		}
		$user_id = Cookie::get('user_id');
		if(empty($user_id)) {
			$user_id = input('param.user_id');
		}
		$AopSdk = new \app\common\AopSdk();
		if(empty($user_id)) {
			$auth_code = input('param.auth_code');
			if(empty($auth_code)) {
				return \befen\jump($AopSdk->getOauthRedirect(url('/pay/alipay/pay_qrcode', input('param.'), null, true)));
			}
			$res = $AopSdk->getOauthAccessToken($auth_code);
			if($res['status'] == 0) {
				if(!isset($res['contents']['sub_msg'])) {
					echo $res['contents']['msg'];
				} else {
					echo $res['contents']['sub_msg'];
				}
				exit();
			}
			$user_id = $res['contents']['user_id'];
			Cookie::forever('user_id', $user_id);
			$param = [];
			//$param['user_id'] = $user_id;
			$param['qid'] = input('param.qid');
			$param['qrcode_url'] = input('param.qrcode_url');
			$the_url = url('/pay/alipay/pay_qrcode', $param, null, true);
			return \befen\jump($the_url);
		}
		list($qrc_key, $qrc_val) = explode('=', $qrcode_url);
		if($qrc_key == 'merchant_id') {
			$merchant = Pay::merchant($qrc_val);
		} else {
			$merchant = Pay::merchant($merchant_id, [$qrc_key => $qrc_val]);
		}
		$this->check($merchant);
		if($this->errMsg) {
			echo '<h1>'.$this->errMsg.'</h1>';
			exit();
		}
		include \befen\view('Pay_Qrcode');
	}

	//统一下单创建
	public function create()
	{
		if(request()->isPost()) {
			if(empty($this->merchant)) {
				$this->merchant = Pay::merchant($merchant_id, ['SN' => input('param.SN')]);
				if(!empty($this->merchant['app_auth_token'])) {
					$this->app_auth_token = $this->merchant['app_auth_token'];
				}
				$this->check($this->merchant);
			}
			if($this->errMsg) {
				return make_json(0, $this->errMsg);
			}
			//交易金额
			$total_amount = input('post.total_amount/f');
			if(!$total_amount) {
				return make_json(0, '缺少参数 [total_amount]');
			}
			//买家id
			$user_id = input('post.user_id/s');
			if(!$user_id) {
				return make_json(0, '缺少参数 [user_id]');
			}
			//商户交易号
			$this->merchant['qrcode_id'] = input('param.qrcode_id');
			$out_trade_no = model('Trade')->_insert($this->merchant, 'alipay', 'qr_code', $total_amount);
			$PostData = [
				//买家id
				'user_id' => $user_id,
				//商户交易号
				'out_trade_no' => $out_trade_no,
				//订单标题
				'subject' => $this->merchant['merchant_name'],
				//订单总金额
				'total_amount' => $total_amount,
			];
			$res = PayAction::gate('alipay')->merchant($this->merchant)->create($PostData);
			if($res->status == 0) {
				return make_json(0, $res->message, $res->contents);
			}
			$res->contents->out_trade_no = $out_trade_no;
			return make_json(1, $res->message, $res->contents);
		}
	}

	//统一下单预创建(生成二维码)
	public function precreate()
	{
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		//交易金额
		$total_amount = input('post.total_amount/f');
		if(!$total_amount) {
			return make_json(0, '缺少参数 [total_amount]');
		}
		//商户交易号
		$out_trade_no = model('Trade')->_insert($this->merchant, 'alipay', 'qr_code', $total_amount);
		$PostData = [
			//商户交易号
			'out_trade_no' => $out_trade_no,
			//订单标题
			'subject' => $this->merchant['merchant_name'],
			//订单总金额
			'total_amount' => $total_amount,
		];
		$res = PayAction::gate('alipay')->merchant($this->merchant)->precreate($PostData);
		if($res->status == 0) {
			return make_json(0, $res->message, $res->contents);
		}
		$res->contents->out_trade_no = $out_trade_no;
		return make_json(1, $res->message, $res->contents);
	}

	//查询接口
	public function query()
	{
		if(request()->isPost()) {
			if($this->errMsg) {
				return make_json(0, $this->errMsg);
			}
			return Index::query($this->merchant, input('post.out_trade_no/s'));
		}
	}

	//撤销接口
	public function cancel()
	{
		if(request()->isPost()) {
			if(!$this->merchant) {
				return make_json(0, $this->errMsg);
			}
			return Index::cancel($this->merchant, input('post.out_trade_no/s'));
		}
	}

	//退款接口
	public function refund()
	{
		if(request()->isPost()) {
			if(!$this->merchant) {
				return make_json(0, $this->errMsg);
			}
			return Index::refund($this->merchant, input('post.out_trade_no/s'));
		}
	}

	//关闭接口
	public function close()
	{
		if(request()->isPost()) {
			if(!$this->merchant) {
				return make_json(0, $this->errMsg);
			}
			return Index::close($this->merchant, input('post.out_trade_no/s'));
		}
	}

	//应用网关
	public function gateway()
	{
		PayAction::log(request()->method() . ' ' . request()->url());
		PayAction::log(input('post.'));
		$post = input('post.');
		$service = input('post.service');
		$charset = input('post.charset');
		$sign = input('post.sign');
		$sign_type = input('post.sign_type');
		$biz_content = input('post.biz_content');
		/*
		$xml = $biz_content;
		$xml_parser = xml_parser_create();
		if(!xml_parse($xml_parser, $xml, true)) {
			xml_parser_free($xml_parser);
			$data = [];
		} else {
			$data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
		}
		*/
		$AopSdk = new \app\common\AopSdk();
		$rsaPrivateKey = $AopSdk->aop->rsaPrivateKey;
		$rsaPublicKey = $AopSdk->aop->rsaPublicKey;
		if($service == 'alipay.service.check') {
			header("Content-Type:text/xml; charset=GBK");
			$bizContent = '<success>true</success><biz_content>'.$rsaPublicKey.'</biz_content>';
			return $AopSdk->aop->encryptAndSign($bizContent, $rsaPublicKey, $rsaPrivateKey, $charset, 0, 1, $AopSdk->aop->signType);
		} else {
			$sign_verify = $AopSdk->aop->rsaCheckV1(input('post.'), null, $AopSdk->aop->signType);
			if(!$sign_verify) {
				return '签名错误';
			}
			$biz_content = json_decode($biz_content, true);
			if($post['notify_type'] == 'open_app_auth_notify') {
				$batch_no = $biz_content['notify_context']['trigger_context']['out_biz_no'];
				if(Db::name('gates')->where('batch_no', '=', $batch_no)->update(['gates_detail' => JSON($biz_content['detail'])])) {
					return 'success';
				}
			}
		}
		return 'fail';
	}

	//异步通知
	public function notify()
	{
		$data = input('post.');
		if(isset($data['fund_bill_list'])) {
			$data['fund_bill_list'] = json_decode($data['fund_bill_list'], true);
		}
		Pay::log($data, ['alipay', 'notify']);
		$res = $this->PayAlipay->notify(['data' => json_encode($data)]);
		$res_data = [];
		$res_data['status'] = $res->status;
		foreach($data as $key => $val) {
			$res_data[$key] = $val;
		}
		Pay::log($res_data, ['alipay', 'notify']);
		if($res->status == 1) {
			model('Trade')->_update($data['out_trade_no'], [
				'trade_status' => preg_replace('/^TRADE_/', '', $data['trade_status']),
				'trade_no' => $data['trade_no'],
				'time_update' => _time(),
			]);
			if(isset($data['out_biz_no'])) {
				Db::name('refund')->where('out_refund_no', '=', $data['out_biz_no'])->update([
					'refund_status' => 1,
					'time_update' => _time(),
				]);
			}
			return 'success';
		}
		return 'fail';
	}

	//商户授权
	public function get_auth_token()
	{
		if(!$this->merchant) {
			return '<h1>商户不存在</h1>';
		}
		$merchant_id = $this->merchant['merchant_id'];
		$grant_type = input('param.grant_type');
		if(!in_array($grant_type, ['authorization_code', 'refresh_token'])) {
			$grant_type = 'authorization_code';
		}
		$auth_time = sys_time();
		$AopSdk = new \app\common\AopSdk();
		$AopSdk->load('alipay.open.auth.token.app');
		$BizContent = [
			'grant_type' => $grant_type,
			'code' => input('param.app_auth_code'),
			'refresh_token' => input('param.refresh_token'),
		];
		$res = $AopSdk->execute($BizContent);
		if($res['status'] == 0) {
			return '<h1>'.$res['message'].'</h1>';
		}
		if(isset($res['contents']['code']) && $res['contents']['code'] == 10000) {
			Db::name('merchant_alipay')->where('merchant_id', '=', $merchant_id)->update([
				'user_id' => $res['contents']['tokens'][0]['user_id'],
				'auth_time' => $auth_time,
				'auth_app_id' => $res['contents']['tokens'][0]['auth_app_id'],
				'app_auth_token' => $res['contents']['tokens'][0]['app_auth_token'],
				'app_refresh_token' => $res['contents']['tokens'][0]['app_refresh_token'],
			]);
			return '<h1>授权成功</h1>';
		} else {
			return '<h1>授权失败</h1>';
		}
	}

	//授权APP权限
	public function query_auth_token()
	{
		$BizContent = [
			'app_auth_token' => input('param.app_auth_token'),
		];
		$AopSdk = new \app\common\AopSdk();
		$AopSdk->load('alipay.open.auth.token.app.query');
		$res = $AopSdk->execute($BizContent);
		return make_json($res['status'], $res['message'], $res['contents']);
	}

	//商户授权地址
	public function get_url_auth_token($merchant_id = '')
	{
		$merchant_id = authcode($merchant_id, 'DECODE');
		return url('/pay/alipay/mch_url_auth_token', ['merchant_id' => authcode($merchant_id, 'ENCODE', '', 300)], null, true);
	}

	//商户授权地址
	public function mch_url_auth_token($merchant_id = '')
	{
		$merchant_id = authcode($merchant_id, 'DECODE');
		if(!$merchant_id) {
			return '<h1>授权码过期</h1>';
		}
		$merchant = Pay::merchant($merchant_id);
		if(!$merchant) {
			return '<h1>商户不存在</h1>';
		}
		if($merchant['app_auth_token']) {
			return '<h1>请勿重复授权</h1>';
		}
		return \befen\jump("https://openauth.alipay.com/oauth2/appToAppBatchAuth.htm?app_id={$this->appId}&application_type=WEBAPP,MOBILEAPP,PUBLICAPP&redirect_uri=" . urlencode(url('/pay/alipay/get_auth_token', null, null, true)) . '?merchant_id=' . authcode($merchant_id, 'ENCODE', '', 300));
	}

	/**
	 * 开卡删卡回调
	 * @param $app_id 服务商appid
	 * @param $auth_app_id 商户appid
	 * @param $template_id
	 * @param $notify_type
	 * @param $user_id
	 * @param $biz_card_no
	 * @param $external_card_no
	 * @return void
	 */
	public function card_change_callback()
	{
		//Pay::log(input('param.'), ['alipay', 'card_change_callback']);
		if(input('param.notify_type') == 'cardcenter_card_open') {
			//开卡回调
		}
		if(input('param.notify_type') == 'cardcenter_card_cancelled') {
			//删卡回调
			Db::name('mch_user')->where('external_card_no', '=', input('param.external_card_no'))->update([
				'biz_card_no' => ''
			]);
		}
		return 'success';
	}

	/**
	 * 用户领卡回调
	 * @param string isv_app_id 服务商appid
	 * @param string app_id 商户appid
	 * @param string auth_code
	 * @param string state
	 * @param string scope auth_base,auth_user,auth_ecard
	 * @param string template_id
	 * @param string request_id
	 * @param string out_string
	 * @return void
	 */
	public function card_activate_callback()
	{
		//Pay::log(input('param.'), ['alipay', 'card_activate_callback']);
		$isv_app_id = input('param.isv_app_id');
		$app_id = input('param.app_id');
		$auth_code = input('param.auth_code');
		$template_id = input('param.template_id');
		$request_id = input('param.request_id');
		$out_string = input('param.out_string');
		$merchant_id = Db::name('merchant_alipay')->where('auth_app_id', '=', $app_id)->where('card_id', '=', $template_id)->value('merchant_id');
		if(empty($merchant_id)) {
			exit();
		}
		$this->UserAlipay->set_mch_id($merchant_id);
		$res = $this->UserAlipay->getOauthAccessToken($auth_code, null, $this->UserAlipay->app_auth_token);
		if($res['status'] == 0) {
			return $res['message'];
		}
		$auth_info = $res['contents'];
		$user_id = $auth_info['user_id'];
		$access_token = $auth_info['access_token'];
		$out_info = authcode($out_string, 'DECODE', $user_id);
		$out_info = json_decode($out_info, true);
		if(empty($out_info)) {
			return '无效的请求参数';
		}
		$mch_user = model('MchUser')->get_user(['user_id' => $user_id, 'merchant_id' => $MchConfig['merchant_id']]);
		if(empty($mch_user)) {
			return '未获取到会员信息';
		}
		//获取会员信息
		$res = $this->UserAlipay->query_card_activateform($template_id, $request_id, $access_token);
		if($res['status'] == 0) {
			return $res['message'];
		}
		$result = $res['contents'];
		$form_info = json_decode($result['infos'], true);
		$user_info = [];
		foreach($form_info as $value) {
			foreach($value as $key => $val) {
				switch($key) {
					case 'OPEN_FORM_FIELD_NAME':
						$user_info['username'] = $val['OPEN_FORM_FIELD_NAME'];
					break;
					case 'OPEN_FORM_FIELD_MOBILE':
						$user_info['phone'] = $val['OPEN_FORM_FIELD_MOBILE'];
					break;
					case 'OPEN_FORM_FIELD_GENDER':
						$user_info['sex'] = '0';
						switch($val['OPEN_FORM_FIELD_GENDER']) {
							case '男':
								$user_data['sex'] = '1';
							break;
							case '女':
								$user_data['sex'] = '2';
							break;
						}
					break;
					case 'OPEN_FORM_FIELD_BIRTHDAY_WITH_YEAR':
						$user_info['birth'] = $val['OPEN_FORM_FIELD_BIRTHDAY_WITH_YEAR'];
					break;
				}
			}
		}
		//检查手机号码
		if(empty($user_data['phone'])) {
			return '手机号码必填';
		}
		if(0 != Db::name('mch_user')->where('merchant_id', '=', $merchant_id)->where('phone', '=', $user_data['phone'])->where('user_id', '<>', '')->count()) {
			return '手机号码已经存在';
		}
		//合并会员数据
		if(empty($mch_user['phone'])) {

		}
		//开通会员卡
		$card_ext_info = [
			'external_card_no' => $mch_user['card_no'],
			'point' => $mch_user['credit'],
			'balance' => $mch_user['balance'],
			'open_date' => gsdate('Y-m-d H:i:s', $mch_user['open_datetime']),
			'level' => 'VIP',
			'valid_date' => '2088-01-01 00:00:00'
		];
		$member_ext_info = [
			'cell' => $user_data['phone']
		];
		if(!empty($user_info['username'])) {
			$member_ext_info['name'] = $user_info['username'];
		}
		if(!empty($user_data['sex'])) {
			$member_ext_info['gende'] = $user_data['sex'] == '1' ? 'MALE' : 'FEMALE';
		}
		if(!empty($user_info['birth'])) {
			$member_ext_info['birth'] = $user_info['birth'];
		}
		$res = $this->UserAlipay->card_open($template_id, $user_id, $access_token, $card_ext_info, $member_ext_info);
		if($res['status'] == 0) {
			return $res['message'];
		}
		$user_data['biz_card_no'] = $res['contents']['card_info']['biz_card_no'];
		model('MchUser')->allowField(true)->save($user_data, ['id' => $mch_user['id']]);
		return \befen\jump('alipayqr://platformapi/startapp?saId=20000021');
	}

}

