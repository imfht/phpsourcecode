<?php
// 定义项目名称
define ( 'APP_NAME', 'SEO' );
// 定义项目路径
define ( 'APP_PATH', './' );
// 开启调试模式
define ( 'APP_DEBUG', TRUE );
// 开启url重写功能
//define ( 'URL_REWRITE',2);
// 加载框架入文件
require 'ThinkPHP/ThinkPHP.php';
/*
$url = "http://www.tudou.com/";
$timeout = 10;
$agent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2) Gecko/20100115 Firefox/3.6 (.NET CLR 3.5.30729)";
$ch = curl_init ();
curl_setopt ( $ch, CURLOPT_URL, $url );
curl_setopt ( $ch, CURLOPT_TIMEOUT, $timeout );
curl_setopt ( $ch, CURLOPT_HEADER, 0);
curl_setopt ( $ch, CURLOPT_USERAGENT, $agent );
curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 ); // 是否抓取跳转后的页面

echo (curl_exec ( $ch ));
*/