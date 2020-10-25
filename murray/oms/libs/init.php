<?php
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package 系统初始化
*/

defined('INPOP') or exit('Access Denied');
date_default_timezone_set("PRC");//设置时区
$PHP_TIME = time();//当前时间
//获取请求
$PHP_IP = ($_SERVER["HTTP_VIA"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"];
$PHP_IP = ($PHP_IP) ? $PHP_IP : $_SERVER["REMOTE_ADDR"];
$PHP_SELF = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : (isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : $_SERVER['ORIG_PATH_INFO']);
$PHP_QUERYSTRING = $_SERVER['QUERY_STRING'];
$PHP_DOMAIN = $_SERVER['HTTP_HOST'];
$PHP_REFERER = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
$PHP_SCHEME = $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
$PHP_URL = $PHP_SCHEME.$PHP_DOMAIN.$PHP_SELF.($PHP_QUERYSTRING ? '?'.$PHP_QUERYSTRING : '');
//安装路径
$ROOT_PATH = str_replace(strstr($PHP_SELF, BASE_FILENAME), "", $PHP_SELF);
//针对ACE定义基础地址
$PHP_SITEURL = $PHP_SCHEME.$PHP_DOMAIN."/";//.$ROOT_PATH;

//配置定义
define('CLASS_PATH', LIB_PATH.DS.'class'); //定义基础类库路径
define('PLUGIN_PATH', LIB_PATH.DS.'plugin'); //定义外挂类库路径
define('SERVICE_PATH', LIB_PATH.DS.'service'); //定义服务库路径
define('FUNC_PATH', LIB_PATH.DS.'func'); //定义基础函数库路径
define('CONTROL_PATH', 'controls'); //定义控制器目录
define('MODEL_PATH', 'models'); //定义模型目录
define('VIEW_PATH', 'views'); //定视图目录
define('SITEURL', $PHP_SITEURL);
define('ROOT_PATH', $ROOT_PATH ); //定义安装路径
define('STATIC_PATH', ROOT_PATH.'static/' ); //定义静态文件路径
define('STATIC_URL', SITEURL.'static/' ); //定义静态文件访问地址
define('UPLOAD_PATH', STATIC_PATH.'upload/' ); //定义上传文件路径
define('UPLOAD_URL', STATIC_URL.'upload/' ); //定义上传文件访问地址
require_once FUNC_PATH.DS.'base'.EXT;//载入基础函数库
require_once CLASS_PATH.DS.'autoloader'.EXT;//自动加载
require_once LIB_PATH.DS.'config_'.PLATFORM.EXT;//加载配置文件
define('TABLEPRE', $_config['db']['tablepre']); //数据表前缀
define('CACHE_PATH', $_config['sys']['cachepath']); //定义基础缓存路径
define('CACHE_MODEL_PATH', $_config['sys']['cachepath'].DS.'models'); //定义数据缓存路径
define('CACHE_VIEW_PATH', $_config['sys']['cachepath'].DS.'views'); //定义模板缓存路径
define('LOG_PATH', $_config['sys']['cachepath']); //定义日志路径
define('DEFAULT_END', $_config['sys']['default_end']); //定义默认展示端
define('DEFAULT_CONTROL', $_config['sys']['default_control']); //定义默认控制器
define('DEFAULT_ACTION', $_config['sys']['default_action']); //定义默认处理
define('PAGE_SIZE', $_config['sys']['pagesize']); //定义默认分页
define('DATA_TABLE_PRE', $_config['sys']['data_table_pre']); //定义数据表前缀
define('DATA_TABLE_INIT_SQL', $_config['sys']['data_table_init_sql']); //定义数据表初始化
define('DEFAULT_LOG', LOG_PATH.DS.$_config['sys']['default_log'].'.txt'); //定义默认日志
define('LOG_MODE', $_config['sys']['log_mode']); //定义日志类型
define('DB_SESSION', $_config['sys']['db_session']); //定义session类型
//对POST和GET过滤
$_POST = strip_sql($_POST);
$_GET = strip_sql($_GET);
$_COOKIE = strip_sql($_COOKIE);
//默认入口路径,是否开启重写
if($_config['sys']['rewrite']['enable']){
	define('SELF_PATH', ROOT_PATH);
	define('SELF_URL', SITEURL);
}else{
	define('SELF_PATH', ROOT_PATH.BASE_FILENAME."/");
	define('SELF_URL', SITEURL.BASE_FILENAME."/");
}
//启动
AutoLoader::getInstance();
?>