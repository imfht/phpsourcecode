<?php
/**
 * Created by PhpStorm.
 * Author: William
 * Date: 2016/9/12
 * Time: 20:53
 */
echo 123;exit;
require ('common.php');

$Loader = Factory::getLoader();

//$Loader->http->redirect(base_url('/view/login','.html'));
$Loader->component('curl');
var_dump($Loader->curl->request('GET','http://weibo.com/weilong123/profile?rightmod=1&wvr=6&mod=personnumber&is_all=1'));