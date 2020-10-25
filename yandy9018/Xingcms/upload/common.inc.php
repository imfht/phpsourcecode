<?php
session_start();
@ob_start();
define('APP_IN', true);
error_reporting(0);
@set_magic_quotes_runtime(0);

if (isset($_REQUEST['GLOBALS']))
	exit('Request tainting attempted.'); 
// 程序目录(有/)
define('WEB_ROOT', str_replace(array('\\', '//'), array('/', '/'), dirname(__FILE__) . DIRECTORY_SEPARATOR));
// 网站URL(无/)
define('WEB_URL', 'http://' . $_SERVER['HTTP_HOST'] . ($_SERVER['SERVER_PORT'] == 80 ? '' : ':' . $_SERVER['SERVER_PORT']) . substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/')));

// 当前文件名(无后缀)
define('FILE', basename($_SERVER['PHP_SELF'], '.php'));

// 网站访问路径，相对于域名
$PHP_SELF = explode("/", $_SERVER['PHP_SELF']);
unset($PHP_SELF[count($PHP_SELF)-1]);
define('WEB_PATH', implode("/", $PHP_SELF));
define('CACHE_DIR',WEB_ROOT.'cache/');  //缓存目录

//网站域名
define('WEB_DOMAIN', "");

// 网站访问路径，相对于域名
// 包含配置文件
include (WEB_ROOT . 'config.php');

// 时区设置
date_default_timezone_set('ETC/GMT'.TIMEZONE);
// 包含模版配置文件
include (INC_DIR . 'tql.inc.php');
include (INC_DIR . 'function.func.php');
include (INC_DIR.'common.func.php');
include (INC_DIR . 'simplehtmldom/simple_html_dom.php');

// 数据库连接
include (INC_DIR . 'Mysql.class.php');
$db = new Mysql($db_config);

//读取缓存
include (INC_DIR . 'cache_class.php');
$fzz = new fzz_cache;
if( !($fzz->_isset( "common_cache" )) ){
	$fzz->set("common_cache",display_common_cache(),CACHETIME);
}
$commoncache = $fzz->get("common_cache");

//初始化分类
include(INC_DIR.'tree.class.php');
$tree = new tree;

// 时间
$mtime = explode(' ', microtime());
define('TIMESTAMP', $mtime[1]);
define('MICROTIME', (float) $mtime[1] + (float) $mtime[0]);

// GPC过滤
define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
if (! MAGIC_QUOTES_GPC) {
	$_GET = _addslashes($_GET);
	$_POST = _addslashes($_POST);
	$_REQUEST = _addslashes($_REQUEST);
	$_COOKIE = _addslashes($_COOKIE);
}
$_GET = _filter($_GET);
$_POST = _filter($_POST);
$_REQUEST = _filter($_REQUEST);
$_COOKIE = _filter($_COOKIE);

?>