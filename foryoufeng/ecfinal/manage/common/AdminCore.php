<?php
define('IN_ECS', true);
define('ECS_ADMIN', true);

error_reporting(E_ALL);

/* 初始化设置 */
@ini_set('memory_limit',          '64M');
@ini_set('session.cache_expire',  180);
@ini_set('session.use_trans_sid', 0);
@ini_set('session.use_cookies',   1);
@ini_set('session.auto_start',    0);
@ini_set('display_errors',        0);

if (DIRECTORY_SEPARATOR == '\\')
{
    @ini_set('include_path',      '.;' . ROOT_PATH);
}
else
{
    @ini_set('include_path',      '.:' . ROOT_PATH);
}

if (file_exists('../data/config.php'))
{
    include('../data/config.php');
}
else
{
    exit('no /data/config.php');
}

if (defined('DEBUG_MODE') == false)
{
    define('DEBUG_MODE', 0);
}

if (PHP_VERSION >= '5.1' && !empty($timezone))
{
    date_default_timezone_set($timezone);
}

require(ROOT_PATH . 'includes/lib_base.php');
require(ROOT_PATH . 'includes/lib_common.php');
require(ROOT_PATH . 'includes/cls_ecshop.php');
include_once(ROOT_PATH . 'includes/lib_main.php');


/* 创建 ECSHOP 对象 */
$ecs = new ECS($db_name, $prefix);


/* 初始化数据库类 */
require(ROOT_PATH . 'includes/cls_mysql.php');
$db = new cls_mysql($db_host, $db_user, $db_pass, $db_name);

/* 初始化session */
require(ROOT_PATH . 'includes/cls_session.php');
$sess = new cls_session($db, $ecs->table('sessions'), $ecs->table('sessions_data'), 'ECSCP_ID');

if (!file_exists(ROOT_PATH.'temp/caches'))
{
    @mkdir(ROOT_PATH.'temp/caches', 0777,true);
    @chmod(ROOT_PATH.'temp/caches', 0777);
}

if (!file_exists(ROOT_PATH.'temp/compiled/admin'))
{
    @mkdir(ROOT_PATH.'temp/compiled/admin', 0777,true);
    @chmod(ROOT_PATH.'temp/compiled/admin', 0777);
}

clearstatcache();

/* 创建 Smarty 对象。*/
require(ROOT_PATH . 'includes/cls_template.php');
$smarty = new cls_template;

$smarty->template_dir  = ROOT_PATH . ADMIN_PATH . '/templates';
$smarty->compile_dir   = ROOT_PATH . 'temp/compiled/admin';
if ((DEBUG_MODE & 2) == 2)
{
    $smarty->force_compile = true;
}

header('content-type: text/html; charset=' . EC_CHARSET);
header('Expires: Fri, 14 Mar 1980 20:53:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

if ((DEBUG_MODE & 1) == 1)
{
    error_reporting(E_ALL);
}
else
{
    error_reporting(E_ALL ^ E_NOTICE);
}

include_once LIB.'AdminController.php';
class AdminCore{
    public static function run(){
        spl_autoload_register('AdminCore::load');
        register_shutdown_function('AdminCore::fatalError');
        set_error_handler('AdminCore::appError');
        set_exception_handler('AdminCore::appException');

    }
    public static function load($class){
        $controller=Controllers.$class.'.php';
        if(is_file($controller)){
            require_once $controller;
        }else{//记录日志并跳转
            // header('Location:/500.html');
        }
    }
    // 致命错误捕获
    public static  function fatalError() {
        $e=error_get_last();
        if($e &&$e['type']!=8192){
            if(DEBUG){
                var_dump(error_get_last());
            }
            header('Content-Type:application/json; charset=utf-8');
            exit(json_encode(['code' => 0, 'msg' => '服务器访问出错了', 'content' => null,'error'=>error_get_last()]));
        }

    }
    /**
     * 自定义异常处理
     * @access public
     * @param mixed $e 异常对象
     */
    public static function appException($e) {
        header('Content-Type:application/json; charset=utf-8');
        var_dump($e);
        exit(json_encode(['code' => 0, 'msg' => '程序异常了!!', 'content' => null]));
    }
    /**
     * 自定义错误处理
     * @access public
     * @param int $errno 错误类型
     * @param string $errstr 错误信息
     * @param string $errfile 错误文件
     * @param int $errline 错误行数
     * @return void
     */
    public static  function appError($errno, $errstr, $errfile, $errline) {
        if(DEBUG){
            echo  $errstr.'在'.$errfile.'在第'.$errline.'行';
        }
        //exit('程序出错了!!');
    }
}

AdminCore::run();
$class=isset($_REQUEST['c'])?trim($_REQUEST['c']):'home';
$class=ucfirst(strtolower($class));
//获取数据库配置信息
db_dns($db_host, $db_name,$db_user,$db_pass);
$db_host = $db_user = $db_pass = $db_name = NULL;
C('prefix',$prefix);
$controller=new $class($smarty);
$action=isset($_REQUEST['a'])?trim($_REQUEST['a']):'index';
$controller->$action();

