<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\controller;

use app\model\Config;
use app\model\Hooks;
use app\model\SeoRule;
use think\App;
use think\exception\ValidateException;
use think\facade\Cache;
use think\facade\Event;
use think\facade\View;
use think\Validate;

class Base {

	use \liliuwei\think\Jump;
	/**
	 * Request实例
	 * @var \think\Request
	 */
	protected $request;

	/**
	 * 应用实例
	 * @var \think\App
	 */
	protected $app;

	/**
	 * 是否批量验证
	 * @var bool
	 */
	protected $batchValidate = false;

	/**
	 * 控制器中间件
	 * @var array
	 */
	protected $middleware = [];
	// 使用内置PHP模板引擎渲染模板输出
	protected $tpl_config = [
		'view_dir_name' => 'public' . DIRECTORY_SEPARATOR . 'template',
		'tpl_replace_string' => [
			'__static__' => '/static',
			'__img__' => '/static/front/images',
			'__css__' => '/static/front/css',
			'__js__' => '/static/front/js',
			'__plugins__' => '/static/plugins',
			'__public__' => '/static/front',
		],
	];

	public $data = []; //渲染数据
	public $config = [];

	/**
	 * 构造方法
	 * @access public
	 * @param  App  $app  应用对象
	 */
	public function __construct(App $app) {
		$this->app = $app;
		$this->request = $this->app->request;

		$this->config = Cache::get('system_config_data');
		if (!$this->config) {
			$this->config = Config::getConfigList($this->request);
			Cache::set('system_config_data', $this->config);
		}
		$hooks = Cache::get('sentcms_hooks');
		if (!$hooks) {
			$hooks = Hooks::where('status', 1)->column('addons', 'name');
			foreach ($hooks as $key => $values) {
				if (is_string($values)) {
					$values = explode(',', $values);
				} else {
					$values = (array) $values;
				}
				$hooks[$key] = array_filter(array_map(function ($v) use ($key) {
					return [get_addons_class($v), $key];
				}, $values));
				// $hooks[$key] = $value ? explode(",", $value) : [];
			}
		}
		if (!empty($hooks)) {
			Cache::set('sentcms_hooks', $hooks);
			Event::listenEvents($hooks);
		}

		View::assign('version', \think\facade\Env::get('VERSION'));
		View::assign('config', $this->config);
		// 控制器初始化
		$this->initialize();
	}

	// 初始化
	protected function initialize() {}

	/**
	 * 验证数据
	 * @access protected
	 * @param  array        $data     数据
	 * @param  string|array $validate 验证器名或者验证规则数组
	 * @param  array        $message  提示信息
	 * @param  bool         $batch    是否批量验证
	 * @return array|string|true
	 * @throws ValidateException
	 */
	protected function validate(array $data, $validate, array $message = [], bool $batch = false) {
		if (is_array($validate)) {
			$v = new Validate();
			$v->rule($validate);
		} else {
			if (strpos($validate, '.')) {
				// 支持场景
				list($validate, $scene) = explode('.', $validate);
			}
			$class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
			$v = new $class();
			if (!empty($scene)) {
				$v->scene($scene);
			}
		}

		$v->message($message);

		// 是否批量验证
		if ($batch || $this->batchValidate) {
			$v->batch(true);
		}

		return $v->failException(true)->check($data);
	}

	protected function fetch($template = '') {
		if ($this->request->param('addon')) {
			$this->tpl_config['view_dir_name'] = 'addons' . DIRECTORY_SEPARATOR . $this->request->param('addon') . DIRECTORY_SEPARATOR . 'view';
		}
		View::config($this->tpl_config);
		View::assign($this->data);
		return View::fetch($template);
	}

	/**
	 * 是否为手机访问
	 * @return boolean [description]
	 */
	public function isMobile() {
//return true;
		// 如果有HTTP_X_WAP_PROFILE则一定是移动设备
		if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
			return true;
		}

		// 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
		if (isset($_SERVER['HTTP_VIA'])) {
			// 找不到为flase,否则为true
			return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
		}
		// 脑残法，判断手机发送的客户端标志,兼容性有待提高
		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			$clientkeywords = array('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile');
			// 从HTTP_USER_AGENT中查找手机浏览器的关键字
			if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
				return true;
			}

		}
		// 协议法，因为有可能不准确，放到最后判断
		if (isset($_SERVER['HTTP_ACCEPT'])) {
			// 如果只支持wml并且不支持html那一定是移动设备
			// 如果支持wml和html但是wml在html之前则是移动设备
			if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
				return true;
			}
		}
		return false;
	}

	public function is_wechat() {
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
			return true;
		}
		return false;
	}

	protected function getCurrentTitle() {
		$mate = '';
		$controller = strtr(strtolower($this->request->controller()), '.', '\\');
		$action = $this->request->action();
		$class = "\\app\\controller\\" . $controller;
		if (class_exists($class)) {
			$reflection = new \ReflectionClass($class);
			$group_doc = $this->Parser($reflection->getDocComment());
			if (isset($group_doc['title'])) {
				$mate = $group_doc['title'];
			}
			$method = $reflection->getMethods(\ReflectionMethod::IS_FINAL | \ReflectionMethod::IS_PUBLIC);
			foreach ($method as $key => $v) {
				if ($action == $v->name) {
					$title_doc = $this->Parser($v->getDocComment());
					if (isset($title_doc['title'])) {
						$mate = $title_doc['title'];
					}
				}
			}
		}
		return $mate;
	}

	protected function Parser($text) {
		$doc = new \doc\Doc();
		return $doc->parse($text);
	}

	protected function setSeo($title = '', $keywords = '', $description = '') {
		$seo = array(
			'title' => $title,
			'keywords' => $keywords,
			'description' => $description,
		);
		//获取还没有经过变量替换的META信息
		$meta = SeoRule::getMetaOfCurrentPage($this->request, $seo);
		foreach ($seo as $key => $item) {
			if (is_array($item)) {
				$item = implode(',', $item);
			}
			$meta[$key] = str_replace("[" . $key . "]", $item . '|', $meta[$key]);
		}

		$data = array(
			'title' => $meta['title'],
			'keywords' => $meta['keywords'],
			'description' => $meta['description'],
		);
		View::assign('seo', $data);
	}

	protected function getAddonsConfig() {
		$config = [];
		$addons = $this->request->param('addon');
		$addon = get_addons_instance($addons)->getConfig();
		$config = \app\model\Addons::where('name', $addons)->value('config');

		return $config ? json_decode($config, true) : $addon;
	}
}
