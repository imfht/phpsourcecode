<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: function_openapi.php 31894 2012-10-23 02:13:29Z pmonkey_w $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function wx_get_access_token(){
    global $_G;
    loadcache('wx_access_token');
    if($_G['cache']['wx_access_token'] && substr($_G['cache']['wx_access_token'], 0, 10) + 7200 > TIMESTAMP){
        $wx_access_token = substr($_G['cache']['wx_access_token'], 10);
    }else{
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$_G['setting']['ec_wxpay_appid'].'&secret='.$_G['setting']['ec_wxpay_appsecret'];
        if($result = dfsockopen($url)){
            $result = json_decode($result, true);
            $wx_access_token = $result && $result['access_token'] ? $result['access_token'] : '';
            $wx_access_token && savecache('wx_access_token', TIMESTAMP.$wx_access_token);
        }else{
            $wx_access_token = '';
        }
    }
    return $wx_access_token;
}

function wx_get_jsapi_ticket(){
    global $_G;
    loadcache('wx_jsapi_ticket');
    if($_G['cache']['jsapi_ticket'] && substr($_G['cache']['jsapi_ticket'], 0, 10) + 7200 > TIMESTAMP){
        $wx_jsapi_ticket = substr($_G['cache']['wx_jsapi_ticket'], 10);
    }else{
        $wx_access_token = wx_get_access_token();
        $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$wx_access_token.'&type=jsapi';
        if($result = dfsockopen($url)){
            $result = json_decode($result, true);
            $wx_jsapi_ticket = $result && $result['ticket'] ? $result['ticket'] : '';
            $wx_jsapi_ticket && savecache('wx_jsapi_ticket', TIMESTAMP.$wx_jsapi_ticket);
        }else{
            $wx_jsapi_ticket = '';
        }
    }
    return $wx_jsapi_ticket;
}

function wx_get_js_noncestr(){
    return random(32);
}

function wx_get_js_signature($noncestr, $timestamp, $url){
    $wx_jsapi_ticket = wx_get_jsapi_ticket();
    list($url) = explode('#', $url);
    $str = "jsapi_ticket={$wx_jsapi_ticket}&noncestr={$noncestr}&timestamp={$timestamp}&url={$url}";
    return sha1($str);
}