<?php

/**
 * 支付处理类
 */
namespace app\index\model;
use think\Validate;
use think\Log;
class Pay extends \think\Model
{
	//============================微信=================================
	private function _weixin_config(){
		define('WXPAY_APPID', "");//微信公众号APPID
		define('WXPAY_MCHID', "");//微信商户号MCHID
		define('WXPAY_KEY', "");//微信商户自定义32位KEY
		define('WXPAY_APPSECRET', "");//微信公众号appsecret
		import('wxpay.WxPay_Api',EXTEND_PATH);
		import('wxpay.WxPay_NativePay',EXTEND_PATH);
	}

	public function weixin($data=[])
	{
		$validate = new Validate([
			['body','require','请输入订单描述'],
			['attach','require','请输入订单标题'],
			['out_trade_no','require|alphaNum','订单编号输入错误|订单编号输入错误'],
			['total_fee','require|number|gt:0','金额输入错误|金额输入错误|金额输入错误'],
			['notify_url','require','异步通知地址不为空'],
			['trade_type','require|in:JSAPI,NATIVE,APP','交易类型错误'],
		]);
		if (!$validate->check($data)) {
			return ['code'=>0,'msg'=>$validate->getError()];
		}
		$this->_weixin_config();
		$notify = new \NativePay();
		$input = new \WxPayUnifiedOrder();
		$input->SetBody($data['body']);
		$input->SetAttach($data['attach']);
		$input->SetOut_trade_no($data['out_trade_no']);
		$input->SetTotal_fee($data['total_fee']);
		$input->SetTime_start($data['time_start']);
		$input->SetTime_expire($data['time_expire']);
		$input->SetGoods_tag($data['goods_tag']);
		$input->SetNotify_url($data['notify_url']);
		$input->SetTrade_type($data['trade_type']);
		$input->SetProduct_id($data['product_id']);
		$result = $notify->GetPayUrl($input);
		if($result['return_code'] != 'SUCCESS'){
			return ['code'=>0,'msg'=> $result['return_msg']];
		}
		if($result['result_code'] != 'SUCCESS'){
			return ['code'=>0,'msg'=> $result['err_code_des']];
		}
		return ['code'=>1,'msg'=>$result["code_url"]];
	}

	public function notify_weixin($data='')
	{
		if(!$data){
			return false;
		}
		$this->_weixin_config();
    	$doc = new \DOMDocument();
		$doc->loadXML($data);
		$out_trade_no = $doc->getElementsByTagName("out_trade_no")->item(0)->nodeValue;
		$transaction_id = $doc->getElementsByTagName("transaction_id")->item(0)->nodeValue;
		$openid = $doc->getElementsByTagName("openid")->item(0)->nodeValue;
		$input = new \WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = \WxPayApi::orderQuery($input);
		if(array_key_exists("return_code", $result) && array_key_exists("result_code", $result) && $result["return_code"] == "SUCCESS" && $result["result_code"] == "SUCCESS"){
			// 处理支付成功后的逻辑业务
			Log::init([
				'type'  =>  'File',
				'path'  =>  LOG_PATH.'../paylog/'
			]);
			Log::write($result,'log');
			return 'SUCCESS';
		}
		return false;
	}
	//============================支付宝=================================
	public static $alipay_config = [
										'partner' 			=> '2088************',//支付宝partner，2088开头数字
										'seller_id' 		=> '2088************',//支付宝partner，2088开头数字
										'key' 				=> '****************',//支付宝密钥
										'sign_type' 		=> 'MD5',
										'input_charset' 	=> 'utf-8',
										'cacert' 			=> '',
										'transport' 		=> 'http',
										'payment_type' 		=> '1',
										'service' 			=> 'create_direct_pay_by_user',
										'anti_phishing_key'	=> '',
										'exter_invoke_ip' 	=> '',
									];

	public function alipay($data=[])
	{
		$validate = new Validate([
			['out_trade_no','require|alphaNum','订单编号输入错误|订单编号输入错误'],
			['total_fee','require|number|gt:0','金额输入错误|金额输入错误|金额输入错误'],
			['subject','require','请输入标题'],
			['body','require','请输入描述'],
			['notify_url','require','异步通知地址不为空'],
		]);
		if (!$validate->check($data)) {
			return ['code'=>0,'msg'=>$validate->getError()];
		}
		$config = self::$alipay_config;
		vendor('alipay.alipay');
		$parameter = [
			"service"       	=> $config['service'],
			"partner"       	=> $config['partner'],
			"seller_id"  		=> $config['seller_id'],
			"payment_type"		=> $config['payment_type'],
			"notify_url"		=> $data['notify_url'],
			"return_url"		=> $data['return_url'],
			"anti_phishing_key"	=> $config['anti_phishing_key'],
			"exter_invoke_ip"	=> $config['exter_invoke_ip'],
			"out_trade_no"		=> $data['out_trade_no'],
			"subject"			=> $data['subject'],
			"total_fee"			=> $data['total_fee'],
			"body"				=> $data['body'],
			"_input_charset"	=> $config['input_charset']
		];
		$alipaySubmit = new \AlipaySubmit($config);
		return ['code'=>1,'msg'=>$alipaySubmit->buildRequestForm($parameter,"get", "确认")];
	}

	public function notify_alipay()
	{
		$config = self::$alipay_config;
		vendor('alipay.alipay');
		$alipayNotify = new \AlipayNotify($config);
		if($result = $alipayNotify->verifyNotify()){
			if(input('trade_status') == 'TRADE_FINISHED' || input('trade_status') == 'TRADE_SUCCESS') {
				// 处理支付成功后的逻辑业务
				Log::init([
					'type'  =>  'File',
					'path'  =>  LOG_PATH.'../paylog/'
				]);
				Log::write($result,'log');
				return 'success';
			}
			return 'fail';
		}
		return 'fail';
	}

}
