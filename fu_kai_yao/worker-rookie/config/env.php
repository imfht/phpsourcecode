<?php
/**
 * 环境参数例子
 * 格式说明:
 * 配置均为kv形式
 * key的格式:  文件名.key1[.keyN]
 * 例子：
 *      需要覆盖config配置文件db的server参数
 *      array(
 *          //数据库链接字符串
 *          'config.db.server' => 'xxxxxx'
 *      )
 */
return [
    'config.env' => 'local_debug',//环境名 local_debug本地调试，dev开发，test测试，prod正式
];
