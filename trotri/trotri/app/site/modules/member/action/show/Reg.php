<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\member\action\show;

use library;
use tfc\saf\Text;
use library\PageHelper;

/**
 * Reg class file
 * 会员注册
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Reg.php 1 2014-08-08 15:49:14Z huan.song $
 * @package modules.member.action.show
 * @since 1.0
 */
class Reg extends library\ShowAction
{
	/**
	 * @var boolean 是否验证登录
	 */
	protected $_validLogin = false;

	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\interfaces\Action::run()
	 */
	public function run()
	{
		Text::_('MOD_MEMBER__');

		$this->assign('http_referer', PageHelper::getHttpReferer());
		$this->render();
	}
}
