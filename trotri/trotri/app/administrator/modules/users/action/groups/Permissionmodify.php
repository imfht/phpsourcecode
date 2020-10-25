<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\users\action\groups;

use library\actions;
use tfc\ap\Ap;
use tfc\mvc\Mvc;
use libapp\Model;
use library\SubmitType;
use library\ErrorNo;

/**
 * Permissionmodify class file
 * 编辑“权限设置”数据
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Permissionmodify.php 1 2014-05-30 11:00:05Z Code Generator $
 * @package modules.users.action.groups
 * @since 1.0
 */
class Permissionmodify extends actions\Modify
{
	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\interfaces\Action::run()
	 */
	public function run()
	{
		$id = $this->getPk();
		if ($id <= 0) {
			$this->err404();
		}

		$ret = array();

		$req = Ap::getRequest();
		$mod = Model::getInstance('Groups');
		$submitType = new SubmitType();
		if ($submitType->isPost()) {
			$ret = $mod->modifyPermissionByPk($id, $req->getPost('amcas', array()));
			if ($ret['err_no'] === ErrorNo::SUCCESS_NUM) {
				if ($submitType->isTypeSave()) {
					$this->forward('permissionmodify', Mvc::$controller, Mvc::$module, $ret);
				}
				elseif ($submitType->isTypeSaveClose()) {
					$url = $this->applyParams($mod->getLLU(), $ret);
					$this->redirect($url);
				}
			}
			else {
				$amcas = $mod->getAmcas($id);
				$ret['data'] = $amcas['data'];
			}
		}
		else {
			$ret = $mod->getAmcas($id);
		}

		$this->assign('id', $id);
		$this->assign('breadcrumbs', $mod->getBreadcrumbs($id));
		$this->assign('elements', $mod);
		$this->render($ret);
	}
}
