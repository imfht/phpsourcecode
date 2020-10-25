<?php
@set_time_limit(0);
defined('iPHP') OR require (dirname(__FILE__).'/../../../iCMS.php');

return patch::upgrade(function(){
    iDB::query("
UPDATE `#iCMS@__apps` SET
`menu` = '[{\"id\":\"system\",\"children\":[{\"id\":\"apps\",\"caption\":\"应用管理\",\"icon\":\"code\",\"sort\":\"0\",\"children\":[{\"caption\":\"应用管理\",\"href\":\"apps\",\"icon\":\"code\"},{\"caption\":\"添加应用\",\"href\":\"apps&do=add\",\"icon\":\"pencil-square-o\"},{\"caption\":\"-\"},{\"caption\":\"钩子管理\",\"href\":\"apps&do=hooks\",\"icon\":\"plug\"},{\"caption\":\"-\"},{\"caption\":\"应用市场\",\"href\":\"apps_store&do=store\",\"icon\":\"bank\"},{\"caption\":\"-\"},{\"caption\":\"插件市场\",\"href\":\"apps_store&do=plugin\",\"icon\":\"bank\"},{\"caption\":\"-\"},{\"caption\":\"模板市场\",\"href\":\"apps_store&do=template\",\"icon\":\"bank\"}]}]}]'
WHERE `app` = 'apps';
    ");
    apps::cache();
    menu::cache();
    $msg.='更新应用市场菜单<iCMS>';
    return $msg;
});

