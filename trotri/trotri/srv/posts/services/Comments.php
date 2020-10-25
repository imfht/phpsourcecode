<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace posts\services;

use libsrv\AbstractService;
use libsrv\Clean;
use posts\library\Constant;

/**
 * Comments class file
 * 业务层：业务处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Comments.php 1 2014-10-31 11:14:54Z Code Generator $
 * @package posts.services
 * @since 1.0
 */
class Comments extends AbstractService
{
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
		if (($pId = (int) $pId) <= 0) {
			return array();
		}

		$rows = $this->findRows(array('comment_pid' => $pId), $order, $limit, $offset);
		return $rows;
	}

	/**
	 * 查询多条记录
	 * @param array $params
	 * @param string $order
	 * @param integer $limit
	 * @param integer $offset
	 * @param string $option
	 * @return array
	 */
	public function findRows(array $params = array(), $order = '', $limit = 0, $offset = 0, $option = '')
	{
		if (($order = trim($order)) === '') {
			$order = DataPosts::ORDER_BY_DT_LAST_MODIFIED;
		}

		$params['is_published'] = DataComments::IS_PUBLISHED_Y;

		$rows = $this->findAll($params, $order, $limit, $offset, $option);
		if ($option === 'SQL_CALC_FOUND_ROWS') {
			if ($rows && is_array($rows)) {
				if (isset($rows['attributes']) && is_array($rows['attributes'])) {
					if (isset($rows['attributes']['is_published'])) {
						unset($rows['attributes']['is_published']);
					}
				}
				if (isset($rows['order']) && $rows['order'] === DataComments::ORDER_BY_DT_LAST_MODIFIED) {
					$rows['order'] = '';
				}
			}
		}

		return $rows;
	}

	/**
	 * 查询多条记录
	 * @param array $params
	 * @param string $order
	 * @param integer $limit
	 * @param integer $offset
	 * @param string $option
	 * @return array
	 */
	public function findAll(array $params = array(), $order = '', $limit = 0, $offset = 0, $option = '')
	{
		$limit = min(max((int) $limit, 1), Constant::FIND_MAX_LIMIT);
		$offset = max((int) $offset, 0);

		if (isset($params['ip_created'])) {
			$ipCreated = trim($params['ip_created']); unset($params['ip_created']);
			if ($ipCreated !== '') {
				$ipCreated = (strpos($ipCreated, '.') !== false) ? Clean::ip2long($ipCreated) : (int) $ipCreated;
				if ($ipCreated !== false) {
					$params['ip_created'] = $ipCreated;
				}
			}
		}

		if (isset($params['ip_last_modified'])) {
			$ipLastModified = trim($params['ip_last_modified']); unset($params['ip_last_modified']);
			if ($ipLastModified !== '') {
				$ipLastModified = (strpos($ipLastModified, '.') !== false) ? Clean::ip2long($ipLastModified) : (int) $ipLastModified;
				if ($ipLastModified !== false) {
					$params['ip_last_modified'] = $ipLastModified;
				}
			}
		}

		$rows = $this->getDb()->findAll($params, $order, $limit, $offset, $option);
		return $rows;
	}

	/**
	 * 通过主键，查询一条记录
	 * @param integer $commentId
	 * @return array
	 */
	public function findByPk($commentId)
	{
		$row = $this->getDb()->findByPk($commentId);
		return $row;
	}

	/**
	 * 通过主键，编辑多条记录
	 * @param array|integer $values
	 * @param array $params
	 * @return integer
	 */
	public function batchModifyByPk($values, array $params = array())
	{
		$rowCount = $this->getDb()->batchModifyByPk($values, $params);
		return $rowCount;
	}

	/**
	 * 通过主键，删除多条记录
	 * @param array|integer $commentIds
	 * @return integer
	 */
	public function batchRemoveByPk($commentIds, array $params = array())
	{
		$rowCount = $this->getDb()->batchRemoveByPk($commentIds, $params);
		return $rowCount;
	}

	/**
	 * 通过“主键ID”，获取“父ID”
	 * @param integer $commentId
	 * @return integer
	 */
	public function getCommentPidByCommentId($commentId)
	{
		$value = $this->getByPk('comment_pid', $commentId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“文档ID”
	 * @param integer $commentId
	 * @return integer
	 */
	public function getPostIdByCommentId($commentId)
	{
		$value = $this->getByPk('post_id', $commentId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“评论内容”
	 * @param integer $commentId
	 * @return string
	 */
	public function getContentByCommentId($commentId)
	{
		$value = $this->getByPk('content', $commentId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“评论作者名”
	 * @param integer $commentId
	 * @return string
	 */
	public function getAuthorNameByCommentId($commentId)
	{
		$value = $this->getByPk('author_name', $commentId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“评论作者邮箱”
	 * @param integer $commentId
	 * @return string
	 */
	public function getAuthorMailByCommentId($commentId)
	{
		$value = $this->getByPk('author_mail', $commentId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“评论作者网址”
	 * @param integer $commentId
	 * @return string
	 */
	public function getAuthorUrlByCommentId($commentId)
	{
		$value = $this->getByPk('author_url', $commentId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“是否发表”
	 * @param integer $commentId
	 * @return string
	 */
	public function getIsPublishedByCommentId($commentId)
	{
		$value = $this->getByPk('is_published', $commentId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“好评次数”
	 * @param integer $commentId
	 * @return integer
	 */
	public function getGoodCountByCommentId($commentId)
	{
		$value = $this->getByPk('good_count', $commentId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“差评次数”
	 * @param integer $commentId
	 * @return integer
	 */
	public function getBadCountByCommentId($commentId)
	{
		$value = $this->getByPk('bad_count', $commentId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“创建人ID”
	 * @param integer $commentId
	 * @return integer
	 */
	public function getCreatorIdByCommentId($commentId)
	{
		$value = $this->getByPk('creator_id', $commentId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“创建人登录名”
	 * @param integer $commentId
	 * @return string
	 */
	public function getCreatorNameByCommentId($commentId)
	{
		$value = $this->getByPk('creator_name', $commentId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“上次编辑人ID”
	 * @param integer $commentId
	 * @return integer
	 */
	public function getLastModifierIdByCommentId($commentId)
	{
		$value = $this->getByPk('last_modifier_id', $commentId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“上次编辑人登录名”
	 * @param integer $commentId
	 * @return string
	 */
	public function getLastModifierNameByCommentId($commentId)
	{
		$value = $this->getByPk('last_modifier_name', $commentId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“创建时间”
	 * @param integer $commentId
	 * @return string
	 */
	public function getDtCreatedByCommentId($commentId)
	{
		$value = $this->getByPk('dt_created', $commentId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“上次编辑时间”
	 * @param integer $commentId
	 * @return string
	 */
	public function getDtLastModifiedByCommentId($commentId)
	{
		$value = $this->getByPk('dt_last_modified', $commentId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“创建IP”
	 * @param integer $commentId
	 * @return integer
	 */
	public function getIpCreatedByCommentId($commentId)
	{
		$value = $this->getByPk('ip_created', $commentId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“上次编辑IP”
	 * @param integer $commentId
	 * @return integer
	 */
	public function getIpLastModifiedByCommentId($commentId)
	{
		$value = $this->getByPk('ip_last_modified', $commentId);
		return $value ? (int) $value : 0;
	}

}
