<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2016/12/16
 * Time: 10:24
 */
return  [
    'local'=>[
        'type'=>'mysql',//数据库类型
        'host'=>'localhost',//数据库主机
        'dbname'=>'test',//数据库名
        'username'=>'root',//用户名
        'password'=>'root',//密码
        'return_result_sets'=>true,//结果形式 true对象集  false数组
        'driver_options'=>[PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'], //pdo选项
        'error_mode'=>PDO::ERRMODE_EXCEPTION,//错误提示等级
        //'error_mode'=>PDO::ERRMODE_WARNING,//错误提示等级
        'id_column'=>'id',//主键
        'id_column_overrides'=>[
            //特殊表主键声明
        //'person' => 'person_id',
        //'role' => 'role_id',
        ],
        'logging'=>true,//开启记录
        'logger'=> function($log_string, $query_time) {
            \naples\lib\Factory::getDebug()->traceDb($log_string . ' in ' . $query_time);
        },
        'caching'=>true,//缓存
        'caching_auto_clear'=>true,//自动更新缓存
    ],
];