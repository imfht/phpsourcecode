<?php
/**
 * oscshop2 B2C电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015-2016 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 *
 */
namespace wechat;
use wechat\Wechat;
use wechat\Curl;
use think\Db;
final class OscshopWechat extends Wechat
{
	
	private static $instance;
	
	private $config;
	
	//此类禁止被继承重载
    final public function __construct($options){
		parent::__construct($options);
		$this->config=$options;
	}
	
	//单例模式	
	public static function getInstance($options){    
        if (!(self::$instance instanceof self))  
        {  
            self::$instance = new self($options);  
        }  
        return self::$instance;  
    }
	//禁克隆
	private function __clone(){} 
		
	/**
	 * log overwrite
	 * @see Wechat::log()
	 */
	protected function log($log){
		if ($this->debug) {
			if (function_exists($this->logcallback)) {
				if (is_array($log)) $log = print_r($log,true);
				return call_user_func($this->logcallback,$log);
			}else {
				return true;
			}
		}
		return false;
	}

	/**
	 * 重载设置缓存
	 * @param string $cachename
	 * @param mixed $value
	 * @param int $expired
	 * @return boolean
	 */
	protected function setCache($cachename,$value,$expired){
		return cache($cachename,$value,$expired);
	}

	/**
	 * 重载获取缓存
	 * @param string $cachename
	 * @return mixed
	 */
	protected function getCache($cachename){
		return cache($cachename);
	}

	/**
	 * 重载清除缓存
	 * @param string $cachename
	 * @return boolean
	 */
	protected function removeCache($cachename){
		return cache($cachename,null);
	}
	
	/**
	 * 回调通知签名验证
	 * @param array $orderxml 返回的orderXml的数组表示，留空则自动从post数据获取
	 * @return boolean
	 */
	public function checkPaySign($orderxml=''){
		
		if (!$orderxml) {
			$postStr = file_get_contents("php://input");
			if (!empty($postStr)) {
				$order_array = $this->xmlToArray($postStr);
			} else return false;
		}
		
		$post_sign=$order_array['sign'];
		
		unset($order_array['sign']);
		
		$sign = $this->paySign($order_array);
		
		if ($post_sign == $sign) {
			return true;
		}
	
		return false;
	}
	
	/**
	 *取得微信用户openid	
	 */
	public function getOpenId(){
		$openid=cookie('openid');
		if($openid){
			return $openid;
		}else{		
			 if (in_wechat()) {			 	             
	            $redirect_uri = request()->url(true);					
	            $AccessCode   = $this->getAccessCode($redirect_uri, "snsapi_base");				
	            if ($AccessCode !== FALSE) {	            	
	                // 获取accesstoken和openid
	                $Result      = $this->getAccessToken($AccessCode);
	                $openid      = $Result->openid ;
	                $AccessToken = $Result->access_token;
					cookie('openid',$openid);
	               				
					return $openid;
	            }	           
	        } else {
	            return false;
	        }		
		}
	}
	
	/**
     * 数组转换XML
     * @param type $arr
     * @return string
     */
    public function toXML($arr) {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }
	/**
	 * 	作用：将xml转为array
	 */
	public function xmlToArray($xml)
	{		
        //将XML转为array        
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);		
		return $array_data;
	}
	/**
     * 生成支付签名
     * @param array $pack
     * @return string
     */
    public function paySign($pack) {
        ksort($pack);		
		$buff = "";		
        foreach ($pack as $k => $v) {
            if ($k != "sign" && $v != "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }
        $string = trim($buff, "&");		
		
        $string = $string . "&key=" .config('partnerkey');
        $string = md5($string);
        $result = strtoupper($string);
        return $result;
    }
	
	/**
	 * 获取收货地址JS的签名
	 */
	public function getAddrSign(){		
		 	             
        $redirect_uri = request()->url(true);					
        $AccessCode   = $this->getAccessCode($redirect_uri, "snsapi_base");				
        if ($AccessCode !== FALSE) {	            	
            // 获取accesstoken和openid
            $Result      = $this->getAccessToken($AccessCode);	      
            $user_token = $Result->access_token;	       
        }

		if (!($user_token)) {	
			die('no user access token found!');		
		}
		
		$url = htmlspecialchars_decode($redirect_uri);
		
		$timestamp = time();
        // 随机字符串
        $nonceStr = rand(100000, 999999);		

		$addrsign=$this->getSignature(array(
				'appid'=>$this->config['appid'],
				'url'=>$url,
				'timestamp'=>strval($timestamp),
				'noncestr'=>$nonceStr,
				'accesstoken'=>$user_token
		));		
		
		return  array(
                "appId" => $this->config['appid'],
                "scope" => "jsapi_address",
                "signType" => "sha1",
                "addrSign" => isset($addrsign) ? $addrsign : false,
                "timeStamp" => (string)$timestamp,
                "nonceStr" => (string)$nonceStr
		);            
	}
	
	/**
     * 获取用户授权access token，使用code凭证
     * @param string $code
     * @return array
     */
	private function getAccessToken($code){

        $RequestUrl            = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$this->config['appid']."&secret=".$this->config['appsecret']."&code=" . $code . "&grant_type=authorization_code";
		
	    $Result                = json_decode(Curl::get($RequestUrl), true);
		
		if(isset($Result['errcode'])){
			return 'get access token fail';
		}
		
        $_return               = new \stdClass();
		
        $_return->access_token = $Result['access_token'];
        $_return->openid       = $Result['openid'];
        return $_return;
	}
	
	/**
     * 获取用户授权凭证code
     * @param $redirect_uri
     * @param $scope
     * @return bool
     */
    private function getAccessCode($redirect_uri, $scope) {
    	
		$get=input('param.');
		
        $request_access_token_url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->config['appid']."&redirect_uri=[REDIRECT_URI]&response_type=code&scope=[SCOPE]#wechat_redirect";
        if (empty($get['code'])) {
            // 未授权而且是拒绝
            if (!empty($get['state'])) {
                return FALSE;
            } else {
                // 未授权
                $redirect_uri = urlencode($redirect_uri);
                $RequestUrl   = str_replace("[REDIRECT_URI]", $redirect_uri, $request_access_token_url);
                $RequestUrl   = str_replace("[SCOPE]", $scope, $RequestUrl);
							
                // 获取授权
                header("location:" . $RequestUrl);
                exit(0);
            }
        } else {
            // 授权成功 返回 access_token 票据
            return $get['code'];
        }
    }
	
	 private function getAuthAccessCode($redirect_uri) {
	 	
		header("location:" . $this->getOauthRedirect($redirect_uri));
    }
	
	
	public function wechatAutoReg($openid){
		
		if (empty($openid)) {
            return false;
        }
		//已经授权注册的
		$info=cookie('mobile_user_info');
		
		if($info){
			return true;
		}		
		
		//已经注册
		if($user=Db::name('member')->where('wechat_openid',$openid)->find()){
			
			$user_info['uid']=$user['uid'];
			$user_info['openid']=$user['wechat_openid'];
			$user_info['username']=$user['nickname'];
			$user_info['sex']=$user['sex'];
			$user_info['userpic']=$user['userpic'];
			$user_info['is_agent']=$user['is_agent'];
			
			cookie('mobile_user_info',$user_info);
			
			Db::execute("UPDATE ".config('database.prefix')."member SET loginnum=(loginnum+1),lastdate=".time()." WHERE uid =".$user['uid']);	
			
			return true;
		}
		
		//已经关注的用户
		$user_info=$this->getUserInfo($openid);		
		//未关注的用户
		if($user_info&&$user_info['subscribe']==0){
			
			$url=request()->baseUrl(true);
			
			$this->getAuthAccessCode($url);
			//osc_test('1-code-'.time().'-'.$AccessCode);
			$code=input('param.code');
			
            if (isset($code)) {                	
                // 获取到accesstoken和openid
                $Result = $this->getOauthAccessToken();	
				
				//没有获取到access_token
				if(!$Result['access_token']){
					die('授权失败');
				}
                // 微信用户资料
                $user_info = $this->getOauthUserinfo($Result['access_token'], $Result['openid']);
				
            }else{
            	die('授权失败，请稍候在试');
            }			
		}
		if($user_info){
						
			$uid=Db::name('member')->insert([
				'wechat_openid'=>$user_info['openid'],
				'reg_type'=>'weixin',
				'nickname'=>$user_info['nickname'],
				'sex'=>$user_info['sex'],
				'userpic'=>$user_info['headimgurl'],
				'checked'=>1,
				'groupid'=>config('default_group_id'),
				'regdate'=>time(),
			],
			false,true);
			
			$user['uid']=$uid;
			$user['openid']=$user_info['openid'];
			$user['username']=$user_info['nickname'];
			$user['sex']=$user_info['sex'];
			$user['userpic']=$user_info['headimgurl'];
			
			cookie('mobile_user_info',$user);
			
			return true;
		}else{
			return false;
		}
		
		
	}
	
	
}
?>