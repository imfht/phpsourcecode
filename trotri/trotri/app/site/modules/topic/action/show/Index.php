<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\topic\action\show;

use library\ShowAction;
use tfc\ap\Ap;
use libapp\Model;
use library\PageHelper;

/**
 * Index class file
 * 专题列表页面
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Index.php 1 2014-01-18 14:19:29Z huan.song $
 * @package modules.topic.action.show
 * @since 1.0
 */
class Index extends ShowAction
{
	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\interfaces\Action::run()
	 */
	public function run()
	{
		$req = Ap::getRequest();
		$mod = Model::getInstance('Topic', 'topic');

		$paged = PageHelper::getCurrPage();
		$ret = $mod->findRows($paged);

		$this->render($ret);
	}
}
