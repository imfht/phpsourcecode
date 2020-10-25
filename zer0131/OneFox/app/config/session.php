<?php

/**
 * @author: ryan<zer0131@vip.qq.com>
 * @desc: session设置文件
 */

$common = [
    'auto_start' => true,//自动开启session, 默认关闭
    'save_path' => APP_PATH . DS . 'session',//session存储路径
    /*'name' => 'PHPSESSID',//session名称
    'save_handler' => 'files',//session存储方式
    'serialize_handler' => 'php',//PHP标准序列化
    'gc_maxlifetime' => 1440,//过期时间\
    'gc_probability' => 1,
    'gc_divisor' => 100,//建议设置1000-5000, 概率=session.gc_probability/session.gc_divisor（1/1000）, 页面访问越频繁概率越小
    'cookie_lifetime' => 0,//cookie存活时间（0为直至浏览器重启，单位秒）
    'cookie_path' => '/',//cookie的有效路径
    'cookie_httponly' => "",//httponly标记增加到cookie上(脚本语言无法抓取)
    'cookie_domain' => '',//cookie的有效域名
    'use_trans_sid' => 0,//trans_sid支持(默认0)
    'use_cookies' => 1,//使用cookies在客户端保存会话
    'use_only_cookies' => 1,//去保护URL中传送session id的用户
    'cache_limiter' => 'nocache',//HTTP缓冲类型(nocache,private,pblic)
    'cache_expire' => 180,//文档过期时间(分钟)
    'bug_compat_42' => 1,//全局初始化session变量
    'bug_compat_warn' => 1,
    'referer_check' => '',//防止带有ID的外部URL
    'hash_function' => 0,//hash方法{0:md5(128 bits),1:SHA-1(160 bits)}
    'hash_bits_per_character' => 4,//当转换二进制hash数据奥可读形式是，每个字符保留位数*/
];

$online = [];

$dev = [];

return DEBUG ? array_merge($common, $dev) : array_merge($common, $online);