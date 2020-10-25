<?php
//版权所有(C) 2014 www.ilinei.com

define('INIT', true);
define('SESSIONCACHEEXPIRE', 30);
define('ROOTPATH', substr(dirname(__FILE__), 0, -6));
define('MAGICQUOTESGPC', function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc());
define('ICONVENABLE', function_exists('iconv'));
define('MBENABLE', function_exists('mb_convert_encoding'));
define('EXTOBGZIP', function_exists('ob_gzhandler'));
define('TIMESTAMP', time());

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_STRICT);

session_cache_limiter('private, must-revalidate');
session_cache_expire(SESSIONCACHEEXPIRE);
session_start();

if(phpversion() < '5.3.0') set_magic_quotes_runtime(0);

require_once ROOTPATH.'/source/lang.php';
require_once ROOTPATH.'/source/config.php';
require_once ROOTPATH.'/source/function/common.php';

//时间
@date_default_timezone_set($config['timezone'] ? $config['timezone'] : 'Asia/Shanghai');

// 环境变量
$_var = array(
'm' => null,
'c' => null,
'e' => null,
'p' => null,
'ac' => null,
'op' => null,
'sid' => null,
'current' => null,
'timestamp' => time(),
'starttime' => get_microtime(),
'clientip' => get_client_ip(),
'referer' => '',
'cookie' => array(),
'auth' => '',
'auth_member' => ''
);

if(!MAGICQUOTESGPC){
    $_GET = eaddslashes($_GET);
    $_POST = eaddslashes($_POST);
    $_COOKIE = eaddslashes($_COOKIE);
    $_FILES = eaddslashes($_FILES);
}

foreach($_COOKIE as $key => $val) $_var['cookie'][$key] = $val;
foreach(array_merge($_POST, $_GET) as $k => $v) $_var['gp_'.$k] = $v;

$_var['sid'] = session_id();

$_var['m'] = empty($_var['gp_m']) ? '' : htmlspecialchars($_var['gp_m']);
$_var['c'] = empty($_var['gp_c']) ? '' : htmlspecialchars($_var['gp_c']);
$_var['e'] = empty($_var['gp_e']) ? '' : htmlspecialchars($_var['gp_e']);
$_var['p'] = empty($_var['gp_p']) ? '' : htmlspecialchars($_var['gp_p']);

$_var['ac'] = empty($_var['gp_ac']) ? '' : htmlspecialchars($_var['gp_ac']);
$_var['op'] = empty($_var['gp_op']) ? '' : htmlspecialchars($_var['gp_op']);

$_var['page'] = empty($_var['gp_page']) ? 1 : max(1, intval($_var['gp_page']));
$_var['psize'] = empty($_var['gp_psize']) ? 10 : (in_array($_var['gp_psize'] + 0, array(10, 20, 30, 50, 100, 500)) ? $_var['gp_psize'] : 10);

$_var['current'] = isset($_SESSION['_current']) ? unserialize($_SESSION['_current']) : null;

// 用户
if(cookie_get('auth')) $_var['auth'] = cookie_get('auth');
else{
    $_var['auth'] = md5($_SERVER['HTTP_USER_AGENT'].get_uuid().random(10));
    cookie_set('auth', $_var['auth'], time() + 86400 * 3000);
}

$_var['auth_member'] = cookie_get('auth_member');

spl_autoload_register('autoload');

$db = \ilinei\database::instance($config);

// 环境变量
$setting = cache_read('setting');
if(empty($setting) && $config['installed']){
    $db->connect($config['host']);

    $_setting = new \admin\model\_setting();

    $setting = $_setting->get();
    $setting = $_setting->format($setting);

    cache_write('setting', $setting);
}

// 站内日志
if($setting['ViewLog'] && !check_robot()) log_view();

if($config['output']['forceheader']){
    @header("content-Type:text/html; charset={$config[output][charset]}");
}

$page_title = $setting['SiteName']; //全局标题
?>