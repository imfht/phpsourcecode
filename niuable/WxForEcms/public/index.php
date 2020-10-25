<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');
define('EmpireCMSAdmin','1');
define('EmpireCMSNFPage','1');
require_once("./../../../class/connect.php");//引用后台管理工具包
require_once("./../../../class/db_sql.php");//引用数据库处理工具包
require_once("./../../../class/functions.php");//引用帝国函数集
require_once('./../../../config/config.php');
define("WEB_PATH", ECMS_PATH) ;//定义网站根路径，这里自定义是为了便于将来移植到其他平台
$link=db_connect();//链接数据库
$empire=new mysqlquery();//数据库操作实例
$editor = 2;

//验证用户
$lur=is_login();

$logininid=$lur['userid'];
$loginin=$lur['username'];
$loginrnd=$lur['rnd'];
$loginlevel=$lur['groupid'];
$loginadminstyleid=$lur['adminstyleid'];

//ehash
$ecms_hashur=hReturnEcmsHashStrAll();

// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
