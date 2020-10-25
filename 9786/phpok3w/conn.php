<?php

define('DT_DEBUG',1);
define('IN_ADMIN',false);
define('IN_SYSTEM', true);


$CFG['timezone'] = 'Etc/GMT-8';
$CFG['timediff'] = '0';
$CFG['file_mod'] = 0777;
$CFG['db_charset'] = 'utf8';
$CFG['cache_dir'] = '';
$CFG['authkey'] = 'KebfvjGbGr9ebR3';
$CFG['database'] = 'mysql';

$CFG['cookie_pre'] = 'ok3w_';
$CFG['cookie_domain'] = '';
$CFG['cookie_path'] = '/';

define('DT_KEY', $CFG['authkey']);
$CFG['authadmin'] = 'cookie';
$secretkey = 'admin_'.strtolower(substr(DT_KEY, -6));

$DT=array();
$DT['login_log']= '1';
$CFG['tb_pre']='ok3w_';
$DT_PRE = $CFG['tb_pre'];

define('DT_VERSION', '0.1');
define('DT_RELEASE', '20150321');

define('DT_ROOT', str_replace("\\", '/', dirname(__FILE__)));
require DT_ROOT.'/config.php';
require DT_ROOT.'/global.func.php';
require DT_ROOT.'/file.func.php';

if (isset($_REQUEST['GLOBALS']) || isset($_FILES['GLOBALS'])) exit('Request Denied');
@set_magic_quotes_runtime(0);
$MQG = get_magic_quotes_gpc();

foreach (array('_POST', '_GET', '_COOKIE') as $__R)
{
    if ($$__R)
    {
        foreach ($$__R as $__k => $__v)
        {
            if (isset($$__k) && $$__k == $__v) unset($$__k);
        }
    }
}
if (!$MQG)
{
    if ($_POST) $_POST = daddslashes($_POST);
    if ($_GET) $_GET = daddslashes($_GET);
    if ($_COOKIE) $_COOKIE = daddslashes($_COOKIE);
}
if (!empty($_SERVER['REQUEST_URI'])) strip_uri($_SERVER['REQUEST_URI']);
if ($_POST)
{
    $_POST = strip_sql($_POST);
    strip_key($_POST);
}
if ($_GET)
{
    $_GET = strip_sql($_GET);
    strip_key($_GET);
}
if ($_COOKIE)
{
    $_COOKIE = strip_sql($_COOKIE);
    strip_key($_COOKIE);
}
if ($_POST) extract($_POST, EXTR_SKIP);
if ($_GET) extract($_GET, EXTR_SKIP);

if(function_exists('date_default_timezone_set')) date_default_timezone_set($CFG['timezone']);
$DT_TIME = time() + $CFG['timediff'];



if (DT_DEBUG)
{
    error_reporting(E_ALL);
    $mtime = explode(' ', microtime());
    $debug_starttime = $mtime[1] + $mtime[0];
} else
{
    error_reporting(0);
}



header("Content-Type:text/html;charset=" . $CFG['charset']);
define('DT_WIN', strpos(strtoupper(PHP_OS), 'WIN') !== false ? true: false);
define('DT_CHMOD', ($CFG['file_mod'] && !DT_WIN) ? $CFG['file_mod'] : 0);
define('DT_CACHE', $CFG['cache_dir'] ? $CFG['cache_dir'] : DT_ROOT.'/file/cache');

require DT_ROOT . '/include/db_' . $CFG['database'] . '.class.php';


$DT_IP = get_env('ip');

$db_class = 'db_' . $CFG['database'];
$db = new $db_class;
$db->pre = $CFG['tb_pre'];

$db->connect($CFG['db_host'], $CFG['db_user'], $CFG['db_pass'], $CFG['db_name'], $CFG['db_expires'], $CFG['db_charset'], $CFG['pconnect']);
?>