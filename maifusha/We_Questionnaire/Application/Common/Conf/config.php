<?php
return array(
    /* 超级管理员配置 */
    'super_user'            =>  'admin',  //账号
    'super_password'        =>  'admin',  //密码

    /* 数据库配置 */
    'DB_NAME'               =>  'questionnaire',  // 数据库名
    'DB_USER'               =>  'questionnaire',  // 用户名
    'DB_PWD'                =>  '1qaz2wsx3edc', // 密码
    'DB_TYPE'               =>  'mysql',  // 数据库类型
    'DB_HOST'               =>  'localhost',  // 服务器地址
    'DB_PORT'               =>  '3306',  // 端口
    'DB_CHARSET'            =>  'utf8',  // 数据库编码默认采用utf8
    'DB_PREFIX'             =>  '',  // 数据库表前缀
    'DB_DEBUG'              =>  TRUE,  // 数据库调试模式 开启后可以记录SQL日志
    'DB_FIELDS_CACHE'       =>  true,  // 启用字段缓存
    'DB_DEPLOY_TYPE'        =>  0,  // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'DB_RW_SEPARATE'        =>  false,  // 数据库读写是否分离 主从式有效
    'DB_MASTER_NUM'         =>  1,  // 读写分离后 主服务器数量
    'DB_SLAVE_NO'           =>  '',  // 指定从服务器序号

    'URL_MODEL'             =>  2,       // URL访问模式,可选参数0、1、2、3,代表以下四种模式：

    'MODULE_ALLOW_LIST'     =>  array('Admin', 'WebService', 'Weixin', 'Common'),
    'DEFAULT_MODULE'        =>  'Admin',  // 默认模块, 必须结合上一行设置使用

);