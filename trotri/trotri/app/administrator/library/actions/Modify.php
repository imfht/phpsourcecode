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
use tfc\mvc\Mvc;
use tfc\auth\Role;
use libapp\Model;
use library\SubmitType;
use library\ErrorNo;

/**
 * Modify abstract class file
 * Modify基类，编辑数据
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Modify.php 1 2013-04-05 01:08:06Z huan.song $
 * @package library.actions
 * @since 1.0
 */
abstract class Modify extends ShowAction
{
	/**
	 * @var integer 允许的权限
	 */
	protected $_power = Role::UPDATE;

	/**
	 * 执行操作：编辑数据
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

		$ret = array();

		$req = Ap::getRequest();
		$mod = Model::getInstance($className, $moduleName);
		$submitType = new SubmitType();
		if ($submitType->isPost()) {
			$ret = $mod->modifyByPk($id, $req->getPost());
			if ($ret['err_no'] === ErrorNo::SUCCESS_NUM) {
				if ($submitType->isTypeSave()) {
					$this->forward($mod->actNameModify, Mvc::$controller, Mvc::$module, $ret);
				}
				elseif ($submitType->isTypeSaveNew()) {
					$this->forward($mod->actNameCreate, Mvc::$controller, Mvc::$module, $ret);
				}
				elseif ($submitType->isTypeSaveClose()) {
					$url = $this->applyParams($mod->getLLU(), $ret);
					$this->redirect($url);
				}
			}

			$ret['data'] = $req->getPost();
		}
		else {
			$ret = $mod->findByPk($id);
		}

		$this->assign('id', $id);
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
