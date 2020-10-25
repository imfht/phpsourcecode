<?php
// +----------------------------------------------------------------------
// | IKPHP.COM [ I can do all the things that you can imagine ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2050 http://www.ikphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 小麦 <810578553@qq.com> <http://www.ikcms.cn>
// +----------------------------------------------------------------------

/**
 * UCenter客户端配置文件
 * 注意：该配置文件请使用常量方式定义
 */

define('UC_APP_ID', 1); //应用ID
define('UC_API_TYPE', 'Model'); //可选值 Model / Service
define('UC_AUTH_KEY', 'p"J#)[7?~8F>.jwm,=_ULk<a0NSf;DW%iv$-GhZE'); //加密KEY 千万要记住该Key 登录密码算法用的
define('UC_DB_DSN', 'mysql://root:@localhost:3306/ikphp'); // 数据库连接，使用Model方式调用API必须配置此项
define('UC_TABLE_PREFIX', 'ik_'); // 数据表前缀，使用Model方式调用API必须配置此项
