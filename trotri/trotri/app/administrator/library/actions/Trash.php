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
 * Trash abstract class file
 * Trash基类，将数据移至回收站和从回收站还原数据
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Trash.php 1 2013-04-05 01:08:06Z huan.song $
 * @package library.actions
 * @since 1.0
 */
abstract class Trash extends ShowAction
{
	/**
	 * @var integer 允许的权限
	 */
	protected $_power = Role::DELETE;

	/**
	 * 执行操作：移至回收站、从回收站还原数据、批量移至回收站、批量从回收站还原数据
	 * @param string $className
	 * @param string $moduleName
	 * @return void
	 */
	public function execute($className, $moduleName = '')
	{
		$mod = Model::getInstance($className, $moduleName);
		$funcName = $this->getFuncName();
		$ret = $mod->$funcName($this->getPk());

		$url = $this->applyParams($mod->getLLU(), $ret);
		$this->redirect($url);
	}

	/**
	 * 获取ID值，如果是批量提交，则ID为英文逗号分隔的字符串
	 * @return mixed
	 */
	public function getPk()
	{
		if ($this->isBatch()) {
			$ids = Ap::getRequest()->getTrim('ids');
			$ids = explode(',', $ids);
			return $ids;
		}

		return Ap::getRequest()->getInteger('id');
	}

	/**
	 * 获取方法名
	 * @return string
	 */
	public function getFuncName()
	{
		$funcName = $this->isRestore() ? 'restoreByPk' : 'trashByPk';
		if ($this->isBatch()) {
			$funcName = 'batch' . ucfirst($funcName);
		}

		return $funcName;
	}

	/**
	 * 验证是否是还原数据
	 * @return boolean
	 */
	public function isRestore()
	{
		$isRestore = Ap::getRequest()->getInteger('is_restore');
		return ($isRestore === 1);
	}

	/**
	 * 验证是否是批量提交
	 * @return boolean
	 */
	public function isBatch()
	{
		$isBatch = Ap::getRequest()->getInteger('is_batch');
		return ($isBatch === 1);
	}
}
