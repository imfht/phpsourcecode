<?php

use WechatOauthProxy\WechatOauth;
require __DIR__ . '/WechatOauthProxy/WechatOauth.php';

// 限制来源
$referer =  getReferer();
$domain = $referer != '' ? parse_url($referer)['host'] : '';
$code = isset($_GET['code']) ? $_GET['code'] : '';
$domain || $code || exit('禁止访问！');
$file = './common/domainName.json';
if (file_exists($file) && $domain) {
    $domainNameArr = json_decode(file_get_contents($file), true);
    count($domainNameArr) > 0 && !in_array($domain, $domainNameArr) && exit('禁止访问！代理接口安全域名校验出错！');
}

$proxyScope = isset($_REQUEST['proxy_scope']) ? $_REQUEST['proxy_scope'] : '';
$proxyScope = $proxyScope ? $proxyScope : 'code';    // 代理操作作用域，默认仅获取code 'code':仅获取code 'access_token':获取access_token及openid
$state = isset($_REQUEST['state']) ? $_REQUEST['state'] : '';
$state = $state ? $state : getNonceStr();

// 有code且代理作用域为code，拼接code和state参数，直接跳转回请求源
if (!empty($code) && $proxyScope == 'code') {
    $redirectUri = isset($_COOKIE['redirect_uri']) ? $_COOKIE['redirect_uri'] : '';
    if (!empty($redirectUri)) {
        $mark = strpos($redirectUri, '?') === false ? '?' : '';
        header('Location:' . $redirectUri . $mark . '&code=' . $code . '&state=' . $state);
    } else {
        exit('授权登录失败，请退出重试！');
    }
}

$appId = isset($_REQUEST['app_id']) ? $_REQUEST['app_id'] : '';
$appSecret = isset($_REQUEST['app_secret']) ? $_REQUEST['app_secret'] : '';
$oauthType = isset($_REQUEST['oauth_type']) ? $_REQUEST['oauth_type'] : '';
$oauthType = $oauthType ? $oauthType : 1;   //授权类型，默认公众号授权 1:公众号授权 2:开放平台网页授权
$scope = isset($_REQUEST['scope']) ? $_REQUEST['scope'] : '';
$scope = $scope ? $scope : 'snsapi_userinfo';

$protocol = isHttps() ? 'https' : 'http';
$phpSelf = $_SERVER['PHP_SELF'];
$mark = strpos($phpSelf, '?') === false ? '?' : '';
$queryString = $proxyScope == 'access_token' ? $mark . '&' . http_build_query(array('app_id'=>$appId,'app_secret'=>$appSecret,'proxy_scope'=>$proxyScope)) : '';
$proxyRedirectUri = $protocol . '://' . $_SERVER['HTTP_HOST'] . $phpSelf . $queryString;
$redirectUri = isset($_REQUEST['redirect_uri']) ? $_REQUEST['redirect_uri'] : '';

// code为空，进行重定向获取code
if (empty($code)) {
    setCookie('redirect_uri', $redirectUri, time () + 300);
    $paramsArr = array(
        'appid'=>$appId,
        'redirect_uri'=>$proxyRedirectUri,
        'response_type'=>'code',
        'scope'=>$scope,
        'state'=>$state
    );
    WechatOauth::toGetCode($paramsArr, $oauthType);
    
// 有code且代理作用域为access_token，获取access_token，拼接access_token和openid参数，直接跳转回请求源
} else if ($proxyScope == 'access_token') {
    $cacheDir = __DIR__ . '/Cache/appid_' . $appId;
    session_start();
    $openid = isset($_SESSION['openid_' . $appId]) ? $_SESSION['openid_' . $appId] : '';
    $res = json_decode(file_get_contents($cacheDir . '/access_token_openid_' . $openid . '.json'), true);
    // access_token缓存文件不存在或者access_token已过期或者openid已过期，则重新获取
    if (!$res || $res['expire_time'] <= time() || empty($openid)) {
        $paramsArr = array(
            'appid'=>$appId,
            'secret'=>$appSecret,
            'code'=>$code,
            'grant_type'=>'authorization_code'
        );
        $res = WechatOauth::getAccessToken($paramsArr);

        if (!isset($res['errcode']) || empty($res['errcode'])) {
            // 缓存目录若不存在，创建目录
            is_dir($cacheDir) || mkdir($cacheDir, 0777, true);
            // 新增过期时间时间戳
            $res['expire_time'] = time() + $res['expires_in'];
            // 处理openid
            $openid = $res['openid'];
            $_SESSION['openid_' . $appId] = $res['openid'];
            unset($res['openid']);
            // 缓存access_token等数据
            file_put_contents($cacheDir . '/access_token_openid_' . $openid . '.json', json_encode($res));
        }
    }

    // 校验access_token
    $checkRes = WechatOauth::checkAccessToken(array(
        'access_token'=>$res['access_token'],
        'openid'=>$openid
    ));

    // access_token校验有误，刷新access_token
    if ($checkRes['errcode'] != 0) {
        $res = WechatOauth::refreshAccessToken(array(
            'appid'=>$appId,
            'grant_type'=>'refresh_token',
            'refresh_token'=>$res['refresh_token']
        ));
        if (!isset($res['errcode']) || empty($res['errcode'])) {
            // 缓存目录若不存在，创建目录
            is_dir($cacheDir) || mkdir($cacheDir, 0777, true);
            // 新增过期时间时间戳
            $res['expire_time'] = time() + $res['expires_in'];
            // 处理openid
            $openid = $res['openid'];
            $_SESSION['openid_' . $appId] = $res['openid'];
            unset($res['openid']);
            // 缓存access_token等数据
            file_put_contents($cacheDir . '/access_token_openid_' . $openid . '.json', json_encode($res));
        }
    }

    if (!isset($res['errcode']) || empty($res['errcode'])) {
        $redirectUri = isset($_COOKIE['redirect_uri']) ? $_COOKIE['redirect_uri'] : '';
        if (!empty($redirectUri)) {
            $mark = strpos($redirectUri, '?') === false ? '?' : '';
            header('Location:' . $redirectUri . $mark . '&access_token=' . $res['access_token'] . '&openid=' . $openid);
        } else {
            exit('授权登录失败，请退出重试！');
        }
    }
}


/**
 * 通用函数
 */
function isHttps()
{
    if (!isset($_SERVER['HTTPS']))  return false;
    if ($_SERVER['HTTPS'] === 1) {  //Apache
        return true;
    } else if ($_SERVER['HTTPS'] === 'on') { //IIS
        return true;
    } else if ($_SERVER['SERVER_PORT'] == 443) { //其他
        return true;
    }
    return false;
}

function getNonceStr($length = 32)
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $str = '';
    for ( $i = 0; $i < $length; $i++ ) {
        $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
    }
    return $str;
}

function getReferer()
{
    return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
}