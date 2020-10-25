<?php

/**
 * MicroPHP
 * Description of test
 * An open source application development framework for PHP 5.2.0 or newer
 *
 * @package		MicroPHP
 * @author		狂奔的蜗牛
 * @email		672308444@163.com
 * @copyright	        Copyright (c) 2013 - 2013, 狂奔的蜗牛, Inc.
 * @link		http://git.oschina.net/snail/microphp
 * @createdtime         2014-4-23 11:27:04
 */

/**
 * MicroPHP HMVC 配置文件
 * 
 * 配置说明:
 * 
 *     1.该配置文件和入口文件里面的主配置一样。
 * 
 *     2.如果HMVC模块有自己的配置，这里就可以覆盖主配置。
 * 
 *     3.下面已经配置了一些必要的配置项目。
 * 
 *     4.如果HMVC模块需要其它不同于主配置的配置，那么取消配置前面的注释,然后配置即可。
 * 
 * 提醒：
 * 
 * 1.$system['my_loader']，$system['hmvc_folder']，$system['hmvc_modules']这三个配置项在HMVC配置中无效。
 * 
 * 2.HMVC模块共享主配置的：模型，helper和类库。当有重名时，优先使用HMVC模块的。
 * 
 */


/**
 * --------------------系统配置-------------------------
 */
$system['application_folder'] = dirname(__FILE__);

$system['controller_folder'] = $system['application_folder'] . '/controllers';

$system['model_folder'] = $system['application_folder'] . '/models';

$system['view_folder'] = $system['application_folder'] . '/views';

$system['library_folder'] = $system['application_folder'] . '/library';

$system['helper_folder'] = $system['application_folder'] . '/helper';

//$system['error_page_404'] = 'application/error/error_404.php';

//$system['error_page_50x'] = 'application/error/error_50x.php';

//$system['error_page_db'] = 'application/error/error_db.php';

//$system['message_page_view'] = '';

$system['default_controller'] = 'home';

$system['default_controller_method'] = 'index';

$system['controller_method_prefix'] = 'do';

$system['controller_file_subfix'] = '.php';

$system['model_file_subfix'] = '.model.php';

$system['view_file_subfix'] = '.view.php';

$system['library_file_subfix'] = '.class.php';

$system['helper_file_subfix'] = '.php';

$system['helper_file_autoload'] = array();

$system['library_file_autoload'] = array();

$system['models_file_autoload'] = array();

//$system['controller_method_ucfirst'] = TRUE;

//$system['autoload_db'] = FALSE;

$system['debug'] = TRUE;

//$system['error_manage'] = FALSE;

//$system['log_error'] = FALSE;

//$system['log_error_handle'] = array(
//    'error' => '',
//    'exception' => '',
//    'db_error' => '',
//);

//$system['default_timezone'] = 'PRC';


$system['route'] = array(
);

//$system['cache_drivers'] = array();

/*
$system['cache_config'] = array(
    "storage" => "auto",
    "path" => $system['application_folder'] . "/cache",
    "fallback" => array(
        "memcache" => "files",
        "memcached" => "files",
        "redis" => "files",
        "wincache" => "files",
        "xcache" => "files",
        "apc" => "files",
        "sqlite" => "files",
    ),
    "htaccess" => false,
    "server" => array(
        array("127.0.0.1", 11211, 1),
    //  array("new.host.ip",11211,1),
    ),
    "redis" => array(
        'type' => 'tcp',
        'prefix' => @$_SERVER['HTTP_HOST'],
        'sock' => '',
        'host' => '127.0.0.1',
        'port' => 6379,
        'password' => NULL,
        'timeout' => 0,
        'retry' => 100,
        'db' => 0,
    ),
);
*/

/**
 * -----------------------SESSION管理配置---------------------------
 */
/*
$system['session_handle'] = array(
    'handle' => '',
    'common' => array(
        'autostart' => true,
        'cookie_path' => '/',
        'cookie_domain' => '.' . @$_SERVER['HTTP_HOST'],
        'session_name' => 'PHPSESSID',
        'lifetime' => 3600,
    ),
    'mongodb' => array(
        'host' => '127.0.0.1',
        'port' => 27017,
        'user' => 'root',
        'password' => 'local',
        'database' => 'local',
        'collection' => 'session',
        'persistent' => false,
        'persistentId' => 'MongoSession',
        'replicaSet' => false,
    ),
    'mysql' => array(
        'host' => '127.0.0.1',
        'port' => 3306,
        'user' => 'root',
        'password' => 'admin',
        'database' => 'test',
        'table' => 'session_handler_table',
    ),
    'memcache' => "tcp://127.0.0.1:11211",
    'redis' => "tcp://127.0.0.1:6379",
);
*/
/**
 * ------------------------数据库配置----------------------------
 */

//$system['db']['active_group'] = 'default';

/**
 * dbdriver：可用的有mysql,mysqli,pdo,sqlite3,配置见下面
 */

/**
 * mysql数据库配置示例
 */
/*
$system['db']['default']['dbdriver'] = "mysql";
$system['db']['default']['hostname'] = '127.0.0.1';
$system['db']['default']['port'] = '3306';
$system['db']['default']['username'] = 'root';
$system['db']['default']['password'] = 'admin';
$system['db']['default']['database'] = 'test';
$system['db']['default']['dbprefix'] = '';
$system['db']['default']['pconnect'] = TRUE;
$system['db']['default']['db_debug'] = TRUE;
$system['db']['default']['char_set'] = 'utf8';
$system['db']['default']['dbcollat'] = 'utf8_general_ci';
$system['db']['default']['swap_pre'] = '';
$system['db']['default']['autoinit'] = TRUE;
$system['db']['default']['stricton'] = FALSE;
*/

/*
 * PDO database config demo
 * 1.pdo sqlite3
 * */
/**
 * sqlite3数据库配置示例
 */
/*
$system['db']['sqlite3']['dbdriver'] = "sqlite3";
$system['db']['sqlite3']['database'] = 'sqlite:d:/wwwroot/sdb.db';
$system['db']['sqlite3']['dbprefix'] = '';
$system['db']['sqlite3']['db_debug'] = TRUE;
$system['db']['sqlite3']['char_set'] = 'utf8';
$system['db']['sqlite3']['dbcollat'] = 'utf8_general_ci';
$system['db']['sqlite3']['swap_pre'] = '';
$system['db']['sqlite3']['autoinit'] = TRUE;
$system['db']['sqlite3']['stricton'] = FALSE;
*/

/**
 * PDO mysql数据库配置示例，hostname 其实就是pdo的dsn部分，
 * 如果连接其它数据库按着pdo的dsn写法连接即可
 */
/*
$system['db']['pdo_mysql']['dbdriver'] = "pdo";
$system['db']['pdo_mysql']['hostname'] = 'mysql:host=localhost;port=3306';
$system['db']['pdo_mysql']['username'] = 'root';
$system['db']['pdo_mysql']['password'] = 'admin';
$system['db']['pdo_mysql']['database'] = 'test';
$system['db']['pdo_mysql']['dbprefix'] = '';
$system['db']['pdo_mysql']['db_debug'] = TRUE;
$system['db']['pdo_mysql']['char_set'] = 'utf8';
$system['db']['pdo_mysql']['dbcollat'] = 'utf8_general_ci';
$system['db']['pdo_mysql']['swap_pre'] = '';
$system['db']['pdo_mysql']['autoinit'] = TRUE;
$system['db']['pdo_mysql']['stricton'] = FALSE;
*/

/**
 * -------------------------数据库配置结束--------------------------
 */
