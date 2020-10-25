<?php
/**
* @package phpBB-WAP MODS
* @license http://opensource.org/licenses/gpl-license.php
**/

/**
* 这是一款自由软件, 您可以在 Free Software Foundation 发布的
* GNU General Public License 的条款下重新发布或修改; 您可以
* 选择目前 version 2 这个版本（亦可以选择任何更新的版本，由
* 你喜欢）作为新的牌照.
**/

class QQConnect {

	var $conifg = array();

	// 初始化一些变量
	function QQConnect()
	{
		if (is_file(dirname(__FILE__) . '/conifg.php'))
		{
			$this -> conifg = require dirname(__FILE__) . '/conifg.php';
		}
		else
		{
			$this -> conifg = array();
			$this -> conifg['appid'] = '';
			$this -> conifg['appkey'] = '';
		}
		$this -> conifg['scope'] 	= 'get_user_info';//API，根据你的需要
	}

	// 登录地址
	function qq_loginurl()
	{
	
		global $board_config;
		
		$callback = $this -> conifg['callback'];
		$callback .= $board_config['script_path'] . 'loading.php?mod=qqlogin';
		$_SESSION['state'] = md5(uniqid(rand(), true));
		$login_url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id="
		 . $this -> conifg['appid'] . "&redirect_uri=" . urlencode($callback)
		 . "&state=" . $_SESSION['state']
		 . "&scope=" . $this -> conifg['scope'];
		return $login_url;
	} 

	// 获取access_token
	function qq_callback($code)
	{
		$callback = $this -> conifg['callback'];
		$token_url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&"
		 . "client_id=" . $this -> conifg['appid'] . "&redirect_uri=" . urlencode($callback)
		 . "&client_secret=" . $this -> conifg['appkey'] . "&code=" . $code;

		// 如果您的主机不支持curl，那么把这里开启
		// 并且把下面的 curl 注释掉
		// 注意：file_get_contents() 函数需要 php_openssl 的支持
		// 不然是不能进行 https 请求的

		// $response = file_get_contents($token_url);

		// curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $token_url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);
        // curl

		if (strpos($response, "callback") !== false) {
			$lpos = strpos($response, "(");
			$rpos = strrpos($response, ")");
			$response = substr($response, $lpos + 1, $rpos - $lpos -1);
			$msg = json_decode($response);
			if (isset($msg -> error)) {
				$error = '<p>error:' . $msg -> error . '</p>';
				$error .= '<p>msg  :' . $msg -> error_description . '</p>';
				trigger_error($error, E_USER_ERROR);
			} 
		} 

		$params = array();

		parse_str($response, $params);

		$_SESSION["access_token"] = $params["access_token"];
	} 

	// 获取 Open id
	function get_openid()
	{
		$graph_url = "https://graph.qq.com/oauth2.0/me?access_token=" . $_SESSION['access_token'];
		
		// 如果您的主机不支持curl，那么把这里开启
		// 并且把下面的 curl 注释掉
		// 注意：file_get_contents() 函数需要 php_openssl 的支持
		// 不然是不能进行 https 请求的
		 
		// $str = file_get_contents($graph_url);

		// curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $graph_url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        $str = curl_exec($ch);
        curl_close($ch);
        // curl

		if (strpos($str, "callback") !== false) {
			$lpos = strpos($str, "(");
			$rpos = strrpos($str, ")");
			$str = substr($str, $lpos + 1, $rpos - $lpos -1);
		} 

		$user = json_decode($str);
		if (isset($user -> error)) {
				$error = '<p>error:' . $msg -> error . '</p>';
				$error .= '<p>msg  :' . $msg -> error_description . '</p>';
				trigger_error($error, E_USER_ERROR);
		} 
		$_SESSION["openid"] = $user -> openid;
	} 
	
	function get_user_info($access_token, $open_id)
	{
		$user_info_url = 'https://graph.qq.com/user/get_user_info?';
		
		$url = $user_info_url
			. 'oauth_consumer_key='. $this -> conifg['appid']
			. '&access_token=' . $access_token
			. '&openid='. $open_id . '&format=json';
		
		// 如果您的主机不支持curl，那么把这里开启
		// 并且把下面的 curl 注释掉
		// 注意：file_get_contents() 函数需要 php_openssl 的支持
		// 不然是不能进行 https 请求的
		 
		// $str = file_get_contents($url);

		// curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        $str = curl_exec($ch);
        curl_close($ch);
        // curl

		$user_info = json_decode($str);
		
		if ( $user_info->ret !== 0 )
		{
			$error = '<p>错误代码:' . $user_info -> ret . '</p>';
			$error .= '<p>提示:' . $user_info -> msg . '</p>';
		}
		
		return $user_info;
	}
}

$QQC = new QQConnect();
?>