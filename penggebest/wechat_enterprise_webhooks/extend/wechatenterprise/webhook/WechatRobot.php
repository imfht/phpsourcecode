<?php
namespace wechatenterprise\webhook;
use \think\Log;
use think\Db; 
class WechatRobot 
{
	 
	/**获取企业微信token*/
	public static $ACCESS_TOKEN_URL = "https://qyapi.weixin.qq.com/cgi-bin/gettoken";
	
	/**获取企业微信信息发送tickit*/
	public static $SEND_MSG_URL = "https://qyapi.weixin.qq.com/cgi-bin/message/send";
	 
 
	public static $expireTime = 1200;//时间 
	
//	(Access Token 必须缓存在磁盘，并定时刷新，建议每 20 分钟请求新的 Access Token，原 Access Token 2 小时（7200S） 失效，获取之后立即使用最新的 Access Token。旧的只有一分钟的并存期 。
//)
	
	
	//开始具体步骤 
	//1.Access Token 获取 
	public static function GetAccessToken()
    { 
		if( $rs = cache('wechat_entper_access_token'))
		{
			return cache('wechat_entper_access_token');
		}
		$httpResponse = HttpHelper::curl(self::$ACCESS_TOKEN_URL."?corpid=".config("weichat_entper_api_setting.CorpID")."&corpsecret=".config("weichat_entper_api_setting.SECRET"), "GET", null, null);
		if (!$httpResponse->isSuccess())
		{
				return null;
		}
		Log::write($httpResponse->getBody());
		$respObj = json_decode($httpResponse->getBody());
		$code = $respObj->errcode;
		if ($code != 0) {
			echo "获取失败:".$respObj->msg;
			return null;
		}
		$access_token = $respObj->access_token;
		// 设置缓存数据 
		cache('wechat_entper_access_token', $access_token, self::$expireTime);
		
		return $access_token;
	}    
	public static function SendMsg($content)
    { 
		  
		$inputStr["touser"] = "@all";
		$inputStr["toparty"] = "";
		$inputStr["totag"] = "";
		$inputStr["msgtype"] = "text"; 
		$inputStr["agentid"] = config("weichat_entper_api_setting.AgentID"); 
		$inputStr["text"] = [
			"content" => $content
		];  
		$inputStr["safe"] = "0";
		//var_dump(JSON($inputStr)); 
		Log::write(JSON($inputStr));
		//$header["Content-Type"] = "application/json; charset=utf-8"; 
		$httpResponse = HttpHelper::curl(self::$SEND_MSG_URL."?access_token=".self::GetAccessToken(), "POST", self::JSON($inputStr)); //, $header
		Log::write($httpResponse->getBody()); 
		$respObj = json_decode($httpResponse->getBody());
		$code = intval($respObj->errcode);
		if ($code != 0) { 
			return $respObj;
		}  
		return $respObj;
	} 
	static function getSign($data){
        foreach ($data as $k => $v)
        {
            $Parameters[$k] = $v;
        }
        //签名步骤一：按字典序排序参数
        sort($Parameters);
        $data = self::formatBizQueryParaMap($Parameters, false);
		//echo "连接串：".$data .'\r\n';
		
        $sign = sha1($data); 
        // //签名步骤二：在string后加入KEY
        // $String = $String."&key=".$key;
        // //签名步骤三：MD5加密
        // $String = md5($String);
        // //签名步骤四：所有字符转为大写
        // $result = strtoupper($String);
        return $sign;      
    }
	
	/**
     *  作用：格式化参数，签名过程需要使用
     */
    static function formatBizQueryParaMap($paraMap, $urlencode) {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v)
        {
            if($urlencode)
            {
                $v = urlencode($v);
            }
            
            //$buff .= $k . "=" . $v . "&";
			$buff .= $v;
        }
        $reqPar = $buff ; 
        return $reqPar;
    }
	
	static function formatBizQueryParaMap2($paraMap, $urlencode) {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v)
        {
            if($urlencode)
            {
                $v = urlencode($v);
            }
            
            $buff .= $k . "=" . $v . "&"; 
        }
        $reqPar = "" ; 
		if (strlen($buff) > 0) 
		{
			$reqPar = substr($buff, 0, strlen($buff)-1);
		} 
        return $reqPar;
    }
    function SHA1withRSA($data,$key){
        $certs = array();
        openssl_pkcs12_read(file_get_contents($key), $certs, "1106@123"); //其中1106@123为你的证书密码

        if(!$certs) return ;
        $signature = '';  
        openssl_sign($data, $signature, $certs['pkey']);
        return base64_encode($signature);  
    }
	
	/**************************************************************
     *
     *  将数组转换为JSON字符串（兼容中文）
     *  @param  array   $array      要转换的数组
     *  @return string      转换得到的json字符串
     *  @access public
     *
     *************************************************************/
    static function JSON($array) {
        self::arrayRecursive($array, 'urlencode', true);
        $json = json_encode($array);
        return urldecode($json);
    }
	/**************************************************************
     *
     *  使用特定function对数组中所有元素做处理
     *  @param  string  &$array     要处理的字符串
     *  @param  string  $function   要执行的函数
     *  @return boolean $apply_to_keys_also     是否也应用到key上
     *  @access public
     *
     *************************************************************/
    static function arrayRecursive(&$array, $function, $apply_to_keys_also = false)
    {
        static $recursive_counter = 0;
        if (++$recursive_counter > 1000) {
            die('possible deep recursion attack');
        }
        foreach ($array as $key => $value) {
            if (is_array($value)) {
               self::arrayRecursive($array[$key], $function, $apply_to_keys_also);
            } else {
                $array[$key] = $function($value);
            }

            if ($apply_to_keys_also && is_string($key)) {
                $new_key = $function($key);
                if ($new_key != $key) {
                    $array[$new_key] = $array[$key];
                    unset($array[$key]);
                }
            }
        }
        $recursive_counter--;
    }
	
	static function createNonceStr($length = 32) {

		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

		$str = "";

		for ($i = 0; $i < $length; $i++) {

		  $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);

		}

    return $str;
  }
}	 
	 
  
	
	
	