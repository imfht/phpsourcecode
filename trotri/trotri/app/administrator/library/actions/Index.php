<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace library\actions;

use library\ShowAction;
use tfc\ap\Ap;
use tfc\saf\Cfg;
use tfc\auth\Role;
use libapp\Model;
use library\PageHelper;
use library\ErrorNo;

/**
 * Index abstract class file
 * Index基类，展示列表数据
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Index.php 1 2013-04-05 01:08:06Z huan.song $
 * @package library.actions
 * @since 1.0
 */
abstract class Index extends ShowAction
{
	/**
	 * @var integer 允许的权限
	 */
	protected $_power = Role::SELECT;

	/**
	 * 执行操作：查询数据列表
	 * @param string $className
	 * @param string $moduleName
	 * @return void
	 */
	public function execute($className, $moduleName = '')
	{
		$ret = array();

		$mod = Model::getInstance($className, $moduleName);

		$params = $this->getSearchParams();
		$order = $this->getOrder();
		$limit = PageHelper::getListRows();
		$offset = PageHelper::getFirstRow();

		$ret = $mod->search($params, $order, $limit, $offset);
		if ($ret['err_no'] !== ErrorNo::SUCCESS_NUM) {
			$this->err404();
		}

		$params = $this->getLLUParams($ret['paginator']);
		$mod->setLLU($params);
		$this->assign('elements', $mod);
		$this->render($ret);
	}

	/**
	 * 获取最后一次访问的列表页参数
	 * @param array $params
	 * @return array
	 */
	public function getLLUParams(array $params = array())
	{
		$attributes = isset($params['attributes']) ? (array) $params['attributes'] : array();
		$order      = isset($params['order'])      ? trim($params['order'])        : '';
		$listRows   = isset($params['limit'])      ? (int) $params['limit']        : 0;
		$firstRow   = isset($params['offset'])     ? (int) $params['offset']       : 0;

		if ($order !== '') {
			$attributes['order'] = $order;
		}

		if ($listRows <= 0) {
			return $attributes;
		}

		if ($listRows !== (int) Cfg::getApp('list_rows', 'paginator')) {
			$attributes[PageHelper::getListRowsVar()] = $listRows;
		}

		$firstRow = max($firstRow, 0);
		$currPage = floor($firstRow / $listRows) + 1;
		if ($currPage > 0) {
			$attributes[PageHelper::getPageVar()] = $currPage;
		}

		return $attributes;
	}

	/**
	 * 获取查询参数
	 * @return array
	 */
	public function getSearchParams()
	{
		$req = Ap::getRequest();
		$ret = array_merge($req->getQuery(), $req->getParams());
		return $ret;
	}

	/**
	 * 获取排序参数
	 * @return array
	 */
	public function getOrder()
	{
		return Ap::getRequest()->getTrim('order', '');
	}
}
