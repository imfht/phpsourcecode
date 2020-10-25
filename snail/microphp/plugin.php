<?php
/*
 * Copyright 2015 狂奔的蜗牛.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * MicroPHP
 *
 * An open source application development framework for PHP 5.2.0 or newer
 *
 * @package       MicroPHP
 * @author        狂奔的蜗牛
 * @email         672308444@163.com
 * @copyright     Copyright (c) 2013 - 2015, 狂奔的蜗牛, Inc.
 * @link          http://git.oschina.net/snail/microphp
 * @since         Version 2.3.3
 * @createdtime   2015-09-02 18:24:59
 */
 

define('IN_WONIU_APP', TRUE);
define('WDS', DIRECTORY_SEPARATOR);
/**
 * --------------------系统配置-------------------------
 */
/**
 * 如果开启了URL Rewrite 功能，请在这里设置为TRUE，没有开启请设置为FALSE
 * 该配置会影响url()函数生成的链接
 */
$system['url_rewrite'] = FALSE;
/**
 * 程序文件夹路径名称，也就是所有的程序文件比如控制器文件夹，
 * 模型文件夹，视图文件夹等所在的文件夹名称。
 */
$system['application_folder'] = dirname(__FILE__) . '/' . 'application';
/**
 * 存放控制器文件的文件夹路径名称
 */
$system['controller_folder'] = $system['application_folder'] . '/controllers';
/**
 * 存放模型文件的文件夹路径名称,支持数组
 */
$system['model_folder'] = $system['application_folder'] . '/models';
/**
 * 存放视图文件的文件夹路径名称,支持数组
 */
$system['view_folder'] = $system['application_folder'] . '/views';
/**
 * 存放类库文件的文件夹路径名称,存放在该文件夹的类库中的类会自动加载,支持数组
 */
$system['library_folder'] = $system['application_folder'] . '/library';
/**
 * 存放函数文件的文件夹路径名称,支持数组
 */
$system['helper_folder'] = $system['application_folder'] . '/helper';
/**
 * table()方法缓存表字段信息的文件夹路径名称
 */
$system['table_cache_folder'] = $system['application_folder'] . '/cache';
/**
 * 存放HMVC模块的文件夹路径名称
 */
$system['hmvc_folder'] = $system['application_folder'] . '/modules';
/**
 * 注册HMVC模块，这里填写模块名称关联数组,键是url中的模块别名，值是模块文件夹名称
 */
$system['hmvc_modules'] = array('demo' => 'hmvc_demo');
/**
 * 404错误文件的路径,该文件会在系统找不到相关内容时显示,
 * 文件里面可以使用$msg变量获取出错提示内容
 */
$system['error_page_404'] = 'application/error/error_404.php';
/**
 * 系统错误文件的路径,该文件会在发生Fatal错误和Exeption时显示,
 * 文件里面可以使用$msg变量获取出错提示内容
 */
$system['error_page_50x'] = 'application/error/error_50x.php';
/**
 * 数据库错误文件的路径,该文件会在发生数据库错误时显示,
 * 文件里面可以使用$msg变量获取出错提示内容
 */
$system['error_page_db'] = 'application/error/error_db.php';
/**
 * $this->message()方法默认使用的视图，该视图会在第4个参数为null时使用。
 * 视图里面可以使用的有三个变量：$msg提示内容，$url跳转的url，$time停留时间。
 * 这里需要填写的是视图名称，不带视图后缀。没有就留空。
 */
$system['message_page_view'] = '';
/**
 * 默认控制器文件名称,不包含后缀,支持子文件夹,比如home.welcome,
 * 就是控制器文件夹下面的home文件夹里面welcome.php(假定后缀是.php)
 */
$system['default_controller'] = 'welcome';
/**
 * 默认控制器方法名称,不要带前缀
 */
$system['default_controller_method'] = 'index';
/**
 * 控制器方法名称前缀
 */
$system['controller_method_prefix'] = 'do';
/**
 * 控制器文件名称后缀,比如.php或者.controller.php
 */
$system['controller_file_subfix'] = '.php';
/**
 * 模型文件名称后缀,比如.model.php
 */
$system['model_file_subfix'] = '.model.php';
/**
 * 视图文件名称后缀,比如.view.php
 */
$system['view_file_subfix'] = '.view.php';
/**
 * 类库文件名称后缀,比如.class.php
 */
$system['library_file_subfix'] = '.class.php';
/**
 * 函数文件名称后缀,比如.php
 */
$system['helper_file_subfix'] = '.php';
/**
 * $this->input->setCookie()和$this->input->cookie()
 * 设置和获取cookie的时候key使用的前缀
 * 使用前缀的目的：
 * 避免主域名和子域名设置的cookie冲突
 */
$system['cookie_key_prefix'] = '';
/**
 * 自定义Loader，用于拓展框架核心功能,
 * Loader是控制器和模型都继承的一个类，大部分核心功能都在loader中完成。
 * 这里是自定义Loader类文件的完整路径
 * 自定义Loader文件名称和类名称必须是：
 * 文件名称：类名.class.php
 * 比如：MyLoader.class.php，文件里面的类名就是:MyLoader
 * 注意：
 * 1.自定义Loader必须继承MpLoader。
 * 2.一个最简单的Loader示意：(假设文件名称是：MyLoader.class.php)
 * class MyLoader extends MpLoader {
 *      public function __construct() {
 *          parent::__construct();
 *      }
 *  } 
 * 3.如果无需自定义Loader，留空即可。
 * 4.自定义Loader在框架核心文件被包含时生效，此后修改$system['my_loader']无效。
 */
$system['my_loader'] = '';
/**
 * 自动加载的helper文件,比如:array($item); 
 * $item是helper文件名,不包含后缀,比如: html 等.
 */
$system['helper_file_autoload'] = array();
/**
 * 自动加载的library文件,比如array($item); 
 * $item是library文件名或者"配置数组",不包含后缀,
 * 比如: ImageTool 或者配置数组array('ImageTool'=>'image'),或者配置数组array('ImageTool'=>'image','new'=>fasle)
 * 配置数组的作用是为长的类库名用别名代替.
 */
$system['library_file_autoload'] = array();
/**
 * 自动加载的model,比如array($item); 
 * $item是model文件名或者"配置数组",不包含后缀,
 * 比如: UserModel 或者配置数组 array('UserModel'=>'user')
 * 配置数组的作用是为长的model名用别名代替.
 */
$system['models_file_autoload'] = array();
/**
 * 控制器方法名称是否首字母大写,默认true
 */
$system['controller_method_ucfirst'] = TRUE;
/**
 * 是否自动连接数据库,默认FALSE
 */
$system['autoload_db'] = FALSE;
/**
 * 是否开启调试模式
 * true：显示错误信息,
 * false：所有错误将不显示
 */
$system['debug'] = TRUE;
/**
 * 是否接管错误信息显示
 * true：所有错误信息将由系统格式化输出
 * false：所有错误信息将原样输出
 */
$system['error_manage'] = FALSE;
/**
 * 是否开启错误日志记录
 * true：开启，如果开启了，系统将接管错误信息输出，忽略system['error_manage']和$system['db']['xxx']['db_debug']，
 *       同时务必设置自己的错误日志记录处理方法
 * false：关闭
 * 提示：
 * 数据库错误信息是否显示是由：$system['debug']和db_debug（$system['db']['xxx']['db_debug']）控制的。
 * 只用都为TRUE时才会显示。
 */
$system['log_error'] = FALSE;
/* * --------------------------------错误日志记录处理配置-----------------------
 * 错误日志记录处理方法，可以是一个“函数名称”或是“类的静态方法”用数组方式array('class_name'=>'method_name')。
 * 提示：
 * 1.如果是类，把类按着类库的命名方式命名，然后放到类库目录即可;
 * 2.如果是函数，把函数放到一个helper文件里面，然后在$system['helper_file_autoload']自动加载的helper文件里面填写上这个helper文件即可。
 * 3.留空则不处理。
 * 4.系统会传递给error、exception处理方法5个参数：（$errno, $errstr, $errfile, $errline,$strace）
 * 参数说明：
 * $errno：错误级别，就是PHP里面的E_NOTICE之类的静态变量，错误级别和具体含义对应关系如下，键是代码，值是代码含义。
 *         array('0'=>'EXCEPTION',//异常信息
 *               '1' => 'ERROR',//致命的运行时错误。这类错误一般是不可恢复的情况，例如内存分配导致的问题。后果是导致脚本终止不再继续运行。
 *               '2' => 'WARNING', //运行时警告 (非致命错误)。仅给出提示信息，但是脚本不会终止运行。
 *               '4' => 'PARSE', //编译时语法解析错误。解析错误仅仅由分析器产生。
 *               '8' => 'NOTICE', //运行时通知。表示脚本遇到可能会表现为错误的情况，但是在可以正常运行的脚本里面也可能会有类似的通知。
 *               '16' => 'CORE_ERROR', //在PHP初始化启动过程中发生的致命错误。该错误类似 E_ERROR，但是是由PHP引擎核心产生的。
 *               '32' => 'CORE_WARNING',//PHP初始化启动过程中发生的警告 (非致命错误) 。类似 E_WARNING，但是是由PHP引擎核心产生的。
 *               '64' => 'COMPILE_ERROR', //致命编译时错误。类似E_ERROR, 但是是由Zend脚本引擎产生的。
 *               '128' => 'COMPILE_WARNING', //编译时警告 (非致命错误)。类似 E_WARNING，但是是由Zend脚本引擎产生的。
 *               '256' => 'USER_ERROR', //用户产生的错误信息。类似 E_ERROR, 但是是由用户自己在代码中使用PHP函数 trigger_error()来产生的。
 *               '512' => 'USER_WARNING', //用户产生的警告信息。类似 E_WARNING, 但是是由用户自己在代码中使用PHP函数 trigger_error()来产生的。
 *               '1024' => 'USER_NOTICE',//用户产生的通知信息。类似 E_NOTICE, 但是是由用户自己在代码中使用PHP函数 trigger_error()来产生的。
 *               '2048' => 'STRICT', //启用 PHP 对代码的修改建议，以确保代码具有最佳的互操作性和向前兼容性。
 *               '4096' => 'RECOVERABLE_ERROR'//可被捕捉的致命错误。 它表示发生了一个可能非常危险的错误，但是还没有导致PHP引擎处于不稳定的状态。 如果该错误没有被用户自定义句柄捕获 (参见 set_error_handler())，将成为一个 E_ERROR　从而脚本会终止运行。
 *               '8192' => 'DEPRECATED', //（php5.3）运行时通知。启用后将会对在未来版本中可能无法正常工作的代码给出警告。
 *               '16384' => 'USER_DEPRECATED', //（php5.3）用户产少的警告信息。 类似 E_DEPRECATED, 但是是由用户自己在代码中使用PHP函数 trigger_error()来产生的。
 *         );
 *         可以通过判断错误级别，然后有针对性的处理。一般我们需要处理的就是致命错误（0，1，4）和一般错误（2，8，2048，8192）.
 * $errstr：具体的错误信息
 * $errfile：出错的文件完整路径
 * $errline：出错的行号
 * $strace： 调用堆栈信息
 * 系统会传递给db_error处理方法2个参数：（$errmsg,$strace）
 * 参数说明：
 * $errmsg：具体的数据库错误信息
 * $strace：调用堆栈信息
 * 错误控制类参考：
 * /blob/development/tests/app/library/ErrorHandle.class.php
 */
$system['log_error_handle'] = array(
	'error' => '', //array('ErrorHandle' => 'error_handle'),
	'exception' => '', //array('ErrorHandle' => 'exception_handle'),
	'db_error' => '', //array('ErrorHandle' => 'db_error_handle')
);
/**
 * 默认时区,PRC是中国
 */
$system['default_timezone'] = 'PRC';
/**
 * ---------------------------自定义URL路由规则------------------------
 * 比如：
 *   (1).http://localhost/index.php?welcome.index
 *   (2).http://localhost/index.php/welcome.index
 *   (3).http://localhost/index.php?news/welcome.index
 *   (4).http://localhost/index.php/news/welcome.index
 * 路由字符串是welcome.index(不包含最前面的?、/、模块名称)，路由规则都是针对“路由字符串”的。
 * 现在定义路由规则：
 *   $system['route']=array(
 *        "/^welcome\\/?(.*)$/u"=>'welcome.ajax/$1'
 *   );
 * 路由规则说明：
 *  1.路由规则是一个关联数组
 *  2.数组的key是匹配“路由字符串”的正则表达式，其实就是preg_match的第一个参数。
 *  3.数组的value是替换后的路由字符串
 *  4.系统使用的url路由就是最后替换后的路由字符串
 */
$system['route'] = array(
	//'|^([^/]+)/(.+)$|u' => '$1.$2',//index.php/home/index路由支持
	//'|^([^/]+)/([^/]+)/(.+)$|u' => '$1.$2.$3',//index.php/user/home/index路由支持
);
/**
 * ---------------------缓存配置-----------------------
 */
/**
 * 自定义缓存类文件的路径是$system['cache_drivers']的一个元素，
 * 可以有多个自定义缓存类。
 * 缓存类文件名称命名规范是：
 * 比如文件名是mycahe.php,那么文件mycahe.php
 * 里面的缓存类就是：class phpfastcache_mycahe{......}
 * mycahe.php的编写规范请参考：
 * /blob/development/modules/cache-drivers/drivers/example.php
 */
$system['cache_drivers'] = array();
/**
 * 缓存配置项
 */
$system['cache_config'] = array(
	/*
	 * 默认存储方式
	 * 可用的方式有：auto,apc,files,sqlite,memcached,redis,wincache,xcache,memcache
	 * auto自动模式寻找的顺序是 : apc,files,sqlite,memcached,redis,wincache,xcache,memcache
	 */
	"storage" => "auto",
	/*
	 * 默认缓存文件存储的路径
	 * 使用绝对全路径，比如： /home/username/cache
	 * 留空，系统自己选择
	 */
	"path" => $system['application_folder'] . "/cache", // 缓存文件存储默认路径,使用files、sqlite缓存的时候确保文件夹存在而且可写
	/*
	 * 第二驱动
	 * 比如：当你现在在代码中使用的是memcached, apc等等，然后你的代码转移到了一个新的服务器而且不支持memcached 或 apc
	 * 这时候怎么办呢？设置第二驱动即可，当你设置的驱动不支持的时候，系统就使用第二驱动。
	 * $key是你设置的驱动，当设置的“storage”=$key不可用时，就使用$key对应的$value驱动
	 */
	"fallback" => array(
		"memcache" => "files",
		"memcached" => "files",
		"redis" => "files",
		"wincache" => "files",
		"xcache" => "files",
		"apc" => "files",
		"sqlite" => "files",
	),
	/*
	 * Memcache服务器地址;
	 */
	"server" => array(
		array("127.0.0.1", 11211, 1),
	),
	/*
	 * Redis服务器地址;
	 */
	"redis" => array(
		'type' => 'tcp', //sock,tcp;连接类型，tcp：使用host port连接，sock：本地sock文件连接
		'prefix' => @$_SERVER['HTTP_HOST'], //key的前缀，便于管理查看，在set和get的时候会自动加上和去除前缀，无前缀请保持null
		'sock' => '', //sock的完整路径
		'host' => '127.0.0.1',
		'port' => 6379,
		'password' => NULL, //密码，如果没有,保持null
		'timeout' => 0, //0意味着没有超时限制，单位秒
		'retry' => 100, //连接失败后的重试时间间隔，单位毫秒
		'db' => 0, // 数据库序号，默认0, 参考 http://redis.io/commands/select
	),
);
/**
 * -----------------------SESSION管理配置---------------------------
 */
$system['session_handle'] = array(
	'handle' => '', //支持的管理类型：mongodb,mysql,memcache,memcached,redis。留空则不管理，使用默认
	'common' => array(//SESSION公共配置，无论是否使用handle托管session，common配置都会起作用。
		'autostart' => false, //是否自动session_start()
		'cookie_path' => '/',
		'cookie_domain' => empty($_SERVER['HTTP_HOST']) ? null : $_SERVER['HTTP_HOST'],
		'session_name' => 'MICROPHP',
		'lifetime' => 3600, // session lifetime in seconds
	),
	'mongodb' => array(
		'host' => '127.0.0.1',
		'port' => 27017,
		'user' => 'root',
		'password' => 'local',
		'database' => 'local', // name of MongoDB database
		'collection' => 'session', // name of MongoDB collection
		'persistent' => false, // persistent connection to DB?
		'persistentId' => 'MongoSession', // name of persistent connection
		'replicaSet' => false,
	),
	/**
	 * mysql表结构
	 *   CREATE TABLE `session_handler_table` (
	  `id` varchar(255) NOT NULL,
	  `data` mediumtext NOT NULL,
	  `timestamp` int(255) NOT NULL,
	  PRIMARY KEY (`id`),
	  UNIQUE KEY `id` (`id`,`timestamp`),
	  KEY `timestamp` (`timestamp`)
	  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	 */
	'mysql' => array(
		'host' => '127.0.0.1',
		'port' => 3306,
		'user' => 'root',
		'password' => 'admin',
		'database' => 'test',
		'table' => 'session_handler_table',
	),
	/**
	 * memcached采用的是session.save_handler管理机制
	 * 需要php安装memcached拓展支持
	 */
	'memcached' => "127.0.0.1:11211",
	/**
	 * memcache采用的是session.save_handler管理机制
	 * 需要php安装memcache拓展支持
	 */
	'memcache' => "tcp://127.0.0.1:11211",
	/**
	 * redis采用的是session.save_handler管理机制
	 * 需要php安装redis拓展支持,你可以在https://github.com/nicolasff/phpredis 找到该拓展。
	 */
	'redis' => "tcp://127.0.0.1:6379",
);
/**
 * ------------------------数据库配置----------------------------
 */
/**
 * 默认使用的数据库组名称，名称就是下面的$system['db'][$key]里面的$key，
 * 可以自定义多个数据库组，然后根据不同的环境选择不同的组作为默认数据库连接信息
 */
$system['db']['active_group'] = 'default';
/**
 * dbdriver：可用的有mysql,mysqli,pdo,sqlite3,配置见下面
 */
/**
 * mysql数据库配置示例,如果用mysqli，把下面的dbdriver驱动由mysql改成mysqli即可
 */
$system['db']['default']['dbdriver'] = "mysql";
$system['db']['default']['hostname'] = '127.0.0.1';
$system['db']['default']['port'] = '3306';
$system['db']['default']['username'] = 'root';
$system['db']['default']['password'] = 'admin';
$system['db']['default']['database'] = 'test';
$system['db']['default']['dbprefix'] = '';
$system['db']['default']['pconnect'] = FALSE;
$system['db']['default']['db_debug'] = TRUE;
$system['db']['default']['char_set'] = 'utf8';
$system['db']['default']['dbcollat'] = 'utf8_general_ci';
$system['db']['default']['swap_pre'] = '';
$system['db']['default']['autoinit'] = TRUE;
$system['db']['default']['stricton'] = FALSE;
/*
 * PDO database config demo
 * 1.pdo sqlite3
 * */
/**
 * sqlite3数据库配置示例
 */
$system['db']['sqlite3']['dbdriver'] = "sqlite3";
$system['db']['sqlite3']['database'] = 'sqlite:d:/wwwroot/sdb.db';
$system['db']['sqlite3']['dbprefix'] = '';
$system['db']['sqlite3']['db_debug'] = TRUE;
$system['db']['sqlite3']['char_set'] = 'utf8';
$system['db']['sqlite3']['dbcollat'] = 'utf8_general_ci';
$system['db']['sqlite3']['swap_pre'] = '';
$system['db']['sqlite3']['autoinit'] = TRUE;
$system['db']['sqlite3']['stricton'] = FALSE;
/**
 * PDO mysql数据库配置示例，hostname 其实就是pdo的dsn部分，
 * 如果连接其它数据库按着pdo的dsn写法连接即可
 */
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
/**
 * -------------------------数据库配置结束--------------------------
 */

/* End of file index.php */
include('MicroPHP.min.php');
MpRouter::setConfig($system);