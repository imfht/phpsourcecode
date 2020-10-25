<?php
/**.-------------------------------------------------------------------
 * |    Software: [QeyserBlog]
 * |    Site: www.qeyser.net
 * |-------------------------------------------------------------------
 * |    Author: 凯撒 <125790757@qq.com>
 * |    WeChat: 15999230034
 * |    Copyright (c) 2017-2018, www.qeyser.net . All Rights Reserved.
 * '-------------------------------------------------------------------*/

/**
 * 前台公共配置文件
 */
return [
    // +----------------------------------------------------------------------
    // | 模板设置
    // +----------------------------------------------------------------------
    'template'  => [
        // 模板引擎类型 支持 php think 支持扩展
        'type'         => 'Think',
        // 模板路径 目前设置模板路径不可用
        'view_path'    => './theme/'.config('default_template').'/',
        // 模板后缀
        'view_suffix'  => 'html',
        //layout模板布局 开启
        'layout_on'     =>  true,
        //模板名称
        'layout_name'   =>  'layout/common',
        // 预先加载的标签库
        'taglib_pre_load'     =>    'app\index\myTags',
        // 默认全局过滤方法 用逗号分隔多个
        'default_filter'         => 'htmlentities',
    ],

];
