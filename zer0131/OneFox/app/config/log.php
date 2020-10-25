<?php

/**
 * @author: ryan<zer0131@vip.qq.com>
 * @desc: 日志配置
 */

$common = [
    'default' => [
        'ext' => '.log',//日志文件类型
        'date_format' => 'Y-m-d H:i:s',//日期格式
        'filename' => '',//日志文件名
        'log_path' => '',//日志路径
        'prefix' => 'onefox_',//日志文件名前缀
        'log_level' => 'info',//日志输出级别
        'log_seperator' => '|',//日志输出内容分隔符
        'log_kv_seperator' => '=',//日志内容中的键值分隔符
        'log_error_ext' => '.wf',//错误日志输出后缀
        'log_debug_ext' => '.dt',//调试日志输出后缀
    ]
];

$online = [];

$dev = [];

return DEBUG ? array_merge($common, $dev) : array_merge($common, $online);