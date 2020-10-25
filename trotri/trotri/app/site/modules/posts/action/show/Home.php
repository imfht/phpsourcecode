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
use tfc\mvc\Mvc;

/**
 * Home class file
 * 文档首页
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Home.php 1 2014-01-18 14:19:29Z huan.song $
 * @package modules.posts.action.show
 * @since 1.0
 */
class Home extends ShowAction
{
	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\interfaces\Action::run()
	 */
	public function run()
	{
		$beforeContent = Mvc::getView()->widget(
			'components\adverts\Adverts',
			array(
				'type_key' => 'mainslide'
			),
			array(), true
		);

		$this->assign('beforeLayoutContent', $beforeContent);

		$this->render();
	}
}
