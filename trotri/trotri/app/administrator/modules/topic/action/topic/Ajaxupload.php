<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\topic\action\topic;

use library\DataAction;
use tfc\ap\Ap;
use tfc\auth\Role;
use files\services\Upload;

/**
 * Ajaxupload class file
 * Ajax上传图片
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Ajaxupload.php 1 2014-09-29 23:33:28Z huan.song $
 * @package modules.topic.action.topic
 * @since 1.0
 */
class Ajaxupload extends DataAction
{
	/**
	 * @var integer 允许的权限
	 */
	protected $_power = Role::INSERT;

	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\interfaces\Action::run()
	 */
	public function run()
	{
		$req = Ap::getRequest();

		$ret = Upload::posts($_FILES['upload']);
		$this->display($ret);
	}
}
