<?php
/*
*	Package:		PHPCrazy.QQConnect
*	Link:			http://git.oschina.net/Crazy-code/PHPCrazy.QQConnect
*	Author: 		Crazy <mailzhangyun@qq.com>
*	Copyright:		2014-2015 Crazy
*	License:		Please read the LICENSE file.
*/

class QQConnect
{

	var $conifg = array();

	// 初始化一些变量
	function __construct() {

		if (!isset($_SESSION)) {
			
			session_start();
		}



		$this->config = array(
			'appid' => $GLOBALS['C']['qqc_appid'],
			'appkey' => $GLOBALS['C']['qqc_appkey'],
			'scope' => $GLOBALS['C']['qqc_scope']
		);

		$this->redirect_uri = HomeUrl('index.php/QQConnect:login/');

	}

	/*
	*	返回 josn
	*/
	function Response($url) {

		//$response = file_get_contents($url);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;

	}

	/**
	*	生成登录的Url
	*/
	function Login() {
		
		//$redirect_uri = HomeUrl('index.php/QQConnect:main/');
		
		$_SESSION['state'] = md5(uniqid(rand(), true));
		
		$Url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id="
		 . $this->config['appid'] . "&redirect_uri=" . urlencode($this->redirect_uri)
		 . "&state=" . $_SESSION['state']
		 . "&scope=" . $this->config['scope'];
		
		return $Url;
	} 

	/*
	* 	获取access_token并保持到Session中
	*/
	function AccessToken($code) {

		$token_url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&"
		 . "client_id=" . $this->config['appid'] . "&redirect_uri=" . urlencode($this->redirect_uri)
		 . "&client_secret=" . $this->config['appkey'] . "&code=" . $code;

		$response = $this->Response($token_url);

		if (strpos($response, "callback") !== false) {

			$lpos = strpos($response, "(");
			$rpos = strrpos($response, ")");

			$response = substr($response, $lpos + 1, $rpos - $lpos -1);

			$msg = json_decode($response);
			
			if (isset($msg -> error)) {

				throw new Exception($msg -> error.'  '.$msg -> error_description);
			} 
		} 

		$params = array();

		parse_str($response, $params);

		$_SESSION["access_token"] = $params["access_token"];
	} 

	/*
	*	获取open_id并保存到Session中
	*/
	function Openid() {

		$graph_url = "https://graph.qq.com/oauth2.0/me?access_token=" . $_SESSION['access_token'];

        $str = $this->Response($graph_url);

		if (strpos($str, "callback") !== false) {

			$lpos = strpos($str, "(");
			$rpos = strrpos($str, ")");
			$str = substr($str, $lpos + 1, $rpos - $lpos -1);
		} 

		$user = json_decode($str);
		
		if (isset($user -> error)) {

			throw new Exception($user -> error.'  '.$user -> error_description);
		}

		$_SESSION["openid"] = $user -> openid;
	} 
	
	/*
	*	返回用户资料
	*/
	function UserInfo($access_token, $open_id) {

		$url = 'https://graph.qq.com/user/get_user_info?oauth_consumer_key='. $this->config['appid']
			. '&access_token=' . $access_token
			. '&openid='. $open_id . '&format=json';

		$str = $this->Response($url);

		$user_info = json_decode($str);
		
		if ( $user_info->ret !== 0 ) {

			throw new Exception($user_info -> ret.'  '.$user_info -> msg);
		}
		
		return $user_info;
	}
}

?>