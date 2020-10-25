<?php
/**
 * 系统初始化程序，加载系统运行所需的各类文件及配置
 */
/** 定义常量 */
define('INIT_MEMORY', memory_get_usage()); //初始内存占用
define('INIT_TIME', microtime(true)); //初始运行时间
define('MOD_VERSION', '2.3.0'); //ModPHP 版本
defined('MOD_ZIP') or define('MOD_ZIP', ''); //ModPHP 压缩文件夹
defined('STDIN') or define('STDIN', fopen('php://stdin','r')); //基本输出
defined('STDOUT') or define('STDOUT', fopen('php://stdout','w')); //基本输出
defined('STDERR') or define('STDERR', fopen('php://stderr','w')); //基本错误
/** 以下常量不区分大小写 */
$file = MOD_ZIP ? dirname(realpath(MOD_ZIP)) : dirname(dirname(realpath(__DIR__)));
define('__ROOT__', str_replace('\\', '/', $file.'/'), true); //网站根目录
define('__CORE__', (MOD_ZIP ? 'zip://'.__ROOT__.MOD_ZIP.'#mod/' : __ROOT__.'mod/'), true); //内核目录
$file = str_replace('\\', '/', realpath($_SERVER['SCRIPT_FILENAME']));
define('__SCRIPT__', substr($file, strlen(__ROOT__)) ?: $file, true); //执行脚本

error_reporting(E_ALL & ~E_STRICT); //抑制严格性错误
if(version_compare(PHP_VERSION, '5.3.0') < 0) //ModPHP 需要运行在 PHP 5.3+ 环境
	exit('PHP version lower 5.3.0, unable to start ModPHP.');

if(__SCRIPT__ == 'mod/common/init.php') return false;

/** 加载核心函数文件 */
include_once __CORE__.'functions/extension.func.php';
include_once __CORE__.'functions/mod.func.php';

$GLOBALS['CORE'.INIT_TIME] = array(); //ZIP 内核文件列表
if(MOD_ZIP){
	foreach (zip_list(__ROOT__.MOD_ZIP, true) as $file) {
		if(strpos($file, 'mod/') === 0) $GLOBALS['CORE'.INIT_TIME][] = substr($file, 4);
	}
}else{
	$files = array_merge(glob(__ROOT__.'mod/*/*.php'), glob(__ROOT__.'mod/*/*/*.php'));
	foreach($files as $file){
		if(strpos($file, __CORE__) === 0) $GLOBALS['CORE'.INIT_TIME][] = substr($file, strlen(__CORE__));
	}
}

//自动加载类文件（优先从用户目录加载）
spl_autoload_register(function($class){
	$class1 = str_replace('\\', '/', strtolower($class)); //小写类名
	if(is_file($file = __ROOT__."user/classes/$class.class.php")){ //从用户目录按原类名导入
		include_once $file;
		return;
	}elseif(is_file($file = __ROOT__."user/classes/$class1.class.php")){ //从用户目录按小写类名导入
		include_once $file;
		return;
	}
	//引入某些类时先检查 PHP 扩展是否启用
	if($class1 == 'database' && !extension_loaded('pdo')) return;
	if($class1 == 'image' && !extension_loaded('gd')) return;
	if($class1 == 'socketserver' && !extension_loaded('sockets')) return;
	if(in_array($file = "classes/$class1.class.php", $GLOBALS['CORE'.INIT_TIME])){ //从内核目录加载
		include_once __CORE__.$file;
	}
});

$installed = config('mod.installed');
$database = database();

set_content_type('text/html'); //设置默认文档类型
ini_set('default_charset', 'UTF-8'); //设置默认脚本编码
ini_set('user_agent', 'ModPHP/'.MOD_VERSION); //设置 PHP 远程请求客户端
date_default_timezone_set(config('mod.timezone')); //设置默认时区
if(is_browser()){ //开/关调试模式
	ini_set('display_errors', config('mod.debug'));
	ini_set('display_startup_errors', config('mod.debug'));
	ini_set('html_errors', config('mod.debug'));
}

/** 加载默认函数文件 */
foreach ($GLOBALS['CORE'.INIT_TIME] as $file) {
	if(strpos($file, 'functions/') === 0){
		$basename = basename($file, '.func.php');
		if($basename == 'console' && !is_console())
			continue; //console.func.php 仅在交互式控制台中引入
		if(!$installed && $basename != 'user' && isset($database[$basename]))
			continue; //模块函数文件仅在系统安装后引入
		include_once __CORE__.$file;
	}
}

if($installed) register_module_functions(); //注册模块函数

//预初始化
$preinit = function() use($installed){
	/** 自动重定向至固定网站地址 */
	if(is_agent() && strpos(url(), site_url()) !== 0 && !is_proxy_server() && path_starts_with(str_replace('\\', '/', realpath($_SERVER['SCRIPT_FILENAME'])), __ROOT__))
		redirect(site_url().substr(url(), strlen(detect_site_url())), 301);

	/** 连接数据库 */
	if($installed){
		$conf = config('mod.database');
		database::open(0)
				->set('type', $conf['type'])
				->set('host', $conf['host'])
				->set('dbname', $conf['name'])
				->set('port', $conf['port'])
				->set('prefix', $conf['prefix'])
				->login($conf['username'], $conf['password']);
		if(database::$error) exit(database::$error); //遇到错误，终止程序
	}

	/** 配置 Session */
	$sess = config('mod.session');
	ini_set('session.gc_maxlifetime', $sess['maxLifeTime']*60); //生存期
	if($sess['name']) session_name($sess['name']); //Session 名称
	$path = $sess['savePath'];
	if($path){
		if($path[0] != '/' && $path[1] != ':') $path = __ROOT__.$path;
		session_save_path($path); //会话文件保存目录
	}

	if(is_agent()){
		/** 设置会话 Cookie */
		$url = parse_url(trim(site_url(), '/'));
		$path = isset($url['path']) ? $url['path'] : '/';
		session_set_cookie_params(0, $path); //客户端 Cookie 作用域
		$sname = session_name();
		$sid = !empty($_COOKIE[$sname]) ? $_COOKIE[$sname] : (isset($_REQUEST[$sname]) ? $_REQUEST[$sname] : '');
		if($sid){
			if(empty($_COOKIE[$sname])){ //如果不使用 Cookie 传输 Session ID
				session_id($sid);
				ini_set('session.use_cookies', 'off'); //则关闭使用 Cookie 的设置
			}
			session_start(); //被动开启 Session
		}

		/** 解析 URL 参数并填充 $_GET */
		if(__SCRIPT__ == 'mod.php'){
			$url = parse_url(url());
			if(isset($url['query']) && preg_match('/[_0-9a-zA-Z]+::.*/', $url['query'])){ //形式：obj::act|arg1:value1[|...]
				array_shift($_GET);
				$delimiter = strpos($url['query'], '|') ? '|' : '&'; //分隔符
				$arg = explode($delimiter, $url['query']);
				$arg[0] = explode('::', $arg[0]);
				$_GET['obj'] = $arg[0][0];
				$_GET['act'] = $arg[0][1];
				$arg = array_slice($arg, 1);
				foreach ($arg as $param) {
					$sep = strpos($param, ':') ? ':' : '='; //分隔符
					$param = explode($sep, $param);
					$_GET = array_merge($_GET, array($param[0] => isset($param[1]) ? $param[1] : ''));
				}
			}elseif(preg_match('/mod.php\/(.+)\/(.+)/i', $url['path'])){ //形式：obj/act/arg1/value1[/...]
				$url['path'] = substr(url(), strlen(site_url())+8);
				$url['path'] = explode('/', $url['path']);
				if(isset($url['path'][0], $url['path'][1])){
					$_GET['obj'] = $url['path'][0];
					$_GET['act'] = $url['path'][1];
					$count = count($url['path']);
					for ($i=2; $i < $count; $i += 2) { 
						$_GET = array_merge($_GET, array($url['path'][$i] => isset($url['path'][$i+1]) ? $url['path'][$i+1] : ''));
					}
				}
			}
		}

		//HTTP 访问认证
		if((config('mod.httpAuth') && !is_logined()) || (!$installed && !empty($_SERVER['PHP_AUTH_DIGEST']))){
			http_auth_login("HTTP Authentication", config('mod.httpAuth'));
		}
	}
};
$preinit();

/** 加载自定义函数文件 */
foreach (glob(__ROOT__.'user/functions/*.php') as $file) {
	include_once $file;
}

unset($installed, $database, $files, $file, $basename, $preinit); //释放变量

/** 加载模板函数文件 */
if(file_exists(template_path('functions.php'))) include_once template_path('functions.php');

init(); //执行系统初始化
function init(){
	/** 禁止客户端访问的方法列表 */
	global ${'DENIES'.INIT_TIME};
	${'DENIES'.INIT_TIME} = array(
		'file::open',
		'file::prepend',
		'file::append',
		'file::write',
		'file::insert',
		'file::output',
		'file::save',
		'file::getcontents',
		'file::getinfo',
		);

	/** 加载自动恢复程序 */
	if(config('mod.debug'))
		include_once __CORE__.'common/recover.php';

	/** 配置模板引擎 */
	template::$rootDir = __ROOT__;
	template::$rootDirURL = site_url();
	template::$saveDir = __ROOT__.config('mod.template.compiler.savePath');
	template::$extraTags = config('mod.template.compiler.extraTags');

	/** 系统初始化接口 */
	$init = array(
		'__DISPLAY__' => null //false 表示展示 404 页面，null 无操作
		);
	if(config('mod.installed'))
		do_hooks('mod.init', $init); //执行初始化回调函数

	/** 解析客户端请求，获取展示页面 */
	if(is_agent()){
		$tplPath = template_path('', false);
		$errPage = config('site.errorPage');
		$err403 = $tplPath.$errPage[403];
		$err404 = $tplPath.$errPage[404];
		$err500 = $tplPath.$errPage[500];
		if(__SCRIPT__ == 'index.php'){
			if($init['__DISPLAY__'] === false || !display_file()){
				display_file($err404, true);
			}elseif($init['__DISPLAY__']){
				display_file($init['__DISPLAY__'], true); //将初始化变量中的 __DISPLAY__ 设置为展示页面
			}
		}elseif(__SCRIPT__ == 'mod.php'){
			if(isset($_SERVER['HTTP_REFERER'])){ //通过来路页面获取展示页面
				$url = explode('?', $_SERVER['HTTP_REFERER']);
				if($url[0] == site_url('mod.php') || url() == site_url('mod.php')) {
					display_file($err403, true);
				}else if($init['__DISPLAY__'] === false){
					display_file($err404, true);
				}else{
					display_file($url[0]);
				}
			}
			if(isset($_GET['obj'], $_GET['act'])){
				$obj = strtolower($_GET['obj']);
				$act = $_GET['act'];
				//判断请求的操作是否合法
				if($obj != 'mod' && !is_subclass_of($obj, 'mod') || (!method_exists($obj, $act) && !is_callable(hooks($obj.'.'.$act))) || in_array($obj.'::'.strtolower($act), ${'DENIES'.INIT_TIME})){
					display_file($err403, true);
				}
			}else{
				display_file($err403, true);
			}
		}
	}
	if(!display_file()) display_file(__SCRIPT__, true);
}

/** 执行客户端请求 */
if(is_agent()){
	if(__SCRIPT__ == 'mod.php'){ //通过 URL 传参的方式执行类方法
		if(is_403() || is_404() || is_500()) goto display; //HTTP 错误跳转到显示页面
		conv_request_vars(); //转换表单请求参数
		$reqMd = $_SERVER['REQUEST_METHOD'];
		$act = $_GET['act'];
		if(!is_get() && !is_post()) $reqMd = 'REQUEST';
		do_hooks('mod.client.call', ${'_'.$reqMd}); //在执行类方法前执行挂钩回调函数
		$result = error() ?: $_GET['obj']::$act(${'_'.$reqMd}); //执行类方法并获取结果
		$result  = array_merge($result, array('obj'=>$_GET['obj'], 'act'=>$act));
		error(null); //清空错误信息
		do_hooks('mod.client.call.complete', $result); //在获取结果后执行回调函数
		set_content_type('application/json'); //设置文档类型为 json
		exit(json_encode($result)); //输出 JSON 结果
	}elseif(__SCRIPT__ == 'index.php'){ /** 载入模板文件 */
		display:
		do_hooks('mod.template.load'); //在载入模板前执行挂钩回调函数
		//错误处理
		if(is_403()) report_403();
		elseif(is_404()) report_404();
		elseif(is_500()) report_500();
		//载入模板
		if(!config('mod.template.compiler.enable')){
			include_once display_file(); //直接载入展示文件
		}else{
			${'FILE'.INIT_TIME} = template::$saveDir.substr(display_file(), 0, strrpos(display_file(), '.')).'.php';
			//通过文件的修改日期来判断模板是否被修改，从而决定是否需要重新编译
			if(config('mod.template.compiler.enable') !== 2 && file_exists(${'FILE'.INIT_TIME}) && filemtime(display_file()) <= filemtime(${'FILE'.INIT_TIME})){
				include_once ${'FILE'.INIT_TIME};
			}else{
				include_once template::compile(display_file()) ?: display_file();
			}
		}
		do_hooks('mod.template.load.complete'); //在模板加载后执行挂钩回调函数
	}
}