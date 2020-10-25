<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

/**
 * 安装程序配置文件
 */

define('INSTALL_APP_PATH', realpath('./') . '/');

return array(

    'ORIGINAL_TABLE_PREFIX' => 'muucmf_', //默认表前缀
    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__STATIC__' => __ROOT__ . '/Public/static',
        '__ADDONS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/Addons',
        '__IMG__' => __ROOT__ . '/Public/' . MODULE_NAME . '/images',
        '__CSS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/css',
        '__JS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/js',
        '__ZUI__'=>__ROOT__.'/Public/zui',
        '__NAME__'=>'MuuCmf开源内容管理框架',
        '__COMPANY__'=>'北京火木科技有限公司',
        '__WEBSITE__'=>'www.muucmf.cn',
        '__COMPANY_WEBSITE__'=>'www.hoomuu.cn'
    ),
    /* URL配置 */
    'URL_MODEL' => 3, //URL模式
    'DEFAULT_THEME' =>  'default',  // 默认模板主题名称
    'COOKIE_PREFIX' => 'muucmf_', // Cookie前缀 避免冲突

    'SESSION_PREFIX' => 'muucmf', //session前缀
);