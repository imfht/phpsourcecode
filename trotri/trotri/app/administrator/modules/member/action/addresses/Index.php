<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\member\action\addresses;

use library\actions;
use tfc\ap\Ap;
use libapp\Model;

/**
 * Index class file
 * 查询数据列表
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Index.php 1 2014-12-04 14:57:46Z Code Generator $
 * @package modules.member.action.addresses
 * @since 1.0
 */
class Index extends actions\Index
{
	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\interfaces\Action::run()
	 */
	public function run()
	{
		$memberId = Ap::getRequest()->getInteger('member_id');
		if ($memberId <= 0) {
			$this->err404();
		}

		$mod = Model::getInstance('Addresses');
		$ret = $mod->findAll($memberId);

		$params = $this->getLLUParams(array('attributes' => array('member_id' => $memberId)));
		$mod->setLLU($params);

		$loginName = Model::getInstance('Portal')->getLoginNameByMemberId($memberId);

		$this->assign('member_id', $memberId);
		$this->assign('login_name', $loginName);
		$this->assign('elements', $mod);
		$this->render($ret);
	}
}
