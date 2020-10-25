<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\posts\model;

use libapp\BaseModel;
use libsrv\Service;
use library\PageHelper;
use posts\services\DataPosts;

/**
 * Posts class file
 * 文档管理
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Posts.php 1 2013-04-20 17:11:06Z huan.song $
 * @package modules.posts.model
 * @since 1.0
 */
class Posts extends BaseModel
{
	/**
	 * 查询上一条记录
	 * @param integer $catId
	 * @param integer $sort
	 * @return array
	 */
	public function getPrevByCatId($catId, $sort)
	{
		if (($catId = (int) $catId) <= 0) {
			return array();
		}

		if (($sort = (int) $sort) <= 0) {
			return array();
		}

		$params = array(
			'category_id' => $catId,
			'sort_lt' => $sort,
		);

		$row = $this->getRow($params, DataPosts::ORDER_BY_SORT . ' DESC');
		return $row;
	}

	/**
	 * 查询下一条记录
	 * @param integer $catId
	 * @param integer $sort
	 * @return array
	 */
	public function getNextByCatId($catId, $sort)
	{
		if (($catId = (int) $catId) <= 0) {
			return array();
		}

		if (($sort = (int) $sort) <= 0) {
			return array();
		}

		$params = array(
			'category_id' => $catId,
			'sort_gt' => $sort,
		);

		$row = $this->getRow($params, DataPosts::ORDER_BY_SORT);
		return $row;
	}

	/**
	 * 查询第一条记录
	 * @param array $params
	 * @param string $order
	 * @return array
	 */
	public function getRow(array $params = array(), $order = '')
	{
		$row = $this->getService()->getRow($params, $order);
		return $row;
	}

	/**
	 * 查询多条记录，包含分页信息
	 * @param array $params
	 * @param string $order
	 * @param integer $paged
	 * @return array
	 */
	public function findRows(array $params = array(), $order = '', $paged = 0)
	{
		$paged = max((int) $paged, 1);
		$limit = PageHelper::getListRowsPosts();
		$offset = PageHelper::getFirstRow($paged, $limit);

		$rows = $this->getService()->findRows($params, $order, $limit, $offset, 'SQL_CALC_FOUND_ROWS');
		return $rows;
	}

	/**
	 * 通过主键，查询一条记录
	 * @param integer $postId
	 * @return array
	 */
	public function findByPk($postId)
	{
		$row = $this->getService()->findByPk($postId, true);
		return $row;
	}

	/**
	 * 获取文档业务处理类
	 * @return \posts\services\Posts
	 */
	public function getService()
	{
		static $service = null;
		if ($service === null) {
			$service = Service::getInstance('Posts', 'posts');
		}

		return $service;
	}
}
