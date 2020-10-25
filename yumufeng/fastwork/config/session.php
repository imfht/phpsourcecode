<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/30
 * Time: 12:40
 */
return [
    'type' => 'redis', //redis 保存session的驱动
    'prefix' => 'mzhua_',
    'name' => 'sess_id', // session_id 名字 也就是发给客户端cookie的名字
    // 是否自动开启 SESSION
    'auto_start' => true,
    'expire' => 7200,
];