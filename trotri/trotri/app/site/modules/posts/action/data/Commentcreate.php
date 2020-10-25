<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\posts\action\data;

use library\DataAction;
use tfc\ap\Ap;
use libapp\Model;

/**
 * Commentcreate class file
 * Ajax提交评论
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Commentcreate.php 1 2014-01-18 14:19:29Z huan.song $
 * @package modules.posts.action.data
 * @since 1.0
 */
class Commentcreate extends DataAction
{
	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\interfaces\Action::run()
	 */
	public function run()
	{
		$req = Ap::getRequest();

		$authorName = $req->getTrim('author_name');
		$authorMail = $req->getTrim('author_mail');
		$content = $req->getParam('content');
		$postId = $req->getInteger('post_id');
		$commentPid = $req->getInteger('comment_pid');

		$mod = Model::getInstance('Comments', 'posts');
		$ret = $mod->create(array(
			'author_name' => $authorName,
			'author_mail' => $authorMail,
			'content' => $content,
			'post_id' => $postId,
			'comment_pid' => $commentPid
		));

		$this->display($ret);
	}
}
