<?php
@set_time_limit(0);
defined('iPHP') OR require (dirname(__FILE__).'/../../../iCMS.php');

return patch::upgrade(function(){
    iDB::query("
UPDATE `#iCMS@__apps` SET
`table` = '{\"spider_post\":[\"spider_post\",\"id\",\"\",\"发布\"],\"spider_project\":[\"spider_project\",\"id\",\"\",\"方案\"],\"spider_rule\":[\"spider_rule\",\"id\",\"\",\"规则\"],\"spider_url\":[\"spider_url\",\"id\",\"\",\"采集结果\"],\"spider_url_data\":[\"spider_url_data\",\"id\",\"\",\"采集附加数据\"],\"spider_error\":[\"spider_error\",\"id\",\"\",\"错误记录\"]}'
WHERE `app` = 'spider';
    ");
    apps::cache();
    $msg.='更新采集器数据表<iCMS>';

    if(!iDB::check_table('spider_url_data')){
        iDB::query("
CREATE TABLE `#iCMS@__spider_url_data` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(200) NOT NULL DEFAULT '',
  `data` mediumtext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
        ");
        $msg.='新增采集数据附加表<iCMS>';
    }
    return $msg;
});
