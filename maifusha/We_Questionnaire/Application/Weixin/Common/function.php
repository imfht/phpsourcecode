<?php 
/**
 * 发起一个get请求
 * @param string $url  带请求字符串的get地址
 * @return string  序列化的json数据字串起
 */
function httpGet($url)
{
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 500);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

	$res = curl_exec($ch);
	curl_close($ch);

	return $res;
}

/**
 * 发起一个post请求
 * @param string $url  post请求地址
 * @param array $data  表单数据
 * @param string $filePath  可选，上传文件路径
 * @return string  序列化的json数据字串起
 */
function httpPost($url, $data, $filePath=null)
{
	if( $filePath ){
		$data['file'] = "@$filePath";
	}

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 500);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_POST,  1 );
	curl_setopt($ch, CURLOPT_POSTFIELDS,  $data);

	$res = curl_exec($ch);
	curl_close($ch);

	return $res;	
}

/**
 * 加密微信消息
 * @param string $rawMsg  待加密的消息
 * @param int $timestamp  请求URL上携带的时戳信息
 * @param string $nonce  请求URL上携带的随机字符串
 * @return 成功: string 加密后的消息    失败： 抛出一个标准异常
 */
function encryptMsg($rawMsg, $timestamp, $nonce)
{
	Vendor('WeixinEncrypt.wxBizMsgCrypt');

	$encryptMsg = '';
	$pc = new WXBizMsgCrypt(C('wechat.Token'), C('wechat.EncodingAESKey'), C('wechat.AppID'));
	$errCode = $pc->encryptMsg($rawMsg, $timestamp, $nonce, $encryptMsg);

	if ( $errCode == 0 ) {
		return $encryptMsg;
	} else {
		throw Exception('微信消息加密出错', $errCode);
	}
}

/**
 * 解密微信消息
 * @param string $rawMsg  接收到的加密消息
 * @param string $msg_signature  消息体签名，不是signature而是msg_signature. 安全模式或者兼容模式下，URL中会增加该参数，用于验证消息体的合法性
 * @param int $timestamp  请求URL上携带的时戳信息
 * @param string $nonce  请求URL上携带的随机字符串
 * @return 成功: string 解密后的消息    失败： 抛出一个标准异常
 */
function decryptMsg($rawMsg, $msg_signature, $timestamp, $nonce)
{
	Vendor('WeixinEncrypt.wxBizMsgCrypt');

	$msg = '';
	$pc = new WXBizMsgCrypt(C('wechat.Token'), C('wechat.EncodingAESKey'), C('wechat.AppID'));
	$errCode = $pc->decryptMsg($msg_signature, $timeStamp, $nonce, $rawMsg, $msg);

	if ($errCode == 0) {
		return $msg;
	} else {
		throw Exception('微信消息解密出错', $errCode);
	}
}

/**
 * 从微信消息中取得指定字段值
 * @param string $field  指定的字段名
 * @param string $type  指定字段的数据类型
 * @param string $msg  微信消息
 * @return mixed  指定字段值
 */
function getMsgField($field, $type, $msg)
{
	$xml = simplexml_load_string($msg, 'SimpleXMLElement', LIBXML_NOCDATA);
	$value = $xml->$field;
	settype($value, $type);
	return $value;
}

?>