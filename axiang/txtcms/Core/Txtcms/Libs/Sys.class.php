<?php
/**
 * TXTCMS 框架执行类
 * @copyright			(C) 2013-2014 TXTCMS
 * @license				http://www.txtcms.com
 * @lastmodify			2014-8-8
 */
class Sys {
	static public function run() {
		session_start();
		Sys::init();
	}
	/**
	  * 加载、配置项目
	  * @access private
	  * @return void
	 */
	static private function init(){
		header("Content-type: text/html; charset=utf-8");
		 //注册AUTOLOAD方法
        spl_autoload_register(array('Sys','autoload'));
		//加载公共配置
		if(is_file(CONFIG_PATH.'config.php')) {
			config(include CONFIG_PATH.'config.php');
		}
		//加载公共函数
		if(is_file(FUNCTION_PATH.'function.php')) {
			include FUNCTION_PATH.'function.php';
		}
		//定义当前请求的系统常量
		define('REQUEST_METHOD',$_SERVER['REQUEST_METHOD']);
        define('IS_GET',REQUEST_METHOD =='GET' ? true : false);
        define('IS_POST',REQUEST_METHOD =='POST' ? true : false);
        define('IS_PUT',REQUEST_METHOD =='PUT' ? true : false);
        define('IS_DELETE',REQUEST_METHOD =='DELETE' ? true : false);
        define('IS_AJAX',((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) ? true : false);
		
		
		$urlMode=config('URL_MODEL');
		if($urlMode ==2){
            // 兼容模式判断
            define('PHP_FILE',_PHP_FILE_);
        }elseif($urlMode ==3) {
            //当前项目地址
            $url    =   dirname(_PHP_FILE_);
            if($url == '/' || $url == '\\') $url='';
            define('PHP_FILE',$url);
        }else {
            //当前项目地址
            define('PHP_FILE',_PHP_FILE_);
        }
		// 当前项目地址
        define('__APP__',strip_tags(PHP_FILE));
		$route=new Route;
		//分析URL
		if(!empty($_SERVER['QUERY_STRING']) && !$route->check()) {
			$depr=config('URL_PATH_DEPR');
			//自动判断模式
			if(isset($_GET[config('GROUP_VAR')])) {
				$urlMode=1;
			}else if(preg_match('~^([\w'.config('URL_PATH_DEPR').']+)~',$_SERVER['QUERY_STRING'])){
				$urlMode=2;
			}
			//普通模式
			if($urlMode==1){
				
			}
			//QUERY模式
			if($urlMode==2){
				$part=pathinfo($_SERVER['QUERY_STRING']);
				$extension=isset($part['extension'])?strtolower($part['extension']):'';
				if($extension){
					$_SERVER['QUERY_STRING']=preg_replace('/\.('.trim(config('URL_PATH_SUFFIX'),'.').')$/i', '',$_SERVER['QUERY_STRING']);
				}else{
					$_SERVER['QUERY_STRING'] = preg_replace('/\.'.$extension.'$/i','',$_SERVER['QUERY_STRING']);
				}
				$paths = explode($depr,$_SERVER['QUERY_STRING']);
				$var  =  array();
                if(config('APP_GROUP_LIST') && !isset($_GET[config('GROUP_VAR')])){
                    $var[config('GROUP_VAR')] = in_array(strtolower($paths[0]),explode(',',strtolower(config('APP_GROUP_LIST'))))? array_shift($paths) : '';
                }
				if(!isset($_GET[config('MODULE_VAR')])) {// 获取模块名称
                    $var[config('MODULE_VAR')]=array_shift($paths);
                }
				$var[config('ACTION_VAR')]=array_shift($paths);

				//解析剩余的URL参数
                preg_replace('@(\w+)\/([^\/]+)@e', '$var[\'\\1\']=strip_tags(\'\\2\');', implode('/',$paths));
                $_GET=array_merge($var,$_GET);
			}
		}
		//获取分组
        if (config('APP_GROUP_LIST')) {
            define('GROUP_NAME', self::getGroup(config('GROUP_VAR')));
			$config_path=CONFIG_PATH.GROUP_NAME.'/';
			$common_path=FUNCTION_PATH.GROUP_NAME.'/';
			//加载分组配置文件
            if(is_file($config_path.'config.php')) config(include $config_path.'config.php');
			//加载分组函数文件
            if(is_file($common_path.'function.php')) include $common_path.'function.php';
		}
		define('MODULE_NAME',self::getModule(config('MODULE_VAR')));
		define('ACTION_NAME',self::getAction(config('ACTION_VAR')));
		$_REQUEST = array_merge($_POST,$_GET);
		
		if(!preg_match('/^[A-Za-z](\w)*$/',MODULE_NAME)){
			throw_exception('未找到模块:'.MODULE_NAME);
		}
		$group=defined('GROUP_NAME') ? GROUP_NAME.'/' : '';
		$module=action($group.MODULE_NAME);
		$action=ACTION_NAME;
		if(!$module) {
			// 是否定义Empty模块
			$module = action($group.'Empty');
			if(!$module){
				_404('未找到模块:'.MODULE_NAME);
			}
		}
		try{
			if(!preg_match('/^[A-Za-z](\w)*$/',$action)){
				//非法操作
				throw new ReflectionException();
			}
			//执行当前操作
            $method =   new ReflectionMethod($module, $action);
			if($method->isPublic()) {
				$class=new ReflectionClass($module);
				// URL参数绑定检测
                if($method->getNumberOfParameters()>0){
                    switch($_SERVER['REQUEST_METHOD']) {
                        case 'POST':
                            $vars=$_REQUEST;
                            break;
                        case 'PUT':
                            parse_str(file_get_contents('php://input'), $vars);
                            break;
                        default:
                            $vars=$_GET;
                    }
                    $params =  $method->getParameters();
                    foreach ($params as $param){
                        $name = $param->getName();
                        if(isset($vars[$name])) {
                            $args[] =  $vars[$name];
                        }elseif($param->isDefaultValueAvailable()){
                            $args[] = $param->getDefaultValue();
                        }else{
                            throw_exception('参数未定义:'.$name);
                        }
                    }
                    $method->invokeArgs($module,$args);
                }else{
                    $method->invoke($module);
                }
			}else{
                //操作方法不是Public 抛出异常
                throw new ReflectionException();
            }
		} catch (ReflectionException $e) { 
            // 方法调用发生异常后 引导到__call方法处理
            $method = new ReflectionMethod($module,'__call');
            $method->invokeArgs($module,array($action,''));
        }
		return ;
	}
	/**
     * 自动加载类库
     * @param string $class 对象类名
     * @return void
     */
	public static function autoload($class){
		if(require_load($class)) return ;
		$group=defined('GROUP_NAME') ? GROUP_NAME.'/' : '';
		$file=$class.'.class.php';
		if(substr($class,-6)=='Action'){ // 加载控制器
            require_load(APPLIB_PATH.'Action/'.$group.$file);
			require_load(APPLIB_PATH.'Action/'.$file);
        }
		return ;
	}
	/**
     * 获得分组名称
     * @access private
     * @return string
     */
    static private function getGroup($var) {
        $group   = (!empty($_GET[$var])?$_GET[$var]:config('DEFAULT_GROUP'));
        return strip_tags(ucfirst($group));
    }
	/**
     * 获得模块名称
     * @access private
     * @return string
     */
    static private function getModule($var) {
        $module = (!empty($_GET[$var])? $_GET[$var]:config('DEFAULT_MODULE'));
        return strip_tags(ucfirst($module));
    }
	/**
     * 获得控制器名称
     * @access private
     * @return string
     */
    static private function getAction($var) {
        $action   = !empty($_POST[$var]) ? $_POST[$var] : (!empty($_GET[$var])?$_GET[$var]:config('DEFAULT_ACTION'));
        return strip_tags(strtolower($action));
    }
}