<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-8-15
 * Time: 上午8:37
 */
header("Content-Type:text/html;charset=utf-8");
$regex['url'] ="/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\’:+!]*([^<>\"])*$/";
$regex['email'] = "([a-z0-9_\-]+)@([a-z0-9_\-]+\.[a-z0-9\-\._\-]+)";


$test=<<<EOT
&nbsp;公司是一个大企业&nbsp;<br />
公司的网站地址是:<br />
www.baidu.com
手机号:13306350098
电话:0635-2312627
邮箱:good@126.com,请注明
欢迎您的到来
EOT;




//去掉标签之间的文字
$string = preg_replace("/1\d{10}/", "",$test );
$string=preg_replace("/[\w\-\.]+@[\w\-]+(\.\w+)+/i","",$string);
$string=preg_replace("/([0-9]{3,4}-)?[0-9]{7,8}/i","",$string);
$string=preg_replace("/(http|https|ftp):\/\/[a-z0-9]+\.[a-z0-9+\-]+\.[a-z]{2,4}/i","",$string);
$string=preg_replace("/[a-z0-9]+\.[a-z0-9+\-]+\.[a-z]{2,4}/i","",$string);
echo $string;
exit;

//去掉JAVASCRIPT代码
$string = preg_match("/<!--.*//-->/i","", $string);