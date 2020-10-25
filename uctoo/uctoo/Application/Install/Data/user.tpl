<?php
// +----------------------------------------------------------------------
// | UCToo [ Universal Convergence Technology ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2017 http://uctoo.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Patrick <contact@uctoo.com>
// +----------------------------------------------------------------------

/**
 * UCenter客户端配置文件
 * 注意：该配置文件请使用常量方式定义
 */

define('UC_APP_ID', 1); //应用ID
define('UC_API_TYPE', 'Model'); //可选值 Model / Service
define('UC_AUTH_KEY', '[AUTH_KEY]'); //加密KEY
define('UC_DB_DSN', '[type]://[username]:[password]@[hostname]:[hostport]/[database]'); // 数据库连接，使用Model方式调用API必须配置此项
define('UC_TABLE_PREFIX', '[prefix]'); // 数据表前缀，使用Model方式调用API必须配置此项