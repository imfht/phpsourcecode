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
use tfc\mvc\Mvc;
use libapp\Model;

/**
 * View class file
 * 专题详情页面
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: View.php 1 2014-01-18 14:19:29Z huan.song $
 * @package modules.topic.action.show
 * @since 1.0
 */
class View extends ShowAction
{
	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\interfaces\Action::run()
	 */
	public function run()
	{
		$req = Ap::getRequest();
		$mod = Model::getInstance('Topic', 'topic');
		$key = $req->getTrim('key');
		if ($key === '') {
			$this->err404();
		}

		$row = $mod->findByTopicKey($key);
		if (!$row || !is_array($row) || !isset($row['topic_id']) || !isset($row['topic_name'])) {
			$this->err404();
		}

		$this->assignSystem();
		$this->assignUrl();
		$this->assignLanguage();

		$this->assign($row);
		Mvc::getView()->display($this->getDefaultTplName());
	}
}
