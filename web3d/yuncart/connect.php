<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

define("IN_CART", true);
define("SITEPATH", dirname(__FILE__));
require_once SITEPATH . "/init.php";


//网站
$w = isset($_GET['w']) ? trim($_GET['w']) : "";
!in_array($w, array('qq', 'sina', 'taobao', 'douban', 'renren')) && halt(__("cannt_third_login", $w));


//配置
$wset = DB::getDB()->selectrow("tlogin", "*", "host='$w' AND ispublish=1");
!$wset && halt(__("cannt_third_login", $w));

$callback = getConfig("weburl") . "connect.php?w={$w}";
$appkey = $wset['appkey'];
$appsecret = $wset['appsecret'];
require_once THIRDPATH . "/OpenSDK/$w/open.php";



if ($w == "qq") {//qq登录
    OpenSDK_QQ::init($appkey, $appsecret);
    if (OpenSDK_QQ::getParam(OpenSDK_QQ::ACCESS_TOKEN)) {
        $uinfo = OpenSDK_QQ::call('user/get_user_info');
        connectuser("qq", OpenSDK_QQ::getParam(OpenSDK_QQ::OPENID), $uinfo['nickname']);
    } else if (isset($_GET['code'])) {
        $code = trim($_GET['code']);
        $token = OpenSDK_QQ::getAccessToken('code', array('code' => $code, 'redirect_uri' => $callback));
        $uinfo = OpenSDK_QQ::call('user/get_user_info');
        connectuser("qq", OpenSDK_QQ::getParam(OpenSDK_QQ::OPENID), $uinfo['nickname']);
    } else {
        $url = OpenSDK_QQ::getAuthorizeURL($callback, 'code', 'state', 'default', 'get_user_info'); //授权地址
        redirect($url);
    }
} else if ($w == "taobao") {//淘宝登陆
    OpenSDK_Taobao::init($appkey, $appsecret);
    if (OpenSDK_Taobao::getParam(OpenSDK_Taobao::ACCESS_TOKEN)) {
        connectuser("tb", OpenSDK_Taobao::getParam(OpenSDK_Taobao::TAOBAO_USER_ID), OpenSDK_Taobao::getParam(OpenSDK_Taobao::TAOBAO_USER_NICK));
    } else if (isset($_GET['code'])) {
        $code = trim($_GET['code']);
        $token = OpenSDK_Taobao::getAccessToken('code', array('code' => $code, 'redirect_uri' => $callback));
        connectuser("tb", OpenSDK_Taobao::getParam(OpenSDK_Taobao::TAOBAO_USER_ID), OpenSDK_Taobao::getParam(OpenSDK_Taobao::TAOBAO_USER_NICK));
    } else {
        $url = OpenSDK_Taobao::getAuthorizeURL($callback); //授权地址
        redirect($url);
    }
} else if ($w == "douban") { //豆瓣登陆
    OpenSDK_Douban::init($appkey, $appsecret);
    if (OpenSDK_Douban::getParam(OpenSDK_Douban::ACCESS_TOKEN) && OpenSDK_Douban::getParam(OpenSDK_Douban::OAUTH_TOKEN_SECRET)) {
        $uinfo = OpenSDK_Douban::call('people/' . OpenSDK_Douban::getParam(OpenSDK_Douban::OAUTH_UID));
        connectuser("db", OpenSDK_Douban::getParam(OpenSDK_Douban::OAUTH_UID), current($uinfo['title']));
    } else if (isset($_GET['oauth_token'])) {
        if (OpenSDK_Douban::getAccessToken()) {
            $uinfo = OpenSDK_Douban::call('people/' . OpenSDK_Douban::getParam(OpenSDK_Douban::OAUTH_UID));
            connectuser("db", OpenSDK_Douban::getParam(OpenSDK_Douban::OAUTH_UID), current($uinfo['title']));
        }
    } else {
        $request_token = OpenSDK_Douban::getRequestToken();
        $url = OpenSDK_Douban::getAuthorizeURL($request_token, $callback);
        redirect($url);
    }
} else if ($w == "renren") { //人人登陆
    OpenSDK_RenRen::init($appkey, $appsecret);
    if (OpenSDK_RenRen::getParam(OpenSDK_RenRen::ACCESS_TOKEN)) {
        $uinfo = current(OpenSDK_RenRen::call('users.getInfo'));
        connectuser('rr', $uinfo['uid'], $uinfo['name']);
    } else if (isset($_GET['code'])) {
        $code = trim($_GET['code']);
        $token = OpenSDK_RenRen::getAccessToken('code', array('code' => $code, 'redirect_uri' => $callback));
        $uinfo = current(OpenSDK_RenRen::call('users.getInfo'));
        connectuser('rr', $uinfo['uid'], $uinfo['name']);
    } else {
        $url = OpenSDK_RenRen::getAuthorizeURL($callback, 'code', 'state');
        redirect($url);
    }
} else if ($w == "sina") { //新浪微博登录
    OpenSDK_Sina::init($appkey, $appsecret);
    if (OpenSDK_Sina::getParam(OpenSDK_Sina::ACCESS_TOKEN)) {
        $uinfo = OpenSDK_Sina::call('users/show', array('uid' => OpenSDK_Sina::getParam(OpenSDK_Sina::OAUTH_USER_ID)));
        connectuser('sn', $uinfo['id'], $uinfo['screen_name']);
    } else if (isset($_GET['code'])) {
        $code = trim($_GET['code']);
        if (OpenSDK_Sina::getAccessToken('code', array('code' => $code, 'redirect_uri' => $callback))) {
            $uinfo = OpenSDK_Sina::call('users/show', array('uid' => OpenSDK_Sina::getParam(OpenSDK_Sina::OAUTH_USER_ID)));
            connectuser('sn', $uinfo['id'], $uinfo['screen_name']);
        }
    } else {
        $url = OpenSDK_Sina::getAuthorizeURL($callback, 'code', 'state');
        redirect($url);
    }
}
exit();

//处理用户
function connectuser($w, $identity = '', $nickname = '')
{
    //用户已经存在
    $user = DB::getDB()->selectrow("user", "uid,uname", "source_identity='$identity' AND source='$w'");
    if ($user) { //登录，重定向首页
        $_SESSION['uname'] = $user['uname'];
        $_SESSION['uid'] = $user['uid'];
        DB::getDB()->update("user", "lasttime=" . time(), "uid=" . $user["uid"]);
    } else { //用户不存在
        $w = strtoupper($w);
        //参数
        $data['uname'] = $w . "_" . $nickname;
        $data += encpass(md5(getRandString(8)));
        $data["regip"] = getClientIp();
        $data["lasttime"] = $data["regtime"] = time();
        $data['source'] = $w;
        $data['source_identity'] = $identity;

        //入库
        $uid = DB::getDB()->insert("user", $data);
        $_SESSION['uname'] = $data['uname'];
        $_SESSION['uid'] = $uid;
    }
    redirect('index.php');
}
