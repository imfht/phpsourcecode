<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.thinkphp.cn>
// +----------------------------------------------------------------------

/**
 * 前台配置文件
 * 所有除开系统级别的前台配置
 */
return array(
    /* 主题设置 */
    'DEFAULT_THEME' => 'Default', // 默认模板主题名

    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__ADDONS__' => __ROOT__ . '/Addons',
        '__STATIC__' => __ROOT__ . '/Application/Static',
        '__IMG__'    => __ROOT__ . '/Application/' . MODULE_NAME . '/Static/images',
        '__CSS__'    => __ROOT__ . '/Application/' . MODULE_NAME . '/Static/css',
        '__JS__'     => __ROOT__ . '/Application/' . MODULE_NAME . '/Static/js',
    ),
);