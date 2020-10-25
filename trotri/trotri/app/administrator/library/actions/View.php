<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace library\actions;

use library\ShowAction;
use tfc\ap\Ap;
use tfc\auth\Role;
use libapp\Model;

/**
 * View abstract class file
 * View基类，展示数据详情
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: View.php 1 2013-04-05 01:08:06Z huan.song $
 * @package library.actions
 * @since 1.0
 */
abstract class View extends ShowAction
{
	/**
	 * @var integer 允许的权限
	 */
	protected $_power = Role::SELECT;

	/**
	 * 执行操作：查询数据详情
	 * @param string $className
	 * @param string $moduleName
	 * @return void
	 */
	public function execute($className, $moduleName = '')
	{
		$id = $this->getPk();
		if ($id <= 0) {
			$this->err404();
		}

		$req = Ap::getRequest();
		$mod = Model::getInstance($className, $moduleName);
		$ret = $mod->findByPk($id);

		$this->assign('elements', $mod);
		$this->render($ret);
	}

	/**
	 * 获取ID值
	 * @return integer
	 */
	public function getPk()
	{
		return Ap::getRequest()->getInteger('id');
	}
}
