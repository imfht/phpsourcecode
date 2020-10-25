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
use tfc\util\Paginator;
use tfc\saf\Text;
use library\PageHelper;

/**
 * PaginatorBuilder class file
 * 分页处理类，基于Bootstrap-v3前端开发框架
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: PaginatorBuilder.php 1 2013-05-18 14:58:59Z huan.song $
 * @package views.bootstrap.widgets
 * @since 1.0
 */
class PaginatorBuilder extends Widget
{
	/**
	 * @var string 样式名
	 */
	public $className = 'pagination';

	/**
	 * @var string 被禁用按钮的样式名
	 */
	public $disabledClassName = 'disabled';

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
		$paginator = $this->getPaginator();
		if ($paginator === null) {
			return ;
		}

		$urlManager = $this->getUrlManager();
		$html = $this->getHtml();
		$disabledAttribute = array('class' => $this->disabledClassName);
		$activeAttribute = array('class' => $this->activeClassName);
		$prevPageLabel = Text::_('CFG_SYSTEM_GLOBAL_PAGE_PREV');
		$nextPageLabel = Text::_('CFG_SYSTEM_GLOBAL_PAGE_NEXT');

		$url     = $paginator->getUrl();
		$pageVar = $paginator->getPageVar();
		$pages   = $paginator->getPages();

		$prevLink = $html->a($prevPageLabel, $urlManager->applyParams($url, array($pageVar => $pages['prev'])));
		$nextLink = $html->a($nextPageLabel, $urlManager->applyParams($url, array($pageVar => $pages['next'])));

		$prevElement = $html->tag('li', (($pages['curr'] <= 1) ? $disabledAttribute : array()), $prevLink);
		$nextElement = $html->tag('li', (($pages['curr'] >= $pages['end']) ? $disabledAttribute : array()), $nextLink);

		$listElements = '';
		for ($pageNo = $pages['first']; $pageNo <= $pages['last']; $pageNo++) {
			$listLink = $html->a($pageNo, $urlManager->applyParams($url, array($pageVar => $pageNo)));
			$listElements .= $html->tag('li', (($pageNo === $pages['curr']) ? $activeAttribute : array()), $listLink);
		}

		echo $html->tag('ul', array('class' => $this->className), $prevElement . $listElements . $nextElement);
		echo '<!-- /.pagination -->';
	}

	/**
	 * 获取分页处理类
	 * @return \tfc\util\Paginator
	 */
	public function getPaginator()
	{
		// 总的记录数 <= 0，表示不需要分页
		if (($totalRows = $this->getTotalRows()) <= 0) {
			return null;
		}

		// 每页展示的行数 <= 0，表示不需要分页
		if (($listRows = $this->getListRows()) <= 0) {
			return null;
		}

		$firstRow = max($this->getFirstRow(), 0);
		$currPage = floor($firstRow / $listRows) + 1;

		$paginator = new Paginator($totalRows, $this->getUrl(), PageHelper::getPageVar());
		$paginator->setListPages(PageHelper::getListPages());
		$paginator->setListRows($listRows);
		$paginator->setCurrPage($currPage);

		return $paginator;
	}

	/**
	 * 获取分页参数：URL
	 * @return string
	 */
	public function getUrl()
	{
		$url = isset($this->_tplVars['url']) ? $this->_tplVars['url'] : '';
		return $url;
	}

	/**
	 * 获取分页参数：总的记录数
	 * @return integer
	 */
	public function getTotalRows()
	{
		$totalRows = isset($this->_tplVars['total']) ? (int) $this->_tplVars['total'] : 0;
		return $totalRows;
	}

	/**
	 * 获取分页参数：当前页开始的记录数
	 * @return integer
	 */
	public function getFirstRow()
	{
		$firstRow = isset($this->_tplVars['offset']) ? (int) $this->_tplVars['offset'] : 0;
		return $firstRow;
	}

	/**
	 * 获取分页参数：每页展示的行数
	 * @return integer
	 */
	public function getListRows()
	{
		$listRows = isset($this->_tplVars['limit']) ? (int) $this->_tplVars['limit'] : 0;
		return $listRows;
	}
}
