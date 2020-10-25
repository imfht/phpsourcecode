<?php
// +----------------------------------------------------------------------
// | sent_ [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.sent_.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

/**
 * 安装程序配置文件
 */

define('INSTALL_APP_PATH', realpath('./') . '/');

return array(
    
    /* 主题设置 */
    'DEFAULT_THEME' =>  '',  // 默认模板主题名称
    
    /* URL配置 */
    'URL_CASE_INSENSITIVE' => fase, //默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'            => 3, //URL模式 使用兼容模式安装
    'VAR_URL_PARAMS'       => '', // PATHINFO URL参数变量
    'URL_PATHINFO_DEPR'    => '/', //PATHINFO URL分割符

    'ORIGINAL_TABLE_PREFIX' => 'sent_', //默认表前缀
    'TAGLIB_BEGIN'          =>  '<',  // 标签库标签开始标记
    'TAGLIB_END'            =>  '>',  // 标签库标签结束标记

    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__STATIC__' => __ROOT__ . '/Public/Static',
        '__ADDONS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/Addons',
        '__IMG__'    => __ROOT__ . '/Public/' . MODULE_NAME . '/images',
        '__CSS__'    => __ROOT__ . '/Public/' . MODULE_NAME . '/css',
        '__JS__'     => __ROOT__ . '/Public/' . MODULE_NAME . '/js',
        '__NAME__'=>'SentCMS',
        '__COMPANY__'=>'南昌腾速科技有限公司',
        '__WEBSITE__'=>'www.tensent.cn',
        '__COMPANY_WEBSITE__'=>'www.tensent.cn'
    ),

    /* SESSION 和 COOKIE 配置 */
    'SESSION_PREFIX' => 'sent_install', //session前缀
    'COOKIE_PREFIX'  => 'sent_install_', // Cookie前缀 避免冲突

);