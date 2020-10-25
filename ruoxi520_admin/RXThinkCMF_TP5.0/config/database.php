<?php
// +----------------------------------------------------------------------
// | RXThink框架 [ RXThink ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2019 南京RXThink工作室
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <rxthink@gmail.com>
// +----------------------------------------------------------------------

/**
 * 数据库配置文件类
 * 
 * @author 牧羊人
 * @date 2018-12-11
 */
use think\Config;

// 获取数据库配置
$config = Config::get('config');
$db_config = $config['db_config'];
$itemArr = explode('://', $db_config);
$db_type = $itemArr[0];
list($db_user, $db_pwd, $db_host,$db_port,$db_name) = preg_split("/[:@\/]/",$itemArr[1]);

return [
    // 数据库类型
    'type'            => $db_type,
    // 服务器地址
    'hostname'        => $db_host,
    // 数据库名
    'database'        => $db_name,
    // 用户名
    'username'        => $db_user,
    // 密码
    'password'        => $db_pwd,
    // 端口
    'hostport'        => $db_port,
    // 连接dsn
    'dsn'             => '',
    // 数据库连接参数
    'params'          => [],
    // 数据库编码默认采用utf8
    'charset'         => $config['db_charset'],
    // 数据库表前缀
    'prefix'          => $config['db_prefix'],
    // 数据库调试模式
    'debug'           => true,
    // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'deploy'          => 0,
    // 数据库读写是否分离 主从式有效
    'rw_separate'     => false,
    // 读写分离后 主服务器数量
    'master_num'      => 1,
    // 指定从服务器序号
    'slave_no'        => '',
    // 自动读取主库数据
    'read_master'     => false,
    // 是否严格检查字段是否存在
    'fields_strict'   => true,
    // 数据集返回类型
    'resultset_type'  => 'array',
    // 自动写入时间戳字段
    'auto_timestamp'  => false,
    // 时间字段取出后的默认时间格式
    'datetime_format' => false,//'Y-m-d H:i:s',
    // 是否需要进行SQL性能分析
    'sql_explain'     => false,
];
