<?php
// +----------------------------------------------------------------------
// | IKPHP.COM [ I can do all the things that you can imagine ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2050 http://www.ikphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 小麦 <810578553@qq.com> <http://www.ikcms.cn>
// +----------------------------------------------------------------------

/**
 * 系统配文件
 * 所有系统级别的配置
 */
return array(
    /* 模块相关配置 */
    'AUTOLOAD_NAMESPACE' => array('Addons' => IKPHP_ADDON_PATH), //扩展模块列表
    'DEFAULT_MODULE'     => 'Home',
    'MODULE_DENY_LIST'   => array('Common','Install','User'),//禁止访问的模块
    'MODULE_ALLOW_LIST'  => array('Home','Admin','Group','Space','Article','Radio'),//允许访问的模块

    /* 系统数据加密设置 */
    'DATA_AUTH_KEY' => 'p"J#)[7?~8F>.jwm,=_ULk<a0NSf;DW%iv$-GhZE', //默认数据加密KEY 千万得记住系统加密Key

    /* 调试配置 */
    'SHOW_PAGE_TRACE' => true,

    /* 用户相关设置 */
    'USER_MAX_CACHE'     => 1000, //最大缓存用户数
    'USER_ADMINISTRATOR' => 1, //管理员用户ID

    /* 全局过滤配置 */
    'DEFAULT_FILTER' => 'htmlspecialchars,trim', //全局过滤函数

    'VAR_MODULE' => 'app',
    'VAR_CONTROLLER' => 'c',
    'VAR_ACTION' => 'a',
    'LOAD_EXT_CONFIG' => 'url,db',

    
    /* 成功和错误模板 */    
    'TMPL_ACTION_SUCCESS' => 'Public:success',
    'TMPL_ACTION_ERROR' => 'Public:error',

);
