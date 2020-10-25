<?php
/**
 * Weixin Pay Api File
 *
 * @author PMonkey_W
 * @copyright Changsha Sinlody Network & Technology Co. Ltd.
 * @link www.sinlody.com
 * @since 2016-01-11
 * @version 1.0
 */
define('IN_API', true);
define('CURSCRIPT', 'api');

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

global $_G;
$ec_wxpay_appid = $_G['setting']['ec_wxpay_appid'];
$ec_wxpay_appsecret = $_G['setting']['ec_wxpay_appsecret'];
$ec_wxpay_mch_id = $_G['setting']['ec_wxpay_mch_id'];
$ec_wxpay_key = $_G['setting']['ec_wxpay_key'];
define('DISCUZ_WXPAY_APPID', $ec_wxpay_appid);
define('DISCUZ_WXPAY_APPSECRET', $ec_wxpay_appsecret);
define('DISCUZ_WXPAY_MCHID', $ec_wxpay_mch_id);
define('DISCUZ_WXPAY_KEY', $ec_wxpay_key);

function credit_payurl($price, &$orderid) {
    global $_G;
    try{
        $orderid = dgmdate(TIMESTAMP, 'YmdHis').random(18);

        $input = new WxPayUnifiedOrder();
        $body = $_G['setting']['bbname'].' - '.$_G['member']['username'].' - '.lang('forum/misc', 'credit_payment');
        $body = diconv($body, CHARSET, 'UTF-8');
        $input->SetBody($body);
        $input->SetAttach("wxpay");
        $input->SetOut_trade_no($orderid);
        $input->SetTotal_fee($price*100);
        $input->SetSpbill_create_ip($_G['clientip']);
        $input->SetNotify_url($_G['siteurl'].'api/trade/notify_credit.php');
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id($orderid);
        $result = WxPayApi::unifiedOrder($input);
        if($result['return_code'] == 'SUCCESS'){
            $url = $result["code_url"];
            return $_G['siteurl'].'api/qrcode/png.php?url='.urlencode($url);
        }else{
            throw new WxPayException($result["return_msg"]);
        }
    }catch (WxPayException $e){
        if($_G['inajax']){
            showmessage('payapi_error', '', array(), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
        }else{
            system_error(lang('message', 'payapi_error'));
        }
    }
}

function invite_payurl($amount, $price, &$orderid) {
    global $_G;
    try{
        $orderid = dgmdate(TIMESTAMP, 'YmdHis').random(18);

        $input = new WxPayUnifiedOrder();
        $body = $_G['setting']['bbname'].' - '.lang('forum/misc', 'invite_payment').'['.lang('forum/misc', 'invite_forum_payment').' '.intval($amount).' '.lang('forum/misc', 'invite_forum_payment_unit').']';
        $body = diconv($body, CHARSET, 'UTF-8');
        $input->SetBody($body);
        $input->SetAttach("wxpay");
        $input->SetOut_trade_no($orderid);
        $input->SetTotal_fee($price*100);
        $input->SetSpbill_create_ip($_G['clientip']);
        $input->SetNotify_url($_G['siteurl'].'api/trade/notify_invite.php');
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id($orderid);
        $result = WxPayApi::unifiedOrder($input);
        if($result['return_code'] == 'SUCCESS'){
            $url = $result["code_url"];
            return $_G['siteurl'].'api/qrcode/png.php?url='.urlencode($url);
        }else{
            throw new WxPayException($result["return_msg"]);
        }
    }catch (WxPayException $e){
        if($_G['inajax']){
            showmessage('payapi_error', '', array(), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
        }else{
            system_error(lang('message', 'payapi_error'));
        }
    }
}

function trade_payurl($pay, $trade, $tradelog) {
    global $_G;
    try{
        $input = new WxPayUnifiedOrder();
        $body = diconv($trade['subject'], CHARSET, 'UTF-8');
        $input->SetBody($body);
        $input->SetAttach("wxpay");
        $input->SetOut_trade_no($tradelog['orderid']);
        $input->SetTotal_fee($tradelog['baseprice']*100);
        $input->SetSpbill_create_ip($_G['clientip']);
        $input->SetNotify_url($_G['siteurl'].'api/trade/notify_trade.php');
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id($tradelog['orderid']);
        $result = WxPayApi::unifiedOrder($input);
        if($result['return_code'] == 'SUCCESS'){
            $url = $result["code_url"];
            return $_G['siteurl'].'api/qrcode/png.php?url='.urlencode($url);
        }else{
            throw new WxPayException($result["return_msg"]);
        }
    }catch (WxPayException $e){
        if($_G['inajax']){
            showmessage('payapi_error', '', array(), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
        }else{
            system_error(lang('message', 'payapi_error'));
        }
    }
}

function arr2xml($data){
    $xml = "<xml>";
    foreach ($data as $key=>$val)
    {
        if (is_numeric($val)){
            $xml.="<".$key.">".$val."</".$key.">";
        }else{
            $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
        }
    }
    $xml.="</xml>";
    return $xml;
}

function trade_notifycheck($type) {
    global $_G;

    $msg = '';

    $notify = WxPayApi::notify($msg);

    if(empty($notify)){
        $return = array(
            'return_code'=>'FAIL',
            'return_msg'=>$msg,
        );
        WxPayApi::replyNotify(arr2xml($return));exit;
    }
    if($type == 'credit' || $type == 'invite') {
        if($notify['result_code'] == 'SUCCESS') {
            return array(
                'validator'	=> isset($notify['result_code']) && $notify['result_code'] == 'SUCCESS' ? 1 : 0,
                'order_no' 	=> $notify['out_trade_no'],
                'trade_no'	=> isset($notify['transaction_id']) ? $notify['transaction_id'] : '',
                'price' 	=> $notify['total_fee'] / 100,
                'appid' => $notify['appid'],
                'notify'	=> arr2xml(array('return_code'=>'SUCCESS')),
                'location'	=> false,
            );
        }
    } else {
        return array(
            'validator'	=> FALSE,
            'location'	=> 'forum.php?mod=memcp&action=credits&operation=addfunds&return=fail'
        );
    }
}

function trade_setprice($data, &$price, &$pay, &$transportfee) {
    if($data['transport'] == 3) {
        $pay['logistics_type'] = 'VIRTUAL';
    }

    if($data['transport'] != 3) {
        if($data['fee'] == 1) {
            $pay['logistics_type'] = 'POST';
            $pay['logistics_fee'] = $data['trade']['ordinaryfee'];
            if($data['transport'] == 2) {
                $price = $price + $data['trade']['ordinaryfee'];
                $transportfee = $data['trade']['ordinaryfee'];
            }
        } elseif($data['fee'] == 2) {
            $pay['logistics_type'] = 'EMS';
            $pay['logistics_fee'] = $data['trade']['emsfee'];
            if($data['transport'] == 2) {
                $price = $price + $data['trade']['emsfee'];
                $transportfee = $data['trade']['emsfee'];
            }
        } else {
            $pay['logistics_type'] = 'EXPRESS';
            $pay['logistics_fee'] = $data['trade']['expressfee'];
            if($data['transport'] == 2) {
                $price = $price + $data['trade']['expressfee'];
                $transportfee = $data['trade']['expressfee'];
            }
        }
    }
}

function trade_getorderurl($orderid) {
    return "https://www.tenpay.com/med/tradeDetail.shtml?b=1&trans_id=$orderid";
}

function trade_typestatus($method, $status = -1) {
    switch($method) {
        case 'buytrades'	: $methodvalue = array(1, 3);break;
        case 'selltrades'	: $methodvalue = array(2, 4);break;
        case 'successtrades'	: $methodvalue = array(5);break;
        case 'tradingtrades'	: $methodvalue = array(1, 2, 3, 4);break;
        case 'closedtrades'	: $methodvalue = array(6, 10);break;
        case 'refundsuccess'	: $methodvalue = array(9);break;
        case 'refundtrades'	: $methodvalue = array(9, 10);break;
        case 'unstarttrades'	: $methodvalue = array(0);break;
    }
    return $status != -1 ? in_array($status, $methodvalue) : $methodvalue;
}

function trade_getstatus($key, $method = 2) {
    $language = lang('forum/misc');
    $status[1] = array(
        'WAIT_BUYER_PAY' => 1,
        'WAIT_SELLER_CONFIRM_TRADE' => 2,
        'WAIT_SELLER_SEND_GOODS' => 3,
        'WAIT_BUYER_CONFIRM_GOODS' => 4,
        'TRADE_FINISHED' => 5,
        'TRADE_CLOSED' => 6,
        'REFUND_SUCCESS' => 9,
        'REFUND_CLOSED' => 10,
    );
    $status[2] = array(
        0  => $language['trade_unstart'],
        1  => $language['trade_waitbuyerpay'],
        2  => $language['trade_waitsellerconfirm'],
        3  => $language['trade_waitsellersend'],
        4  => $language['trade_waitbuyerconfirm'],
        5  => $language['trade_finished'],
        6  => $language['trade_closed'],
        9 => $language['trade_refundsuccess'],
        10 => $language['trade_refundclosed']
    );
    return $method == -1 ? $status[2] : $status[$method][$key];
}

class WxPayConfig{
    const APPID = DISCUZ_WXPAY_APPID;
    const APPSECRET = DISCUZ_WXPAY_APPSECRET;
    const MCHID = DISCUZ_WXPAY_MCHID;
    const KEY = DISCUZ_WXPAY_KEY;


    const CURL_PROXY_HOST = "0.0.0.0";//"10.152.18.220";
    const CURL_PROXY_PORT = 0;//8080;

    const REPORT_LEVENL = 1;
}

class WxPayException extends Exception {
    public function errorMessage(){
        return $this->getMessage();
    }
}

class WxPayApi{
    public static function unifiedOrder($inputObj, $timeOut = 6){
        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        if(!$inputObj->IsOut_trade_noSet()) {
            throw new WxPayException("miss out_trade_no!");
        }else if(!$inputObj->IsBodySet()){
            throw new WxPayException("miss body!");
        }else if(!$inputObj->IsTotal_feeSet()) {
            throw new WxPayException("miss total_fee!");
        }else if(!$inputObj->IsTrade_typeSet()) {
            throw new WxPayException("miss trade_type!");
        }

        if($inputObj->GetTrade_type() == "JSAPI" && !$inputObj->IsOpenidSet()){
            throw new WxPayException("miss openid！if trade_type is 'JSAPI' , openid is must fill!");
        }
        if($inputObj->GetTrade_type() == "NATIVE" && !$inputObj->IsProduct_idSet()){
            throw new WxPayException("miss product_id！if trade_type is 'JSAPI',product_id is must fill!");
        }

        if(!$inputObj->IsNotify_urlSet()){
            $inputObj->SetNotify_url(WxPayConfig::NOTIFY_URL);
        }

        $inputObj->SetAppid(WxPayConfig::APPID);
        $inputObj->SetMch_id(WxPayConfig::MCHID);
        $inputObj->SetNonce_str(self::getNonceStr());

        $inputObj->SetSign();
        $xml = $inputObj->ToXml();

        $startTimeStamp = self::getMillisecond();
        $response = self::postXmlCurl($xml, $url, false, $timeOut);
        $result = WxPayResults::Init($response);
        self::reportCostTime($url, $startTimeStamp, $result);

        return $result;
    }

    public static function orderQuery($inputObj, $timeOut = 6){
        $url = "https://api.mch.weixin.qq.com/pay/orderquery";
        if(!$inputObj->IsOut_trade_noSet() && !$inputObj->IsTransaction_idSet()) {
            throw new WxPayException("you must fill out_trade_no or transaction_id at least one!");
        }
        $inputObj->SetAppid(WxPayConfig::APPID);
        $inputObj->SetMch_id(WxPayConfig::MCHID);
        $inputObj->SetNonce_str(self::getNonceStr());

        $inputObj->SetSign();
        $xml = $inputObj->ToXml();

        $startTimeStamp = self::getMillisecond();
        $response = self::postXmlCurl($xml, $url, false, $timeOut);
        $result = WxPayResults::Init($response);
        self::reportCostTime($url, $startTimeStamp, $result);

        return $result;
    }

    public static function closeOrder($inputObj, $timeOut = 6){
        $url = "https://api.mch.weixin.qq.com/pay/closeorder";
        if(!$inputObj->IsOut_trade_noSet()) {
            throw new WxPayException("miss out_trade_no!");
        }
        $inputObj->SetAppid(WxPayConfig::APPID);
        $inputObj->SetMch_id(WxPayConfig::MCHID);
        $inputObj->SetNonce_str(self::getNonceStr());

        $inputObj->SetSign();
        $xml = $inputObj->ToXml();

        $startTimeStamp = self::getMillisecond();
        $response = self::postXmlCurl($xml, $url, false, $timeOut);
        $result = WxPayResults::Init($response);
        self::reportCostTime($url, $startTimeStamp, $result);

        return $result;
    }

    public static function report($inputObj, $timeOut = 1){
        $url = "https://api.mch.weixin.qq.com/payitil/report";
        if(!$inputObj->IsInterface_urlSet()) {
            throw new WxPayException("miss interface_url!");
        } if(!$inputObj->IsReturn_codeSet()) {
            throw new WxPayException("miss return_code!");
        } if(!$inputObj->IsResult_codeSet()) {
            throw new WxPayException("miss result_code!");
        } if(!$inputObj->IsUser_ipSet()) {
            throw new WxPayException("miss user_ip!");
        } if(!$inputObj->IsExecute_time_Set()) {
            throw new WxPayException("miss execute_time_set!");
        }
        $inputObj->SetAppid(WxPayConfig::APPID);
        $inputObj->SetMch_id(WxPayConfig::MCHID);
        $inputObj->SetUser_ip($_SERVER['REMOTE_ADDR']);
        $inputObj->SetTime(date("YmdHis"));
        $inputObj->SetNonce_str(self::getNonceStr());

        $inputObj->SetSign();
        $xml = $inputObj->ToXml();

        $startTimeStamp = self::getMillisecond();
        $response = self::postXmlCurl($xml, $url, false, $timeOut);
        return $response;
    }

    public static function notify(&$msg){
        $xml = file_get_contents("php://input");
        try {
            $result = WxPayResults::Init($xml);
        } catch (WxPayException $e){
            $msg = $e->errorMessage();
            return false;
        }
        return $result;
    }

    public static function getNonceStr($length = 32){
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }

    public static function replyNotify($xml){
        echo $xml;
    }

    private static function reportCostTime($url, $startTimeStamp, $data){
        if(WxPayConfig::REPORT_LEVENL == 0){
            return;
        }
        if(WxPayConfig::REPORT_LEVENL == 1 &&
            array_key_exists("return_code", $data) &&
            $data["return_code"] == "SUCCESS" &&
            array_key_exists("result_code", $data) &&
            $data["result_code"] == "SUCCESS")
        {
            return;
        }

        $endTimeStamp = self::getMillisecond();
        $objInput = new WxPayReport();
        $objInput->SetInterface_url($url);
        $objInput->SetExecute_time_($endTimeStamp - $startTimeStamp);
        if(array_key_exists("return_code", $data)){
            $objInput->SetReturn_code($data["return_code"]);
        }
        if(array_key_exists("return_msg", $data)){
            $objInput->SetReturn_msg($data["return_msg"]);
        }
        if(array_key_exists("result_code", $data)){
            $objInput->SetResult_code($data["result_code"]);
        }
        if(array_key_exists("err_code", $data)){
            $objInput->SetErr_code($data["err_code"]);
        }
        if(array_key_exists("err_code_des", $data)){
            $objInput->SetErr_code_des($data["err_code_des"]);
        }
        if(array_key_exists("out_trade_no", $data)){
            $objInput->SetOut_trade_no($data["out_trade_no"]);
        }
        if(array_key_exists("device_info", $data)){
            $objInput->SetDevice_info($data["device_info"]);
        }

        try{
            self::report($objInput);
        } catch (WxPayException $e){
        }
    }

    private static function postXmlCurl($xml, $url, $useCert = false, $second = 30){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);

        if(WxPayConfig::CURL_PROXY_HOST != "0.0.0.0" && WxPayConfig::CURL_PROXY_PORT != 0){
            curl_setopt($ch,CURLOPT_PROXY, WxPayConfig::CURL_PROXY_HOST);
            curl_setopt($ch,CURLOPT_PROXYPORT, WxPayConfig::CURL_PROXY_PORT);
        }
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        if($useCert == true){
        }
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        $data = curl_exec($ch);
        if($data){
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            throw new WxPayException("curl is error, error code is {$error}");
        }
    }

    private static function getMillisecond(){
        $time = explode ( " ", microtime () );
        $time = $time[1] . ($time[0] * 1000);
        $time2 = explode( ".", $time );
        $time = $time2[0];
        return $time;
    }
}

class WxPayDataBase
{
    protected $values = array();

    public function __call($name, $arguments = array()){
        if(substr($name,0,3) == 'Get'){
            return $this->values[strtolower(substr($name,3))];
        }elseif(substr($name,0,3) == 'Set'){
            $this->values[strtolower(substr($name,3))] = $arguments[0];
        }elseif(substr($name,0,2) == 'Is' && substr($name,-3) == 'Set'){
            return array_key_exists(strtolower(substr(substr($name, 2),0,-3)), $this->values);
        }else{
            throw new WxPayException("miss user function {$name}!");
        }
    }

    public function SetSign(){
        $sign = $this->MakeSign();
        $this->values['sign'] = $sign;
        return $sign;
    }

    public function ToXml(){
        if(!is_array($this->values)
            || count($this->values) <= 0)
        {
            throw new WxPayException("array data is error!");
        }

        $xml = "<xml>";
        foreach ($this->values as $key=>$val)
        {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }

    public function FromXml($xml){
        if(!$xml){
            throw new WxPayException("xml data is error!");
        }
        libxml_disable_entity_loader(true);
        $this->values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $this->values;
    }

    public function ToUrlParams(){
        $buff = "";
        foreach ($this->values as $k => $v)
        {
            if($k != "sign" && $v != "" && !is_array($v)){
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }

    public function MakeSign(){
        ksort($this->values);
        $string = $this->ToUrlParams();
        $string = $string . "&key=".WxPayConfig::KEY;
        $string = md5($string);
        $result = strtoupper($string);
        return $result;
    }

    public function GetValues(){
        return $this->values;
    }
}

class WxPayResults extends WxPayDataBase{
    public function CheckSign(){
        if(!$this->IsSignSet()){
            throw new WxPayException("Sign error!");
        }

        $sign = $this->MakeSign();
        if($this->GetSign() == $sign){
            return true;
        }
        throw new WxPayException("Sign error!");
    }

    public function FromArray($array)
    {
        $this->values = $array;
    }

    public static function InitFromArray($array, $noCheckSign = false)
    {
        $obj = new self();
        $obj->FromArray($array);
        if($noCheckSign == false){
            $obj->CheckSign();
        }
        return $obj;
    }

    public function SetData($key, $value)
    {
        $this->values[$key] = $value;
    }

    public static function Init($xml)
    {
        $obj = new self();
        $obj->FromXml($xml);
        if($obj->values['return_code'] != 'SUCCESS'){
            return $obj->GetValues();
        }
        $obj->CheckSign();
        return $obj->GetValues();
    }
}

class WxPayUnifiedOrder extends WxPayDataBase{

}

class WxPayOrderQuery extends WxPayDataBase{

}

class WxPayCloseOrder extends WxPayDataBase{

}

class WxPayReport extends WxPayDataBase{

}