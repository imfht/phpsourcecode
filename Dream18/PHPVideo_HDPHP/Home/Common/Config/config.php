<?php
if (!defined("HDPHP_PATH"))exit('No direct script access allowed');
//更多配置请查看hdphp/Config/config.php
$config = array(
    /********************************基本参数********************************/
    'AUTO_LOAD_FILE'                => array(),     //自动加载文件
    /********************************模块相关********************************/
    'MODULE_LIST'                   => 'Index,Install,Member,Admin',
    'DEFAULT_MODULE'                => 'Index', // 默认模块
    /********************************程序信息********************************/
    'SYSTEM_WEBNAME'                =>  '「PHP联盟」视频管理系统',
    'SYSTEM_DOMAIN'                 =>  'http://www.PHPUnion.cn',
    'SYSTEM_VERSION'                =>  'Beta V0.1',
    'SYSTEM_VERSION_DATE'           =>  '2015-5-13',
    'SYSTEM_AUTHOR'                 =>  '楚羽幽',
    'SYSTEM_EMAIL'                  =>  'Nmae_Cyu@Foxmail.com',
    'SYSTEM_QQ'                     =>  '958416459',
    'SYSTEM_FLOCK'                  =>  '383186297',
    /********************************第三方扩展标签库********************************/
    'TPL_TAGS' => array(
        '@.Common.Tag.CommonTag'
    ),
);
return array_merge($config,require 'Data/Config/db.config.php',require 'Data/Config/config.inc.php',require 'Data/Config/config.php');
?>