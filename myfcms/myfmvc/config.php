<?php

/*
 *  @author myf
 *  @date 2014-11-25
 *  @Description 配置文件
 */
$dev = "pro";
if($dev=="dev"){
    return array(
         'DEFAULT_DB'=>array(
            'DB_HOST'=>'localhost',
            'DB_NAME'=>'myfmvc',
            'DB_USER'=>'test',
            'DB_PWD'=>'test',
            'DB_PORT'=>'3306',
            'DB_PREFIX'=>"",
        ),
        //命名空间定义
        "namespaces"=>array(
            "Minyifei\Model"=>APP_PATH."/app/models",
            "Minyifei\Lib"=>APP_PATH."/app/libs",
        ),
        'OPEN_SQL_LOG'=>true,//是否记录sql语句
        'OPEN_HTTP_LOG'=>true,//是否记录http语句
    );
}else if($dev=="pro"){
    return array(
            'DEFAULT_DB'=>array(
            'DB_HOST'=>'10.4.14.186',
            'DB_NAME'=>'dd39b68d2499346b986ca42611da842ce',
            'DB_USER'=>'uLiir5vlTUElT',
            'DB_PWD'=>'pu5ofFT0XxihC',
            'DB_PORT'=>'3306',
            'DB_PREFIX'=>"",
        ),
        //命名空间定义
        "namespaces"=>array(
            "Minyifei\Model"=>APP_PATH."/app/models",
            "Minyifei\Lib"=>APP_PATH."/app/libs",
        ),
        'OPEN_SQL_LOG'=>true,//是否记录sql语句
        'OPEN_HTTP_LOG'=>true,//是否记录http语句
    );
}

