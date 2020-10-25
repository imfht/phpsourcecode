<?php
/**
	SAE数据库配置
 */
return array(
    'DB_HOST' => SAE_MYSQL_HOST_M . ',' . SAE_MYSQL_HOST_S, // 服务器地址
    'DB_NAME' => SAE_MYSQL_DB, // 数据库名
    'DB_USER' => SAE_MYSQL_USER, // 用户名
    'DB_PWD' => SAE_MYSQL_PASS, // 密码
    'DB_PORT' => SAE_MYSQL_PORT, // 端口
    'DB_PREFIX' =>'muucmf_', //数据表前缀


    'PICTURE_UPLOAD_DRIVER' => 'Sae',
    'UPLOAD_SAE_CONFIG' => array(
        'rootPath' => 'http://' . $_SERVER['HTTP_APPNAME'] . '-uploads.stor.sinaapp.com/',
        'domain'=>'uploads')
);

//本文件兼容sae访问