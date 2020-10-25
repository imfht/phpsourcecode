<?php if (!defined('HDPHP_PATH')) exit; 
return array (
  /********************************基本参数********************************/
    'AUTO_LOAD_FILE'                => array(),     //自动加载文件
    /********************************模块相关********************************/
    'MODULE_LIST'                   => 'Index,Install,Member,Admin',
    'DEFAULT_MODULE'                => 'Index', // 默认模块
    /********************************URL路由相关********************************/
    'PATHINFO_DLI'                  => '/',	//PATHINFO 分隔符
    'HTML_SUFFIX'                   => '',	// 伪静态扩展名
    'VAR_GROUP'                     => 'g', // 模块组 URL 变量
    'VAR_MODULE'                    => 'm', // 模块变量名
    'VAR_CONTROLLER'                => 'c', // 控制器变量
    'VAR_ACTION'                    => 'a', // 动作变量
    'URL_TYPE'                      => 2,	//类型 1:PATHINFO模式 2:普通模式 3:兼容模式
    /********************************分页处理********************************/
    'PAGE_VAR'                      => 'page',      //分页GET变量
    'PAGE_ROW'                      => 10,          //页码数量
    'PAGE_SHOW_ROW'                 => 10,          //每页显示条数
    'PAGE_STYLE'                    => 2,           //页码风格
    'PAGE_DESC'                     => array(
        'pre' => '上一页', 'next' => '下一页',//分页文字设置
        'first' => '首页', 'end' => '尾页', 'unit' => '条'
        ),
     /********************************钓子********************************/
    'HOOK'                          => array(
        "APP_INIT" => array("AppInitHook")
        ),
);
?>