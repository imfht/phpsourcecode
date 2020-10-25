<?php
/*---------------------------------------------------------------------
 * 数据库连接配置文件
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/
define('DB_ACCESS', DB_ACCESS_SINGLE);  //默认使用单台数据库服务器
return array(
    //mysql数据库配置
    'mysql'     =>  array(
        array(
            'db_type'      => 'mysql',
            'db_host'      => '{db_host}',
            'db_port'      => 3306,
            'db_user'      => '{db_user}',
            'db_pass'      => '{db_pass}',
            'db_name'      => '{db_name}',
            'db_charset'   => '{db_charset}',
            'serial'       => 'db-write',      //写服务器,如果没有配置读写分离，则此处不用理它
        ),

        array(
            'db_type'      => 'mysql',
            'db_host'      => '192.168.1.119',
            'db_port'      => 3306,
            'db_user'      => 'root',
            'db_pass'      => '123456',
            'db_name'      => 'test',
            'db_charset'   => 'utf8',
            'serial'       => 'db-read',   //读服务器,如果没有配置读写分离，则此处不用理它
        ),

        array(
            'db_type'      => 'mysql',
            'db_host'      => '192.168.1.40',
            'db_port'      => 3306,
            'db_user'      => 'root',
            'db_pass'      => '123456',
            'db_name'      => 'test',
            'db_charset'   => 'utf8',
            'serial'       => 'db-read',   //读服务器,如果没有配置读写分离，则此处不用理它
        ),
    ),

    //mongo DB 数据库配置
    'mongo'     => array(
        'host'      => '192.168.1.119',
        'port'      => 27017,
        'user'      => 'root',
        'pass'      => '123456',
        'db'        => 'herosphp'
    )
);
