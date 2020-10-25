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
use tfc\saf\Log;
use libsrv\Service;
use libsrv\Clean;
use posts\library\Constant;
use posts\library\Plugin;

/**
 * Posts class file
 * 业务层：业务处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Posts.php 1 2014-10-17 11:27:20Z Code Generator $
 * @package posts.services
 * @since 1.0
 */
class Posts extends AbstractService
{
	/**
	 * 查询多条头条
	 * @param integer $limit
	 * @param integer $offset
	 * @return array
	 */
	public function getHeads($limit = 0, $offset = 0)
	{
		$rows = $this->findRows(array('is_head' => DataPosts::IS_HEAD_Y), DataPosts::ORDER_BY_SORT, $limit, $offset);
		return $rows;
	}

	/**
	 * 查询多条推荐
	 * @param integer $limit
	 * @param integer $offset
	 * @return array
	 */
	public function getRecommends($limit = 0, $offset = 0)
	{
		$rows = $this->findRows(array('is_recommend' => DataPosts::IS_RECOMMEND_Y), DataPosts::ORDER_BY_SORT, $limit, $offset);
		return $rows;
	}

	/**
	 * 通过类别ID，查询访问次数最多的记录
	 * @param integer $catId
	 * @param integer $limit
	 * @param integer $offset
	 * @return array
	 */
	public function getHitsByCatId($catId, $limit = 0, $offset = 0)
	{
		$rows = $this->getRowsByCatId($catId, DataPosts::ORDER_BY_HITS, $limit, $offset);
		return $rows;
	}

	/**
	 * 通过类别ID，查询赞美次数最多的记录
	 * @param integer $catId
	 * @param integer $limit
	 * @param integer $offset
	 * @return array
	 */
	public function getPraisesByCatId($catId, $limit = 0, $offset = 0)
	{
		$rows = $this->getRowsByCatId($catId, DataPosts::ORDER_BY_PRAISE, $limit, $offset);
		return $rows;
	}

	/**
	 * 通过类别ID，查询评论次数最多的记录
	 * @param integer $catId
	 * @param integer $limit
	 * @param integer $offset
	 * @return array
	 */
	public function getCommentsByCatId($catId, $limit = 0, $offset = 0)
	{
		$rows = $this->getRowsByCatId($catId, DataPosts::ORDER_BY_COMMENT, $limit, $offset);
		return $rows;
	}

	/**
	 * 通过类别ID，查询多条记录
	 * @param integer $catId
	 * @param integer $limit
	 * @param integer $offset
	 * @return array
	 */
	public function getRowsByCatId($catId, $order = '', $limit = 0, $offset = 0)
	{
		if (($catId = (int) $catId) <= 0) {
			return array();
		}

		$rows = $this->findRows(array('category_id' => $catId), $order, $limit, $offset);
		return $rows;
	}

	/**
	 * 查询第一条记录
	 * @param array $params
	 * @param string $order
	 * @return array
	 */
	public function getRow(array $params = array(), $order = '')
	{
		$rows = $this->findRows($params, $order, 1);
		if ($rows && is_array($rows)) {
			$row = array_shift($rows);
			return $row;
		}

		return array();
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
			$order = DataPosts::ORDER_BY_SORT;
		}

		$nowTime = date('Y-m-d H:i:s');

		$params['trash'] = DataPosts::TRASH_N;
		$params['is_published'] = DataPosts::IS_PUBLISHED_Y;
		$params['dt_publish_up'] = $nowTime;
		$params['dt_publish_down'] = $nowTime;

		$rows = $this->findAll($params, $order, $limit, $offset, $option);
		if ($option === 'SQL_CALC_FOUND_ROWS') {
			if ($rows && is_array($rows)) {
				if (isset($rows['attributes']) && is_array($rows['attributes'])) {
					if (isset($rows['attributes']['trash'])) {
						unset($rows['attributes']['trash']);
					}
					if (isset($rows['attributes']['is_published'])) {
						unset($rows['attributes']['is_published']);
					}
					if (isset($rows['attributes']['dt_publish_up'])) {
						unset($rows['attributes']['dt_publish_up']);
					}
					if (isset($rows['attributes']['dt_publish_down'])) {
						unset($rows['attributes']['dt_publish_down']);
					}
				}
				if (isset($rows['order']) && $rows['order'] === DataPosts::ORDER_BY_SORT) {
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
	 * @param integer $postId
	 * @param boolean $usable
	 * @return array
	 */
	public function findByPk($postId, $usable = false)
	{
		$row = $this->getDb()->findByPk($postId);
		if ($row && is_array($row) && isset($row['post_id'])) {
			if ($usable) {
				if ($row['trash'] !== DataPosts::TRASH_N) {
					return array();
				}

				if ($row['is_published'] !== DataPosts::IS_PUBLISHED_Y) {
					return array();
				}

				$nowTime = date('Y-m-d H:i:s');
				if ($row['dt_publish_up'] > $nowTime) {
					return array();
				}

				if ($row['dt_publish_down'] !== '0000-00-00 00:00:00' && $row['dt_publish_down'] < $nowTime) {
					return array();
				}
			}

			$dispatcher = Plugin::getInstance();
			$dispatcher->trigger('onAfterFind', array(__METHOD__, &$row));

			return $row;
		}

		return array();
	}

	/**
	 * 通过类别ID，查询记录数
	 * @param integer $categoryId
	 * @return integer
	 */
	public function countByCategoryId($categoryId)
	{
		$count = $this->getDb()->countByCategoryId($categoryId);
		return $count;
	}

	/**
	 * (non-PHPdoc)
	 * @see \libsrv\AbstractService::create()
	 */
	public function create(array $params = array(), $ignore = false)
	{
		$postId = parent::create($params, $ignore);
		if (($postId = (int) $postId) <= 0) {
			return false;
		}

		$dispatcher = Plugin::getInstance();
		$dispatcher->trigger('onAfterSave', array(__METHOD__, &$params, $postId));

		return $postId;
	}

	/**
	 * (non-PHPdoc)
	 * @see \libsrv\AbstractService::modifyByPk()
	 */
	public function modifyByPk($value, array $params = array())
	{
		$rowCount = parent::modifyByPk($value, $params);
		if ($rowCount === false) {
			return false;
		}

		$dispatcher = Plugin::getInstance();
		$dispatcher->trigger('onAfterSave', array(__METHOD__, &$params, $value));

		return true;
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
	 * 通过主键，将一条记录移至回收站
	 * @param integer $value
	 * @return integer
	 */
	public function trashByPk($value)
	{
		return $this->batchModifyByPk($value, array('trash' => DataPosts::TRASH_Y));
	}

	/**
	 * 通过主键，将多条记录移至回收站
	 * @param array $values
	 * @return integer
	 */
	public function batchTrashByPk(array $values)
	{
		return $this->batchModifyByPk($values, array('trash' => DataPosts::TRASH_Y));
	}

	/**
	 * 通过主键，从回收站还原一条记录
	 * @param integer $value
	 * @return integer
	 */
	public function restoreByPk($value)
	{
		return $this->batchModifyByPk($value, array('trash' => DataPosts::TRASH_N));
	}

	/**
	 * 通过主键，将多条记录移至回收站
	 * @param array $values
	 * @return integer
	 */
	public function batchRestoreByPk(array $values)
	{
		return $this->batchModifyByPk($values, array('trash' => DataPosts::TRASH_N));
	}

	/**
	 * 批量编辑排序
	 * @param array $params
	 * @return integer
	 */
	public function batchModifySort(array $params = array())
	{
		$rowCount = 0;
		$columnName = 'sort';

		foreach ($params as $pk => $value) {
			if ($this->batchModifyByPk($pk, array($columnName => $value))) {
				$rowCount += 1;
			}
			else {
				$errors = $this->getErrors();
				if ($errors) {
					Log::warning(sprintf(
						'Posts update args error, id "%d", params "%s", errors "%s"',
						$pk, serialize($params), serialize($errors)
					), 0, __METHOD__);
				}
			}
		}

		return $rowCount;
	}

	/**
	 * 获取“是否头条”
	 * @param string $isHead
	 * @return string
	 */
	public function getIsHeadLangByIsHead($isHead)
	{
		$enum = DataPosts::getIsHeadEnum();
		return isset($enum[$isHead]) ? $enum[$isHead] : '';
	}

	/**
	 * 获取“是否推荐”
	 * @param string $isRecommend
	 * @return string
	 */
	public function getIsRecommendLangByIsRecommend($isRecommend)
	{
		$enum = DataPosts::getIsRecommendEnum();
		return isset($enum[$isRecommend]) ? $enum[$isRecommend] : '';
	}

	/**
	 * 获取“是否发表”
	 * @param string $isPublished
	 * @return string
	 */
	public function getIsPublishedLangByIsPublished($isPublished)
	{
		$enum = DataPosts::getIsPublishedEnum();
		return isset($enum[$isPublished]) ? $enum[$isPublished] : '';
	}

	/**
	 * 获取“评论设置”
	 * @param string $commentStatus
	 * @return string
	 */
	public function getCommentStatusLangByCommentStatus($commentStatus)
	{
		$enum = DataPosts::getCommentStatusEnum();
		return isset($enum[$commentStatus]) ? $enum[$commentStatus] : '';
	}

	/**
	 * 通过“主键ID”，获取“文档扩展字段”
	 * @param integer $postId
	 * @return array
	 */
	public function getModuleFieldsByPostId($postId)
	{
		$moduleId = $this->getModuleIdByPostId($postId);
		if ($moduleId <= 0) {
			return array();
		}

		$value = Service::getInstance('Modules', 'posts')->getFieldsByModuleId($moduleId);
		return is_array($value) ? $value : array();
	}

	/**
	 * 通过“主键ID”，获取“文档标题”
	 * @param integer $postId
	 * @return string
	 */
	public function getTitleByPostId($postId)
	{
		$value = $this->getByPk('title', $postId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“别名”
	 * @param integer $postId
	 * @return string
	 */
	public function getAliasByPostId($postId)
	{
		$value = $this->getByPk('alias', $postId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“内容”
	 * @param integer $postId
	 * @return string
	 */
	public function getContentByPostId($postId)
	{
		$value = $this->getByPk('content', $postId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“关键字”
	 * @param integer $postId
	 * @return string
	 */
	public function getKeywordsByPostId($postId)
	{
		$value = $this->getByPk('keywords', $postId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“内容摘要”
	 * @param integer $postId
	 * @return string
	 */
	public function getDescriptionByPostId($postId)
	{
		$value = $this->getByPk('description', $postId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“排序”
	 * @param integer $postId
	 * @return integer
	 */
	public function getSortByPostId($postId)
	{
		$value = $this->getByPk('sort', $postId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“所属类别”
	 * @param integer $postId
	 * @return integer
	 */
	public function getCategoryIdByPostId($postId)
	{
		$value = $this->getByPk('category_id', $postId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“类别名”
	 * @param integer $postId
	 * @return string
	 */
	public function getCategoryNameByPostId($postId)
	{
		$value = $this->getByPk('category_name', $postId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“所属模型”
	 * @param integer $postId
	 * @return integer
	 */
	public function getModuleIdByPostId($postId)
	{
		$value = $this->getByPk('module_id', $postId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“访问密码”
	 * @param integer $postId
	 * @return string
	 */
	public function getPasswordByPostId($postId)
	{
		$value = $this->getByPk('password', $postId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“主图地址”
	 * @param integer $postId
	 * @return string
	 */
	public function getPictureByPostId($postId)
	{
		$value = $this->getByPk('picture', $postId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“是否头条”
	 * @param integer $postId
	 * @return string
	 */
	public function getIsHeadByPostId($postId)
	{
		$value = $this->getByPk('is_head', $postId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“是否推荐”
	 * @param integer $postId
	 * @return string
	 */
	public function getIsRecommendByPostId($postId)
	{
		$value = $this->getByPk('is_recommend', $postId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“是否跳转”
	 * @param integer $postId
	 * @return string
	 */
	public function getIsJumpByPostId($postId)
	{
		$value = $this->getByPk('is_jump', $postId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“跳转链接”
	 * @param integer $postId
	 * @return string
	 */
	public function getJumpUrlByPostId($postId)
	{
		$value = $this->getByPk('jump_url', $postId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“是否发表”
	 * @param integer $postId
	 * @return string
	 */
	public function getIsPublishedByPostId($postId)
	{
		$value = $this->getByPk('is_published', $postId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“开始发表时间”
	 * @param integer $postId
	 * @return string
	 */
	public function getDtPublishUpByPostId($postId)
	{
		$value = $this->getByPk('dt_publish_up', $postId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“结束发表时间”
	 * @param integer $postId
	 * @return string
	 */
	public function getDtPublishDownByPostId($postId)
	{
		$value = $this->getByPk('dt_publish_down', $postId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“评论设置”
	 * @param integer $postId
	 * @return string
	 */
	public function getCommentStatusByPostId($postId)
	{
		$value = $this->getByPk('comment_status', $postId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“允许其他人编辑”
	 * @param integer $postId
	 * @return string
	 */
	public function getAllowOtherModifyByPostId($postId)
	{
		$value = $this->getByPk('allow_other_modify', $postId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“访问次数”
	 * @param integer $postId
	 * @return integer
	 */
	public function getHitsByPostId($postId)
	{
		$value = $this->getByPk('hits', $postId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“赞美次数”
	 * @param integer $postId
	 * @return integer
	 */
	public function getPraiseCountByPostId($postId)
	{
		$value = $this->getByPk('praise_count', $postId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“评论次数”
	 * @param integer $postId
	 * @return integer
	 */
	public function getCommentCountByPostId($postId)
	{
		$value = $this->getByPk('comment_count', $postId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“创建人ID”
	 * @param integer $postId
	 * @return integer
	 */
	public function getCreatorIdByPostId($postId)
	{
		$value = $this->getByPk('creator_id', $postId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“创建人”
	 * @param integer $postId
	 * @return string
	 */
	public function getCreatorNameByPostId($postId)
	{
		$value = $this->getByPk('creator_name', $postId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“上次编辑人ID”
	 * @param integer $postId
	 * @return integer
	 */
	public function getLastModifierIdByPostId($postId)
	{
		$value = $this->getByPk('last_modifier_id', $postId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“上次编辑人登录名”
	 * @param integer $postId
	 * @return string
	 */
	public function getLastModifierNameByPostId($postId)
	{
		$value = $this->getByPk('last_modifier_name', $postId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“创建时间”
	 * @param integer $postId
	 * @return string
	 */
	public function getDtCreatedByPostId($postId)
	{
		$value = $this->getByPk('dt_created', $postId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“上次编辑时间”
	 * @param integer $postId
	 * @return string
	 */
	public function getDtLastModifiedByPostId($postId)
	{
		$value = $this->getByPk('dt_last_modified', $postId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“创建IP”
	 * @param integer $postId
	 * @return string
	 */
	public function getIpCreatedByPostId($postId)
	{
		$value = $this->getByPk('ip_created', $postId);
		return $value ? long2ip((int) $value) : false;
	}

	/**
	 * 通过“主键ID”，获取“上次编辑IP”
	 * @param integer $postId
	 * @return string
	 */
	public function getIpLastModifiedByPostId($postId)
	{
		$value = $this->getByPk('ip_last_modified', $postId);
		return $value ? long2ip((int) $value) : false;
	}

	/**
	 * 通过“主键ID”，获取“是否删除”
	 * @param integer $postId
	 * @return string
	 */
	public function getTrashByPostId($postId)
	{
		$value = $this->getByPk('trash', $postId);
		return $value ? $value : '';
	}

}
