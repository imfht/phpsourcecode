<?php
/**
 * wch_checkout.php UTF8
 * User: djks
 * Date: 15/4/20 17:00
 * Copyright: http://www.weicaihong.com
 */

if(!$_POST)
{
    if (strpos($_SERVER["HTTP_USER_AGENT"], "MicroMessenger"))
    {
        $wch_back = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'&shop_user_id='.$_SESSION['user_id'];
        $go_url = 'http://mp.weicaihong.com/index.php/open/jsapi/jsapi_address'.'?wchToken='.md5(appId).'&url='.$wch_back;
        wch_header($go_url);
    }
}
