<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\member\action\data;

use library\DataAction;
use tfc\ap\Ap;
use files\services\Upload AS FileUpload;

/**
 * Upload class file
 * Ajax上传图片
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Upload.php 1 2014-09-29 23:33:28Z huan.song $
 * @package modules.member.action.data
 * @since 1.0
 */
class Upload extends DataAction
{
	/**
	 * @var boolean 是否验证登录，默认不验证
	 */
	protected $_validLogin = true;

	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\interfaces\Action::run()
	 */
	public function run()
	{
		$req = Ap::getRequest();

		$ret = FileUpload::headPortrait($_FILES['file']);
		$this->display($ret);
	}
}
