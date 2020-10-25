<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\system\action\show;

use library\ShowAction;
use library\PageHelper;

/**
 * Err404 class file
 * 404错误页
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Err404.php 1 2014-01-18 14:19:29Z huan.song $
 * @package modules.system.action.show
 * @since 1.0
 */
class Err404 extends ShowAction
{
	/**
	 * @var boolean 是否验证登录
	 */
	protected $_validLogin = false;

	/**
	 * @var string 页面首次渲染的布局名
	 */
	public $layoutName = 'column1';

	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\interfaces\Action::run()
	 */
	public function run()
	{
		$httpReferer = PageHelper::getHttpReferer();

		$this->assign('http_referer', $httpReferer);
		$this->render();
	}
}
