<?php
class AopClient {
	//应用ID
	public $appId;
    //私钥文件路径
	public $rsaPrivateKeyFilePath;
    //网关
	public $gatewayUrl = "https://openapi.alipay.com/gateway.do";
    //返回数据格式
	public $format = "json";
    //api版本
	public $apiVersion = "1.0";
    //签名类型
	protected $signType = "RSA";
	protected $alipaySdkVersion = "alipay-sdk-php-20130320";

	public function generateSign($params) {
		return $this->sign($this->getSignContent($params));
	}

	public function rsaSign($params) {
		return $this->sign($this->getSignContent($params));
	}

	protected function getSignContent($params){
		ksort($params);

		$stringToBeSigned = "";
		$i = 0;
		foreach ($params as $k => $v) {
			if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {
				if ($i == 0) {
					$stringToBeSigned .= "$k" . "=" . "$v";
				} else {
					$stringToBeSigned .= "&" . "$k" . "=" . "$v";
				}
				$i++;
			}
		}
		unset ($k, $v);
		return $stringToBeSigned;
	}

	protected function sign($data) {
		$priKey = file_get_contents($this->rsaPrivateKeyFilePath);
		$res = openssl_get_privatekey($priKey);
		openssl_sign($data, $sign, $res);
		openssl_free_key($res);
		$sign = base64_encode($sign);
		return $sign;
	}

	protected function curl($url, $postFields = null) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FAILONERROR, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);

		$postBodyString = "";
		if (is_array($postFields) && 0 < count($postFields)) {
			
			$postMultipart = false;
			foreach ($postFields as $k => $v) {
				if ("@" != substr($v, 0, 1)) //判断是不是文件上传
					{
					$postBodyString .= "$k=" . urlencode($v) . "&";
				} else //文件上传用multipart/form-data，否则用www-form-urlencoded
					{
					$postMultipart = true;
				}
			}
			unset ($k, $v);
			curl_setopt($ch, CURLOPT_POST, true);
			if ($postMultipart) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
			} else {
				curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString, 0, -1));
			}
		}
		$headers = array('content-type: application/x-www-form-urlencoded;charset=UTF-8');	
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$reponse = curl_exec($ch);

		if (curl_errno($ch)) {
			throw new Exception(curl_error($ch), 0);
		} else {
			$httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if (200 !== $httpStatusCode) {
				throw new Exception($reponse, $httpStatusCode);
			}
		}
		curl_close($ch);
		return $reponse;
	}

	protected function logCommunicationError($apiName, $requestUrl, $errorCode, $responseTxt) {
        dump($apiName);
        dump($requestUrl);
        dump($errorCode);
        dump($responseTxt);
        return;
		$localIp = isset ($_SERVER["SERVER_ADDR"]) ? $_SERVER["SERVER_ADDR"] : "CLI";
		$logger = new LtLogger;
		$logger->conf["log_file"] = rtrim(AOP_SDK_WORK_DIR, '\\/') . '/' . "logs/aop_comm_err_" . $this->appId . "_" . date("Y-m-d") . ".log";
		$logger->conf["separator"] = "^_^";
		$logData = array (
			date("Y-m-d H:i:s"),
			$apiName,
			$this->appId,
			$localIp,
			PHP_OS,
			$this->alipaySdkVersion,
			$requestUrl,
			$errorCode,
			str_replace("\n", "", $responseTxt)
		);
		$logger->log($logData);
	}

	public function execute($request, $authToken = null) {
		//组装系统参数
		$sysParams["app_id"] = $this->appId;
		$sysParams["version"] = $this->apiVersion;
		$sysParams["format"] = $this->format;
		$sysParams["sign_type"] = $this->signType;
		$sysParams["method"] = $request->getApiMethodName();
		$sysParams["timestamp"] = date("Y-m-d H:i:s");
		$sysParams["auth_token"] = $authToken;
		$sysParams["alipay_sdk"] = $this->alipaySdkVersion;
		$sysParams["terminal_type"] = $request->getTerminalType();
		$sysParams["terminal_info"] = $request->getTerminalInfo();
		$sysParams["prod_code"] = $request->getProdCode();

		//获取业务参数
		$apiParams = $request->getApiParas();

		//签名
		$sysParams["sign"] = $this->generateSign(array_merge($apiParams, $sysParams));

		//系统参数放入GET请求串
		$requestUrl = $this->gatewayUrl . "?";
		foreach ($sysParams as $sysParamKey => $sysParamValue) {
			$requestUrl .= "$sysParamKey=" . urlencode($sysParamValue) . "&";
		}
		$requestUrl = substr($requestUrl, 0, -1);

		//发起HTTP请求
		try {
			$resp = $this->curl($requestUrl, $apiParams);
		} catch (Exception $e) {
			$this->logCommunicationError($sysParams["method"], $requestUrl, "HTTP_ERROR_" . $e->getCode(), $e->getMessage());
			return false;
		}

		//解析AOP返回结果
		$respWellFormed = false;

		if ("json" == $this->format) {
			$respObject = json_decode($resp);
			if (null !== $respObject) {
				$respWellFormed = true;								
			}
		} else
			if ("xml" == $this->format) {
				$respObject = @ simplexml_load_string($resp);
				if (false !== $respObject) {
					$respWellFormed = true;
				}
			}

		//返回的HTTP文本不是标准JSON或者XML，记下错误日志
		if (false === $respWellFormed) {
			$this->logCommunicationError($sysParams["method"], $requestUrl, "HTTP_RESPONSE_NOT_WELL_FORMED", $resp);
			return false;
		}

		//如果AOP返回了错误码，记录到业务错误日志中
		if (isset ($respObject->code)) {
			$logger = new LtLogger;
			$logger->conf["log_file"] = rtrim(AOP_SDK_WORK_DIR, '\\/') . '/' . "logs/aop_biz_err_" . $this->appId . "_" . date("Y-m-d") . ".log";
			$logger->log(array (
				date("Y-m-d H:i:s"),
				$resp
			));
		}
		return $respObject;
	}

	public function exec($paramsArray) {
		if (!isset ($paramsArray["method"])) {
			trigger_error("No api name passed");
		}
		$inflector = new LtInflector;
		$inflector->conf["separator"] = ".";
		$requestClassName = ucfirst($inflector->camelize(substr($paramsArray["method"], 7))) . "Request";
		if (!class_exists($requestClassName)) {
			trigger_error("No such api: " . $paramsArray["method"]);
		}

		$session = isset ($paramsArray["session"]) ? $paramsArray["session"] : null;

		$req = new $requestClassName;
		foreach ($paramsArray as $paraKey => $paraValue) {
			$inflector->conf["separator"] = "_";
			$setterMethodName = $inflector->camelize($paraKey);
			$inflector->conf["separator"] = ".";
			$setterMethodName = "set" . $inflector->camelize($setterMethodName);
			if (method_exists($req, $setterMethodName)) {
				$req-> $setterMethodName ($paraValue);
			}
		}
		return $this->execute($req, $session);
	}
	
	/**
	 * 校验$value是否非空
	 *  if not set ,return true;
	 *	if is null , return true;
	 **/
	protected function checkEmpty($value) {
		if(!isset($value))
			return true ;
		if($value === null )
			return true;
		if(trim($value) === "")
			return true;
		
		return false;
	}

	public function rsaCheckV1($params,$rsaPublicKeyFilePath){
		$sign = $params['sign'];
		$params['sign_type'] = null;
		$params['sign'] = null;
		
		return $this->verify($this->getSignContent($params),$sign,$rsaPublicKeyFilePath);
	}
	
	public function rsaCheckV2($params,$rsaPublicKeyFilePath){
		$sign = $params['sign'];
		$params['sign'] = null;
		
		return $this->verify($this->getSignContent($params),$sign,$rsaPublicKeyFilePath);
	}
	
	function verify($data, $sign, $rsaPublicKeyFilePath) {
		//读取公钥文件
		$pubKey = file_get_contents($rsaPublicKeyFilePath);

		//转换为openssl格式密钥
		$res = openssl_get_publickey($pubKey);

		//调用openssl内置方法验签，返回bool值
		$result = (bool) openssl_verify($data, base64_decode($sign), $res);

		//释放资源  
		openssl_free_key($res);

		return $result;
	}

	public function checkSignAndDecrypt($params,$rsaPublicKeyPem,$rsaPrivateKeyPem,$isCheckSign,$isDecrypt){
		$charset=$params['charset'];
		$bizContent=$params['biz_content'];
		if($isCheckSign){
			if(!$this->rsaCheckV2($params,$rsaPublicKeyPem)){
				echo "<br/>checkSign failure<br/>";
				exit;
			}
		}
		if($isDecrypt){
			return $this->rsaDecrypt($bizContent,$rsaPrivateKeyPem,$charset);
		}

		return $bizContent;
	}

	public function encryptAndSign($bizContent,$rsaPublicKeyPem,$rsaPrivateKeyPem,$charset,$isEncrypt,$isSign){
		// 加密，并签名
		if($isEncrypt&&$isSign){
			$encrypted=$this->rsaEncrypt($bizContent,$rsaPublicKeyPem,$charset);
			$sign=$this->sign($bizContent);
			$response = "<?xml version=\"1.0\" encoding=\"$charset\"?><alipay><response>$encrypted</response><encryption_type>RSA</encryption_type><sign>$sign</sign><sign_type>RSA</sign_type></alipay>";
			return $response;
		}
		// 加密，不签名
		if($isEncrypt&&(!$isSign)){
			$encrypted=$this->rsaEncrypt($bizContent,$rsaPublicKeyPem,$charset);
			$response = "<?xml version=\"1.0\" encoding=\"$charset\"?><alipay><response>$encrypted</response><encryption_type>RSA</encryption_type></alipay>";
			return $response;
		}
		// 不加密，但签名
		if((!$isEncrypt)&&$isSign){
			$sign=$this->sign($bizContent);
			$response = "<?xml version=\"1.0\" encoding=\"$charset\"?><alipay><response>$bizContent</response><sign>$sign</sign><sign_type>RSA</sign_type></alipay>";
			return $response;
		}
		// 不加密，不签名
		$response = "<?xml version=\"1.0\" encoding=\"$charset\"?>$bizContent";
		return $response;
	}

	public function rsaEncrypt($data, $rsaPublicKeyPem, $charset) {
		//读取公钥文件
		$pubKey = file_get_contents($rsaPublicKeyPem);
		//转换为openssl格式密钥
		$res = openssl_get_publickey($pubKey);
		$blocks = $this->splitCN($data, 0, 30, $charset);
		$chrtext  = null;
		$encodes  = array ();
		foreach ($blocks as $n => $block) {
			if (!openssl_public_encrypt($block, $chrtext , $res)) {
				echo "<br/>" . openssl_error_string() . "<br/>";
			}
			$encodes[] = $chrtext ;
		}
		$chrtext = implode(",", $encodes);
		
		return $chrtext;
	}

	public function rsaDecrypt($data, $rsaPrivateKeyPem, $charset) {
		//读取私钥文件
		$priKey = file_get_contents($rsaPrivateKeyPem);
		//转换为openssl格式密钥
		$res = openssl_get_privatekey($priKey);
		$decodes = explode(',', $data);
		$strnull = "";
		$dcyCont = "";
		foreach ($decodes as $n => $decode) {
			if (!openssl_private_decrypt($decode, $dcyCont, $res)) {
				echo "<br/>" . openssl_error_string() . "<br/>";
			}
			$strnull.=$dcyCont;
		}
		return $strnull;
	}

	function splitCN($cont, $n = 0, $subnum, $charset) {
		//$len = strlen($cont) / 3;
		$arrr = array ();
		for ($i = $n; $i < strlen($cont); $i += $subnum) {
			$res = $this->subCNchar($cont, $i, $subnum, $charset);
			if (!empty ($res)) {
				$arrr[] = $res;
			}
		}

		return $arrr;
	}

	function subCNchar($str, $start = 0, $length, $charset = "gbk") {
		if (strlen($str) <= $length) {
			return $str;
		}
		$re['utf-8']="/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re['gb2312']="/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re['gbk']="/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re['big5']="/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all($re[$charset], $str, $match);
		$slice = join("", array_slice($match[0], $start, $length));
		return $slice;
	}

}