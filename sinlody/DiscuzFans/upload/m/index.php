<?php

define('IN_MOBILE', 4);
define('IN_NEWMOBILE', true);

global $_G;

chdir('../');
require './source/class/class_core.php';

$discuz = C::app();

$discuz->reject_robot();
$discuz->init_cron = false;
$discuz->init_session = false;
$discuz->init_user = false;
$discuz->init_mobile = false;

$discuz->init();

if ($_GET['c'] == 'static') {
	$path = DISCUZ_ROOT . $_G['style']['tpldir'] . '/m/js/';
	$bpath = DISCUZ_ROOT . 'template/default/m/js/';
	header("Content-type: application/json");
	$files = explode(',', $_GET['f']);
	$version = $_GET['v'];
	$cachename = 'static_' . md5($_GET['f']) . $version;
	if (!$files) {
		$array = array('code' => 1);
		echo json_encode($array);
		exit;
	}
	$contents = array();
	foreach ($files as $file) {
		if (strpos($file, '..') !== false) {
			continue;
		}
		$filename = file_exists($path . $file) ? $path . $file : $bpath . $file;
		$contents[$file] = file_get_contents($filename);
	}
	$array = array('code' => 0, 'file' => $contents);
	$result = json_encode($array);
	echo $result;
	exit;
}

$jsglobal = array(
    'source' => '',
    'cookiepre' => $discuz->config['cookie']['cookiepre'],
    'jsversion' => !$discuz->config['debug'] ? $_G['style']['verhash'] : time(),
    'f' => '',
    'path' => '',
    'formhash' => FORMHASH,
    'plugins' => array()
);

$site = array(
    'siteId' => 0,
    'siteUrl' => $_G['siteurl'],
    'siteName' => diconv($_G['setting']['sitename'], CHARSET, 'UTF-8'),
    'siteLogo' => $_G['siteurl'] . '/static/image/common/logom.png',
    'openApi' => array(),
);

if($_G['setting']['ec_wxpay_appid']){
    require_once libfile('function/openapi');
    if($_GET['wxlogin'] == 'yes'){
        dsetcookie('wxlogin_referer',urldecode($_GET['referer']));
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$_G['setting']['ec_wxpay_appid'].'&redirect_uri='.urlencode($_G['siteurl'].'m/?wxlogin=success').'&response_type=code&scope=snsapi_bas
e&state=wxlogin#wechat_redirect';
        dheader('location: ' . $url);
    }elseif($_GET['wxlogin'] == 'success'){
        $code = $_GET['code'];
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$_G['setting']['ec_wxpay_appid'].'&secret='.$_G['setting']['ec_wxpay_appsecret'].'&code='.$code.'&grant_type=authorization_code';
        $result = dfsockopen($url);
        if($result && ($result = json_decode($result, true)) && $result['openid']){
            if($openuser = C::t('common_member_openid')->fetch_first_by_openid_type($result['openid'], 'wx')){
                $user = getuserbyuid($openuser['uid']);
                require_once libfile('function/member');
                setloginstatus($user, 2592000);
                $url = $_G['siteurl'].'m/'.getcookie('wxlogin_referer');
            }else{
                $url = $_G['siteurl'].'m/?a=login&loginErr=2001&referer='.urlencode(getcookie('wxlogin_referer'));
                dsetcookie('wxlogin_openid', $result['openid']);
            }
            dheader('location: ' . $url);
        }else{
            $url = $_G['siteurl'].'m/?a=login&referer='.urlencode(getcookie('wxlogin_referer'));
            dheader('location: ' . $url);
        }
    }

    $timestamp = TIMESTAMP;
    $url = 'http'.($_SERVER['SERVER_PORT'] == 443 ? 's' : '').'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
    $noncestr = wx_get_js_noncestr();
    $site['openApi']['wx'] = array(
        'appid' => $_G['setting']['ec_wxpay_appid'],
        'mchid' => $_G['setting']['ec_wxpay_mch_id'],
        'js_noncestr' => $noncestr,
        'js_timestamp' => $timestamp,
        'js_signature' => wx_get_js_signature($noncestr, $timestamp, $url),
    );
}


if (!$_G['setting']['mobile']['allowmnew']) {
	dheader('location: ' . $_G['siteurl']);
}

$a = $_GET['a'] && preg_match('/^\w+$/', $_GET['a']) ? $_GET['a'] : 'forumlist';

include template('m/' . $a);