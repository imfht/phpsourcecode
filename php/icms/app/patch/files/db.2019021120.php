<?php
@set_time_limit(0);
defined('iPHP') OR require (dirname(__FILE__).'/../../../iCMS.php');

return patch::upgrade(function(){
    iDB::query("
UPDATE `#iCMS@__apps` SET
`menu` = '[{\"id\":\"tools\",\"children\":[{\"id\":\"spider\",\"sort\":\"-994\",\"caption\":\"采集管理\",\"href\":\"spider\",\"icon\":\"magnet\",\"children\":[{\"caption\":\"错误信息\",\"href\":\"spider_error&do=manage\",\"icon\":\"info-circle\"},{\"caption\":\"-\"},{\"caption\":\"采集列表\",\"href\":\"spider&do=manage\",\"icon\":\"list-alt\"},{\"caption\":\"未发文章\",\"href\":\"spider&do=inbox\",\"icon\":\"inbox\"},{\"caption\":\"-\"},{\"caption\":\"采集方案\",\"href\":\"spider_project&do=manage\",\"icon\":\"magnet\"},{\"caption\":\"添加方案\",\"href\":\"spider_project&do=add\",\"icon\":\"edit\"},{\"caption\":\"-\"},{\"caption\":\"采集规则\",\"href\":\"spider_rule&do=manage\",\"icon\":\"magnet\"},{\"caption\":\"添加规则\",\"href\":\"spider_rule&do=add\",\"icon\":\"edit\"},{\"caption\":\"-\"},{\"caption\":\"发布模块\",\"href\":\"spider_post&do=manage\",\"icon\":\"magnet\"},{\"caption\":\"添加发布\",\"href\":\"spider_post&do=add\",\"icon\":\"edit\"}]},{\"caption\":\"-\",\"sort\":\"-993\"}]}]'
WHERE `app` = 'spider';
    ");
    apps::cache();
    menu::cache();
    $msg.='更新采集器菜单<iCMS>';
    return $msg;
});

