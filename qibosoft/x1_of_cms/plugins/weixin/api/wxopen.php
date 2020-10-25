<?php


//ini_set('date.timezone','Asia/Shanghai');
//error_reporting(E_ERROR);
require_once dirname(__FILE__).'/'."lib/WxPay.Api.php";
require_once dirname(__FILE__).'/'."WxPay.JsApiPay.php";
require_once dirname(__FILE__).'/'.'log.php';

//初始化日志
$logHandler= new CLogFileHandler(ROOT_PATH.date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

//打印输出数组信息
function error_info($data)
{
    $array = [];
    foreach($data as $key=>$value){
        if($data['return_code']=='FAIL'){
            $array[$key] = $value;
		}
		if($data['err_code_des']!=''){
		    $array[$key] = $value;
		}
    }
	
	if(strstr($data['return_msg'],'time_expire')){
	    $array['time_expire'] ="当前服务器时间有误！请检查一下，当前服务器上显示的时间是：".date('Y-m-d H:i:s');
	}
	return $array;
}

//①、获取用户openid
$tools = new JsApiPay();
//$openId = $tools->GetOpenid();
//$openId = get_cookie('WeiXin_OpenId');
$openId = $array['openId']; //$this->user['wxapp_api'];
if(!$openId){
	//echo 'empty openId';exit;
	return $this->err_js('empty openId');
	//$openId = $tools->GetOpenid();
}
//②、统一下单
$input = new WxPayUnifiedOrder();
$input->SetBody($array['title']);
$input->SetAttach($array['other']);
$input->SetOut_trade_no( $array['numcode'] );
$input->SetTotal_fee($array['money']*100);
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 1800));
$input->SetGoods_tag($array['title']);
$input->SetNotify_url($array['wx_notify_url']);
$input->SetTrade_type("JSAPI");
$input->SetOpenid($openId);
$order = WxPayApi::unifiedOrder($input);

$array = error_info($order);
if($array){
    return $this->err_js('参数有误'.current($array),$array);
}

$timestamp = time();
$pa = [
    'appid'=>config('webdb.wxopen_appid'),
    'partnerid'=>config('webdb.weixin_payid'),
    'prepayid'=>$order['prepay_id'],
    'package'=>'Sign=WXPay',
    'noncestr'=>rands(5).$timestamp,
    'timestamp'=>$timestamp,
];
ksort($pa);
$buff = "";
foreach ($pa as $k => $v)
{
    if($k != "sign" && $v != "" && !is_array($v)){
        $buff .= $k . "=" . $v . "&";
    }
}
$buff = trim($buff, "&");
$string = $buff . "&key=".config('webdb.weixin_paykey');
$string = md5($string);
$string = strtoupper($string);
$pa['sign'] = $string;
return $this->ok_js($pa);

/*
$jsApiParameters = $tools->GetJsApiParameters($order);

//echo $jsApiParameters;exit;

$array = json_decode($jsApiParameters,true);
$array['json'] = $jsApiParameters;
return $this->ok_js($array);
*/

?>