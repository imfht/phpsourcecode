<?php
namespace Weixin\Controller;
use Weixin\Controller\SDKRuntimeException;

class Weixinpay{
    //=======【基本信息设置】=====================================
	//微信公众号身份的唯一标识。审核通过后，在微信发送的邮件中查看
	const APPID = '';
	//受理商ID，身份标识
	const MCHID = '';
	//商户支付密钥Key。审核通过后，在微信发送的邮件中查看
	const KEY = '';
	//JSAPI接口中获取openid，审核后在公众平台开启开发模式后可查看
	const APPSECRET = '';
	
	//=======【JSAPI路径设置】===================================
	//获取access_token过程中的跳转uri，通过跳转将code传入jsapi支付页面
	const JS_API_CALL_URL = '';
	
	//=======【证书路径设置】=====================================
	//证书路径,注意应该填写绝对路径
	const SSLCERT_PATH = '/Public/Weixin/cert/apiclient_cert.pem';
	const SSLKEY_PATH = '/Public/Weixin/cert/apiclient_key.pem';
	
	//=======【异步通知url设置】===================================
	//异步通知url，商户根据实际开发过程设定
	const NOTIFY_URL = '';
	
	//=======【curl超时设置】===================================
	//本例程通过curl使用HTTP POST方法，此处可修改其超时时间，默认为30秒
	const CURL_TIMEOUT = 30;
	
	
	
	private $code;//code码，用以获取openid
    private $url;//设置接口链接
	private $openid;//用户的openid
	private $prepay_id;//使用统一支付接口得到的预支付id
	private $curl_timeout;//curl超时时间
	private $parameters;//请求参数，类型为关联数组
	public $response;//微信返回的响应
	public $result;//返回参数，类型为关联数组
	public $data;//接收到的微信数据，类型为关联数组
	private $returnParameters;//返回给微信的参数，类型为关联数组
	
	function __construct()
	{
		//设置curl超时时间
		$this->curl_timeout = self::CURL_TIMEOUT;
		//设置接口链接
		$this->url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
		$this->code=$code;
		$this->openid=$openid;
		$this->parameters=$parameters;
		$this->prepay_id=$prepay_id;
		$this->response=$response;
		$this->result=$result;
		$this->data=$data;
		$this->returnParameters=$returnParameters;
	}
	
	/**
	 * 	作用：生成可以获得code的url
	 */
	public function createOauthUrlForCode($redirectUrl)
	{
		$urlObj["appid"] = self::APPID;
		$urlObj["redirect_uri"] = urlencode($redirectUrl);
		$urlObj["response_type"] = "code";
		$urlObj["scope"] = "snsapi_base";
		$urlObj["state"] = "STATE"."#wechat_redirect";
		$str = $this->format($urlObj, false);
		return "https://open.weixin.qq.com/connect/oauth2/authorize?".$str;
	}
	
	/**
	 * 	作用：生成可以获得openid的url
	 */
	private function createOauthUrlForOpenid()
	{
		$urlObj["appid"] = self::APPID;
		$urlObj["secret"] = self::APPSECRET;
		$urlObj["code"] = $this->code;
		$urlObj["grant_type"] = "authorization_code";
		$str = $this->format($urlObj, false);
		return "https://api.weixin.qq.com/sns/oauth2/access_token?".$str;
	}
	
	
	/**
	 * 	作用：通过curl向微信提交code，以获取openid
	 */
	public function getOpenid()
	{
		$url = $this->createOauthUrlForOpenid();
		//初始化curl
		$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOP_TIMEOUT, $this->curl_timeout);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		//运行curl，结果以json形式返回
		$res = curl_exec($ch);
		curl_close($ch);
		//取出openid
		$data = json_decode($res,true);
		$this->openid = $data['openid'];
		return $this->openid;
	}
	
	/**
	 * 	作用：设置prepay_id
	 */
	public function setPrepayId($prepayId)
	{
		$this->prepay_id = $prepayId;
	}
	
	/**
	 * 	作用：设置code
	 */
	public function setCode($code)
	{
		$this->code = $code;
	}
	
	/**
	 * 	作用：设置jsapi的参数
	 */
	public  function getParameters()
	{
		$jsApiObj["appId"] = self::APPID;
		$timeStamp = time();
		$jsApiObj["timeStamp"] = "$timeStamp";
		$jsApiObj["nonceStr"] = $this->createNoncestr();
		$jsApiObj["package"] = "prepay_id=".$this->prepay_id;
		$jsApiObj["signType"] = "MD5";
		$jsApiObj["paySign"] = $this->getSign($jsApiObj);
		return json_encode($jsApiObj);
	}


	//============================统一支付============================
	/**
	 * 生成接口参数xml
	 */
	private function createXml()
	{
		try
		{
			//检测必填参数
			if($this->parameters["out_trade_no"] == null)
			{
				throw new SDKRuntimeException("缺少统一支付接口必填参数out_trade_no！"."<br>");
			}elseif($this->parameters["body"] == null){
				throw new SDKRuntimeException("缺少统一支付接口必填参数body！"."<br>");
			}elseif ($this->parameters["total_fee"] == null ) {
				throw new SDKRuntimeException("缺少统一支付接口必填参数total_fee！"."<br>");
			}elseif ($this->parameters["notify_url"] == null) {
				throw new SDKRuntimeException("缺少统一支付接口必填参数notify_url！"."<br>");
			}elseif ($this->parameters["trade_type"] == null) {
				throw new SDKRuntimeException("缺少统一支付接口必填参数trade_type！"."<br>");
			}elseif ($this->parameters["trade_type"] == "JSAPI" &&
					$this->parameters["openid"] == NULL){
				throw new SDKRuntimeException("统一支付接口中，缺少必填参数openid！trade_type为JSAPI时，openid为必填参数！"."<br>");
			}
			$this->parameters["appid"] = self::APPID;//公众账号ID
			$this->parameters["mch_id"] = self::MCHID;//商户号
			$this->parameters["spbill_create_ip"] = $_SERVER['REMOTE_ADDR'];//终端ip
			$this->parameters["nonce_str"] = $this->createNoncestr();//随机字符串
			$this->parameters["sign"] = $this->getSign($this->parameters);//签名
			return  $this->arrayToXml($this->parameters);
		}catch (SDKRuntimeException $e)
		{
			die($e->errorMessage());
		}
	}
	
	/**
	 * 获取prepay_id
	 */
	public function getPrepayId()
	{
		$this->postXml();
		$this->result = $this->xmlToArray($this->response);
		$prepay_id = $this->result["prepay_id"];
		return $prepay_id;
	}

	
	//===========================请求型接口的基类=============================
	/**
	 * 	作用：设置请求参数
	 */
	public function setParameter($parameter, $parameterValue)
	{
		$this->parameters[$this->trimString($parameter)] = $this->trimString($parameterValue);
	}

	/**
	 * 	作用：设置标配的请求参数，生成签名，生成接口参数xml
	 */
// 	private function createXml(){
// 		$this->parameters["appid"] = self::APPID;//公众账号ID
// 		$this->parameters["mch_id"] = self::MCHID;//商户号
// 		$this->parameters["nonce_str"] = $this->createNoncestr();//随机字符串
// 		$this->parameters["sign"] = $this->getSign($this->parameters);//签名
// 		return  $this->arrayToXml($this->parameters);
// 	}
	
	/**
	 * 	作用：post请求xml
	 */
	private function postXml(){
		$xml = $this->createXml();
		$this->response = $this->postXmlCurl($xml,$this->url,$this->curl_timeout);
		return $this->response;
	}
	
	/**
	 * 	作用：使用证书post请求xml
	 */
	private function postXmlSSL(){
		$xml = $this->createXml();
		$this->response = $this->postXmlSSLCurl($xml,$this->url,$this->curl_timeout);
		return $this->response;
	}
	
	/**
	 * 	作用：获取结果，默认不使用证书
	 */
	private function getResult(){
		$this->postXml();
		$this->result = $this->xmlToArray($this->response);
		return $this->result;
	}
	
	//================以下是工具方法===============
	private function trimString($value){
		$ret = null;
		if (null != $value) 
		{
			$ret = $value;
			if (strlen($ret) == 0) 
			{
				$ret = null;
			}
		}
		return $ret;
	}
	
	/**
	 * 	作用：产生随机字符串，不长于32位
	 */
	private function createNoncestr( $length = 32 ){
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";  
		$str ="";
		for ( $i = 0; $i < $length; $i++ )  {  
			$str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
		}  
		return $str;
	}
	
	/**
	 * 	作用：格式化参数，签名过程需要使用
	 *  由数组转换成键值对
	 */
	private function format($arr, $urlencode)
	{
		$buff = "";
		ksort($arr);
		foreach ($arr as $k => $v)
		{
			if($urlencode)
			{
				$v = urlencode($v);
			}
			//$buff .= strtolower($k) . "=" . $v . "&";
			$buff .= $k . "=" . $v . "&";
		}
		$reqPar;
		if (strlen($buff) > 0)
		{
			$reqPar = substr($buff, 0, strlen($buff)-1);
		}
		return $reqPar;
	}
	
	/**
	 * 	作用：生成签名
	 */
	public function getSign($Obj){
		foreach ($Obj as $k => $v)
		{
			$Parameters[$k] = $v;
		}
		//签名步骤一：按字典序排序参数
		ksort($Parameters);
		$str = $this->format($Parameters, false);
		//签名步骤二：在string后加入KEY
		$str = $str."&key=".self::KEY;
		//签名步骤三：MD5加密
		$str = md5($str);
		//签名步骤四：所有字符转为大写
		$result = strtoupper($str);
		return $result;
	}
	
	/**
	 * 	作用：array转xml
	 */
	private function arrayToXml($arr){
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
        	 if (is_numeric($val))
        	 {
        	 	$xml.="<".$key.">".$val."</".$key.">"; 

        	 }
        	 else
        	 	$xml.="<".$key."><![CDATA[".$val."]]></".$key.">";  
        }
        $xml.="</xml>";
        return $xml; 
    }
	
	/**
	 * 	作用：将xml转为array
	 */
	public function xmlToArray($xml){		
        //将XML转为array        
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);		
		return $array_data;
	}

	/**
	 * 	作用：以post方式提交xml到对应的接口url
	 */
	private function postXmlCurl($xml,$url,$second=30){		
        //初始化curl        
       	$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOP_TIMEOUT, $second);
        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
		//设置header
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		//post提交方式
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		//运行curl
        $data = curl_exec($ch);
		curl_close($ch);
		//返回结果
		if($data)
		{
			curl_close($ch);
			return $data;
		}
		else 
		{ 
			$error = curl_errno($ch);
			echo "curl出错，错误码:$error"."<br>"; 
			echo "<a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html'>错误原因查询</a></br>";
			curl_close($ch);
			return false;
		}
	}

	/**
	 * 	作用：使用证书，以post方式提交xml到对应的接口url
	 */
	private function postXmlSSLCurl($xml,$url,$second=30){
		$ch = curl_init();
		//超时时间
		curl_setopt($ch,CURLOPT_TIMEOUT,$second);
		//这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
		//设置header
		curl_setopt($ch,CURLOPT_HEADER,FALSE);
		//要求结果为字符串且输出到屏幕上
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
		//设置证书
		//使用证书：cert 与 key 分别属于两个.pem文件
		//默认格式为PEM，可以注释
		curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
		curl_setopt($ch,CURLOPT_SSLCERT, self::SSLCERT_PATH);
		//默认格式为PEM，可以注释
		curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
		curl_setopt($ch,CURLOPT_SSLKEY, self::SSLKEY_PATH);
		//post提交方式
		curl_setopt($ch,CURLOPT_POST, true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$xml);
		$data = curl_exec($ch);
		//返回结果
		if($data){
			curl_close($ch);
			return $data;
		}
		else { 
			$error = curl_errno($ch);
			echo "curl出错，错误码:$error"."<br>"; 
			echo "<a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html'>错误原因查询</a></br>";
			curl_close($ch);
			return false;
		}
	}
	
	/**
	 * 	作用：打印数组
	 */
	 private function printErr($wording='',$err=''){
		print_r('<pre>');
		echo $wording."</br>";
		var_dump($err);
		print_r('</pre>');
	}

	//=====================微信返回数据的处理==========================
	/**
	 * 将微信的请求xml转换成关联数组，以方便数据处理
	 */
	public function saveData($xml)
	{
		$this->data = $this->xmlToArray($xml);
	}
	
	public function checkSign()
	{
		$tmpData = $this->data;
		unset($tmpData['sign']);
		$sign = $this->getSign($tmpData);//本地签名
		if ($this->data['sign'] == $sign) {
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * 获取微信的请求数据
	 */
	private function getData()
	{		
		return $this->data;
	}
	
	/**
	 * 设置返回微信的xml数据
	 */
	public function setReturnParameter($parameter, $parameterValue)
	{
		$this->returnParameters[$this->trimString($parameter)] = $this->trimString($parameterValue);
	}
	
	/**
	 * 生成接口参数xml
	 */
	private function createReturnXml()
	{
		return $this->arrayToXml($this->returnParameters);
	}
	
	/**
	 * 将xml数据返回微信
	 */
	public function returnXml()
	{
		$returnXml = $this->createReturnXml();
		return $returnXml;
	}

	//=========================打印log===================================
	// 打印log
	public function  log_result($file,$word) 
	{
	    $fp = fopen($file,"a");
	    flock($fp, LOCK_EX) ;
	    fwrite($fp,"执行日期：".strftime("%Y-%m-%d-%H：%M：%S",time())."\n".$word."\n\n");
	    flock($fp, LOCK_UN);
	    fclose($fp);
	}
	
}
?>