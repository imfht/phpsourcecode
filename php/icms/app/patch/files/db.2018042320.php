<?php
@set_time_limit(0);
defined('iPHP') OR require (dirname(__FILE__).'/../../../iCMS.php');

return patch::upgrade(function(){
    $count = iDB::value("
        SELECT count(*) FROM `#iCMS@__user`
    ");
    if($count<10000){
        iDB::query("
            ALTER TABLE `#iCMS@__user`
            AUTO_INCREMENT=10000;
        ");
        $msg.= '更新用户表自增ID<iCMS>';
    }

    $weixin = iDB::query("
        UPDATE `#iCMS@__apps`
        SET
        `type` = '3' ,
        `config` = '{\"info\":\"微信公众平台接口程序\",\"version\":\"v1.0.0\",\"menu\":\"default\"}'
        WHERE `app` = 'weixin'
    ");

    $id = iDB::value("
        SELECT id FROM `#iCMS@__apps_store` where `app`='weixin'
    ");
    if($id){
        iDB::query("
            UPDATE `#iCMS@__apps_store`
            SET `version` = '1.0.0'
            WHERE `app`='weixin' and `version` = 'v1.0.0'
        ");
        $msg.= '更新微信应该用信息<iCMS>';
    }else{
        $appid = iDB::value("
            SELECT id FROM `#iCMS@__apps` where `app`='weixin'
        ");
        if(empty($id) && $appid){
            $store = apps_store::remote_send('10019');
            $store['appid'] = $appid;
            $store['sid']   = '10019';
            $store['data']  = $store['authcode'];
            apps_store::save($store);
            $msg.= '更新微信应该用信息<iCMS>';
        }
    }

    return $msg;
});

