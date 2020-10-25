<?php

namespace app\pay\controller;

use \think\Db;
use \think\Cookie;
use \app\common\Pay;
use \app\common\PayAction;
use \app\common\PayWeixin;
use \app\common\UserWeixin;

class Weixin
{

	//应用ID
	protected $appid;

	//商户
	protected $merchant;
	protected $sub_mch_id;

	//错误信息
	public $errNo = 0;
	public $errMsg = null;

	//初始化
	public function __construct($merchant = [])
	{

		$this->appid = Pay::config('weixin')['appid'];

		$store_id = input('param.store_id');
		$person_id = input('param.person_id');
		$merchant_id = authcode(input('param.merchant_id'), 'DECODE');
		$merchant_id = $merchant_id ? $merchant_id : input('param.merchant_id');
		$this->merchant = Pay::merchant($merchant_id, ['store_id' => $store_id, 'person_id' => $person_id]);

		if(!empty($this->merchant['sub_mch_id'])) {
			$this->sub_mch_id = $this->merchant['sub_mch_id'];
		}

		$this->check($this->merchant);
		$this->PayWeixin = new PayWeixin($this->sub_mch_id);

		$this->UserWeixin = new UserWeixin($this->merchant);

	}

	public function check($merchant)
	{

		$this->errMsg = Index::check_merchant('weixin', $merchant);

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
			$out_trade_no = model('Trade')->_insert($this->merchant, 'weixin', 'bar_code', $total_amount);
			$PostData = [
				//商户交易号
				'out_trade_no' => $out_trade_no,
				//支付授权码
				'auth_code' => $auth_code,
				//商品描述
				'body' => $this->merchant['merchant_name'],
				//订单总金额
				'total_amount' => $total_amount,
				//终端IP地址
				'spbill_create_ip' => get_ipaddr(),
			];
			$res = PayAction::gate('weixin')->merchant($this->merchant)->pay($PostData);
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
		$openid = Cookie::get('openid');
		if(empty($openid)) {
			$openid = input('param.openid');
		}
		$Oauth = \app\common\WeChatConsole::init()->Oauth();
		if(empty($openid)) {
			$code = input('param.code');
			if(empty($code)) {
				return \befen\jump($Oauth->getOauthRedirect(url('/pay/weixin/pay_qrcode', input('param.'), null, true)));
			}
			try {
				$res = $Oauth->getOauthAccessToken();
			} catch (\Exception $e) {
				echo $e->getMessage();
				exit();
			}
			$openid = $res['openid'];
			Cookie::forever('openid', $openid);
			$param = [];
			//$param['openid'] = $openid;
			$param['qid'] = input('param.qid');
			$param['qrcode_url'] = input('param.qrcode_url');
			$the_url = url('/pay/weixin/pay_qrcode', $param, null, true);
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
		try {
			$wx_config = \app\common\WeChatConsole::init()->Script()->getJsSign(\befen\get_url(true));
		} catch (\Exception $e) {
			echo $e->getMessage();
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
				if(!empty($this->merchant['sub_mch_id'])) {
					$this->sub_mch_id = $this->merchant['sub_mch_id'];
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
			$openid = input('post.openid/s');
			if(!$openid) {
				return make_json(0, '缺少参数 [openid]');
			}
			//商户交易号
			$this->merchant['qrcode_id'] = input('param.qrcode_id');
			$out_trade_no = model('Trade')->_insert($this->merchant, 'weixin', 'qr_code', $total_amount);
			$PostData = [
				//买家id
				'openid' => $openid,
				//商户交易号
				'out_trade_no' => $out_trade_no,
				//商品描述
				'body' => $this->merchant['merchant_name'],
				//订单总金额
				'total_amount' => $total_amount,
				//终端IP地址
				'spbill_create_ip' => get_ipaddr(),
			];
			$res = PayAction::gate('weixin')->merchant($this->merchant)->create($PostData);
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
		$out_trade_no = model('Trade')->_insert($this->merchant, 'weixin', 'qr_code', $total_amount);
		$PostData = [
			//商户交易号
			'out_trade_no' => $out_trade_no,
			//商品描述
			'body' => $this->merchant['merchant_name'],
			//订单总金额
			'total_amount' => $total_amount,
			//终端IP地址
			'spbill_create_ip' => get_ipaddr(),
		];
		$res = PayAction::gate('weixin')->merchant($this->merchant)->precreate($PostData);
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

	//异步通知
	public function notify()
	{
		$xml = file_get_contents('php://input');
		$xml_parser = xml_parser_create();
		if(!xml_parse($xml_parser, $xml, true)) {
			xml_parser_free($xml_parser);
			$data = [];
		} else {
			$data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
		}
		Pay::log($data, ['weixin', 'notify']);
		if(isset($data['req_info'])) {
			$decrypt = base64_decode($data['req_info'], true);
			$xml = openssl_decrypt($decrypt, 'AES-256-ECB', md5(Pay::config('weixin')['mch_key']), OPENSSL_RAW_DATA);
			$res_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
			Pay::log($res_data, ['weixin', 'notify']);
			if($res_data['refund_status'] == 'SUCCESS') {
				Db::name('refund')->where('out_refund_no', '=', $res_data['out_refund_no'])->update([
					'refund_status' => 1,
					'time_update' => _time(),
				]);
				$xml = '';
				$xml .= '<xml>';
				$xml .= '<return_code><![CDATA[SUCCESS]]></return_code>';
				$xml .= '<return_msg><![CDATA[DEAL WITH SUCCESS]]></return_msg>';
				$xml .= '</xml>';
				return $xml;
			}
		} else {
			$res = $this->PayWeixin->notify(['data' => json_encode($data)]);
			$res_data = [];
			$res_data['status'] = $res->status;
			foreach($data as $key => $val) {
				$res_data[$key] = $val;
			}
			Pay::log($res_data, ['weixin', 'notify']);
			if($res->status == 1) {
				$data['trade_state'] = '';
				if(isset($data['transaction_id'])) {
					$data['trade_state'] = 'SUCCESS';
				}
				model('Trade')->_update($data['out_trade_no'], [
					'trade_status' => $data['trade_state'],
					'trade_no' => $data['transaction_id'],
					'time_update' => _time(),
				]);
				$xml = '';
				$xml .= '<xml>';
				$xml .= '<return_code><![CDATA[SUCCESS]]></return_code>';
				$xml .= '<return_msg><![CDATA[DEAL WITH SUCCESS]]></return_msg>';
				$xml .= '</xml>';
				return $xml;
			}
		}
	}

}
