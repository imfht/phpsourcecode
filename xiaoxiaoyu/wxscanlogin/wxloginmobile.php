<!DOCTYPE html>
<html>
    <head>
        <title>微信登陆演示</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
           <script src="http://hstatic.cn/uajsapi/uajsapi.js" type="text/javascript"></script>
    </head>
  
    <body>
<?php
include_once 'comm.php';
include_once 'config.php';

$token=  getData($_REQUEST, "token", "");
if(strlen($token)==0 || $wxlogin->isExprise($token)){
    die("无效的参数，请重新扫描");
}else if($wxlogin->isLogined($token)){
    die("不要重复登陆哦");
}
$action=  getData($_REQUEST, "action", "wxlogin");
if($action=="wxlogin"){
    $url=  getCurrentUrl("wxloginmobile.php?token=$token&action=getinfo");
        $url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=".urlencode($url)
           ."&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect";
       // die($url);
        header("location:$url");
        die();
    
    
}else if($action=="getinfo"){
   $code=getData($_GET, "code","");
     if(strlen($code)==0) return ;
     $b=getWxUserInfo($code);
    // var_dump($b);
    $wxlogin->setUserInfo($token,$b);
     echo "登陆成功，请在网页上继续操作";
}

function getWxUserInfo($code){
    global $appid,$appsecret;
    $url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$appsecret&code=$code&grant_type=authorization_code ";
    $res=file_get_contents($url);
    $json=  json_decode($res);
    $accesstoken=$json->access_token;
    $openid=$json->openid;
    $url="https://api.weixin.qq.com/sns/userinfo?access_token=$accesstoken&openid=$openid&lang=zh_CN";
    $res=file_get_contents($url);
    $json=  json_decode($res);
 //   var_dump($json);
    return $json;
}

?>

  </body>
</html>   
        