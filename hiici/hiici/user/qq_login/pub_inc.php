<?php
@session_start();

global $config;
$_SESSION["appid"]    = $config['qq_appid']; 
$_SESSION["appkey"]   = $config['qq_appkey']; 
$_SESSION["callback"] = $config['qq_callback']; //get_access_token.php"; 
//$_SESSION["scope"] = "get_user_info,add_share,list_album,add_album,upload_pic,add_topic,add_one_blog,add_weibo";

function qq_callback()
{
    if($_REQUEST['state'] == $_SESSION['state']) //csrf
    {
        $token_url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&"
            . "client_id=" . $_SESSION["appid"]. "&redirect_uri=" . urlencode($_SESSION["callback"])
            . "&client_secret=" . $_SESSION["appkey"]. "&code=" . $_REQUEST["code"];

        $response = file_get_contents($token_url);
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

        $_SESSION["access_token"] = $params["access_token"];

    }
    else 
    {
        echo("The state does not match. You may be a victim of CSRF.");
    }
}
function get_openid()
{
    $graph_url = "https://graph.qq.com/oauth2.0/me?access_token=" 
        . $_SESSION['access_token'];

    $str  = file_get_contents($graph_url);
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

    $_SESSION["openid"] = $user->openid;
}
function get_user_info()
{
    $get_user_info = "https://graph.qq.com/user/get_user_info?"
        . "access_token=" . $_SESSION['access_token']
        . "&oauth_consumer_key=" . $_SESSION["appid"]
        . "&openid=" . $_SESSION["openid"]
        . "&format=json";

    $info = file_get_contents($get_user_info);
    $arr = json_decode($info, true);

    return $arr;
}
