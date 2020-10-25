<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\posts\model;

use libapp\BaseModel;
use libsrv\Service;
use library\PageHelper;

/**
 * Comments class file
 * 文档评论
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Comments.php 1 2014-10-31 11:14:54Z Code Generator $
 * @package modules.posts.model
 * @since 1.0
 */
class Comments extends BaseModel
{
	/**
	 * @var integer 查询子评论记录数
	 */
	const SUB_LIST_ROWS = 5;

	/**
	 * 通过文档ID，查询多条评论，包括2层子评论
	 * @param integer $postId
	 * @param string $order
	 * @param integer $paged
	 * @return array
	 */
	public function getRowsByPostId($postId, $order = '', $paged = 0)
	{
		if (($postId = (int) $postId) <= 0) {
			return array();
		}

		$ret = $this->findRows(array('post_id' => $postId, 'comment_pid' => 0), $order, $paged);
		if ($ret && is_array($ret)) {
			$ret['attributes'] = array('postid' => $postId);
			if (isset($ret['rows']) && is_array($ret['rows'])) {
				foreach ($ret['rows'] as $key => $row) {
					$data = $this->getRowsByPid($row['comment_id'], $order, self::SUB_LIST_ROWS);
					if ($data && is_array($data)) {
						foreach ($data as $_k => $_r) {
							$data[$_k]['data'] = $this->getRowsByPid($_r['comment_id'], $order, self::SUB_LIST_ROWS);
						}

						$ret['rows'][$key]['data'] = $data;
					}
				}
			}
		}

		return $ret;
	}

	/**
	 * 通过父评论ID，查询多条记录
	 * @param integer $pId
	 * @param string $order
	 * @param integer $limit
	 * @param integer $offset
	 * @return array
	 */
	public function getRowsByPid($pId, $order = '', $limit = 0, $offset = 0)
	{
		$rows = $this->getService()->getRowsByPid($pId, $order, $limit, $offset);
		return $rows;
	}

	/**
	 * 查询多条记录
	 * @param array $params
	 * @param string $order
	 * @param integer $paged
	 * @return array
	 */
	public function findRows(array $params = array(), $order = '', $paged = 0)
	{
		$paged = max((int) $paged, 1);
		$limit = PageHelper::getListRowsPostComments();
		$offset = PageHelper::getFirstRow($paged, $limit);

		$rows = $this->getService()->findRows($params, $order, $limit, $offset, 'SQL_CALC_FOUND_ROWS');
		return $rows;
	}

	/**
	 * 新增一条记录
	 * @param array $params
	 * @param boolean $ignore
	 * @return array
	 */
	public function create(array $params = array(), $ignore = false)
	{
		$ret = $this->callCreateMethod($this->getService(), 'create', $params, $ignore);
		return $ret;
	}

	/**
	 * 获取文档评论业务处理类
	 * @return posts\services\Comments
	 */
	public function getService()
	{
		static $service = null;
		if ($service === null) {
			$service = Service::getInstance('Comments', 'posts');
		}

		return $service;
	}
}
