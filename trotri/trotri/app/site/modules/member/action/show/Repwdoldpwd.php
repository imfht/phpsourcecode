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

/**
 * Repwdoldpwd class file
 * 通过旧密码重设新密码
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Login.php 1 2014-08-08 15:49:14Z huan.song $
 * @package modules.member.action.show
 * @since 1.0
 */
class Repwdoldpwd extends library\ShowAction
{
	/**
	 * @var boolean 是否验证登录
	 */
	protected $_validLogin = true;

	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\interfaces\Action::run()
	 */
	public function run()
	{
		Text::_('MOD_MEMBER__');

		$this->render();
	}
}
