<?php
/**
 * 开放授权配置
 */
$config = array();
// 微博账号配置
$config["weibo"] = array(
    "clientId" => "",
    "clientSecret" => "",
    "redirectUri" => urlencode(Router::buildUrl("User_LoginCallback", array("type" => "weibo"))),
);
// QQ账号配置
$config["qq"] = array(
    "clientId" => "",
    "clientSecret" => "",
    "redirectUri" => urlencode(Router::buildUrl("User_LoginCallback", array("type" => "qq"))),
);
// 百度账号配置
$config["baidu"] = array(
    "clientId" => "",
    "clientSecret" => "",
    "redirectUri" => urlencode(Router::buildUrl("User_LoginCallback", array("type" => "baidu"))),
);
return $config;
