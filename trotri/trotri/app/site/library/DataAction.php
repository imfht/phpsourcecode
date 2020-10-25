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

use libapp;
use tfc\auth\Identity;

/**
 * DataAction abstract class file
 * DataAction基类，用于Ajax调用和对其他项目提供的接口，需要规范输出数据格式
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: DataAction.php 1 2013-04-05 01:08:06Z huan.song $
 * @package library
 * @since 1.0
 */
abstract class DataAction extends libapp\DataAction
{
	/**
	 * @var boolean 是否验证登录，默认不验证
	 */
	protected $_validLogin = false;

	/**
	 * (non-PHPdoc)
	 * @see \libapp\DataAction::_init()
	 */
	protected function _init()
	{
		parent::_init();
		$this->_isLogin();
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

		$this->display(array(
			'err_no' => ErrorNo::ERROR_NO_LOGIN,
			'err_msg' => libapp\Lang::_('ERROR_MSG_ERROR_NO_LOGIN')
		));
	}
}
