<?php

/**
 * @author: ryan<zer0131@vip.qq.com>
 * @desc: 默认配置文件
 */

$common = [
    'test' => 'ryan zhang'
];

$online = [];

$dev = [];

return DEBUG ? array_merge($common, $dev) : array_merge($common, $online);