<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace libapp;

use tfc\ap\Ap;
use tfc\ap\ErrorException;
use tfc\ap\InvalidArgumentException;
use tfc\mvc\Mvc;
use tfc\mvc\Action;
use tfc\util\Encoder;
use tfc\saf\Cfg;

/**
 * BaseAction abstract class file
 * Action基类
 * request中保留参数：version（当前的版本）、ie（输入内容编码）、ol（输出语种）、od（输出数据格式）
 * <pre>
 * 配置 /cfg/app/appname/main.php：
 * return array (
 *   'encoding' => 'utf-8', // 项目编码，不区分大小写
 *   'language' => 'zh-CN', // 输出的语言种类，区分大小写
 * );
 * </pre>
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: BaseAction.php 1 2013-04-05 01:08:06Z huan.song $
 * @package libapp
 * @since 1.0
 */
abstract class BaseAction extends Action
{
	/**
	 * @var array 项目支持的编码
	 */
	protected $_encodings = array('UTF-8', 'GBK');

	/**
	 * @var array 项目支持的语言种类
	 */
	protected $_languageTypes = array('zh-CN', 'en-GB');

	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\Action::_init()
	 */
	protected function _init()
	{
		parent::_init();
		$this->_initVersion();
		$this->_initEncoding();
		$this->_initLanguageType();
	}

	/**
	 * 初始化当前的版本
	 * @return void
	 */
	protected function _initVersion()
	{
		// 从RGP中获取‘version’的值（version）
		$version = Ap::getRequest()->getTrim('version');
		if ($version !== '') {
			Ap::setVersion($version);
		}
	}

	/**
	 * 初始化项目编码和输入内容编码，如果输入编码和项目编码不一致，自动转换RPG中数据编码
	 * @return void
	 * @throws InvalidArgumentException 如果不是可支持的编码，抛出异常
	 */
	protected function _initEncoding()
	{
		// 验证配置中的项目编码是否合法
		try {
			$encoding = strtoupper(trim(Cfg::getApp('encoding')));
			Ap::setEncoding($encoding);
		}
		catch (ErrorException $e) {
		}

		if (!in_array(Ap::getEncoding(), $this->_encodings)) {
			throw new InvalidArgumentException(
				'BaseAction is unable to determine the charset of the config.'
			);
		}

		// 从RGP中获取‘ie’的值（input encode），并验证是否合法
		$encoding = Ap::getRequest()->getTrim('ie');
		if ($encoding !== '') {
			$encoding = strtoupper($encoding);
			if (!in_array($encoding, $this->_encodings)) {
				throw new InvalidArgumentException(
					'BaseAction is unable to determine the charset of the request.'
				);
			}

			// 转换输入内容编码
			if (Ap::getEncoding() !== $encoding) {
				$encoder = Encoder::getInstance();
				$_GET = $encoder->convert($_GET, $encoding);
				$_POST = $encoder->convert($_POST, $encoding);
				// $_COOKIE = $encoder->convert($_COOKIE, $encoding);
			}
		}
	}

	/**
	 * 初始化输出的语言种类
	 * @return void
	 * @throws InvalidArgumentException 如果不是可支持的输出语种，抛出异常
	 */
	protected function _initLanguageType()
	{
		// 验证配置中的当前输出语种是否合法
		try {
			$languageType = trim(Cfg::getApp('language'));
			Ap::setLanguageType($languageType);
		}
		catch (ErrorException $e) {
		}

		if (!in_array(Ap::getLanguageType(), $this->_languageTypes)) {
			throw new InvalidArgumentException(
				'BaseAction is unable to determine the language of the config.'
			);
		}

		// 从RGP中获取‘ol’的值（output language type），并验证是否合法
		// 以RGP中指定的输出语种为主
		$languageType = Ap::getRequest()->getTrim('ol');
		if ($languageType !== '') {
			if (in_array($languageType, $this->_languageTypes)) {
				Ap::setLanguageType($languageType);
			}
			else {
				throw new InvalidArgumentException(
					'BaseAction is unable to determine the language of the request.'
				);
			}
		}
	}

	/**
	 * 获取URL管理类
	 * @return \tfc\mvc\UrlManager
	 */
	public function getUrlManager()
	{
		return Mvc::getView()->getUrlManager();
	}

	/**
	 * 在URL后拼接QueryString参数
	 * @param string $url
	 * @param array $params
	 * @return string
	 */
	public function applyParams($url, array $params = array())
	{
		return $this->getUrlManager()->applyParams($url, $params);
	}

	/**
	 * 页面重定向到当前页面
	 * @param array $params
	 * @param string $message
	 * @param integer $delay
	 * @return void
	 */
	public function refresh($params = array(), $message = '', $delay = 0)
	{
		$url = $this->getUrlManager()->applyParams($this->getUrlManager()->getRequestUri(), $params);
		$this->redirect($url, $message, $delay);
	}

	/**
	 * 页面重定向到指定的路由
	 * @param string $action
	 * @param string $controller
	 * @param string $module
	 * @param array $params
	 * @param string $message
	 * @param integer $delay
	 * @return void
	 */
	public function forward($action = '', $controller = '', $module = '', array $params = array(), $message = '', $delay = 0)
	{
		$url = $this->getUrlManager()->getUrl($action, $controller, $module, $params);
		$this->redirect($url, $message, $delay);
	}

	/**
	 * 页面重定向到指定的链接
	 * @param string $url
	 * @param string $message
	 * @param integer $delay
	 * @return void
	 */
	public function redirect($url, $message = '', $delay = 0)
	{
		Ap::getResponse()->redirect($url, $message, $delay);
		exit;
	}
}
