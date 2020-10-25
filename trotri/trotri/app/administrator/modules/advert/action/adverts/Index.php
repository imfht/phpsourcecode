<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\advert\action\adverts;

use library\actions;
use tfc\ap\Ap;
use libapp\Model;

/**
 * Index class file
 * 查询数据列表
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Index.php 1 2014-10-26 19:08:03Z Code Generator $
 * @package modules.advert.action.adverts
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
		$typeKey = Ap::getRequest()->getTrim('type_key');
		if ($typeKey === '') {
			$this->err404();
		}

		$typeName = Model::getInstance('Adverts')->getTypeNameByTypeKey($typeKey);

		$this->assign('type_key', $typeKey);
		$this->assign('type_name', $typeName);

		Ap::getRequest()->setParam('order', 'sort');
		$this->execute('Adverts');
	}
}
