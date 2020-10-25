<?php
@set_time_limit(0);
defined('iPHP') OR require (dirname(__FILE__).'/../../../iCMS.php');

return patch::upgrade(function(){
    iDB::query("
ALTER TABLE `#iCMS@__category`
  DROP INDEX `s_o_cid`,
  DROP INDEX `t_o_cid`,
  ADD  KEY `sortnum` (`status`, `sortnum`),
  ADD  KEY `appid` (`appid`, `sortnum`),
  ADD  INDEX `rootid` (`status`, `rootid`, `sortnum`);
    ");

    $msg.='更新栏目索引<iCMS>';
	iDB::query("
ALTER TABLE `#iCMS@__prop`
  ADD COLUMN `info` VARCHAR(512) DEFAULT '' NOT NULL AFTER `val`;
	");
$msg.='增加属性说明字段<iCMS>';
    return $msg;
});

