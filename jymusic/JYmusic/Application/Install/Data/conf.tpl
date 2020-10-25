<?php
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+

/**
 * 系统配文件
 * 所有系统级别的配置
 */

$drive_conf=include './Application/Common/Conf/up_drive_config.php';
$route_conf = include './Application/Common/Conf/route_config.php';
$config =  array(
    /* 模块相关配置 */
    'AUTOLOAD_NAMESPACE' => array('Addons' => ONETHINK_ADDON_PATH), //扩展模块列表
    'MODULE_DENY_LIST'   => array('Common','Admin','Install'),
     // 允许访问的模块列表
	'MODULE_ALLOW_LIST'    =>    array('Home','Admin','User'),
	'DEFAULT_MODULE'       =>    'Home',  // 默认模块

    /* 系统数据加密设置 */
    'DATA_AUTH_KEY' => '[AUTH_KEY]', //默认数据加密KEY

    /* 用户相关设置 */
    'USER_MAX_CACHE'     => 1000, //最大缓存用户数
    'USER_ADMINISTRATOR' => 1, //管理员用户ID

    /* URL配置 */
    'URL_CASE_INSENSITIVE' => true, //默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'            => 1, //URL访问模式,可选参数0、1、2、3,代表以下四种模式：// 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE  模式); 3 (兼容模式)  默认为PATHINFO 模式
    //'VAR_URL_PARAMS'       => '', // PATHINFO URL参数变量
    //'URL_PATHINFO_DEPR'    => '/', //PATHINFO URL分割符

    /* 全局过滤配置 */
    'DEFAULT_FILTER' => '', //全局过滤函数

    /*更改普遍模板的起始标签和结束标签*/
    'TMPL_L_DELIM'    =>    '<{',
    'TMPL_R_DELIM'    =>    '}>',
    'TMPL_STRIP_SPACE'      =>  true,  

    /* 数据库配置 */
    'DB_TYPE'   => '[DB_TYPE]', // 数据库类型
    'DB_HOST'   => '[DB_HOST]', // 服务器地址
    'DB_NAME'   => '[DB_NAME]', // 数据库名
    'DB_USER'   => '[DB_USER]', // 用户名
    'DB_PWD'    => '[DB_PWD]',  // 密码
    'DB_PORT'   => '[DB_PORT]', // 端口
    'DB_PREFIX' => '[DB_PREFIX]', // 数据库表前缀
	
    //SQL解析缓存
    'DB_SQL_BUILD_CACHE' => true,
    'DB_SQL_BUILD_LENGTH' => 30, // SQL缓存的队列长度	
    /* 文档模型配置 (文档模型核心配置，请勿更改) */
    'DOCUMENT_MODEL_TYPE' => array(2 => '主题', 1 => '目录', 3 => '段落'),   
);
if(is_array($route_conf)) {
	 $configs  =  array_merge($config,$drive_conf,$route_conf);
}else{
	 $configs  = array_merge($config,$drive_conf);
}
return $configs;