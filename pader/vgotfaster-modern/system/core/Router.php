<?php
/**
 * VgotFaster PHP Framework
 *
 * @package VgotFaster
 * @author pader
 * @copyright Copyright (c) 2009-2010, VGOT.NET
 * @link http://www.vgot.net/ http://vgotfaster.googlecode.com
 * @filesource
 */

namespace VF\Core;

/**
 * VgotFaster 系统路由类
 *
 * 根据 URI　和配置分发路由访问相对应的控制器并向控制器传递 URI 中的参数
 * 注: 路由器在实例化时就开始工作!
 *
 * @package VgotFaster
 * @author pader
 */
class Router {

	protected $config;
	protected $URIString;
	protected $trueURIString = '';
	protected $URI;
	protected $controllerFile;
	protected $controllerName;
	protected $controllerAction;
	protected $visitParams = array();

	/**
	 * SYS_Router::__construct()
	 *
	 * 路由在启动时，控制器并没有实例化，所以不建议也不使用 getInstance() 获取关联
	 * 这里的配置类直接使用 loadClass() 载入
	 */
	public function __construct()
	{
		$config = getConfig('config');
		$routes = getConfig('routes');

		$this->config = array(
			'defaultController' => $config['default_controller'],
			'method'            => strtoupper($config['router_method']),
			'params'            => $config['router_get_params'],
			'separator'         => $config['uri_separator'],
			'suffix'            => $config['url_suffix'],
			'allowedRegular'    => $config['uri_allowed_regular'],
			'routes'            => isset($routes) ? $routes : NULL
		);
	}

	/**
	 * analysis
	 *
	 * @return array
	 */
	public function analysis()
	{
		//支持使用入口文件自定义控制器目录路径
		$controllerDir = defined('CONTROLLER_PATH') ? CONTROLLER_PATH : APPLICATION_PATH.'/controllers';

		$this->URIExport();  //解压 URI 到数组 $this->URI

		// ---  开始文件及变量路由 ---
		$path = join('/', $this->URI);

		//直接访问目录,即该目录的默认控制器
		if (is_dir($dir = $controllerDir.'/'.$path)) {
			$controllerName = $this->config['defaultController'];  //ControllerName
			$controllerFile = "{$dir}/{$controllerName}.php";

		//访问指定控制器文件
		} elseif (is_file($file = "{$controllerDir}/{$path}.php")) {
			$controllerName = strtolower(end($this->URI));
			$controllerFile = $file;

		//访问控制器文件并且带有参数
		} else {
			$lpath = $controllerDir;
			$export = $this->URI;

			//从左至右寻找控制器文件
			foreach ($export as $i => $seg) {
				$lpath .= '/'.$seg;  //猜测的级别字串
				$controllerFile = $lpath.'.php';  //猜测的文件
				unset($export[$i]);
				if (is_file($controllerFile)) {
					$controllerName = $seg;
					$this->visitParams = array_values($export);  //获取后面的预处理的参数
					break;
				}
			}
		}

		$ControllerFile = str_replace('//', '/', $controllerFile);  //更正控制器文件路径中可能出现的两个斜杠的错误
		if (!is_file($ControllerFile)) {  //控制器文件不存在则返回 FALSE
			return FALSE;
		}

		//相关参数加入到类属性中
		$this->controllerFile = $controllerFile;
		$this->controllerName = $controllerName;
		$this->controllerAction = $this->visitParams ? array_shift($this->visitParams) : 'index';

		//返回路由分析结果参数
		return array(
			'file' => $this->controllerFile,
			'controller' => $this->controllerName,
			'action' => $this->controllerAction,
			'params' => $this->visitParams,
			'string' => $this->URIString,
			'uri' => $this->URI,
			'route' => $this->trueURIString
		);
	}

	/**
	 * URI 解压控制
	 *
	 * 根据配置中指定的方式获取并处理格式化的URI到属性中
	 *
	 * @return void
	 */
	private function URIExport()
	{
		$split = $this->config['separator'];

		switch($this->config['method'])
		{
			case 'PATH_INFO': $uri = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : ''; break;
			case 'QUERY_STRING': $uri = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''; break;
			case 'GET':
				$p = $this->config['params'];
				$controller = isset($_GET[$p['controller']]) ? $_GET[$p['controller']] : $this->config['defaultController'];
				$action = isset($_GET[$p['action']]) ? $_GET[$p['action']] : '';
				$uri = empty($action) ? $controller : $controller.$split.$action;
			break;
			default: showError('Unsupport Router Method');
		}

		$this->URIString = trim($uri, $split.'/');

		if ($this->URIString) {
			//检查URI合法性
			if ($this->URIString && !preg_match($this->config['allowedRegular'], $this->URIString)) {
				showError('URI Not Allow!');
			}
			$this->trueURIString = $this->removeSuffix($this->URIString);  //后缀处理
			$this->trueURIString = $this->translateRoute($this->trueURIString);  //路由转换
			$this->URI = explode($split, $this->trueURIString);
		}

		if (!is_array($this->URI) and count($this->URI) == 0) {
			$this->URI = array($this->config['defaultController']);
		}
	}

	/**
	 * 转换路由
	 *
	 * 根据设置匹配并转到到实际路由
	 *
	 * @param mixed $uri
	 * @return string Orig uri
	 */
	private function translateRoute($uri)
	{
		if (is_array($this->config['routes']) && count($this->config['routes']) > 0) {
			foreach ($this->config['routes'] as $exp => $route) {
				$exp = '#^'.$exp.'$#';
				if (preg_match($exp, $uri)) {
					return preg_replace($exp, $route, $uri);
				} elseif($uri == $exp) {
					return $route;
				} else continue;
			}
		}

		return $uri;
	}

	/**
	 * URI 后缀处理
	 *
	 * @param mixed $uri
	 * @return string
	 */
	private function removeSuffix($uri)
	{
		if (!$this->config['suffix']) {
			return $uri;
		}
		if (!strpos($uri, $this->config['suffix'])) {
			return $uri;
		} else {
			$suffix = preg_quote($this->config['suffix']);
			return preg_replace("/$suffix$/",'',$uri);
		}
	}

}
