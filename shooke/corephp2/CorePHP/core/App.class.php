<?php
namespace Core;
use \Exception;
/**
 * @author shooke
 * 框架主文件
 * 对框架进行整体初始化和文件载入判断
 */
class App {
	public static $group;//模块名称
	public static $module;//模块名称
	public static $action;//操作名称
	public static $appConfig = array(); //配置	

	//执行模块，单一入口控制核心	
	public static function run() {
		//加载配置类
		require(CP_CORE_PATH . 'Config.class.php');
		//加载常用函数库		
		require(CP_LIB_PATH . 'Common.function.php');	
		//加载字符串处理类库
		require(CP_LIB_PATH . 'String.function.php');		
		//加载扩展函数库
		require(CP_EXT_PATH . 'extend.php');
		
		//载入app全局配置				
		$config = require( APP_CONFIG );		
		//合并参数配置
		self::$appConfig = array_merge(Config::get('APP'), $config);
		Config::set('APP', self::$appConfig );//参数配置保存到Config类
		
		// 注册AUTOLOAD方法
      	spl_autoload_register('\Core\App::autoload'); 
		
		//初始化时区
		date_default_timezone_set(self::$appConfig['DATE_TIMEZONE']);			
		
		//http请求魔术引号处理
		HttpRequest::normalizeRequest();
		
		//网址解析
		if(function_exists('url_parse_ext')) {
			url_parse_ext();//自定义网址解析
		} else {
			if (self::$appConfig['URL_REWRITE_ON']<2) {//原生url
				/* 
				 * 0 index.php?g=group&m=module&a=action
				 * 1 ?g=group&m=module&a=action				  
				 */
				Route::gmaUrl();
			}else{//采用pathinfo方式
				/* 
				 * 2 index.php/group/module/action
				 * 3 /group/module/action
				 * 4 index.php?r=/group/module/action
				 * 5 ?r=/group/module/action				
				 */
				Route::parseUrl();//解析模块和操作
			}
		}
			
		
		//合并分组配置
		if(Route::$group){			
			$group_config_file = CP_CONFIG_PATH . Route::$group . '.php';
			$group_config = array();//初始化变量防止E_NOTICE报错			
			if(file_exists($group_config_file)){				
				$group_config = require($group_config_file);
			}
			!empty($group_config) && self::$appConfig = array_merge(self::$appConfig, $group_config);//参数配置
			
			//最新加载的参数配置保存到Config类
			Config::set('APP', self::$appConfig );
		}		
		
		//debug设置
		self::_debug();		

		
		try{
			//如果存在初始程序，则先加载初始程序
			if ( file_exists( self::$appConfig['MODULE_PATH'] . self::$appConfig['MODULE_INIT']) ) {
				require( self::$appConfig['MODULE_PATH'] . self::$appConfig['MODULE_INIT'] );
			}
			
			//常量定义
			self::_define();

			//检查指定模块是否存在
			if( preg_match("#^[a-z0-9_]+$#i",Route::$module) && self::_checkModuleExists(Route::$module) ) {
				$module = Route::$module;				
			} else if ( self::_checkModuleExists( self::$appConfig['MODULE_EMPTY'] ) ) {//如果指定模块不存在，则检查是否存在空模块
				$module = self::$appConfig['MODULE_EMPTY'];
			} else {				
				$error = self::$appConfig['GROUP_DEFAULT'] ? '分组'.Route::$group : '';//分组提示
				throw new Exception($error.' ' . Route::$module . "模块不存在");//指定模块和空模块都不存在，则显示出错信息，并退出程序。
			}

			//如果开启静态页面缓存，则尝试读取静态缓存
			if ( false == self::_readHtmlCache($module, Route::$action) ) {				
				//静态缓存读取失败，执行模块操作
				self::_execute($module);
			}

			//如果存在回调函数cp_app_end，则在即将结束前调用
			if ( function_exists('cp_app_end') ) {
				cp_app_end();
			}
		} catch( Exception $e){
			Error::show( $e->getMessage() );
		}
	}

	

	//常量定义
	private static function _define() {
		//取得当前执行文件，带路径
		$script_name = HttpRequest::getScriptUrl();		
		//__ROOT__和__PUBLIC__常用于图片，css，js定位，__APP__和__URL__常用于网址定位
		//去掉文件名得到目录
		$root = str_replace(basename($script_name), '', $script_name);
		//当前入口所在的目录，后面不带 "/"
		define('__ROOT__', substr($root, 0, -1));
		//取得目录名称组成公用文件夹路径，用于模板中
		define('__PUBLIC__', __ROOT__ . '/' . basename(self::$appConfig['TPL_PUBLIC_PATH']) );

		//如果奇数，则网址不包含入口文件名，如index.php，偶数则带入口文件
		if ( self::$appConfig['URL_REWRITE_ON']%2==1 ) {
			define('__APP__', __ROOT__);
		} else {
			define('__APP__', __ROOT__ . '/' . basename($script_name));//当前入口文件
		}
		
		//定义分组常量
		define('CP_GROUP', Route::$group);
		//定义模块常量
		define('CP_MODULE', Route::$module);
		//定义方法常量
		define('CP_ACTION', Route::$action);
	}

	//检查模块文件是否存在
	private static function _checkModuleExists($module){
		//分组模块路径
		$path = self::$appConfig['MODULE_PATH'].$module.self::$appConfig['MODULE_SUFFIX'];
		if(file_exists($path)){
			require_once($path);//加载模块文件
			return true;
		}else {
			return false;
		}
	}

	//执行操作
	private static function _execute($module){
		//处理字符成为命名空间格式
		$namespace = str_replace(array('../','./','/'), '\\', self::$appConfig['MODULE_PATH']);
		//模块名+模块后缀组成完整类名
		$suffix_arr = explode('.', self::$appConfig['MODULE_SUFFIX'], 2);
		$classname = $module . $suffix_arr[0];
		//命名空间与类名合并
		$spaceClass = $namespace.$classname;
		//检查类是否存在
		if(!class_exists($spaceClass)) {
			$error = self::$appConfig['GROUP_DEFAULT'] ? '分组'.Route::$group : '';//分组提示
			throw new Exception($error.' ' . $classname . "类未定义");
		}
		//实例化模块对象
		$object=new $spaceClass();
		//添加方法后缀组成完整方法
		$action = Route::$action.self::$appConfig['MODULE_ACTION_SUFFIX'];
		//类和方法同名，直接返回，因为跟类同名的方法会当成构造函数，已经被调用，不需要再次调用		
		if($classname==$action){
			return true;
		}

		if ( method_exists($object, $action)) {	
		    	    
		    $object->$action();//执行指定模块的指定操作
		    
		} else if ( method_exists($object, self::$appConfig['ACTION_EMPTY'].self::$appConfig['MODULE_ACTION_SUFFIX']) ) {
			$action=self::$appConfig['ACTION_EMPTY'].self::$appConfig['MODULE_ACTION_SUFFIX'];
			//解决空操作的静态页面缓存读取
			if( self::_readHtmlCache($module, Route::$action) ) {
				return true;
			}			
			$object->$action();//执行指定模块的指定操作
		} else {
			$error = self::$appConfig['GROUP_DEFAULT'] ? '分组'.Route::$group : '';//分组提示
			throw new Exception($error.' ' . $action."操作方法在" . $module . "模块中不存在");
		}
		

		//如果缓存开启，写入静态缓存，只有符合规则的，才会创建缓存
		self::_writeHtmlCache();
	}

	//读取静态页面缓存
	private static function _readHtmlCache($module = '', $action = '') {
		if ( self::$appConfig['HTML_CACHE_ON'] ) {			
			return HtmlCache::read($module, $action);
		}
		return false;
	}

	//写入静态页面缓存
	private static function _writeHtmlCache() {
		if ( self::$appConfig['HTML_CACHE_ON'] ) {
			HtmlCache::write();
		}
	}

	/**
	 * debug设置
	 */
	private static function _debug(){
	    define('DEBUG', self::$appConfig['DEBUG']);
		//调试模式开关		
		if ( self::$appConfig['DEBUG'] ) {
			ini_set("display_errors", 1);
			error_reporting( E_ALL ^ E_NOTICE );//除了notice提示，其他类型的错误都报告
		} else {
			ini_set("display_errors", 0);
			error_reporting(0);//把错误报告，全部屏蔽
		}
	}
	

	//实现类的自动加载
	public static function autoload($classname) {
		//处理路径格式，namespace\classname 改为 namespace/classname符合路径格式
		$classname = str_replace('\\','/', $classname);
		//文件加载目录
		$dir_array = array(		
		CP_ROOT_PATH ,	//框架所在目录		
		self::$appConfig['MODULE_PATH'],//模块文件
		self::$appConfig['MODEL_PATH'],	//模型文件
		);
		$dir_array = array_merge($dir_array, self::$appConfig['AUTOLOAD_DIR']);
		foreach($dir_array as $dir) {
			$file = $dir . $classname . '.class.php';			
			if ( is_file($file) ) {
				require_once($file);
				return true;
			}
		}
		return false;
	}

}