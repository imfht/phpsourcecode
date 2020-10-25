<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\poll\action\polloptions;

use library\actions;
use libapp\Model;

/**
 * Modify class file
 * 编辑数据
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Modify.php 1 2014-12-06 22:30:41Z Code Generator $
 * @package modules.poll.action.polloptions
 * @since 1.0
 */
class Modify extends actions\Modify
{
	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\interfaces\Action::run()
	 */
	public function run()
	{
		$mod = Model::getInstance('Polloptions');
		$pollId = $mod->getPollId();
		if ($pollId <= 0) {
			$this->err404();
		}

		$this->assign('poll_id', $pollId);
		$this->assign('poll_name', $mod->getPollNameByPollId($pollId));
		$this->assign('poll_key', $mod->getPollKeyByPollId($pollId));
		$this->execute('Polloptions');
	}
}
