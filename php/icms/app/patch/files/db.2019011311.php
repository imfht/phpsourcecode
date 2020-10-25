<?php
@set_time_limit(0);
defined('iPHP') OR require (dirname(__FILE__).'/../../../iCMS.php');

return patch::upgrade(function(){
    $indexs = apps_db::indexes('#iCMS@__category');
    $indexs['s_o_cid']       && iDB::query("ALTER TABLE `#iCMS@__category` DROP INDEX `s_o_cid`;");
    $indexs['t_o_cid']       && iDB::query("ALTER TABLE `#iCMS@__category` DROP INDEX `t_o_cid`;");
    empty($indexs['sortnum'])&& iDB::query("ALTER TABLE `#iCMS@__category` ADD  KEY `sortnum` (`status`, `sortnum`);");
    empty($indexs['appid'])  && iDB::query("ALTER TABLE `#iCMS@__category` ADD  KEY `appid` (`appid`, `sortnum`);");
    empty($indexs['rootid']) && iDB::query("ALTER TABLE `#iCMS@__category` ADD  INDEX `rootid` (`status`, `rootid`, `sortnum`);");
    $msg.='更新栏目索引<iCMS>';

    $fields  = apps_db::fields('#iCMS@__prop');
    if(empty($fields['info'])){
    	iDB::query("
ALTER TABLE `#iCMS@__prop`
ADD COLUMN `info` VARCHAR(512) DEFAULT '' NOT NULL AFTER `val`;
    	");
        $msg.='增加属性说明字段<iCMS>';
    }
    return $msg;
});

