<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace views\bootstrap\widgets;

use tfc\mvc\Widget;

/**
 * Breadcrumbs class file
 * 面包屑处理类，基于Bootstrap-v3前端开发框架
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Breadcrumbs.php 1 2014-02-10 14:58:59Z huan.song $
 * @package views.bootstrap.widgets
 * @since 1.0
 */
class Breadcrumbs extends Widget
{
	/**
	 * @var string 样式名
	 */
	public $className = 'breadcrumb';

	/**
	 * @var string 当前页按钮的样式名
	 */
	public $activeClassName = 'active';

	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\Widget::run()
	 */
	public function run()
	{
		$html = $this->getHtml();
		$breadcrumbs = array_values($this->_tplVars);
		$total = count($breadcrumbs);

		echo $html->openTag('ol', array('class' => $this->className)), "\n";
		for ($i = 0; $i < $total; $i++) {
			if ($i < ($total - 1)) {
				echo $html->tag('li', array(), $html->a($breadcrumbs[$i]['label'], $breadcrumbs[$i]['href'])), "\n";
			}
			else {
				echo $html->tag('li', array('class' => $this->activeClassName), $breadcrumbs[$i]['label']), "\n";
			}
		}

		echo $html->closeTag('ol');
		echo '<!-- /.breadcrumb -->';
	}
}
