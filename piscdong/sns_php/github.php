<?php
/**
 * GitHub API client for PHP
 *
 * @author PiscDong (http://www.piscdong.com/)
 */
class githubPHP
{
	public $api_url='https://api.github.com/';

	public function __construct($client_id, $client_secret, $access_token=NULL){
		$this->client_id=$client_id;
		$this->client_secret=$client_secret;
		$this->access_token=$access_token;
	}

	//生成授权网址
	public function login_url($callback_url, $scope=''){
		$params=array(
			'client_id'=>$this->client_id,
			'redirect_uri'=>$callback_url,
			'scope'=>$scope
		);
		return 'https://github.com/login/oauth/authorize?'.http_build_query($params);
	}

	//获取access token
	public function access_token($callback_url, $code){
		$params=array(
			'code'=>$code,
			'client_id'=>$this->client_id,
			'client_secret'=>$this->client_secret,
			'redirect_uri'=>$callback_url
		);
		$url='https://github.com/login/oauth/access_token';
		$result_str=$this->http($url, http_build_query($params), 'POST');
		$json_r=array();
		if($result_str!='')parse_str($result_str, $json_r);
		return $json_r;
	}

	/**
	//使用refresh token获取新的access token，GitHub暂时不支持
	public function access_token_refresh($refresh_token){
	}
	**/

	//获取登录用户信息
	public function me(){
		$params=array();
		return $this->api('user', $params);
	}

	//获取登录用户代码仓库
	public function repos(){
		$params=array();
		return $this->api('user/repos', $params);
	}

	//获取登录用户代码片段
	public function gists(){
		$params=array();
		return $this->api('gists', $params);
	}

	//调用接口
	/**
	//示例：获取登录用户的issues
	$result=$github->api('user/issues', array(), 'GET');
	**/
	public function api($url, $params=array(), $method='GET'){
		$url=$this->api_url.$url;
		$params['access_token']=$this->access_token;
		if($method=='GET'){
			$result_str=$this->http($url.'?'.http_build_query($params));
		}else{
			$result_str=$this->http($url, http_build_query($params), 'POST');
		}
		$result=array();
		if($result_str!='')$result=json_decode($result_str, true);
		return $result;
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
		$headers[]='User-Agent: GitHub.PHP(piscdong.com)';
		curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ci, CURLOPT_URL, $url);
		$response=curl_exec($ci);
		curl_close($ci);
		return $response;
	}
}
