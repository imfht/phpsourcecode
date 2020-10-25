<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace posts\db;

use tdo\AbstractDb;
use libsrv\Clean;
use posts\library\Constant;
use posts\library\TableNames;

/**
 * Comments class file
 * 业务层：数据库操作类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Comments.php 1 2014-10-31 10:47:03Z Code Generator $
 * @package posts.db
 * @since 1.0
 */
class Comments extends AbstractDb
{
	/**
	 * @var string 数据库配置名
	 */
	protected $_clusterName = Constant::DB_CLUSTER;

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
		$commandBuilder = $this->getCommandBuilder();
		$tableName = $this->getTblprefix() . TableNames::getComments();
		$sql = 'SELECT ' . $option . ' `comment_id`, `comment_pid`, `post_id`, `content`, `author_name`, `author_mail`, `author_url`, `is_published`, `good_count`, `bad_count`, `creator_id`, `creator_name`, `last_modifier_id`, `last_modifier_name`, `dt_created`, `dt_last_modified`, `ip_created`, `ip_last_modified` FROM `' . $tableName . '`';

		$condition = '1';
		$attributes = array();

		if (isset($params['comment_pid'])) {
			$commentPid = (int) $params['comment_pid'];
			if ($commentPid >= 0) {
				$condition .= ' AND `comment_pid` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['comment_pid'] = $commentPid;
			}
		}

		if (isset($params['post_id'])) {
			$postId = (int) $params['post_id'];
			if ($postId > 0) {
				$condition .= ' AND `post_id` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['post_id'] = $postId;
			}
		}

		if (isset($params['content'])) {
			$content = $params['content'];
			if ($content !== '') {
				$condition .= ' AND `content` LIKE ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['content'] = '%' . $content . '%';
			}
		}

		if (isset($params['author_name'])) {
			$authorName = trim($params['author_name']);
			if ($authorName !== '') {
				$condition .= ' AND `author_name` LIKE ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['author_name'] = '%' . $authorName . '%';
			}
		}

		if (isset($params['author_mail'])) {
			$authorMail = trim($params['author_mail']);
			if ($authorMail !== '') {
				$condition .= ' AND `author_mail` LIKE ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['author_mail'] = '%' . $authorMail . '%';
			}
		}

		if (isset($params['author_url'])) {
			$authorUrl = trim($params['author_url']);
			if ($authorUrl !== '') {
				$condition .= ' AND `author_url` LIKE ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['author_url'] = '%' . $authorUrl . '%';
			}
		}

		if (isset($params['is_published'])) {
			$isPublished = trim($params['is_published']);
			if ($isPublished !== '') {
				$condition .= ' AND `is_published` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['is_published'] = $isPublished;
			}
		}

		if (isset($params['creator_id'])) {
			$creatorId = (int) $params['creator_id'];
			if ($creatorId > 0) {
				$condition .= ' AND `creator_id` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['creator_id'] = $creatorId;
			}
		}

		if (isset($params['creator_name'])) {
			$creatorName = trim($params['creator_name']);
			if ($creatorName !== '') {
				$condition .= ' AND `creator_name` LIKE ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['creator_name'] = '%' . $creatorName . '%';
			}
		}

		if (isset($params['last_modifier_id'])) {
			$lastModifierId = (int) $params['last_modifier_id'];
			if ($lastModifierId > 0) {
				$condition .= ' AND `last_modifier_id` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['last_modifier_id'] = $lastModifierId;
			}
		}

		if (isset($params['last_modifier_name'])) {
			$lastModifierName = trim($params['last_modifier_name']);
			if ($lastModifierName !== '') {
				$condition .= ' AND `last_modifier_name` LIKE ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['last_modifier_name'] = '%' . $lastModifierName . '%';
			}
		}

		if (isset($params['dt_created_ge'])) {
			$dtCreated = trim($params['dt_created_ge']);
			if ($dtCreated !== '') {
				$condition .= ' AND `dt_created` >= ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['dt_created_ge'] = $dtCreated;
			}
		}

		if (isset($params['dt_created_le'])) {
			$dtCreated = trim($params['dt_created_le']);
			if ($dtCreated !== '') {
				$condition .= ' AND `dt_created` <= ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['dt_created_le'] = $dtCreated;
			}
		}

		if (isset($params['ip_created'])) {
			$ipCreated = (int) $params['ip_created'];
			$condition .= ' AND `ip_created` = ' . $commandBuilder::PLACE_HOLDERS;
			$attributes['ip_created'] = $ipCreated;
		}

		if (isset($params['ip_last_modified'])) {
			$ipLastModified = (int) $params['ip_last_modified'];
			$condition .= ' AND `ip_last_modified` = ' . $commandBuilder::PLACE_HOLDERS;
			$attributes['ip_last_modified'] = $ipLastModified;
		}

		if (isset($params['comment_id'])) {
			$commentId = (int) $params['comment_id'];
			if ($commentId > 0) {
				$condition .= ' AND `comment_id` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['comment_id'] = $commentId;
			}
		}

		$sql = $commandBuilder->applyCondition($sql, $condition);
		$sql = $commandBuilder->applyOrder($sql, $order);
		$sql = $commandBuilder->applyLimit($sql, $limit, $offset);

		if ($option === 'SQL_CALC_FOUND_ROWS') {
			$ret = $this->fetchAllNoCache($sql, $attributes);
			if (isset($attributes['content'])) {
				$attributes['content'] = $content;
			}
			if (isset($attributes['author_name'])) {
				$attributes['author_name'] = $authorName;
			}
			if (isset($attributes['author_mail'])) {
				$attributes['author_mail'] = $authorMail;
			}
			if (isset($attributes['author_url'])) {
				$attributes['author_url'] = $authorUrl;
			}
			if (isset($attributes['creator_name'])) {
				$attributes['creator_name'] = $creatorName;
			}
			if (isset($attributes['last_modifier_name'])) {
				$attributes['last_modifier_name'] = $lastModifierName;
			}
			if (is_array($ret)) {
				$ret['attributes'] = $attributes;
				$ret['order']      = $order;
				$ret['limit']      = $limit;
				$ret['offset']     = $offset;
			}
		}
		else {
			$ret = $this->fetchAll($sql, $attributes);
		}

		return $ret;
	}

	/**
	 * 通过主键，查询一条记录
	 * @param integer $commentId
	 * @return array
	 */
	public function findByPk($commentId)
	{
		if (($commentId = (int) $commentId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getComments();
		$sql = 'SELECT `comment_id`, `comment_pid`, `post_id`, `content`, `author_name`, `author_mail`, `author_url`, `is_published`, `good_count`, `bad_count`, `creator_id`, `creator_name`, `last_modifier_id`, `last_modifier_name`, `dt_created`, `dt_last_modified`, `ip_created`, `ip_last_modified` FROM `' . $tableName . '` WHERE `comment_id` = ?';
		return $this->fetchAssoc($sql, $commentId);
	}

	/**
	 * 新增一条记录
	 * @param array $params
	 * @param boolean $ignore
	 * @return integer
	 */
	public function create(array $params = array(), $ignore = false)
	{
		$commentPid = isset($params['comment_pid']) ? (int) $params['comment_pid'] : 0;
		$postId = isset($params['post_id']) ? (int) $params['post_id'] : 0;
		$content = isset($params['content']) ? $params['content'] : '';
		$authorName = isset($params['author_name']) ? trim($params['author_name']) : '';
		$authorMail = isset($params['author_mail']) ? trim($params['author_mail']) : '';
		$authorUrl = isset($params['author_url']) ? trim($params['author_url']) : '';
		$isPublished = isset($params['is_published']) ? trim($params['is_published']) : '';
		$goodCount = isset($params['good_count']) ? (int) $params['good_count'] : 0;
		$badCount = isset($params['bad_count']) ? (int) $params['bad_count'] : 0;
		$creatorId = isset($params['creator_id']) ? (int) $params['creator_id'] : 0;
		$creatorName = isset($params['creator_name']) ? trim($params['creator_name']) : '';
		$lastModifierId = isset($params['last_modifier_id']) ? (int) $params['last_modifier_id'] : 0;
		$lastModifierName = isset($params['last_modifier_name']) ? trim($params['last_modifier_name']) : '';
		$dtCreated = isset($params['dt_created']) ? trim($params['dt_created']) : '';
		$dtLastModified = isset($params['dt_last_modified']) ? trim($params['dt_last_modified']) : '';
		$ipCreated = isset($params['ip_created']) ? (int) $params['ip_created'] : 0;
		$ipLastModified = isset($params['ip_last_modified']) ? (int) $params['ip_last_modified'] : 0;

		if ($commentPid < 0 || $postId < 0 || $content === '' || $authorName === '' || $authorMail === '') {
			return false;
		}

		if ($isPublished === '') {
			$isPublished = 'n';
		}

		if ($dtCreated === '') {
			$dtCreated = date('Y-m-d H:i:s');
		}

		$dtLastModified = $dtCreated;

		$tableName = $this->getTblprefix() . TableNames::getComments();
		$attributes = array(
			'comment_pid' => $commentPid,
			'post_id' => $postId,
			'content' => $content,
			'author_name' => $authorName,
			'author_mail' => $authorMail,
			'author_url' => $authorUrl,
			'is_published' => $isPublished,
			'good_count' => $goodCount,
			'bad_count' => $badCount,
			'creator_id' => $creatorId,
			'creator_name' => $creatorName,
			'last_modifier_id' => $lastModifierId,
			'last_modifier_name' => $lastModifierName,
			'dt_created' => $dtCreated,
			'dt_last_modified' => $dtLastModified,
			'ip_created' => $ipCreated,
			'ip_last_modified' => $ipLastModified,
		);

		$sql = $this->getCommandBuilder()->createInsert($tableName, array_keys($attributes), $ignore);
		$lastInsertId = $this->insert($sql, $attributes);
		return $lastInsertId;
	}

	/**
	 * 增加一个好评
	 * @param integer $commentId
	 * @return integer
	 */
	public function incrementGoodCount($commentId)
	{
		if (($commentId = (int) $commentId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getComments();
		$sql = 'UPDATE `' . $tableName . '` SET `good_count` = `good_count` + 1 WHERE `comment_id` = ?';
		$rowCount = $this->update($sql, $commentId);
		return $rowCount;
	}

	/**
	 * 增加一个差评
	 * @param integer $commentId
	 * @return integer
	 */
	public function incrementBadCount($commentId)
	{
		if (($commentId = (int) $commentId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getComments();
		$sql = 'UPDATE `' . $tableName . '` SET `bad_count` = `bad_count` + 1 WHERE `comment_id` = ?';
		$rowCount = $this->update($sql, $commentId);
		return $rowCount;
	}

	/**
	 * 通过主键，编辑一条记录
	 * @param integer $commentId
	 * @param array $params
	 * @return integer
	 */
	public function modifyByPk($commentId, array $params = array())
	{
		if (($commentId = (int) $commentId) <= 0) {
			return false;
		}

		$attributes = array();

		if (isset($params['content'])) {
			$content = $params['content'];
			if ($content !== '') {
				$attributes['content'] = $content;
			}
			else {
				return false;
			}
		}

		if (isset($params['is_published'])) {
			$isPublished = trim($params['is_published']);
			if ($isPublished !== '') {
				$attributes['is_published'] = $isPublished;
			}
			else {
				return false;
			}
		}

		if (isset($params['good_count'])) {
			$goodCount = (int) $params['good_count'];
			if ($goodCount >= 0) {
				$attributes['good_count'] = $goodCount;
			}
			else {
				return false;
			}
		}

		if (isset($params['bad_count'])) {
			$badCount = (int) $params['bad_count'];
			if ($badCount >= 0) {
				$attributes['bad_count'] = $badCount;
			}
			else {
				return false;
			}
		}

		if (isset($params['last_modifier_id'])) {
			$lastModifierId = (int) $params['last_modifier_id'];
			if ($lastModifierId > 0) {
				$attributes['last_modifier_id'] = $lastModifierId;
			}
			else {
				return false;
			}
		}

		if (isset($params['last_modifier_name'])) {
			$lastModifierName = trim($params['last_modifier_name']);
			if ($lastModifierName !== '') {
				$attributes['last_modifier_name'] = $lastModifierName;
			}
			else {
				return false;
			}
		}

		if (isset($params['dt_last_modified'])) {
			$dtLastModified = trim($params['dt_last_modified']);
			if ($dtLastModified !== '') {
				$attributes['dt_last_modified'] = $dtLastModified;
			}
			else {
				return false;
			}
		}
		else {
			$attributes['dt_last_modified'] = date('Y-m-d H:i:s');
		}

		if (isset($params['ip_last_modified'])) {
			$attributes['ip_last_modified'] = (int) $params['ip_last_modified'];
		}

		$rowCount = 0;

		if ($attributes === array()) {
			return $rowCount;
		}

		$tableName = $this->getTblprefix() . TableNames::getComments();
		$sql = $this->getCommandBuilder()->createUpdate($tableName, array_keys($attributes), '`comment_id` = ?');
		$attributes['comment_id'] = $commentId;
		$rowCount = $this->update($sql, $attributes);
		return $rowCount;
	}

	/**
	 * 通过主键，编辑多条记录
	 * @param array|integer $commentIds
	 * @param array $params
	 * @return integer
	 */
	public function batchModifyByPk($commentIds, array $params = array())
	{
		$commentIds = Clean::positiveInteger($commentIds);
		if ($commentIds === false) {
			return false;
		}

		if (is_array($commentIds)) {
			$commentIds = implode(', ', $commentIds);
		}

		$attributes = array();

		if (isset($params['is_published'])) {
			$isPublished = trim($params['is_published']);
			if ($isPublished !== '') {
				$attributes['is_published'] = $isPublished;
			}
			else {
				return false;
			}
		}

		$rowCount = 0;

		if ($attributes === array()) {
			return $rowCount;
		}

		$tableName = $this->getTblprefix() . TableNames::getComments();
		$condition = '`comment_id` IN (' . $commentIds . ')';
		$sql = $this->getCommandBuilder()->createUpdate($tableName, array_keys($attributes), $condition);
		$rowCount = $this->update($sql, $attributes);
		return $rowCount;
	}

	/**
	 * 通过主键，删除一条记录
	 * @param integer $commentId
	 * @return integer
	 */
	public function removeByPk($commentId)
	{
		if (($commentId = (int) $commentId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getComments();
		$sql = $this->getCommandBuilder()->createDelete($tableName, '`comment_id` = ?');
		$rowCount = $this->delete($sql, $commentId);
		return $rowCount;
	}

	/**
	 * 通过主键，删除多条记录
	 * @param array|integer $commentIds
	 * @return integer
	 */
	public function batchRemoveByPk($commentIds, array $params = array())
	{
		$commentIds = Clean::positiveInteger($commentIds);
		if ($commentIds === false) {
			return false;
		}

		if (is_array($commentIds)) {
			$commentIds = implode(', ', $commentIds);
		}

		$tableName = $this->getTblprefix() . TableNames::getComments();
		$condition = '`comment_id` IN (' . $commentIds . ')';
		$sql = $this->getCommandBuilder()->createDelete($tableName, $condition);
		$rowCount = $this->delete($sql);
		return $rowCount;
	}

}
