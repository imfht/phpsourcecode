<?php

// 判断是否是在微信浏览器里
function isWeixinBrowser() {
    $agent = $_SERVER ['HTTP_USER_AGENT'];
    if (! strpos ( $agent, "icroMessenger" )) {
        return false;
    }
    return true;
}

// php获取当前访问的完整url地址
function GetCurUrl() {
    return get_url();
}

//获取分享url的方法，解决controler在鉴权时二次回调jssdk获取分享url错误的问题
function get_shareurl(){
    $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    $findme   = 'https://open.weixin.qq.com/';
    $pos = strpos($url, $findme);
    // 使用 !== 操作符。使用 != 不能像我们期待的那样工作，
    // 因为 'a' 的位置是 0。语句 (0 != false) 的结果是 false。
    $share_url = '';
    if ($pos !== false) {             //url是微信的回调授权地址
        return '';
    } else {                           //url是本地的分享地址
        return $url;
    }
}
//判断是http or https
function get_http_https(){
    $url = 'http://';
       if (isset ( $_SERVER ['HTTPS'] ) && $_SERVER ['HTTPS'] == 'on') {
       $url = 'https://';
       }else{
        $url = 'http://';
       }
    return $url;
}

