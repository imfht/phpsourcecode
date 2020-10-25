<?php
/**
 * 前台配置文件
 * 所有除开系统级别的前台配置
 */

return array(

// 预先加载的标签库
    'TAGLIB_PRE_LOAD' => 'OT\\TagLib\\Article,OT\\TagLib\\Think',

    /* 主题设置 */
    'DEFAULT_THEME' => 'default', // 默认模板主题名称
    // URL伪静态后缀
    'url_html_suffix'        => 'html|xml|json',
    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__PUBLIC__' => __ROOT__ . '/Public',
        '__ADDONS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/Addons',
        '__IMG__' => __ROOT__ . '/Application/' . MODULE_NAME . '/Static/images',
        '__CSS__' => __ROOT__ . '/Application/' . MODULE_NAME . '/Static/css',
        '__JS__' => __ROOT__ . '/Application/' . MODULE_NAME . '/Static/js',
        '__LIB__' => __ROOT__ . '/Application/' . MODULE_NAME . '/Static/lib',
        '__ZUI__' => __ROOT__ . '/Public/zui',
        '__APPLICATION__'=>__ROOT__.'/Application/'
    ),

    'NEED_VERIFY'=>0,//此处控制默认是否需要审核，该配置项为了便于部署起见，暂时通过在此修改来设定。
    /*加载扩展配置文件*/
    'LOAD_EXT_CONFIG' => 'city',

);

