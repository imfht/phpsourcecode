<?php
defined('BASE_PATH') OR exit('No direct script access allowed');
/**
 * YanPHP
 * User: weilongjiang(江炜隆)<willliam@jwlchina.cn>
 */


/**
 *  -------------------------------------------------------------
 * |Database config                                              |
 *  -------------------------------------------------------------
 *
 */

$config['db'] = [
    'default' => [
        /** host */
        'db_host' => 'mysql',
        /** 数据库用户名 */
        'db_user' => 'root',
        /** 数据库密码 */
        'db_password' => 'root',
        /** 端口 */
        'db_port' => 3306,
        /** 数据库 */
        'db_database' => 'yan',
        /** 表名前缀 */
        'db_prefix' => '',
        /**
         * mysql/postgres/sqlite/sqlsrv
         */
        'db_driver' => 'mysql',
        'db_charset' => 'utf8',
        'db_collation' => 'utf8_unicode_ci'
    ],
    'mysql1'=>[
        'db_host' => '',
        'db_user' => '',
        'db_password' => '',
        'db_port' => 3306,
        'db_database' => '',
        'db_prefix' => '',
        'db_driver' => 'mysql',
        'db_charset' => '',
        'db_collation' => ''
    ]
];

