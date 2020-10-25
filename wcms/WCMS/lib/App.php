<?php
/**
 * 
 * @author wolf [Email: 116311316@qq.com]
 *@since 2011-07-20
 *@version 1.0
 */
class App {
	protected $_uri = '';
	protected $_defaultModule = '';
	static $_instance = '';
	/**
	 * 初始化配置 自动加载
	 */
	private function __construct() {
		spl_autoload_register ( array (__CLASS__, 'loadClass' ), false );
	}
	/**
	 * 单例模式
	 * @return App
	 */
	public static function getInstance() {
		if (self::$_instance == NULL) {
			self::$_instance = new self ();
		}
		return self::$_instance;
	}
	private function __clone() {
	}
	/**  
	 * 运行
	 * @return Route
	 */
	public function run() {
		return $this->dispatch ( new Route () );
	}
	/**
	 * 派遣模块
	 * @param Route $request
	 */
	public function dispatch(Route $request) {
		$request->paresUri (); //进行路由解析
		

		$className = $request->getControllerName ();
		$l2 = substr ( $className, 1, strlen ( $className ) );
		$l1 = strtoupper ( $className {0} );
		$className = $l1 . $l2 . "Controller";

		$controllerName = APP . "controllers/" . $className . ".php";

		if(!file_exists($controllerName)){
		    $controllerName=APP . "admin/" . $className . ".php";
        }

		$this->preDispath ();
		//检查相应数据库模块是否存在 可删除该模块   不再做要求
		//检查控制器是否存在
		if (is_file ( $controllerName )) {
			require $controllerName;
			if (class_exists ( $className, false )) {
				$controller = new $className ( $request );
			}
		} else {
			//是否开启错误调试
			echo "你访问的方法不存在哦！";
			throw new Exception ( $controllerName . " not exit!" );
		}
		if (! $controller->getPremission ()) {
			return false;
		}
		$action = $request->getActionName ();
		//如果方法不存在
		if (! method_exists ( $controller, $action )) {
			echo "the action is not exit";
		} else {
			$controller->$action ();
		}
	}
	/**
	 * 获取当前模块
	 */
	public function preDispath() {
		
		$dirs = scandir ( APP );
		
		if (count ( $dirs ) < 3) {
			return;
		}
		
		$pathArr = array (get_include_path () );
		for($i = 2; $i < count ( $dirs ); $i ++) {
			array_push ( $pathArr, realpath ( APP . $dirs [$i] ) );
		}
		
		set_include_path ( implode ( PATH_SEPARATOR, $pathArr ) );
	
	}
	/**
	 * 
	 * 自定义路由－多域名
	 * @param string $Domain 域名$_SERVER[HTTP_HOST]
	 * @return string 模块
	 */
	public function getRoute($Domain) {
		$config = require_once 'Bootstrap.php';
		foreach ( $config as $k => $v ) {
			if ($Domain == $v ['Domain']) {
				return $k;
			}
		}
	}
	/**
	 * 自动加载类库
	 */
	public static function loadClass($class) {
		//解决与smarty 自身加载问题
		if (strpos ( $class, 'Smarty' ) !== false) {
			return false;
		}
		//切割符号 自动加载
		if (strpos ( $class, '_' ) !== false) {
			$path = explode ( '_', $class );
			$num = count ( $path );
			$fullpath = '';
			for($i = 0; $i < $num - 1; $i ++) {
				$fullpath .= $path [$i] . "/";
			}
			include $fullpath . $class . ".php";
		} else {
			include $class . ".php";
		}
	}
}