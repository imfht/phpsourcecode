<?php
/**
 * 淘宝 API client for PHP
 *
 * @author PiscDong studio (http://www.piscdong.com/)
 */
class taobaoPHP
{
	public $api_url='http://gw.api.taobao.com/router/rest';

	public function __construct($client_id, $client_secret, $access_token=NULL){
		$this->client_id=$client_id;
		$this->client_secret=$client_secret;
		$this->access_token=$access_token;
	}

	//生成授权网址
	public function login_url($callback_url){
		$params=array(
			'response_type'=>'code',
			'client_id'=>$this->client_id,
			'redirect_uri'=>$callback_url
		);
		return 'https://oauth.taobao.com/authorize?'.http_build_query($params);
	}

	//获取access token
	public function access_token($callback_url, $code){
		$params=array(
			'grant_type'=>'authorization_code',
			'code'=>$code,
			'client_id'=>$this->client_id,
			'client_secret'=>$this->client_secret,
			'redirect_uri'=>$callback_url
		);
		$url='https://oauth.taobao.com/token';
		$result_str=$this->http($url, http_build_query($params), 'POST');
		$json_r=array();
		if($result_str!='')$json_r=json_decode($result_str, true);
		return $json_r;
	}

	//使用refresh token获取新的access token
	public function access_token_refresh($refresh_token){
		$params=array(
			'grant_type'=>'refresh_token',
			'refresh_token'=>$refresh_token,
			'client_id'=>$this->client_id,
			'client_secret'=>$this->client_secret
		);
		$url='https://oauth.taobao.com/token';
		$result_str=$this->http($url, http_build_query($params), 'POST');
		$json_r=array();
		echo $result_str;
		if($result_str!='')$json_r=json_decode($result_str, true);
		return $json_r;
	}

	//获取登录用户信息
	public function me(){
		$params=array(
			'fields'=>'user_id,nick,sex,avatar'
		);
		return $this->api('taobao.user.get', $params);
	}

	//调用接口
	/**
	//示例：获取登录用户信息
	$result=$taobao->api('taobao.user.get', array('fields'=>'user_id,nick,sex,avatar'), 'GET');
	**/
	public function api($url, $params=array(), $method='GET'){
		$params['method']=$url;
		$params['timestamp']=date('Y-m-d H:i:s');
		$params['format']='json';
		$params['app_key']=$this->client_id;
		$params['v']='2.0';
		$params['session']=$this->access_token;
		$params['sign_method']='md5';
		ksort($params);
		$sign_c=$this->client_secret;
		foreach($params as $k=>$v)$sign_c.=$k.$v;
		$sign_c.=$this->client_secret;
		$params['sign']=strtoupper(md5($sign_c));
		var_dump($params);
		if($method=='GET'){
			$result_str=$this->http($this->api_url.'?'.http_build_query($params));
		}else{
			$result_str=$this->http($this->api_url, http_build_query($params), 'POST');
		}
		$json_r=array();
		if($result_str!='')$json_r=json_decode($result_str, true);
		return $json_r;
	}

	//提交请求
	private function http($url, $postfields='', $method='GET', $headers=array()){
		$ci=curl_init();
		curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE); 
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ci, CURLOPT_TIMEOUT, 30);
		if($method=='POST'){
			curl_setopt($ci, CURLOPT_POST, TRUE);
			if($postfields!='')curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
		}
		$headers[]='User-Agent: Taobao.PHP(piscdong.com)';
		curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ci, CURLOPT_URL, $url);
		$response=curl_exec($ci);
		curl_close($ci);
		return $response;
	}
}
