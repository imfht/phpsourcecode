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
use libapp\ErrorNo;

/**
 * Preview class file
 * 广告预览
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: View.php 1 2014-10-26 19:08:03Z Code Generator $
 * @package modules.advert.action.adverts
 * @since 1.0
 */
class Preview extends actions\View
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

		$req = Ap::getRequest();
		$mod = Model::getInstance('Adverts');
		$ret = $mod->findByPk($id);

		if ($ret['err_no'] !== ErrorNo::SUCCESS_NUM) {
			echo $ret['err_msg'];
		}
		else {
			echo $ret['data']['show_code'];
		}

		exit;
	}
}
