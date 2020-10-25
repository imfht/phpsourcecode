<?php
$systemConfig = include('Common/Conf/system_config.php');

$appConfig = array(
    // 调试页
    // 'SHOW_PAGE_TRACE' =>true,
    'URL_CASE_INSENSITIVE'  => false,

    // 默认模块和Action
    'MODULE_ALLOW_LIST' => array('Home'),
    'DEFAULT_MODULE' => 'Home',

    // 默认控制器
    'DEFAULT_CONTROLLER' => 'Public',

    // 分页列表数
    'PAGE_LIST_ROWS' => 10,

    // 开启布局
    'LAYOUT_ON' => true,
    'LAYOUT_NAME' => 'Common/layout',

    // error，success跳转页面
    'TMPL_ACTION_ERROR' => 'Common:dispatch_jump',
    'TMPL_ACTION_SUCCESS' => 'Common:dispatch_jump',


    // 文件上传根目录
    'UPLOAD_ROOT' => 'Public/uploads/',
    // 系统公用配置目录
    'COMMON_CONF_PATH' => WEB_ROOT . 'Common/Conf/',


    'TAGLIB_LOAD'=> true,//加载标签库打开
    'TAGLIB_PRE_LOAD'=>'Home\\TagLib\\MyTag',


    //公共的Lib扩展 路径
//    'AUTOLOAD_NAMESPACE' => array(
//        'Lib' => APP_PATH . 'Lib',
//    ),
);

return array_merge($appConfig,$systemConfig);
