<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace library;

use libapp\BaseAction;
use tfc\ap\Ap;
use tfc\ap\Registry;
use tfc\mvc\Mvc;
use tfc\auth\Identity;
use tfc\saf\Text;
use tfc\saf\Log;
use tfc\saf\Cfg;
use system\services\Options;

/**
 * ShowAction abstract class file
 * ShowAction基类，用于展示数据，加载模板
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: ShowAction.php 1 2013-04-05 01:08:06Z huan.song $
 * @package library
 * @since 1.0
 */
abstract class ShowAction extends BaseAction
{
	/**
	 * @var boolean 是否验证登录，默认不验证
	 */
	protected $_validLogin = false;

	/**
	 * @var string 页面首次渲染的布局名
	 */
	public $layoutName = 'column2';

	/**
	 * (non-PHPdoc)
	 * @see \libapp\BaseAction::_init()
	 */
	protected function _init()
	{
		parent::_init();
		$this->_isLogin();
		$this->_initView();
		$this->assignAccount();
	}

	/**
	 * 检查用户是否登录，如果没有登录，跳转到登录页面
	 * @return void
	 */
	protected function _isLogin()
	{
		if (!$this->_validLogin) {
			return ;
		}

		if (Identity::isLogin()) {
			return ;
		}

		$this->toLogin();
	}

	/**
	 * 页面重定向到登录页面
	 * @return void
	 */
	public function toLogin()
	{
		$this->forward('login', 'show', 'member');
	}

	/**
	 * 页面重定向到404页面
	 * @return void
	 */
	public function err404()
	{
		$this->forward('err404', 'show', 'system', array(
			'http_referer' => Mvc::getView()->getUrlManager()->getUrl(Mvc::$action, Mvc::$controller, Mvc::$module)
		));
	}

	/**
	 * 页面重定向到403页面
	 * @return void
	 */
	public function err403()
	{
		$this->forward('err403', 'show', 'system', array(
			'http_referer' => Mvc::getView()->getUrlManager()->getUrl(Mvc::$action, Mvc::$controller, Mvc::$module)
		));
	}

	/**
	 * 页面重定向到500页面
	 * @return void
	 */
	public function err500()
	{
		$this->forward('err500', 'show', 'system', array(
			'http_referer' => Mvc::getView()->getUrlManager()->getUrl(Mvc::$action, Mvc::$controller, Mvc::$module)
		));
	}

	/**
	 * 初始化模板解析类
	 *
	 * 配置 /cfg/app/appname/main.php：
	 * <pre>
	 * return array (
	 *   'view' => array (
	 *     'skin_name' => 'bootstrap',     // 模板风格
	 *     'charset' => 'utf-8',           // HTML编码
	 *     'tpl_extension' => '.php',      // 模板后缀
	 *     'version' => '1.0',             // Js、Css文件的版本号
	 *     'skin_version' => '3.0.3',      // 模板风格文件的版本号
	 *   ),
	 * );
	 * </pre>
	 * @return void
	 */
	protected function _initView()
	{
		$viw = Mvc::getView();
		$viw->viewDirectory = DIR_APP_VIEWS;
		$viw->skinName      = Cfg::getApp('skin_name', 'view');
		$viw->tplExtension  = Cfg::getApp('tpl_extension', 'view');
		$viw->charset       = Cfg::getApp('charset', 'view');
		$viw->version       = Cfg::getApp('version', 'view');
		$viw->skinVersion   = Cfg::getApp('skin_version', 'view');
	}

	/**
	 * 展示页面，输出数据
	 * @param array $data
	 * @param string $tplName
	 * @return void
	 */
	public function render(array $data = array(), $tplName = null)
	{
		$this->assignSystem();
		$this->assignUrl();
		$this->assignLanguage();

		$viw = Mvc::getView();
		$viw->addLayoutName('layouts' . DS . $this->layoutName);
		if ($tplName === null) {
			$tplName = $this->getDefaultTplName();
		}

		isset($data['err_no'])  || $data['err_no'] = ErrorNo::SUCCESS_NUM;
		isset($data['err_msg']) || $data['err_msg'] = '';

		$viw->render($tplName, $data);
	}

	/**
	 * 将会员账户信息设置到模板变量中
	 * @return void
	 */
	public function assignAccount()
	{
		$viw = Mvc::getView();

		$viw->assign('is_login',    Identity::isLogin());
		$viw->assign('member_id',   Identity::getUserId());
		$viw->assign('login_name',  Identity::getLoginName());
		$viw->assign('member_name', Identity::getNickname());
		$viw->assign('type_id',     Identity::getTypeId());
		$viw->assign('rank_id',     Identity::getRankId());
	}

	/**
	 * 将常用数据设置到模板变量中
	 * @return void
	 */
	public function assignSystem()
	{
		$viw = Mvc::getView();

		$viw->assign('app',        APP_NAME);
		$viw->assign('module',     Mvc::$module);
		$viw->assign('controller', Mvc::$controller);
		$viw->assign('action',     Mvc::$action);
		$viw->assign('sidebar',    Mvc::$module . '/' . Mvc::$action . '_sidebar');
		$viw->assign('log_id',     Log::getId());
		$viw->assign('language',   Ap::getLanguageType());

		$viw->assign('urlHelper', UrlHelper::getInstance());
		$viw->assign('site_name', Options::getSiteName());		
		if (!isset($viw->meta_title)) {
			$viw->assign('meta_title', Options::getMetaTitle());
		}
		if (!isset($viw->meta_keywords)) {
			$viw->assign('meta_keywords', Options::getMetaKeywords());
		}
		if (!isset($viw->meta_description)) {
			$viw->assign('meta_description', Options::getMetaDescription());
		}
		$viw->assign('powerby', Options::getPowerby());
		$viw->assign('stat_code', Options::getStatCode());

		if (($wfBackTrace = Registry::get('warning_backtrace')) !== null) {
			$viw->assign('warning_backtrace', $wfBackTrace);
		}
	}

	/**
	 * 将链接信息设置到模板变量中
	 * @return void
	 */
	public function assignUrl()
	{
		$req = Ap::getRequest();
		$viw = Mvc::getView();

		$baseUrl    = $req->getBaseUrl();
		$basePath   = $req->getBasePath();
		$scriptUrl  = $req->getScriptUrl();
		$requestUri = $req->getRequestUri();
		$staticUrl  = $baseUrl . '/static';
		$skinUrl    = $staticUrl . '/' . APP_NAME . '/' . $viw->skinName;

		$viw->assign('root_url',    $baseUrl . '/..');
		$viw->assign('base_path',   $basePath);
		$viw->assign('base_url',    $baseUrl);
		$viw->assign('script_url',  $scriptUrl);
		$viw->assign('request_uri', $requestUri);
		$viw->assign('static_url',  $staticUrl);
		$viw->assign('js_url',      $skinUrl . '/js');
		$viw->assign('css_url',     $skinUrl . '/css');
		$viw->assign('imgs_url',    $skinUrl . '/images');
	}

	/**
	 * 将公共的语言包和当前模块的语言包设置到模板变量中
	 * @return void
	 */
	public function assignLanguage()
	{
		$viw = Mvc::getView();

		Text::_('CFG_SYSTEM__');

		$strings = Text::getStrings();
		$viw->assign($strings);
	}

	/**
	 * 设置一对或多对模板变量
	 * @param mixed $key
	 * @param mixed $value
	 * @return \tfc\mvc\interfaces\View
	 */
	public function assign($key, $value = null)
	{
		return Mvc::getView()->assign($key, $value);
	}

	/**
	 * 获取默认的模板名
	 * @return string
	 */
	public function getDefaultTplName()
	{
		return Mvc::$module . DS . Mvc::$action;
	}
}
