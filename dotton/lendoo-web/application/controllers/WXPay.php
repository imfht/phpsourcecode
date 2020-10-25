<?php
require_once __DIR__ . '/../third_party/wxpay/WxPay.Api.php';
require_once __DIR__ . '/../third_party/wxpay/PayNotifyCallBack.php';
use \LeanCloud\Object;

class WXPay extends BaseController {
	// pay order 
	// 此函数即将废弃，已改用云函数实现，详见：http://blog.it577.net/index.php/archives/7/
	function index() {
		// 		初始化值对象
		$input = new WxPayUnifiedOrder();
		// 		文档提及的参数规范：商家名称-销售商品类目
		$input->SetBody($this->input->post('body'));
		// 		订单号应该是由小程序端传给服务端的，在用户下单时即生成，demo中取值是一个生成的时间戳
		$input->SetOut_trade_no($this->input->post('tradeNo'));
		// 		费用应该是由小程序端传给服务端的，在用户下单时告知服务端应付金额，demo中取值是1，即1分钱
		$input->SetTotal_fee($this->input->post('totalFee'));
		$input->SetNotify_url("https://lendoo.leanapp.cn/WXPay/notify");
		$input->SetTrade_type("JSAPI");
		// 		由小程序端传给服务端
		$input->SetOpenid($this->input->post('openid'));
		// 		向微信统一下单，并返回order，它是一个array数组
		$order = WxPayApi::unifiedOrder($input);
		// 		json化返回给小程序端
		header("Content-Type: application/json");
		echo $this->getJsApiParameters($order);
	}

	// 此函数即将废弃，已改用云函数实现，详见：http://blog.it577.net/
	private function getJsApiParameters($UnifiedOrderResult)
	{
		if(!array_key_exists("appid", $UnifiedOrderResult)
		|| !array_key_exists("prepay_id", $UnifiedOrderResult)
		|| $UnifiedOrderResult['prepay_id'] == "")
		{
			throw new WxPayException("参数错误");
		}
		$jsapi = new WxPayJsApiPay();
		$jsapi->SetAppid($UnifiedOrderResult["appid"]);
		$timeStamp = time();
		$jsapi->SetTimeStamp("$timeStamp");
		$jsapi->SetNonceStr(WxPayApi::getNonceStr());
		$jsapi->SetPackage("prepay_id=" . $UnifiedOrderResult['prepay_id']);
		$jsapi->SetSignType("MD5");
		$jsapi->SetPaySign($jsapi->MakeSign());
		$parameters = json_encode($jsapi->GetValues());
		return $parameters;
	}

	public function notify() {
		//获取通知的数据
		$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
		//如果返回成功则验证签名
		$result = WxPayResults::Init($xml);
		$notify = new PayNotifyCallBack();
		$notify->Handle(false);
		$returnValues = $notify->GetValues(); 
		//交易成功
		if(!empty($returnValues['return_code']) && $returnValues['return_code'] == 'SUCCESS'){  
		    //商户逻辑处理，如订单状态更新为已支付  
		    $out_trade_no = $result['out_trade_no'];
		    // 通过订单id，将它改为已支付状态
		    $order = Object::create('Order', $out_trade_no);
		    $order->set('status', 1);
		    $order->save();
		}              
		echo $notify->ToXml();//返回给微信确认 
	}

	
	// get openid & session_key
	// 此函数即将废弃，已改用云函数实现，详见：http://blog.it577.net/index.php/archives/7/
	public function getSession() {
		$code = $this->input->post('code');
		$url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.WxPayConfig::APPID.'&secret='.WxPayConfig::APPSECRET.'&js_code='.$code.'&grant_type=authorization_code';
            header("Content-Type: application/json");
            echo file_get_contents($url);
	}

	// get access token
	// private function getAccessToken() {
	// 	$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.WxPayConfig::APPID.'&secret='.WxPayConfig::APPSECRET;
 //            // header("Content-Type: application/json");
 //            return file_get_contents($url);
	// }

	// 服务端生成图片，与微信支付无关，做分享二维码用
	public function getQRCode() {
		// 获取access_token
		$accessTokenObject = json_decode(file_get_contents('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.WxPayConfig::APPID.'&secret='.WxPayConfig::APPSECRET));
		// 拼接微信服务端获取二维码需要的url，见文档https://mp.weixin.qq.com/debug/wxadoc/dev/api/qrcode.html
		$url = 'https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?access_token=' . $accessTokenObject->access_token;
		$uid = $this->input->get('uid');
		$json = '{"path": "pages/index/index"' . $uid . ', "width": 430}';
		$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		//如果有配置代理这里就设置代理
		if(WxPayConfig::CURL_PROXY_HOST != "0.0.0.0" 
			&& WxPayConfig::CURL_PROXY_PORT != 0){
			curl_setopt($ch,CURLOPT_PROXY, WxPayConfig::CURL_PROXY_HOST);
			curl_setopt($ch,CURLOPT_PROXYPORT, WxPayConfig::CURL_PROXY_PORT);
		}
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验
		//设置header
		header('Content-Type: image/jpeg');
		//要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		//post提交方式
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		//运行curl
		$data = curl_exec($ch);
		//返回结果
		curl_close($ch);
		echo $data;
	}
}