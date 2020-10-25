<?php
@set_time_limit(0);
defined('iPHP') OR require (dirname(__FILE__).'/../../../iCMS.php');

return patch::upgrade(function(){
    $fields  = apps_db::fields('#iCMS@__user_openid');
    if(empty($fields['appid'])){
      iDB::query("
  ALTER TABLE `#iCMS@__user_openid`
    ADD COLUMN `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT FIRST,
    ADD COLUMN `appid` VARCHAR(255) DEFAULT '' NOT NULL AFTER `platform`,
    CHANGE `uid` `uid` INT(10) UNSIGNED NOT NULL,
    CHANGE `openid` `openid` VARCHAR(255) DEFAULT '' NOT NULL,
    DROP PRIMARY KEY,
    ADD PRIMARY KEY (`id`),
    ADD INDEX `idx_upa` (`uid`, `platform`, `appid`);
      ");
      $msg.= '更新user_openid表<iCMS>';
    }
    return $msg;
});

