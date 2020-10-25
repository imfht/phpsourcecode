<?php 
/**
 * 服务窗接口
 * @author T-Team
 *
 */
//file_put_contents ( "log.txt",var_export ( $biz_content_array, true ) . "\r\n", FILE_APPEND );//日志记录

class AlipayFuwu{
	const SCheck = "alipay.service.check";//开发者接入验证接口
	const MessageNotify = "alipay.mobile.public.message.notify";//接收用户操作事件通知
	const CustomSend = "alipay.mobile.public.message.custom.send";//开发者可以使用该接口实现对用户消息的回复功能，也可以使用该接口实现客服等扩展功能。
	const TotalSend = "alipay.mobile.public.message.total.send";//开发者可以通过本接口向所有关注该服务窗用户发送消息。
	const Follow = "alipay.mobile.public.follow.list";//获取帐号的关注者列表
	const GisGet = "alipay.mobile.public.gis.get";//查询该用户当前的位置信息
	const AccessToken = "alipay.system.oauth.token";//换取授权访问令牌，开发者可通过获取到的auth_code换取access_token。
	const UserInfo = "alipay.user.userinfo.share";//通过获取到的授权访问令牌，再调用此接口便可获取用户的基本信息。
	const MenuAdd	= "alipay.mobile.public.menu.add";//创建菜单
	const MenuUpdate = "alipay.mobile.public.menu.update";//更新菜单
	const MenuGet = "alipay.mobile.public.menu.get";//查询菜单
	const QrcodeCreate = "alipay.mobile.public.qrcode.create";//创建带参二维码
	
	const MediaDownUrl = "https://openfile.alipay.com/chat/multimedia.do";//多媒体文件下载网关
	const gatewayUrl = "https://openapi.alipay.com/gateway.do";//支付宝网关
	//const signType = "";//const静态参数定义备留
	
	
	private $appid;//开发者appid
	private $merchant_private_key;//开发者密匙
	private $merchant_public_key;//开发者公匙
	private $alipay_public_key;//支付宝官方公匙
	private $_post;//支付宝post传送过来的信息
	private $_get;//url带有的参数
	private $_msg;//组装出来的回复消息
	
	public function __construct($options)
	{
		$this->appid = isset($options['appid'])?$options['appid']:'';
		$this->alipay_public_key = isset($options['alipay_public_key'])?$options['alipay_public_key']:"./public/alipay_public_key.pem";
		$this->merchant_public_key = isset($options['merchant_public_key'])?$options['merchant_public_key']:"./public/merchant_public_key.pem";
		$this->merchant_private_key = isset($options['merchant_private_key'])?$options['merchant_private_key']:"./public/merchant_private_key.pem";
	}
	
	//验证消息
	public function valid()
    {
		$sign = $this->_post['sign'];
		$sign_type = $this->_post['sign_type'];
		$biz_content = $this->_post['biz_content'];
		$service = $this->_post['service'];
		$charset = $this->_post['charset'];
		
		if (empty($sign)||empty($sign_type)||empty($biz_content)||empty($service)||empty($charset)){
			echo "有参数不全，蛋疼！！";
			exit();
		}
		//收到请求，先验证签名
		$sign_verify= $this->rsaCheckV2 ( $this->_post, $this->alipay_public_key );
		if (!$sign_verify){
			echo "签名验证失败，我擦，神马节奏";
			exit();
		}
		//验证网关请求
		echo $this->verifygw($biz_content);
    }
	
	//获取从支付宝的所有参数，并且去除转义斜杠
	public function getRev()
    {
		if (get_magic_quotes_gpc ()) {
			foreach ( $_POST as $key => $value ) {
				$_POST [$key] = stripslashes ( $value );
			}
			foreach ( $_GET as $key => $value ) {
				$_GET [$key] = stripslashes ( $value );
			}
		}
		$this->_get = $_GET;
		$this->_post = $_POST;
		return $this;
	}
	
	//获取从支付宝的POST参数
	public function getPost()
    {
		if (isset($this->_post)){
			return $this->_post;
		}else{
			return false;
		}
	}
	
	//获取消息体中的sign签名参数
	public function getSign()
    {
		return $this->_post['sign'];
	}
	
	//获取消息体中的加密类型参数
	public function getSignType()
    {
		return $this->_post['sign_type'];
	}
	
	//获取消息体中的biz_content XML参数
	public function getBizContent()
    {
		return $this->_post['biz_content'];
	}
	
	//获取消息体中的service支付宝接口参数
	public function getService()
    {
		return $this->_post['service'];
	}
	
	//获取文本消息内容
	public function getRevText() {
		$Content = $this->getNode ( $this->_post['biz_content'], "Content" );
		if (isset($Content))
			return $Content;
		else 
			return false;
	}
	
	//获取图片消息的图片信息
	public function getRevImage() {
		$MediaId = $this->getNode ( $this->_post['biz_content'], "MediaId" );
		$Format = $this->getNode ( $this->_post['biz_content'], "Format" );
		if (isset($Format))
			return array('mediaid'=>$MediaId,'format'=>$Format);
		else 
			return false;
	}
	
	//获取消息发送者
	public function getRevFrom() {
		$FromUserId = $this->getNode ( $this->_post['biz_content'], "FromUserId" );
		if (isset($FromUserId))
			return $FromUserId;
		else 
			return false;
	}
	
	//获取消息接收者
	public function getRevTo() {
		$AppId = $this->getNode ( $this->_post['biz_content'], "AppId" );
		if (isset($AppId))
			return $AppId;
		else 
			return false;
	}
	
	//获取接收消息的类型
	public function getRevType() {
		$MsgType = $this->getNode ( $this->_post['biz_content'], "MsgType" );
		if (isset($MsgType))
			return $MsgType;
		else 
			return false;
	}
	
	//获取消息ID
	public function getRevID() {
		$MsgId = $this->getNode ( $this->_post['biz_content'], "MsgId" );
		if (isset($MsgId))
			return $MsgId;
		else 
			return false;
	}
	
	//获取消息事件类型
	public function getRevEventType() {
		$EventType = $this->getNode ( $this->_post['biz_content'], "EventType" );
		if (isset($EventType))
			return $EventType;
		else 
			return false;
	}
	
	//获取消息加星用户信息
	public function getRevUserInfo() {
		$UserInfo = $this->getNode ( $this->_post['biz_content'], "UserInfo" );
		if (isset($UserInfo))
			return $UserInfo;
		else 
			return false;
	}
	
	/*
	*enter事件中是用户从特定场景（比如扫描开发者自定义的二维码）进入服务窗时，值为开发者自定义参数
	*click事件中是行为参数，菜单中设置的actionParam值
	*/
	public function ActionParam() {
		$ActionParam = $this->getNode ( $this->_post['biz_content'], "ActionParam" );
		if (isset($ActionParam))
			return $ActionParam;
		else 
			return false;
	}
	
	//-----获取消息加星用户信息
	public function getRevAgreementId() {
		$AgreementId = $this->getNode ( $this->_post['biz_content'], "AgreementId" );
		if (isset($AgreementId))
			return $UserInfo;
		else 
			return false;
	}
	
	//--------获取消息加星用户信息
	public function getRevAccountNo() {
		$AccountNo = $this->getNode ( $this->_post['biz_content'], "AccountNo" );
		if (isset($AccountNo))
			return $AccountNo;
		else 
			return false;
	}
	
	//---获取消息加星用户信息
	public function getRevUserInfo1() {
		$UserInfo = $this->getNode ( $this->_post['biz_content'], "UserInfo" );
		if (isset($UserInfo))
			return $UserInfo;
		else 
			return false;
	}
	
	//组装文字消息
	public function text($text='') {
		$text = array ('content' => iconv ( "UTF-8", "GBK",$text ));
		$biz_content = array (
				'msgType' => 'text',
				'text' => $text,
				'toUserId' => $this->getRevFrom()
		);
		
		$this->_msg = $this->JSON ( $biz_content );
		return $this;
	}
	
	//组装图文消息
	public function news($articles_arr) {
		$biz_content = array (
				'msgType' => 'image-text',
				'createTime' => time (),
				'articles' => $articles_arr,
				'toUserId' => $this->getRevFrom()
		);
		$this->_msg = $this->JSON ( $biz_content );
		return $this;
	}
	
	private function toBizContentJson($biz_content, $toUserId) {
		// 如果toUserId为空，则是发给所有关注的而用户，且不可删除，慎用
		if (isset ( $toUserId ) && ! empty ( $toUserId )) {
			$biz_content ['toUserId'] = $toUserId;
		}
		
		$content = $this->JSON ( $biz_content );
		return $content;
	}
	
	/**
	 * ************************************************************
	 *
	 * 将数组转换为JSON字符串（兼容中文）
	 *
	 * ***********************************************************
	 */
	protected function JSON($array) {
		$this->arrayRecursive ( $array, 'urlencode', true );
		$json = json_encode ( $array );
		return urldecode ( $json );
	}
	
	/**
	 * 异步发送消息给用户
	 *
	 * @param string $biz_content        	
	 * @param string $isMultiSend
	 *        	如果发给所有人，则此参数必须为true，且biz_content中的toUserId必须为空
	 * @return string
	 */
	public function reply($biz_content='', $isMultiSend = FALSE) {
		if(empty($biz_content)){
			$biz_content = $this->_msg;
		}
		$paramsArray = array (
				'method' => "alipay.mobile.public.message.custom.send",
				'biz_content' => $biz_content,
				'charset' => 'GBK',
				'sign_type' => 'RSA',
				'app_id' => $this->appid,
				'timestamp' => date ( 'Y-m-d H:i:s', time () ) 
		);
		if ($isMultiSend) {
			$paramsArray ['method'] = "alipay.mobile.public.message.total.send";
		}
		
		$sign = $this->sign_request ( $paramsArray, $this->merchant_private_key );
		$paramsArray ['sign'] = $sign;
		
		return self::sendPostRequst ( self::gatewayUrl, $paramsArray );
	}
	
	//生成POST给支付宝API的消息体，$biz_content为json数据，$method为post给网关的类型，有的是菜单添加，都得是图片下载
	public function PostToApi($biz_content, $method) {
		return array(
				'app_id' => $this->appid,
				'method' => $method,
				'charset' => 'GBK',
				'sign_type' => 'RSA',
				'timestamp' => date ( 'Y-m-d H:i:s', time () ),
				'biz_content' => $biz_content
		);
	}
	
	//获取用户的地理位置信息
	public function getGis() {
		$biz_content = '{"userId":"'.$this->getRevFrom().'"}';
		$paramsArray = $this->PostToApi($biz_content,self::GisGet);
		$sign = $this->sign_request ( $paramsArray, $this->merchant_private_key );
		$paramsArray ['sign'] = $sign;
		//将支付宝返回的json转编码，否则不能编译成数组
		$json = iconv("GB2312", "UTF-8//IGNORE", self::sendPostRequst ( self::gatewayUrl, $paramsArray ));
		$array = json_decode($json,true);
		return $array['alipay_mobile_public_gis_get_response'];
	}
	
	//接口名称：alipay.system.oauth.token
	//换取授权访问令牌，开发者可通过获取到的auth_code换取access_token。
	public function getOauthToken($code) {
		$biz_content = array(
				'app_id' => $this->appid,
				'method' => self::AccessToken,
				'charset' => 'GBK',
				'sign_type' => 'RSA',
				'timestamp' => date ( 'Y-m-d H:i:s', time () ),
				'version' => '1.0',
				'grant_type' => 'authorization_code',
				'code' => $code
		);
		$sign = $this->sign_request ( $biz_content, $this->merchant_private_key );
		$biz_content ['sign'] = $sign;
		
		//将支付宝返回的json转编码，否则不能编译成数组
		$json = iconv("GB2312", "UTF-8//IGNORE", self::sendPostRequst ( self::gatewayUrl, $biz_content ));
		$array = json_decode($json,true);
		return $array['alipay_system_oauth_token_response'];
	}
	
	//获取用户信息接口名称：alipay.user.userinfo.share
	//通过获取到的授权访问令牌，再调用此接口便可获取用户的基本信息。
	public function getUserInfo($code) {
		$tokenarray = $this->getOauthToken($code);
		$biz_content = array(
				'app_id' => $this->appid,
				'method' => self::UserInfo,
				'charset' => 'GBK',
				'auth_token' => $tokenarray['access_token'],
				'sign_type' => 'RSA',
				'timestamp' => date ( 'Y-m-d H:i:s', time () ),
				'version' => '1.0'
		);
		$sign = $this->sign_request ( $biz_content, $this->merchant_private_key );
		$biz_content ['sign'] = $sign;
		
		//将支付宝返回的json转编码，否则不能编译成数组
		$json = iconv("GB2312", "UTF-8//IGNORE", self::sendPostRequst ( self::gatewayUrl, $biz_content ));
		$array = json_decode($json,true);
		return $array['alipay_user_userinfo_share_response'];
	}
	
	//带参推广二维码 接口名称：alipay.mobile.public.qrcode.create
	//使用场景举例：开发者通过调用该接口创建带参二维码。通过参数开发者可以区分二维码的渠道等信息。
	public function QrcodeCreate($sceneId,$type='TEMP',$time='1800',$showlogo='N') {
		$biz_content ='{"codeInfo":{"scene":{"sceneId": "'.$sceneId.'"}},"codeType": "'.$type.'","expireSecond": "'.$time.'","showLogo": "'.$showlogo.'"}';
		
		$paramsArray = array (
				'method' => self::QrcodeCreate,
				'biz_content' => $biz_content,
				'charset' => 'GBK',
				'sign_type' => 'RSA',
				'app_id' => $this->appid,
				'timestamp' => date ( 'Y-m-d H:i:s', time () ) 
		);
		$sign = $this->sign_request ( $paramsArray, $this->merchant_private_key );
		$paramsArray ['sign'] = $sign;
		
		//将支付宝返回的json转编码，否则不能编译成数组
		$json = iconv("GB2312", "UTF-8//IGNORE", self::sendPostRequst ( self::gatewayUrl, $paramsArray ));
		$array = json_decode($json,true);
		return $array['alipay_mobile_public_qrcode_create_response'];
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//----BEGIN-----thisMsg.php------
	
	//----END-----thisMsg.php------
	
	
	//----BEGIN-----AlipaySign.php------
	public function rsa_sign($data, $merchant_private_key) {
		$priKey = file_get_contents ( $merchant_private_key );
		$res = openssl_get_privatekey ( $priKey );
		openssl_sign ( $data, $sign, $res );
		openssl_free_key ( $res );
		$sign = base64_encode ( $sign );
		return $sign;
	}
	
	public function sign_request($params, $merchant_private_key) {
		return $this->rsa_sign ( $this->getSignContent ( $params ), $merchant_private_key );
	}
	
	public function sign_response($bizContent, $merchant_private_key) {
		$sign = $this->rsa_sign ( $bizContent, $merchant_private_key );
		$response = "<?xml version=\"1.0\" encoding=\"GBK\"?><alipay><response>$bizContent</response><sign>$sign</sign><sign_type>RSA</sign_type></alipay>";
		return $response;
	}
	
	public function rsa_verify($data, $sign, $alipay_public_key) {
		$pubKey = file_get_contents ( $alipay_public_key );
		$res = openssl_get_publickey ( $pubKey );
		$result = ( bool ) openssl_verify ( $data, base64_decode ( $sign ), $res );
		openssl_free_key ( $res );
		return $result;
	}
	
	public function rsaCheckV2($params, $alipay_public_key) {
		$sign = $params ['sign'];
		$params ['sign'] = null;
		
		return $this->rsa_verify ( $this->getSignContent ( $params ), $sign, $alipay_public_key );
	}
	
	protected function getSignContent($params) {
		ksort ( $params );
		
		$stringToBeSigned = "";
		$i = 0;
		foreach ( $params as $k => $v ) {
			if (false === $this->checkEmpty ( $v ) && "@" != substr ( $v, 0, 1 )) {
				if ($i == 0) {
					$stringToBeSigned .= "$k" . "=" . "$v";
				} else {
					$stringToBeSigned .= "&" . "$k" . "=" . "$v";
				}
				$i ++;
			}
		}
		unset ( $k, $v );
		return $stringToBeSigned;
	}
	
	/**
	 * 校验$value是否非空
	 * if not set ,return true;
	 * if is null , return true;
	 */
	protected function checkEmpty($value) {
		if (! isset ( $value ))
			return true;
		if ($value === null)
			return true;
		if (trim ( $value ) === "")
			return true;
		
		return false;
	}
	
	//将公匙文件转化成字符串
	public function getPublicKeyStr($pub_pem_path) {
		$content = file_get_contents ( $pub_pem_path );
		$content = str_replace ( "-----BEGIN PUBLIC KEY-----", "", $content );
		$content = str_replace ( "-----END PUBLIC KEY-----", "", $content );
		$content = str_replace ( "\r", "", $content );
		$content = str_replace ( "\n", "", $content );
		return $content;
	}
	
	//将密匙文件转化成字符串
	public function getPrivateKeyStr($pri_pem_path) {
		$content = file_get_contents ( $pri_pem_path );
		$content = str_replace ( "-----BEGIN RSA PRIVATE KEY-----", "", $content );
		$content = str_replace ( "-----END RSA PRIVATE KEY-----", "", $content );
		$content = str_replace ( "\r", "", $content );
		$content = str_replace ( "\n", "", $content );
		return $content;
	}
	//----END-----AlipaySign.php------
	
	
	//----BEGIN-----Gateway.php------
	public function verifygw($biz_content) {
		$xml = simplexml_load_string ( $biz_content );
		$EventType = ( string ) $xml->EventType;
		if ($EventType == "verifygw") {
			$response_xml = "<success>true</success><biz_content>" . $this->getPublicKeyStr($this->merchant_public_key) . "</biz_content>";
			$return_xml = $this->sign_response ( $response_xml, $this->merchant_private_key );
			return $return_xml;
			exit ();
		}
	}
	//----END-----Gateway.php------
	
	
	//----BEGIN-----PushMsg.php------
	public function sendRequest($biz_content) {
		$custom_send = new AlipayMobilePublicMessageCustomSendRequest ();
		$custom_send->setBizContent ( $biz_content );
		
		require 'config.php';
		$aop = new AopClient ();
		$aop->appId = $this->appid;
		$aop->rsaPrivateKeyFilePath = $config ['merchant_private_key_file'];
		$result = $aop->execute ( $custom_send );
		return $result;
	}
	
	function is_utf8($text) {
		$e = mb_detect_encoding ( $text, array (
				'UTF-8',
				'GBK' 
		) );
		switch ($e) {
			case 'UTF-8' : // 如果是utf8编码
				return true;
			case 'GBK' : // 如果是gbk编码
				return false;
		}
	}
	
	/**
	 * ************************************************************
	 *
	 * 使用特定function对数组中所有元素做处理
	 *
	 * @param
	 *        	string &$array 要处理的字符串
	 * @param string $function
	 *        	要执行的函数
	 * @return boolean $apply_to_keys_also 是否也应用到key上
	 * @access public
	 *        
	 *         ***********************************************************
	 */
	protected function arrayRecursive(&$array, $function, $apply_to_keys_also = false) {
		foreach ( $array as $key => $value ) {
			if (is_array ( $value )) {
				$this->arrayRecursive ( $array [$key], $function, $apply_to_keys_also );
			} else {
				$array [$key] = $function ( $value );
			}
			
			if ($apply_to_keys_also && is_string ( $key )) {
				$new_key = $function ( $key );
				if ($new_key != $key) {
					$array [$new_key] = $array [$key];
					unset ( $array [$key] );
				}
			}
		}
	}
	//----END-----PushMsg.php------
	
	
	
	
	
	//----BEGIN-----Message.php------
	public function getNode($xml, $node) {
		$xml = "<?xml version=\"1.0\" encoding=\"GBK\"?>" . $xml;
		$dom = new DOMDocument ( "1.0", "GBK" );
		$dom->loadXML ( $xml );
		$event_type = $dom->getElementsByTagName ( $node );
		return $event_type->item ( 0 )->nodeValue;
	}
	
	// 给支付宝返回ACK回应消息，不然支付宝会再次重试发送消息,再调用此方法之前，不要打印输出任何内容
	public function mkAckMsg() {
		require 'config.php';
		$response_xml = "<XML><ToUserId><![CDATA[" . $this->getRevFrom() . "]]></ToUserId><AppId><![CDATA[" . $this->appid . "]]></AppId><CreateTime>" . time () . "</CreateTime><MsgType><![CDATA[ack]]></MsgType></XML>";
		$return_xml = $as->sign_response ( $response_xml, $this->merchant_private_key );
		return $return_xml;
	}
	//----END-----Message.php------
	
	
	
	
	
	
	//----BEGIN-----HttpRequst.php------
	public static function sendPostRequst($url, $data) {
		$postdata = http_build_query ( $data );
		$opts = array (
				'http' => array (
						'method' => 'POST',
						'header' => 'Content-type: application/x-www-form-urlencoded',
						'content' => $postdata 
				) 
		);
		
		
		$context = stream_context_create ( $opts );
		
		$result = file_get_contents ( $url, false, $context );
		return $result;
	}
	
	public static function getRequest($key) {
		$request = null;
		if (isset ( $_GET [$key] ) && ! empty ( $_GET [$key] )) {
			$request = $_GET [$key];
		} elseif (isset ( $_POST [$key] ) && ! empty ( $_POST [$key] )) {
			$request = $_POST [$key];
		}
		return $request;
	}
	//----END-----HttpRequst.php------
	
	
	
	
	
	//----BEGIN-----Xml.class.php------
	
	//----END-----Xml.class.php------
	
	
	
	
}
?>