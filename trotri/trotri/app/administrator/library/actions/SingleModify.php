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

use tfc\ap\Ap;
use tfc\auth\Role;
use libapp\Model;

/**
 * SingleModify abstract class file
 * SingleModify基类，编辑单个字段
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Modify.php 1 2013-04-05 01:08:06Z huan.song $
 * @package library.actions
 * @since 1.0
 */
abstract class SingleModify extends Modify
{
	/**
	 * @var integer 允许的权限
	 */
	protected $_power = Role::UPDATE;

	/**
	 * 执行操作：编辑单个字段和批量编辑单个字段
	 * @param string $className
	 * @param string $moduleName
	 * @return void
	 */
	public function execute($className, $moduleName = '')
	{
		$ret = array();
		$req = Ap::getRequest();
		$mod = Model::getInstance($className, $moduleName);

		$columnName = $req->getTrim('column_name', '');
		$value = $req->getParam('value', '');
		if ($columnName === '') {
			$this->err404();
		}

		$funcName = $this->getFuncName();
		$ret = $mod->$funcName($this->getPk(), array($columnName => $value));

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
		$funcName = $this->isBatch() ? 'batchModifyByPk' : 'modifyByPk';
		return $funcName;
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
