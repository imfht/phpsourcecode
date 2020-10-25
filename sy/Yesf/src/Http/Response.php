<?php
/**
 * HTTP响应类
 * 
 * @author ShuangYa
 * @package Yesf
 * @category Base
 * @link https://www.sylingd.com/
 * @copyright Copyright (c) 2017-2019 ShuangYa
 * @license https://yesf.sylibs.com/license.html
 */
namespace Yesf\Http;

use Yesf\Yesf;
use Yesf\Config;
use Yesf\DI\Container;
use Yesf\Http\Vars as HttpVars;
use Yesf\Exception\InvalidClassException;

class Response {
	public $result;
	protected static $tpl_auto_config = false;
	//模板文件扩展名
	protected static $tpl_extension = 'phtml';
	//模板引擎
	protected static $tpl_engine = null;
	//模板目录
	protected $tpl_path;
	//Swoole的Response
	protected $sw_response = null;
	//是否自动渲染
	protected $tpl_auto = null;
	//默认模板
	protected $tpl_default = '';
	//模板引擎的实例化
	protected $tpl_engine_obj = null;
	//是否已经结束
	protected $is_end = false;
	//Cookie相关配置
	protected static $cookie = [
		'path' => '/',
		'domain' => ''
	];
	/**
	 * 初始化函数
	 * 
	 * @access public
	 */
	public static function init() {
		self::$tpl_auto_config = Yesf::app()->getConfig('view.auto', Yesf::CONF_PROJECT) === true;
		self::$tpl_extension = Yesf::app()->getConfig('view.extension', Yesf::CONF_PROJECT, 'phtml');
		self::$tpl_engine = Template::class;
		Container::getInstance()->setMulti(Template::class, Container::MULTI_CLONE);
	}
	public static function initInWorker() {
		if (Yesf::app()->getConfig('cookie.path')) {
			self::$cookie['path'] = Yesf::app()->getConfig('cookie.path');
		}
		if (Yesf::app()->getConfig('cookie.domain')) {
			self::$cookie['domain'] = Yesf::app()->getConfig('cookie.domain');
		}
	}
	/**
	 * 设置默认模板类
	 * 
	 * @access public
	 * @param string $id
	 */
	public static function setTemplateEngine(string $id) {
		Container::getInstance()->setMulti($id, Container::MULTI_CLONE);
		$clazz = Container::getInstance()->get($id);
		if (!is_subclass_of($clazz, __NAMESPACE__ . '\\TemplateInterface')) {
			throw new InvalidClassException("$clazz not implemented TemplateInterface");
		}
		self::$tpl_engine = $id;
	}
	/**
	 * 设置当前响应使用的模板类
	 * 
	 * @access public
	 * @param string $id
	 */
	public function setCurrentTemplateEngine(string $id) {
		Container::getInstance()->setMulti($id, Container::MULTI_CLONE);
		$clazz = Container::getInstance()->get($id);
		if (!is_subclass_of($clazz, __NAMESPACE__ . '\\TemplateInterface')) {
			throw new InvalidClassException("$clazz not implemented TemplateInterface");
		}
		$this->tpl_engine_obj = $clazz;
	}
	/**
	 * 构建函数
	 * 
	 * @access public
	 * @param object $response Swoole的Response
	 */
	public function __construct($response) {
		$this->sw_response = $response;
		$this->tpl_path = APP_PATH . 'View/';
		$this->tpl_engine_obj = Container::getInstance()->get(self::$tpl_engine);
		$this->result = null;
	}
	public function setTemplatePath($path) {
		$this->tpl_path = $path;
	}
	public function setTemplate($name) {
		$this->tpl_default = $name;
	}
	/**
	 * 关闭模板自动渲染
	 * 
	 * @access public
	 */
	public function disableView() {
		$this->tpl_auto = false;
	}
	/**
	 * 将一个模板的渲染结果输出至浏览器
	 * 
	 * @access public
	 * @param string $tpl 模板路径
	 * @param bool $is_abs_path 是否为绝对路径
	 */
	public function display($tpl = null, $is_abs_path = false) {
		if ($tpl === null) {
			$tpl = $this->tpl_default;
		}
		$data = $this->render($tpl, $is_abs_path);
		if (!empty($data)) $this->write($data);
	}
	/**
	 * 获取模板类实例
	 * 
	 * @access public
	 * @return object
	 */
	public function getTemplate() {
		return $this->tpl_engine_obj;
	}
	/**
	 * 获取一个模板的渲染结果但不输出
	 * 
	 * @access public
	 * @param string $tpl 模板路径
	 * @param bool $is_abs_path 是否为绝对路径
	 * @return string
	 */
	public function render($tpl, $is_abs_path = false) {
		if ($is_abs_path) {
			$_tpl_full_path = $tpl;
		} else {
			$_tpl_full_path = $this->tpl_path . $tpl . '.' . self::$tpl_extension;
		}
		return $this->tpl_engine_obj->render($_tpl_full_path);
	}
	/**
	 * 注册一个模板变量
	 * 
	 * @access public
	 * @param string $k 名称
	 * @param mixed $v 值
	 */
	public function assign($k, $v) {
		$this->tpl_engine_obj->assign($k, $v);
	}
	/**
	 * 清空模板变量
	 * 
	 * @access public
	 */
	public function clearAssign() {
		$this->tpl_engine_obj->clearAssign();
	}
	/**
	 * 将一个字符串输出至浏览器
	 * 
	 * @access public
	 * @param string $content 要输出的字符串
	 */
	public function write($content) {
		$this->sw_response->write($content);
	}
	/**
	 * 将一个内容转为JSON输出至浏览器
	 * 
	 * @access public
	 * @param mixed $content 要输出的内容
	 */
	public function json($content) {
		$this->sw_response->write(json_encode($content));
	}
	/**
	 * 发送一个文件
	 * 
	 * @access public
	 * @param string $filepath
	 * @param int $offset
	 * @param int $length
	 */
	public function sendfile($filepath, $offset, $length) {
		$this->sw_response->sendfile($filepath, $offset, $length);
		$this->is_end = true;
		$this->sw_response = null;
		$this->tpl_engine_obj = null;
	}
	/**
	 * 向浏览器发送一个header信息
	 * 
	 * @access public
	 * @param string $k 名称
	 * @param mixed $v 值
	 */
	public function header($k, $v) {
		$this->sw_response->header($k, $v);
	}
	/**
	 * 发送mimeType的header
	 * 
	 * @access public
	 * @param string $extension 扩展名，例如JSON
	 */
	public function mimeType($extension) {
		$this->header('Content-Type', HttpVars::mimeType($extension));
	}
	/**
	 * 向浏览器发送一个状态码
	 * 
	 * @access public
	 * @param int $code
	 */
	public function status($code) {
		$this->sw_response->status($code);
	}
	/**
	 * 设置Cookie
	 * 
	 * @access public
	 * @param array $param
	 * @param string $param[name] 名称
	 * @param string $param[value] 内容
	 * @param int $param[expire] 过期时间，-1为失效，不传递或0为SESSION，其他为当前时间+$expire
	 * @param string $param[path] 若不传递，则从config读取
	 * @param string $param[domain] 若不传递，则从config读取
	 * @param bool $param[httponly] 是否为httponly
	 */
	public function cookie($param) {
		$name = $param['name'];
		//处理过期时间
		if (!isset($param['expire']) || $param['expire'] === 0) {
			$expire = 0;
		} elseif ($param['expire'] === -1) {
			$expire = time() - 3600;
		} else {
			$expire = time() + $param['expire'];
		}
		//其他参数的处理
		!isset($param['path']) && $param['path'] = self::$cookie['path'];
		!isset($param['domain']) && $param['domain'] = self::$cookie['domain'];
		!isset($param['httponly']) && $param['httponly'] = false;
		//设置
		$this->sw_response->cookie($name, $param['value'], $expire, $param['path'], $param['domain'], $param['https'], $param['httponly']);
	}
	/**
	 * 析构函数
	 * 
	 * @access public
	 */
	public function end() {
		if ($this->is_end) {
			return;
		}
		$this->is_end = true;
		if ($this->sw_response) {
			if (($this->tpl_auto === null && self::$tpl_auto_config) || $this->tpl_auto) {
				$this->display();
			}
			$this->sw_response->end();
			$this->sw_response = null;
		}
		$this->tpl_engine_obj = null;
	}
	public function __destruct() {
		$this->end();
	}
}
