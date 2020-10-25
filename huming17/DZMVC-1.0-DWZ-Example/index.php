<?php
if(!file_exists('./config/config_global.php')){
    header('location: install/index.php');
}
/**
 * 系统入口文件
 * @author HumingXu E-mail:huming17@126.com
 */

require_once './dz_framework/init.php';
//DEBUG 基于URL用户权限校验 暂放入口页面 具体根据实际业务逻辑使用
//ext::auth_check();

//DEBUG 接收对象 动作
//$obj=isset($_REQUEST['obj']) ? $_REQUEST['obj']:'index'; //DEBUG 备用对象入口参数
$mod=isset($_REQUEST['mod']) ? $_REQUEST['mod']:'index'; //DEBUG 对应 source/module 下文件夹名
$action=isset($_REQUEST['action']) ? $_REQUEST['action']:'index'; //DEBUG 对应 source/module/{$mod}_{$action}.php 文件名
$do=isset($_REQUEST['do']) ? $_REQUEST['do']:''; //DEBUG 对应 source/module/{$mod}_{$action}.php 文件内动作 (其他参数可在各入口模块内接收)

//DEBUG 转发执行 对象动作
require libfile($mod.'/'.$action, 'module','..');