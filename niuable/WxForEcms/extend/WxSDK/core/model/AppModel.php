<?php
/**
 * @author WangWei
 *
 */
namespace WxSDK\core\model;

class AppModel{
    public $id;
	public $appId;
	public $appSecret;
	public $accessToken;
	public $tokenExpire;
    public $token;
    public $encodingAesKey;
    public function getAppId() {
		return $this->appId;
	}
	public function getAppSecret() {
		return $this->appSecret;
	}
	public function getToken(){
	    return $this->accessToken;
	}
	
	public function getTokenExpire(){
		return $this->tokenExpire;
	}
	public function setToken(string $token){
	    $this->accessToken = $token;
	}
	
	public function setTokenExpire(string $tokenExpire){
	    $this->tokenExpire = $tokenExpire;
	}
	function __construct($appId, $appSecret, $accessToken, $tokenExpire, $token, $encodingAesKey, $id){
	    $this->id = $id;
		$this->appId=$appId;
		$this->appSecret=$appSecret;
		$this->accessToken = $accessToken;
		$this->tokenExpire = $tokenExpire;
		$this->token = $token;
		$this->encodingAesKey = $encodingAesKey;
	}
	
	function isExpire() {
	    if(!isset($this->accessToken)){
	        return true;
	    }
	    return time()>$this->tokenExpire;
	}
}