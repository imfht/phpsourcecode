<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\system\action\regions;

use library\DataAction;
use tfc\ap\Ap;
use tfc\auth\Role;
use tfc\saf\Text;
use libsrv\Service;

/**
 * Index class file
 * 查询数据列表
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Index.php 1 2014-12-01 16:15:48Z Code Generator $
 * @package modules.system.action.regions
 * @since 1.0
 */
class Index extends DataAction
{
	/**
	 * @var integer 允许的权限
	 */
	protected $_power = Role::SELECT;

	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\interfaces\Action::run()
	 */
	public function run()
	{
		$req = Ap::getRequest();
		$pid = $req->getInteger('pid');
		$def = $req->getInteger('def');
		$srv = Service::getInstance('Regions', 'system');

		$data = $srv->findPairs($pid);

		if ($data && $def === 1) {
			$id = array_shift(array_keys(array_slice($data, 0, 1, true)));
			$type = $srv->getRegionTypeByRegionId($id);
			$hint = $srv->getRegionTypeLangByRegionType($type);
			if ($hint === '') {
				$hint = Text::_('CFG_SYSTEM_GLOBAL_SELECT_HINT');
			}

			$default = array(0 => '--' . $hint . '--');
			$data = $default + $data;
		}

		$this->display($data);
	}
}
