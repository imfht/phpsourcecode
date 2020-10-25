<?php
return array(
    //'配置项'=>'配置值'
    //数据库配置信息
    'DB_TYPE' => 'mysql', // 数据库类型
    'DB_HOST' => '192.168.1.13', // 服务器地址
    'DB_NAME' => 'df27e16ee3ce947b99ffd7cb9beb2bdc4', // 数据库名
    'DB_USER' => '32bb1b76-676f', // 用户名
    'DB_PWD' => '6b288368-3210', // 密码
    'DB_PORT' => 3306, // 端口
    'DB_PREFIX' => '', // 数据库表前缀
    'DB_CHARSET' => 'utf8', // 字符集
    'DB_DEBUG' => TRUE, // 数据库调试模式 开启后可以记录SQL日志 3.2.3新增

    'SHOW_PAGE_TRACE' => true,

    //0:普通模式
    //1：PATHINFO模式
    //2：Rewrite模式
    'URL_MODEL' => 2,

    // 设置禁止访问的模块列表
    //'MODULE_DENY_LIST'      =>  array('Common','Runtime','Home'),

    //=========设置允许访问的模块及默认模块=======//
    //    'MODULE_ALLOW_LIST'    =>    array('Home','Admin','User'),
    //=========关闭多模块访问============//
    //'MULTI_MODULE'          =>  false,
    'DEFAULT_MODULE'       =>    'Home',
    'DEFAULT_CONTROLLER'=>'Login',
    //=========设置URL是否区分大小写===========//
    'URL_CASE_INSENSITIVE' => false,

    //设置默认视图文件夹名称
    //'DEFAULT_V_LAYER'       =>  'Mobile', // 默认的视图层名称更改为Mobile

    //伪静态
    //'URL_HTML_SUFFIX'=>'shtml'

    'LOG_RECORD' => true, // 开启日志记录
    'LOG_LEVEL'  =>'EMERG,ALERT,CRIT,ERR', // 只记录EMERG ALERT CRIT ERR 错误
    'LOG_TYPE'              =>  'File',


    'domainName'=>'.speed.com', //主域名
    'LOAD_EXT_FILE'=>'USER'
);