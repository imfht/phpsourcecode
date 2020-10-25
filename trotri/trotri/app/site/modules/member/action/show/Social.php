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
use tfc\auth\Identity;
use tfc\saf\Text;
use libapp\Model;
use member\services\DataSocial;

/**
 * Social class file
 * 会员详情
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Reg.php 1 2014-08-08 15:49:14Z huan.song $
 * @package modules.member.action.show
 * @since 1.0
 */
class Social extends library\ShowAction
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

		$mod = Model::getInstance('Social', 'member');
		$row = $mod->findByPk(Identity::getUserId());
		if (is_array($row) && isset($row['birth_ymd'])) {
			$row['birth_y'] = substr($row['birth_ymd'], 0, 4);
			$row['birth_m'] = substr($row['birth_ymd'], 5, 2);
			$row['birth_d'] = substr($row['birth_ymd'], 8, 2);
		}
		//\tfc\saf\debug_dump($row);
		$this->assign('sex_enum', DataSocial::getSexEnum());
		$this->assign('interests_enum', DataSocial::getInterestsEnum());

		$this->render($row);
	}
}
