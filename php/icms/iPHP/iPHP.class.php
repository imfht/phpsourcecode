<?php
/**
 * iPHP - i PHP Framework
 * Copyright (c) iiiPHP.com. All rights reserved.
 *
 * @author iPHPDev <master@iiiphp.com>
 * @website http://www.iiiphp.com
 * @license http://www.iiiphp.com/license
 * @version 2.1.0
 */
class iPHP {
	public static $apps       = null;
	public static $app        = null;
	public static $app_name   = null;
	public static $app_do     = null;
	public static $app_method = null;
	public static $app_tpl    = null;
	public static $app_path   = null;
	public static $app_file   = null;
	public static $app_args   = null;
	public static $app_vars   = null;
	public static $app_dirs   = array();
	public static $class_name = null;

	public static $mobile     = false;
	public static $time_start = false;
	public static $callback   = array();
	public static $is_callable= false;

	public static $handler    = array(
		'autoload' => array('iPHP', 'autoload'),
		'error'    => array('iPHP', 'error_handler'),
	);
	public static $reserved   = array('API','ACTION','DO','MY');

	public static function bootstrap(){
		self::timer_start();
		@ini_set('magic_quotes_sybase', 0);
		@ini_set("magic_quotes_runtime",0);
		@ini_set("magic_quotes_gpc",0);
		@header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
		if(function_exists('ini_get')) {
		    $memorylimit = @ini_get('memory_limit');
		    if($memorylimit && get_bytes($memorylimit) < 33554432 && function_exists('ini_set')) {
		        ini_set('memory_limit', iPHP_MEMORY_LIMIT);
		    }
		}
		date_default_timezone_set(iPHP_TIME_ZONE);
		set_error_handler(self::$handler['error'],E_ALL & ~E_NOTICE);
		spl_autoload_register(self::$handler['autoload'], true, true);
		// self::composer();
		//security
		iSecurity::filter();
		iSecurity::globals('page','GP',2);
		iDefine::request();
		//waf
		iWAF::filter();
	}
	public static function autoload($class,$core=null){
		$require = self::auto_require($class);
		if (!$require) {
			$autoload_finish = true;
			$functions = spl_autoload_functions();
			if($functions)foreach ($functions as $key => $autoload) {
				if($autoload!=iPHP::$handler['autoload']){
					$autoload_finish = false;
					spl_autoload_register($autoload);
				}
			}
			// iPHP::callback 不提示
			if(self::$is_callable){
				self::$is_callable = false;
				return false;
			}
			if (iPHP_DEBUG) {
				$autoload_finish && self::error_throw("Class '$class' not found", '0020');
			}else{
				return false;
			}
		}
	}
	public static function runit($it=null){
		empty($it) && $it = array(iPHP_APP,'init');
		iPHP_APP_INIT && self::callback($it);
	}
	public static function config($site=null) {
		$config = self::sys_config($site);
		//config.php 中开启后 此处设置无效
		iDefine::debug($config['debug']);
		iDefine::datatime($config['time']);
		iDefine::router($config['router']);

		self::define_apps($config['apps']);
		//config.php --END--
		defined("iPHP_INIT_CONFIG") && self::callback(iPHP_INIT_CONFIG,array(&$config));
		return $config;
	}
    public static function sys_config($site=null) {
		if(defined("iPHP_INIT_SYSCONFIG")){
			return self::callback(iPHP_INIT_SYSCONFIG);
		}
		if($site===null){
			$site = iPHP_MULTI_SITE ? $_SERVER['HTTP_HOST'] : iPHP_APP;
			if (iPHP_MULTI_DOMAIN) {
				preg_match("/[^\.\/][\w\-]+\.[^\.\/]+$/", $site, $matches);//只绑定主域
				$site = $matches[0];
			}
			strpos($site, '..') === false OR self::error_throw('What are you doing','001');
		}

		if(!defined('iPHP_APP_SITE')){
			$site OR self::error_throw('Please define iPHP_APP_SITE ', '0000');
			define('iPHP_APP_SITE', $site);
		}

		define('iPHP_APP_CONF', iPHP_CONF_DIR . '/' . iPHP_APP_SITE); //网站配置目录
		define('iPHP_APP_CONFIG', iPHP_APP_CONF . '/config.php'); //网站配置文件
		is_file(iPHP_APP_CONFIG) OR self::error_throw('Unable to find "' . iPHP_APP_SITE . '" config file ('.iPHP_APP_CONFIG.').Please install '.iPHP_APP, '0001');
		$config = require_once iPHP_APP_CONFIG;
		defined('iPHP_APP_SITE') && $config['cache']['prefix'] = iPHP_APP_SITE;
		return $config;
    }
    public static function define_apps($conf) {
        self::$apps = $conf;
        empty(self::$apps) && self::$apps = self::callback(self::$callback['config']['apps']);
        if(is_array(self::$apps)){
            foreach (self::$apps as $_app => $_appid) {
                $_app && define(iPHP_APP.'_APP_'.strtoupper($_app),$_appid);
            }
        }
    }
    /**
     * [define_device]
     * @param  string  $device [设备标识]
     * @param  boolean $mobile [是否移动设设备]
     * @return [type]          [description]
     */
    public static function define_device($device='',$mobile=false) {
        defined('iPHP_DEVICE') OR define('iPHP_DEVICE', $device);
        defined('iPHP_MOBILE') OR define('iPHP_MOBILE', $mobile);
    }
	public static function run($app = NULL, $do = NULL, $args = NULL, $prefix = "do_") {
		empty($app) && $app = iSecurity::request('app'); //单一入口
		if (empty($app)) {
			$fi = iFS::name(iPHP_SELF);
			$app = $fi['name'];
		}
		empty($app) && $app = 'index';

		self::$apps OR iPHP::error_404('Please update the application cache');

		$file = $app.'.app.php';
		$dir  = $app;
		if(strpos($app,'_') !== false) {
			list($dir,$sub) = explode('_', $app);
		}
		self::$apps[$dir] OR iPHP::error_404('Unable to find application <b>' . var_export($app,true) . '</b>', '0001');

		self::$app_path = iPHP_APP_DIR . '/' . $dir;
		self::$app_file = self::$app_path . '/' . $file;
		//自定义APP调用
		//并初始化 iPHP::$app,iPHP::$app_file
		is_file(self::$app_file) OR self::callback(array('contentApp','run'),array($app));
		is_file(self::$app_file) OR iPHP::error_404('Unable to find application <b>' . $file . '</b>', '0002');

		self::$class_name = $app.'App';

		if ($do === NULL) {
			$do = iPHP_APP;
			$_GET['do'] && $do = iSecurity::escapeStr($_GET['do']);
		}
		if ($_POST['action']) {
			$do     = iSecurity::escapeStr($_POST['action']);
			$prefix = 'ACTION_';
		}
		self::$app_name   = $app;
		self::$app_do     = $do;
		self::$app_method = $prefix . $do;
        self::define_device();
		self::callback(self::$callback['run']['begin']);
		if(self::$app===null){
			$flag = self::app_startup($app,$prefix);
			self::callback(self::$callback['run']['init'],array(self::$app));
		}

		if (self::$app_do && self::$app->methods) {
			$method = self::$app_method;
			if(!in_array(self::$app_do, self::$app->methods)){
				iPHP::error_404('Call to undefined method <b>' . self::$class_name . '::'.$method.'</b>', '0003');
			}
			$args === null && $args = self::$app_args;
			self::callback(self::$callback['run']['call'],array(self::$app,$method,$args));

			$init_method   = '__init__';
			$prefix_start  = $prefix.'init';
			$before_method = 'before_'.$method;
			$after_method  = 'after_'.$method;

			if ($args) {
				if ($args === 'object') return self::$app;

				method_exists(self::$app, $init_method)   && call_user_func_array(array(self::$app, $init_method), (array) $args);
				method_exists(self::$app, $prefix_start)  && call_user_func_array(array(self::$app, $prefix_start), (array) $args);
				method_exists(self::$app, $before_method) && call_user_func_array(array(self::$app, $before_method), (array) $args);
				return call_user_func_array(array(self::$app, $method), (array) $args);
			} else {
				if(!method_exists(self::$app, $method)){
					iPHP::error_404('Call to undefined method <b>' . self::$class_name . '::'.$method.'</b>', '0004');
				}
				method_exists(self::$app, $init_method)   && self::$app->$init_method();
				method_exists(self::$app, $prefix_start)  && self::$app->$prefix_start();
				method_exists(self::$app, $before_method) && self::$app->$before_method();
				return self::$app->$method();
			}
		} else {
			iPHP::error_404('Unable to find method <b>' . self::$class_name . '::'.$method.'</b>', '0005');
		}
	}
	public static function composer() {
		$dir = iPATH .'vendor/composer';
		if(file_exists($dir)){
			$path = iPATH .'vendor/autoload.php';
			require_once $path;
		}
	}
	public static function app_dir($key,$value) {
		self::$app_dirs[$key] = $value;
	}
	public static function app_destruct() {
		$method = 'after_'.iPHP::$app_method;
		$key    = 'is_'.$method;
		$isrun  = iPHP::$callback[$key];
		if(method_exists(self::$app, $method) && !$isrun){
			iPHP::$callback[$key] = true;
			self::$app->$method();
		}
	}
	public static function app_startup($app,$prefix=null) {
		$prefix = strtoupper($prefix);
		$path   = self::$app_path . '/'.$prefix.$app.'.app.php';
		// a/API_a.app.php
		// a/ACTION_a.app.php
		if(is_file($path)){
			require_once $path;
			$class_name = $prefix.$app.'App';
			if(class_exists($class_name,false)){
				self::$class_name = $class_name;
				self::$app_file   = $path;
				self::$app        = new $class_name();
				$class_methods    = get_class_methods(self::$app);
				if(in_array(self::$app_method, $class_methods)){
					return true;
				}else{
					if(!$prefix) return false;
				}
			}
		}
		// a/MY_a.app.php
		if(iPHP_APP_DEFINE){
			if($prefix===iPHP_APP_DEFINE) return self::app_startup($app);
			return self::app_startup($app,iPHP_APP_DEFINE);
		}else{
			return self::app_startup($app);
		}
	}
	public static function auto_require($name) {
		$o_name = $name;
		//app_mo.class.php
		if(strpos($name,'_') !== false) {
			list($a,$b) = explode('_', $name);
			if( !(substr($name,-7,7) == 'Admincp') &&
				!(substr($name,-3,3) == 'App') &&
				!(substr($name,-4,4) == 'Func') &&
				!in_array($a, self::$reserved)
			) {
				$file = $name.'.class';
				$name = $a;
			}
		}
		//app.app.php
		if(substr($name,-3,3) == 'App') {
			$app  = substr($name,0,-3);
			$file = $app.'.app';
			if(strpos($app,'_') !== false) {
				$pieces = explode('_', $app);
				if(in_array($pieces[0], self::$reserved)){
					//DO_app.app.php
					//MY_app.app.php
					list($flag,$app) = $pieces;
				}else{
					//app_ooxx.app.php
					list($app,$flag) = $pieces;
				}
			}
			$dir  = self::$app_dirs[$app]?:$app;
			$path = iPHP_APP_DIR . '/' . $app . '/' . $file . '.php';
		}else if(substr($name,-4,4) == 'Func'||substr($name,-4,4) == 'Tmpl') {
			$type = substr($name,-4,4);
			//app.func.php:
			//app.tmpl.php:
			$app  = substr($name,0,-4);
			$file = $app.'.'.strtolower($type);
			if(strpos($app,'_') !== false) {
				$pieces = explode('_', $app);
				if(in_array($pieces[0], self::$reserved)){
					//DO_app.func.php
					//MY_app.func.php
					//DO_app.tmpl.php
					//MY_app.tmpl.php
					list($flag,$app) = $pieces;
				}else{
					//app_ooxx.func.php
					//app_ooxx.tmpl.php
					list($app,$flag) = $pieces;
				}
			}
			$dir  = self::$app_dirs[$app]?:$app;
			$path = iPHP_APP_DIR . '/' . $dir . '/' . $file . '.php';
		}else if(substr($name,-7,7) == 'Admincp') {
			//app.admincp.php
			$app  = substr($name,0,-7);
			$file = $app.'.admincp';
			if(strpos($app,'_') !== false) {
				//app_mo.admincp.php
				list($app,$flag) = explode('_', $name);
			}
			$dir  = self::$app_dirs[$app]?:$app;
			$path = iPHP_APP_DIR . '/' . $dir . '/' . $file . '.php';
		}else if (in_array($name, explode(',', iPHP_CORE_CLASS))) {
			//iclass.class.php
			$path = iPHP_CORE.'/'.$name.'.class.php';
		}else if(array_key_exists($name,(array)iPHP::$apps)){
			//app.class.php
			$file OR $file = $name.'.class';
			$dir  = self::$app_dirs[$name]?:$name;
			$path = iPHP_APP_DIR . '/' . $dir . '/' . $file . '.php';
		}else if(strpos($name,'\\') !== false) {
			//namespace aaa\bbb
			$space = str_replace('\\', DIRECTORY_SEPARATOR, $name);
			$path = iPATH . $space . '.php';
		}
		if (file_exists($path)) {
			require_once $path;
			return true;
		}
		//namespace aaa\bbb
		if(strpos($name,'\\') !== false) {
			return false;
		}
		// iPHP::callback 不提示
		if(self::$is_callable){
			return false;
		}
		$path && self::error_throw("Unable to load class '$o_name',file path '$path'", '0021');
		return false;
	}
	public static function debug_info($tpl) {
		if (iPHP_DEBUG && iPHP_DEBUG_TRACE) {
			echo '<div class="well">';
			echo '<h3 class="label label-default">调试信息</h3>';
			echo '<span class="label label-success">模板:'.$tpl.' 内存:'.iFS::sizeUnit(memory_get_usage()).', 执行时间:'.self::timer_stop().'s, SQL累计执行:'.iDB::$num_queries.'次</span>';
			if(iDB::$trace_info && iPHP_DB_TRACE){
				echo '<br /><h3 class="label label-default">数据调用汇总:</h3>';
				echo '<pre class="alert alert-info">';
				print_r(iDB::$trace_info);
				echo '</pre>';
				iDB::$trace_info = null;
			}
			echo '</div>';
		}
	}

	// 获取客户端IP
	public static function get_ip($format = 0) {
		if (isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP'] && strcasecmp($_SERVER['HTTP_CLIENT_IP'], 'unknown')) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] &&  strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], 'unknown')) {
			$ip  = $_SERVER['HTTP_X_FORWARDED_FOR'];
			$ips = explode(',', $ip);
			$key = array_search('unknown', $ips);
			if($key!==false) unset($ips[$key]);
			$ip = trim($ips[0]);
		} elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		// IP地址合法验证
		$long = sprintf("%u", ip2long($ip));
		$ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
		return $ip[$format];
	}
	//设置COOKIE
	public static function set_cookie($name, $value = "", $life = 0, $httponly = true) {
		// $cookiedomain = iPHP_COOKIE_DOMAIN;
		$cookiedomain = '';
		$cookiepath = iPHP_COOKIE_PATH;
		$value = rawurlencode($value);
		$life = ($life ? $life : iPHP_COOKIE_TIME);
		$name = iPHP_COOKIE_PRE . '_' . $name;
		$timestamp = time();
		$life = $life > 0 ? $timestamp + $life : ($life < 0 ? $timestamp - 31536000 : 0);
		$secure = $_SERVER['SERVER_PORT'] == 443 ? 1 : 0;
		setcookie($name, $value, $life, $cookiepath, $cookiedomain, $secure, $httponly);
	}
	//取得COOKIE
	public static function get_cookie($name) {
		$name = iPHP_COOKIE_PRE . '_' . $name;
		return rawurldecode($_COOKIE[$name]);
	}

	public static function import($path, $dump = false) {
		$key = iSecurity::filter_path($path);
		// $key =substr(md5($path), 8,16) ;
		if ($dump) {
			if (!isset($GLOBALS['iPHP_REQ'][$key])) {
				$GLOBALS['iPHP_REQ'][$key] = include $path;
			}
			return $GLOBALS['iPHP_REQ'][$key];
		}

		if (isset($GLOBALS['iPHP_REQ'][$key])) {
			return;
		}

		$GLOBALS['iPHP_REQ'][$key] = true;
		require $path;
	}

	public static function appid($app=null,$trans=false) {
        return apps::id($app,$trans);
	}

    /**
     * [hook 应用钩子]
     * @param  [type] $app      [应用]
     * @param  [type] $resource [资源]
     * @param  [type] $hooks    [钩子]
     * @return [type]           [description]
     */
    public static function hook($app,&$resource=null,$hooks=null){
        if($hooks){
            foreach ($hooks as $field => $call) {
                foreach ($call as $key => $cb) {
                    $data = iPHP::callback($cb,array($resource[$field],&$resource),'nohook');
                    $data=='nohook' OR $resource[$field] = $data;
                }
            }
        }
        return $resource;
    }
    /**
     * [callback 回调执行]
     * @param  [type] $callback [执行函数]
     * @param  [type] $value    [引用参数]
     * @return [type]           [description]
     */
    public static function callback($callback,$value=null,$return=null){
    	if(empty($callback)) return;

    	$reference = false;
    	if(is_array($callback)){
    		if(is_string($callback[1])){
		    	if (stripos($callback[1], '_FALSE') !== false) {
		    		$return = false;
		    	}
		    	if (stripos($callback[1], '_TRUE') !== false) {
		    		$return = true;
		    	}
    		}elseif(is_array($callback[0])||is_object($callback[0])){
	        	$res = array();
	        	foreach ($callback as $key => $call) {
	        		self::is_callable($call) && $res[$key] = call_user_func_array($call,(array)$value);
	        	}
	        	return $res;
        	}
    	}

        if (self::is_callable($callback)) {
	        return call_user_func_array($callback,(array)$value);
        }else{
	        if($return===null){
				return $value;
	        }else{
	        	return $return;
	        }
        }
    }
    public static function callfunc($callback,$value) {
	    $callable = self::$is_callable;
	    if(class_exists($callback[0]) && method_exists($callback[0],$callback[1])){
	    	return call_user_func_array($callback,(array)$value);
	    }
	    self::$is_callable = $callable;
    }
    public static function is_callable($callback) {
    	self::$is_callable = true;
    	return is_callable($callback);
    }
	public static function is_callback($var){
	    if (is_array($var) && count($var) == 2) {
	        $var = array_values($var);
	        if ((!is_string($var[0]) && !is_object($var[0])) || (is_string($var[0]) && !class_exists($var[0]))) {
	            return false;
	        }
	        $isObj = is_object($var[0]);
	        $class = new ReflectionClass($isObj ? get_class($var[0]) : $var[0]);
	        if ($class->isAbstract()) {
	            return false;
	        }
	        try {
	            $method = $class->getMethod($var[1]);
	            if (!$method->isPublic() || $method->isAbstract()) {
	                return false;
	            }
	            if (!$isObj && !$method->isStatic()) {
	                return false;
	            }
	        } catch (ReflectionException $e) {
	            return false;
	        }
	        return true;
	    } elseif (is_string($var) && function_exists($var)) {
	        return true;
	    }
	    return false;
	}

	public static function vendor($name, $args = null,$self=false) {
		return iVendor::run($name,$args,$self);
	}
    //------------------------------------
    public static function timer_task(){
        $timestamp = iCache::get('timer_task');
        //list($_today,$_week,$_month) = $timestamp ;
        $time     = $_SERVER['REQUEST_TIME'];
        $today    = get_date($time,"Ymd");
        $yday     = get_date($time-86400+1,"Ymd");
        $week     = get_date($time,"YW");
        $month    = get_date($time,"Ym");
        $timestamp[0]==$today OR iCache::set('timer_task',array($today,$week,$month),0);
        return array(
            'yday'  => ($today-$timestamp[0]),
            'today' => ($timestamp[0]==$today),
            'week'  => ($timestamp[1]==$week),
            'month' => ($timestamp[2]==$month),
        );
    }
	/**
	 * Starts the timer, for debugging purposes
	 */
	public static function timer_start() {
		$mtime = microtime();
		$mtime = explode(' ', $mtime);
		self::$time_start = $mtime[1] + $mtime[0];
	}

	/**
	 * Stops the debugging timer
	 * @return int total time spent on the query, in milliseconds
	 */
	public static function timer_stop($restart=false) {
		$mtime = microtime();
		$mtime = explode(' ', $mtime);
		$time_end = $mtime[1] + $mtime[0];
		$time_total = $time_end - self::$time_start;
		$restart && self::$time_start = $time_end;
		return round($time_total, 4);
	}
	public static function redirect($url = '',$flag=null) {
		if($flag){
			//防止从重复跳转
			$redirect_num = (int)iPHP::get_cookie('redirect_num');
			if($redirect_num){
				$url = iPHP_URL;
				iPHP::set_cookie('redirect_num', '',-31536000);
			}else{
				iPHP::set_cookie('redirect_num', ++$redirect_num);
			}
		}
		$url OR $url = iPHP_REFERER;
		if (@headers_sent()) {
			echo '<meta http-equiv=\'refresh\' content=\'0;url=' . $url . '\'><script type="text/javascript">window.location.replace(\'' . $url . '\');</script>';
		} else {
			header("Location: $url");
		}
		exit;
	}
	public static function http_status($code, $ECODE = '') {
		static $_status = array(
			// Success 2xx
			200 => 'OK',
			// Redirection 3xx
			301 => 'Moved Permanently',
			302 => 'Moved Temporarily ', // 1.1
            304 => 'Not Modified',
			// Client Error 4xx
			400 => 'Bad Request',
			403 => 'Forbidden',
			404 => 'Not Found',
			// Server Error 5xx
			500 => 'Internal Server Error',
			503 => 'Service Unavailable',
		);
		if (isset($_status[$code])) {
			header('HTTP/1.1 ' . $code . ' ' . $_status[$code]);
			$ECODE && header("X-iPHP-ECODE:" . $ECODE);
		}
	}
	public static function error_throw($msg, $code=null) {
		trigger_error($msg.($code?"($code)":null), E_USER_ERROR);
	}
	public static function error_404($msg = "", $code = "") {
        if(is_array($msg)||@strstr($msg, ':')){
            $msg = iUI::lang($msg, false);
        }
		iPHP_DEBUG && self::error_throw($msg, $code);
		self::http_status(404, $code);

		if (defined('iPHP_URL_404')) {
			if(iPHP_URL_404){
				if(iHttp::is_url(iPHP_URL_404)){
					self::redirect(iPHP_URL_404 . '?url=' . urlencode($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));
				}else{
					$tpl = iPHP_APP."://".iPHP_URL_404;
					if(iView::tpl_exists($tpl)){
						iView::display($tpl);
					}else{
						iView::display(iPHP_APP."://404.htm");
					}
				}
			}
		}
		exit();
	}
	public static function error_log($erro) {
		defined('iPHP_DEBUG_ERRORLOG') OR define('iPHP_DEBUG_ERRORLOG', false); //兼容PHP7
		if(!iPHP_DEBUG_ERRORLOG) return;

		@file_put_contents(iPHP_APP_CACHE.'/error_log_'.md5(sha1(iPHP_KEY)).'.log',
			"<?php exit('What the fuck!');?>"
			.PHP_EOL.'['.date("Y-m-d H:i:s").'] '.self::get_ip()
			.PHP_EOL.iPHP_REQUEST_URL
			.($_GET?PHP_EOL.'$_GET=>'.var_export($_GET,true):'')
			.($_POST?PHP_EOL.'$_POST=>'.var_export($_POST,true):'')
			.PHP_EOL.html2text($erro)
			.PHP_EOL.PHP_EOL,
			FILE_APPEND
		);
	}
	public static function error_handler($errno, $errstr, $errfile, $errline) {
	    if (!(error_reporting() & $errno)) {
	        // This error code is not included in error_reporting, so let it fall
	        // through to the standard PHP error handler
	        return false;
	    }
		defined('E_STRICT') OR define('E_STRICT', 2048);
		defined('E_RECOVERABLE_ERROR') OR define('E_RECOVERABLE_ERROR', 4096);
		switch ($errno) {
	        case E_ERROR:              $type = "Error";                  break;
	        case E_WARNING:            $type = "Warning";                break;
	        case E_PARSE:              $type = "Parse Error";            break;
	        case E_NOTICE:             $type = "Notice";                 break;
	        case E_CORE_ERROR:         $type = "Core Error";             break;
	        case E_CORE_WARNING:       $type = "Core Warning";           break;
	        case E_COMPILE_ERROR:      $type = "Compile Error";          break;
	        case E_COMPILE_WARNING:    $type = "Compile Warning";        break;
	        case E_USER_ERROR:         $type = "iPHP Error";             break;
	        case E_USER_WARNING:       $type = "iPHP Warning";           break;
	        case E_USER_NOTICE:        $type = "iPHP Notice";            break;
	        case E_STRICT:             $type = "Strict Notice";          break;
	        case E_RECOVERABLE_ERROR:  $type = "Recoverable Error";      break;
	        default:                   $type = "Unknown error ($errno)"; break;
		}
		$html = "<pre style='font-size: 14px;white-space: normal;'>";
		$html.= "<b style='font-size: 18px;color:red;'>{$type}</b>\n{$errstr}";
		$html.= " in <b>{$errfile}</b> on line <b>{$errline}</b>\n";
		if (function_exists('debug_backtrace')) {
			$backtrace = debug_backtrace();
			krsort($backtrace);
			$c = count($backtrace);
			$html.= "<pre style='font-size: 14px;white-space: normal;'>";
			foreach ($backtrace as $i => $bt) {
				$html .= ($c-$i).". <b>{$bt['class']}{$bt['type']}{$bt['function']}()</b>";
				// $html .= '(' . implode(', ', $bt['args']) . ')';
				$bt['file'] && $html .= " in <b>{$bt['file']}</b>";
				$bt['line'] && $html .= " on line <b>{$bt['line']}</b>";
				$html .= "\n";
			}
			$html .= "</pre>";
		}
		$html .= "</pre>";
		$html = iSecurity::filter_path($html);
		self::error_log($html);
		self::$callback['error'] OR self::$callback['error'] = array('iUI','error');
		self::callback(self::$callback['error'],array($html,'system',$errstr));
	}
}
