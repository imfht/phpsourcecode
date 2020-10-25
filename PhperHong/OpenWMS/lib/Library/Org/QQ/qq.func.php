<?php
define('QQ_SCOPE',"get_user_info,add_share,list_album,add_album,upload_pic,add_topic,add_one_blog,add_weibo");

function getQqLoginUrl($appid)
{
	$state = $_SESSION['qq_state'] = md5(uniqid(rand(),TRUE));
    $url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id="
        . $appid . "&redirect_uri=".urlencode(WB_CALLBACK_URL)
        . "&state=" .$state
        . "&scope=".QQ_SCOPE;
    return $url;
}

function getQqAccessToken($appid, $appkey)
{
	if($_REQUEST['state'] == $_SESSION['qq_state'])
    {
        $token_url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&"
            . "client_id=" . $appid. "&redirect_uri=" . urlencode(WB_CALLBACK_URL)
            . "&client_secret=" . $appkey. "&code=" . $_REQUEST["code"];

		//打开php.ini找到 ;extension=php_openssl.dll ，去掉双引号”;” ，重启web服务器即可。
		$start = microtime(true);
		$response = get_url_contents($token_url);
		/*
		$response = file_get_contents($token_url);
		*/
        if (strpos($response, "callback") !== false)
        {
            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
            $msg = json_decode($response);
            if (isset($msg->error))
            {
                echo "<h3>error:</h3>" . $msg->error;
                echo "<h3>msg  :</h3>" . $msg->error_description;
                exit;
            }
        }

        $params = array();
        parse_str($response, $params);

		if(empty($params["access_token"])) {
			logstr('empty access_token timeuse ' . sprintf("%0.4f", microtime(true) - $start) . ' response:' . $response . ' url:' . $token_url);
		}

        return $params["access_token"];

    }
    else
    {
        echo("The state does not match. You may be a victim of CSRF.");
    }
}

function getQqOpenid($access_token)
{
    $graph_url = "https://graph.qq.com/oauth2.0/me?access_token=".$access_token;
	$str  = get_url_contents($graph_url);
	/*
    $str  = file_get_contents($graph_url);
	*/
    if (strpos($str, "callback") !== false)
    {
        $lpos = strpos($str, "(");
        $rpos = strrpos($str, ")");
        $str  = substr($str, $lpos + 1, $rpos - $lpos -1);
    }

    $user = json_decode($str);
    if (isset($user->error))
    {
        echo "<h3>error:</h3>" . $user->error;
        echo "<h3>msg  :</h3>" . $user->error_description;
        exit;
    }
    return $user->openid;
}

function getQqUserInfo($appid,$access_token,$openid)
{
    $get_user_info = "https://graph.qq.com/user/get_user_info?"
        . "access_token=" . $access_token
        . "&oauth_consumer_key=" . $appid
        . "&openid=" . $openid
        . "&format=json";
	$info  = get_url_contents($get_user_info);
	/*
    $info = file_get_contents($get_user_info);
	*/
    $arr = json_decode($info, true);

	if(empty($arr) || !is_array($arr)) {
		logstr('qq_login_error getqquserinfo' . PHP_EOL . $get_user_info . PHP_EOL . $info . PHP_EOL . print_r($arr, true));
	}

    return $arr;
}


function get_url_contents($url)
{
//    if (ini_get("allow_url_fopen") == "1")
//        return file_get_contents($url);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $result =  curl_exec($ch);
    curl_close($ch);
    return $result;
}

function logstr($content){
	global $app;
	$app->log->debug($content);
}