<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace library;

use tfc\ap\Ap;
use tfc\ap\ErrorException;
use tfc\mvc\Mvc;
use tfc\util\Paginator;
use tfc\saf\Cfg;

/**
 * PageHelper class file
 * 页面辅助类
 * <pre>
 * 配置 /cfg/app/appname/main.php：
 * return array (
 *   'paginator' => array (
 *     'page_var' => 'paged',      // 从$_GET或$_POST中获取当前页的键名，缺省：paged
 *     'list_rows_var' => 'limit', // 从$_GET或$_POST中获取每页展示的行数的键名，缺省：limit
 *     'list_rows' => 10,          // 每页展示的行数
 *     'list_pages' => 4,          // 每页展示的页码数
 *   ),
 * );
 * </pre>
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: PageHelper.php 1 2013-04-05 01:08:06Z huan.song $
 * @package library
 * @since 1.0
 */
class PageHelper
{
	/**
	 * 获取上一个页面链接
	 * @return string
	 */
	public static function getHttpReferer()
	{
		$referer = Ap::getRequest()->getTrim('http_referer');
		if ($referer !== '') {
			return $referer;
		}

		return Ap::getRequest()->getServer('HTTP_REFERER', '');
	}

	/**
	 * 在URL后拼接QueryString参数
	 * @param string $url
	 * @param array $params
	 * @return string
	 */
	public static function applyParams($url, array $params = array())
	{
		return Mvc::getView()->getUrlManager()->applyParams($url, $params);
	}

	/**
	 * 获取分页参数：当前开始查询的行数
	 * @param integer $paged
	 * @param integer $listRows
	 * @return integer
	 */
	public static function getFirstRow($paged = 0, $listRows = 0)
	{
		if (($paged = (int) $paged) <= 0) {
			$paged = self::getCurrPage();
		}

		if (($listRows = (int) $listRows) <= 0) {
			$listRows = self::getListRows();
		}

		$firstRow = ($paged - 1) * $listRows;
		$firstRow = max($firstRow, 0);
		return $firstRow;
	}

	/**
	 * 获取分页参数：每页展示的行数
	 * @return integer
	 */
	public static function getListRows()
	{
		$listRowsVar = self::getListRowsVar();
		$listRows = Ap::getRequest()->getInteger($listRowsVar);
		if ($listRows > 0) {
			return $listRows;
		}

		$listRows = (int) Cfg::getApp('list_rows', 'paginator');
		$listRows = max($listRows, 1);
		return $listRows;
	}

	/**
	 * 获取分页参数：每页展示的页码数
	 * @return integer
	 */
	public static function getListPages()
	{
		$listPages = (int) Cfg::getApp('list_pages', 'paginator');
		$listPages = max($listPages, 1);
		return $listPages;
	}

	/**
	 * 获取当前页码
	 * @return integer
	 */
	public static function getCurrPage()
	{
		$pageVar = self::getPageVar();
		$currPage = Ap::getRequest()->getInteger($pageVar);
		$currPage = max($currPage, 1);
		return $currPage;
	}

	/**
	 * 获取从$_GET或$_POST中获取每页展示的行数的键名
	 * @return string
	 */
	public static function getListRowsVar()
	{
		try {
			$listRowsVar = Cfg::getApp('list_rows_var', 'paginator');
		}
		catch (ErrorException $e) {
			$listRowsVar = 'limit';
		}

		return $listRowsVar;
	}

	/**
	 * 获取从$_GET或$_POST中获取当前页的键名
	 * @return string
	 */
	public static function getPageVar()
	{
		try {
			$pageVar = Cfg::getApp('page_var', 'paginator');
		}
		catch (ErrorException $e) {
			$pageVar = Paginator::DEFAULT_PAGE_VAR;
		}

		return $pageVar;
	}
}
