<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/5
 * Time: 0:14
 */

use fastwork\facades\Env;

return [
    // 模板引擎类型 支持 php think 支持扩展
    'type' => 'Think',
    // 默认模板渲染规则 1 解析为小写+下划线 2 全部转换小写 3 保持操作方法
    'auto_rule' => 1,
    'cache_path' => Env::get('runtime_path') . 'temp' . DIRECTORY_SEPARATOR,
    'view_suffix' => 'php',
    // 模板文件名分隔符
    'view_depr' => DIRECTORY_SEPARATOR,
    // 模板引擎普通标签开始标记
    'tpl_begin' => '{',
    // 模板引擎普通标签结束标记
    'tpl_end' => '}',
    // 标签库标签开始标记
    'taglib_begin' => '<',
    // 标签库标签结束标记
    'taglib_end' => '>',
    'tpl_replace_string' => [
        '__STATIC__' => '/static',
        '__JS__' => '/static/js',
        '__CSS__' => '/static/css',
        '__IMG__' => '/static/image',
    ],
];