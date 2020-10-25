<?php
if (empty($_GET['token'])||empty($_GET['secret'])||
(preg_match('/^[0-9a-zA-Z]{40}$/',$_GET['token'],$match) == 0) ||
(preg_match('/^[0-9a-zA-Z]{32}$/',$_GET['secret'],$match) == 0)){
echo <<<HTML
<form action="demo_getdata.php" method="get">
<b>请输入刚才授权时获得的access_token和access_secret</b><input type="submit" value="请求" />
<hr />
access_token : <textarea rows="1" cols="50" name="token" ></textarea><br />
access_secret : <textarea rows="1" cols="40" name="secret"></textarea><br /><br />
请求的API：<br /><br />
<input type="radio" checked="checked" name="api" value="1" />http://i.buaa.edu.cn/plugin/iauthClient/api/do_getuser.php 获得用户真实信息<br />
<input type="radio" checked="checked" name="api" value="2" />http://i.buaa.edu.cn/plugin/iauthClient/api/do_addnews.php 发新鲜事<br />
</form>
HTML;
exit();
}
/* your code here */


require_once('./iauth_getdata.php');

$accessToken = $_GET['token'];
$accessSecret = $_GET['secret'];
if (empty($_GET['api'])||$_GET['api']==1){
$apiUrl = 'http://i.buaa.edu.cn/plugin/iauthClient/api/do_getuser.php'; 
$params = array();
}
else{
$apiUrl = 'http://i.buaa.edu.cn/plugin/iauthClient/api/do_addnews.php';
$params = array('message'=>'用<a href="/plugin.php?pluginid=apps&ac=detail&appsid=225">ihome开放PHPSDK</a>调用<a href="/space.php?uid=665&do=blog&id=33881">ihomeAPI</a>发送新鲜事成功！<a href="http://git.oschina.net/songjinghe/iauth-php-sdk">你也试试！</a>'); }

$data = iauth_getdata( $accessToken, $accessSecret, $apiUrl, $params );


/* your code here */

echo $data;

?>