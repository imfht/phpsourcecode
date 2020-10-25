<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\posts\action\show;

use library\ShowAction;
use tfc\ap\Ap;
use libapp\Model;

/**
 * View class file
 * 文档详情页面
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: View.php 1 2014-01-18 14:19:29Z huan.song $
 * @package modules.posts.action.show
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
		$mod = Model::getInstance('Posts', 'posts');
		$id = $req->getInteger('id');
		if ($id <= 0) {
			$this->err404();
		}

		$row = $mod->findByPk($id);
		if (!$row || !is_array($row) || !isset($row['post_id']) || !isset($row['title'])) {
			$this->err404();
		}

		$prev = $mod->getPrevByCatId($row['category_id'], $row['sort']);
		$next = $mod->getNextByCatId($row['category_id'], $row['sort']);

		$this->assign('prev', $prev);
		$this->assign('next', $next);

		$this->assign('meta_title', isset($row['title']) ? $row['title'] : '');
		$this->assign('meta_keywords', isset($row['keywords']) ? $row['keywords'] : '');
		$this->assign('meta_description', isset($row['description']) ? $row['description'] : '');
		$this->render($row);
	}
}
