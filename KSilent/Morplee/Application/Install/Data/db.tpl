<?php

/**
 * UCenter客户端配置文件
 * 注意：该配置文件请使用常量方式定义
 */

define('DB_APP_ID', 1); //应用ID
define('DB_API_TYPE', 'Model'); //可选值 Model / Service
define('DB_AUTH_KEY', '[AUTH_KEY]'); //加密KEY
define('DB_DB_DSN', '[DB_TYPE]://[DB_USER]:[DB_PWD]@[DB_HOST]:[DB_PORT]/[DB_NAME]'); // 数据库连接，使用Model方式调用API必须配置此项
define('DB_TABLE_PREFIX', '[DB_PREFIX]'); // 数据表前缀，使用Model方式调用API必须配置此项
