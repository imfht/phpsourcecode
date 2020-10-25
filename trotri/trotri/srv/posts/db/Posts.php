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
 * Posts class file
 * 业务层：数据库操作类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Posts.php 1 2014-10-17 11:27:20Z Code Generator $
 * @package posts.db
 * @since 1.0
 */
class Posts extends AbstractDb
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
		$tableName = $this->getTblprefix() . TableNames::getPosts();
		$sql = 'SELECT ' . $option . ' `post_id`, `title`, `alias`, `content`, `keywords`, `description`, `sort`, `category_id`, `category_name`, `module_id`, `password`, `picture`, `is_head`, `is_recommend`, `is_jump`, `jump_url`, `is_published`, `dt_publish_up`, `dt_publish_down`, `comment_status`, `allow_other_modify`, `hits`, `praise_count`, `comment_count`, `creator_id`, `creator_name`, `last_modifier_id`, `last_modifier_name`, `dt_created`, `dt_last_modified`, `ip_created`, `ip_last_modified`, `trash` FROM `' . $tableName . '`';

		$condition = '1';
		$attributes = array();

		if (isset($params['title'])) {
			$title = trim($params['title']);
			if ($title !== '') {
				$condition .= ' AND `title` LIKE ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['title'] = '%' . $title . '%';
			}
		}

		if (isset($params['alias'])) {
			$alias = trim($params['alias']);
			if ($alias !== '') {
				$condition .= ' AND `alias` LIKE ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['alias'] = '%' . $alias . '%';
			}
			else {
				$condition .= ' AND `alias` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['alias'] = '';
			}
		}

		if (isset($params['keywords'])) {
			$keywords = trim($params['keywords']);
			if ($keywords !== '') {
				$condition .= ' AND `keywords` LIKE ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['keywords'] = '%' . $keywords . '%';
			}
			else {
				$condition .= ' AND `keywords` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['keywords'] = '';
			}
		}

		if (isset($params['category_id'])) {
			$categoryId = (int) $params['category_id'];
			if ($categoryId > 0) {
				$condition .= ' AND `category_id` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['category_id'] = $categoryId;
			}
		}

		if (isset($params['module_id'])) {
			$moduleId = (int) $params['module_id'];
			if ($moduleId > 0) {
				$condition .= ' AND `module_id` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['module_id'] = $moduleId;
			}
		}

		if (isset($params['is_head'])) {
			$isHead = trim($params['is_head']);
			if ($isHead !== '') {
				$condition .= ' AND `is_head` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['is_head'] = $isHead;
			}
		}

		if (isset($params['is_recommend'])) {
			$isRecommend = trim($params['is_recommend']);
			if ($isRecommend !== '') {
				$condition .= ' AND `is_recommend` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['is_recommend'] = $isRecommend;
			}
		}

		if (isset($params['is_jump'])) {
			$isJump = trim($params['is_jump']);
			if ($isJump !== '') {
				$condition .= ' AND `is_jump` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['is_jump'] = $isJump;
			}
		}

		if (isset($params['jump_url'])) {
			$jumpUrl = trim($params['jump_url']);
			if ($jumpUrl !== '') {
				$condition .= ' AND `jump_url` LIKE ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['jump_url'] = '%' . $jumpUrl . '%';
			}
			else {
				$condition .= ' AND `jump_url` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['jump_url'] = '';
			}
		}

		if (isset($params['is_published'])) {
			$isPublished = trim($params['is_published']);
			if ($isPublished !== '') {
				$condition .= ' AND `is_published` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['is_published'] = $isPublished;
			}
		}

		if (isset($params['dt_publish_up'])) {
			$dtPublishUp = trim($params['dt_publish_up']);
			if ($dtPublishUp !== '') {
				$condition .= ' AND `dt_publish_up` <= ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['dt_publish_up'] = $dtPublishUp;
			}
		}

		if (isset($params['dt_publish_down'])) {
			$dtPublishDown = trim($params['dt_publish_down']);
			if ($dtPublishDown !== '') {
				if ($dtPublishDown !== '0000-00-00 00:00:00') {
					$condition .= ' AND (`dt_publish_down` >= ' . $commandBuilder::PLACE_HOLDERS . ' OR `dt_publish_down` = \'0000-00-00 00:00:00\')';
				}
				else {
					$condition .= ' AND `dt_publish_down` = ' . $commandBuilder::PLACE_HOLDERS;					
				}

				$attributes['dt_publish_down'] = $dtPublishDown;
			}
		}

		if (isset($params['comment_status'])) {
			$commentStatus = trim($params['comment_status']);
			if ($commentStatus !== '') {
				$condition .= ' AND `comment_status` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['comment_status'] = $commentStatus;
			}
		}

		if (isset($params['allow_other_modify'])) {
			$allowOtherModify = trim($params['allow_other_modify']);
			if ($allowOtherModify !== '') {
				$condition .= ' AND `allow_other_modify` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['allow_other_modify'] = $allowOtherModify;
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

		if (isset($params['sort_gt'])) {
			$sortGt = (int) $params['sort_gt'];
			if ($sortGt > 0) {
				$condition .= ' AND `sort` > ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['sort_gt'] = $sortGt;
			}
		}

		if (isset($params['sort_lt'])) {
			$sortLt = (int) $params['sort_lt'];
			if ($sortLt > 0) {
				$condition .= ' AND `sort` < ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['sort_lt'] = $sortLt;
			}
		}

		if (isset($params['dt_created_ge'])) {
			$dtCreatedGe = trim($params['dt_created_ge']);
			if ($dtCreatedGe !== '') {
				$condition .= ' AND `dt_created` >= ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['dt_created_ge'] = $dtCreatedGe;
			}
		}

		if (isset($params['dt_created_le'])) {
			$dtCreatedLe = trim($params['dt_created_le']);
			if ($dtCreatedLe !== '') {
				$condition .= ' AND `dt_created` <= ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['dt_created_le'] = $dtCreatedLe;
			}
		}

		if (isset($params['dt_last_modified_ge'])) {
			$dtLastModifiedGe = trim($params['dt_last_modified_ge']);
			if ($dtLastModifiedGe !== '') {
				$condition .= ' AND `dt_last_modified` >= ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['dt_last_modified_ge'] = $dtLastModifiedGe;
			}
		}

		if (isset($params['dt_last_modified_le'])) {
			$dtLastModifiedLe = trim($params['dt_last_modified_le']);
			if ($dtLastModifiedLe !== '') {
				$condition .= ' AND `dt_last_modified` <= ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['dt_last_modified_le'] = $dtLastModifiedLe;
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

		if (isset($params['trash'])) {
			$trash = trim($params['trash']);
			if ($trash !== '') {
				$condition .= ' AND `trash` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['trash'] = $trash;
			}
		}

		if (isset($params['post_id'])) {
			$postId = (int) $params['post_id'];
			if ($postId > 0) {
				$condition .= ' AND `post_id` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['post_id'] = $postId;
			}
		}

		$sql = $commandBuilder->applyCondition($sql, $condition);
		$sql = $commandBuilder->applyOrder($sql, $order);
		$sql = $commandBuilder->applyLimit($sql, $limit, $offset);

		if ($option === 'SQL_CALC_FOUND_ROWS') {
			$ret = $this->fetchAllNoCache($sql, $attributes);
			if (isset($attributes['title'])) {
				$attributes['title'] = $title;
			}
			if (isset($attributes['alias'])) {
				$attributes['alias'] = $alias;
			}
			if (isset($attributes['keywords'])) {
				$attributes['keywords'] = $keywords;
			}
			if (isset($attributes['jump_url'])) {
				$attributes['jump_url'] = $jumpUrl;
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
	 * @param integer $postId
	 * @return array
	 */
	public function findByPk($postId)
	{
		if (($postId = (int) $postId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getPosts();
		$sql = 'SELECT `post_id`, `title`, `alias`, `content`, `keywords`, `description`, `sort`, `category_id`, `category_name`, `module_id`, `password`, `picture`, `is_head`, `is_recommend`, `is_jump`, `jump_url`, `is_published`, `dt_publish_up`, `dt_publish_down`, `comment_status`, `allow_other_modify`, `hits`, `praise_count`, `comment_count`, `creator_id`, `creator_name`, `last_modifier_id`, `last_modifier_name`, `dt_created`, `dt_last_modified`, `ip_created`, `ip_last_modified`, `trash` FROM `' . $tableName . '` WHERE `post_id` = ?';
		return $this->fetchAssoc($sql, $postId);
	}

	/**
	 * 通过类别ID，查询记录数
	 * @param integer $categoryId
	 * @return integer
	 */
	public function countByCategoryId($categoryId)
	{
		if (($categoryId = (int) $categoryId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getPosts();
		$sql = 'SELECT COUNT(*) FROM `' . $tableName . '` WHERE `trash` = ? AND `category_id` = ?';
		return $this->fetchColumn($sql, array('trash' => 'n', 'category_id' => $categoryId));
	}

	/**
	 * 新增一条记录
	 * @param array $params
	 * @param boolean $ignore
	 * @return integer
	 */
	public function create(array $params = array(), $ignore = false)
	{
		$title = isset($params['title']) ? trim($params['title']) : '';
		$alias = isset($params['alias']) ? trim($params['alias']) : '';
		$content = isset($params['content']) ? $params['content'] : '';
		$keywords = isset($params['keywords']) ? trim($params['keywords']) : '';
		$description = isset($params['description']) ? $params['description'] : '';
		$sort = isset($params['sort']) ? (int) $params['sort'] : 0;
		$categoryId = isset($params['category_id']) ? (int) $params['category_id'] : 0;
		$categoryName = isset($params['category_name']) ? trim($params['category_name']) : '';
		$moduleId = isset($params['module_id']) ? (int) $params['module_id'] : 0;
		$password = isset($params['password']) ? trim($params['password']) : '';
		$picture = isset($params['picture']) ? trim($params['picture']) : '';
		$isHead = isset($params['is_head']) ? trim($params['is_head']) : '';
		$isRecommend = isset($params['is_recommend']) ? trim($params['is_recommend']) : '';
		$isJump = isset($params['is_jump']) ? trim($params['is_jump']) : '';
		$jumpUrl = isset($params['jump_url']) ? trim($params['jump_url']) : '';
		$isPublished = isset($params['is_published']) ? trim($params['is_published']) : '';
		$dtPublishUp = isset($params['dt_publish_up']) ? trim($params['dt_publish_up']) : '';
		$dtPublishDown = isset($params['dt_publish_down']) ? trim($params['dt_publish_down']) : '';
		$commentStatus = isset($params['comment_status']) ? trim($params['comment_status']) : '';
		$allowOtherModify = isset($params['allow_other_modify']) ? trim($params['allow_other_modify']) : '';
		$hits = isset($params['hits']) ? (int) $params['hits'] : 0;
		$praiseCount = isset($params['praise_count']) ? (int) $params['praise_count'] : 0;
		$commentCount = isset($params['comment_count']) ? (int) $params['comment_count'] : 0;
		$creatorId = isset($params['creator_id']) ? (int) $params['creator_id'] : 0;
		$creatorName = isset($params['creator_name']) ? trim($params['creator_name']) : '';
		$lastModifierId = isset($params['last_modifier_id']) ? (int) $params['last_modifier_id'] : 0;
		$lastModifierName = isset($params['last_modifier_name']) ? trim($params['last_modifier_name']) : '';
		$dtCreated = isset($params['dt_created']) ? trim($params['dt_created']) : '';
		$dtLastModified = isset($params['dt_last_modified']) ? trim($params['dt_last_modified']) : '';
		$ipCreated = isset($params['ip_created']) ? (int) $params['ip_created'] : 0;
		$ipLastModified = isset($params['ip_last_modified']) ? (int) $params['ip_last_modified'] : 0;
		$trash = 'n';

		if ($title === '' || $sort <= 0 || $categoryId <= 0 || $categoryName === '' || $moduleId <= 0 
			|| $hits < 0 || $praiseCount < 0 || $commentCount < 0 || $creatorId <= 0 || $creatorName === '') {
			return false;
		}

		if ($isHead === '') {
			$isHead = 'n';
		}

		if ($isRecommend === '') {
			$isRecommend = 'n';
		}

		if ($isJump === '') {
			$isJump = 'n';
			$jumpUrl = '';
		}

		if ($jumpUrl === 'y' && $jumpUrl === '') {
			return false;
		}

		if ($isPublished === '') {
			$isPublished = 'n';
		}

		if ($dtPublishUp === '') {
			$dtPublishUp = date('Y-m-d H:i:s');
		}

		if ($dtPublishDown === '') {
			$dtPublishDown = '0000-00-00 00:00:00';
		}

		if ($commentStatus === '') {
			$commentStatus = 'publish';
		}

		if ($allowOtherModify === '') {
			$allowOtherModify = 'y';
		}

		$lastModifierId = $creatorId;
		$lastModifierName = $creatorName;

		if ($dtCreated === '') {
			$dtCreated = date('Y-m-d H:i:s');
		}

		$dtLastModified = $dtCreated;

		$tableName = $this->getTblprefix() . TableNames::getPosts();
		$attributes = array(
			'title' => $title,
			'alias' => $alias,
			'content' => $content,
			'keywords' => $keywords,
			'description' => $description,
			'sort' => $sort,
			'category_id' => $categoryId,
			'category_name' => $categoryName,
			'module_id' => $moduleId,
			'password' => $password,
			'picture' => $picture,
			'is_head' => $isHead,
			'is_recommend' => $isRecommend,
			'is_jump' => $isJump,
			'jump_url' => $jumpUrl,
			'is_published' => $isPublished,
			'dt_publish_up' => $dtPublishUp,
			'dt_publish_down' => $dtPublishDown,
			'comment_status' => $commentStatus,
			'allow_other_modify' => $allowOtherModify,
			'hits' => $hits,
			'praise_count' => $praiseCount,
			'comment_count' => $commentCount,
			'creator_id' => $creatorId,
			'creator_name' => $creatorName,
			'last_modifier_id' => $lastModifierId,
			'last_modifier_name' => $lastModifierName,
			'dt_created' => $dtCreated,
			'dt_last_modified' => $dtLastModified,
			'ip_created' => $ipCreated,
			'ip_last_modified' => $ipLastModified,
			'trash' => $trash,
		);

		$sql = $this->getCommandBuilder()->createInsert($tableName, array_keys($attributes), $ignore);
		$lastInsertId = $this->insert($sql, $attributes);
		return $lastInsertId;
	}

	/**
	 * 通过主键，编辑一条记录，不编辑module_id
	 * @param integer $postId
	 * @param array $params
	 * @return integer
	 */
	public function modifyByPk($postId, array $params = array())
	{
		if (($postId = (int) $postId) <= 0) {
			return false;
		}

		$attributes = array();

		if (isset($params['title'])) {
			$title = trim($params['title']);
			if ($title !== '') {
				$attributes['title'] = $title;
			}
			else {
				return false;
			}
		}

		if (isset($params['alias'])) {
			$attributes['alias'] = trim($params['alias']);
		}

		if (isset($params['content'])) {
			$attributes['content'] = $params['content'];
		}

		if (isset($params['keywords'])) {
			$attributes['keywords'] = trim($params['keywords']);
		}

		if (isset($params['description'])) {
			$attributes['description'] = $params['description'];
		}

		if (isset($params['sort'])) {
			$sort = (int) $params['sort'];
			if ($sort > 0) {
				$attributes['sort'] = $sort;
			}
			else {
				return false;
			}
		}

		if (isset($params['category_id'])) {
			$categoryId = (int) $params['category_id'];
			if ($categoryId > 0) {
				$attributes['category_id'] = $categoryId;
			}
			else {
				return false;
			}
		}

		if (isset($params['category_name'])) {
			$categoryName = trim($params['category_name']);
			if ($categoryName !== '') {
				$attributes['category_name'] = $categoryName;
			}
			else {
				return false;
			}
		}

		if ((isset($attributes['category_id']) && !isset($attributes['category_name']))
			|| (isset($attributes['category_name']) && !isset($attributes['category_id']))) {
			return false;
		}

		if (isset($params['password'])) {
			$attributes['password'] = trim($params['password']);
		}

		if (isset($params['picture'])) {
			$attributes['picture'] = trim($params['picture']);
		}

		if (isset($params['is_head'])) {
			$isHead = trim($params['is_head']);
			if ($isHead !== '') {
				$attributes['is_head'] = $isHead;
			}
			else {
				return false;
			}
		}

		if (isset($params['is_recommend'])) {
			$isRecommend = trim($params['is_recommend']);
			if ($isRecommend !== '') {
				$attributes['is_recommend'] = $isRecommend;
			}
			else {
				return false;
			}
		}

		if (isset($params['is_jump'])) {
			$isJump = trim($params['is_jump']);
			if ($isJump !== '') {
				$attributes['is_jump'] = $isJump;
			}
			else {
				return false;
			}
		}

		if (isset($params['jump_url'])) {
			$attributes['jump_url'] = trim($params['jump_url']);
		}

		if (isset($attributes['is_jump']) && $attributes['is_jump'] === 'y'
			&& isset($attributes['jump_url']) && $attributes['jump_url'] === '') {
			return false;
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

		if (isset($params['dt_publish_up'])) {
			$dtPublishUp = trim($params['dt_publish_up']);
			if ($dtPublishUp !== '') {
				$attributes['dt_publish_up'] = $dtPublishUp;
			}
			else {
				return false;
			}
		}

		if (isset($params['dt_publish_down'])) {
			$dtPublishDown = trim($params['dt_publish_down']);
			if ($dtPublishDown !== '') {
				$attributes['dt_publish_down'] = $dtPublishDown;
			}
			else {
				$attributes['dt_publish_down'] = '0000-00-00 00:00:00';
			}
		}

		if (isset($params['comment_status'])) {
			$commentStatus = trim($params['comment_status']);
			if ($commentStatus !== '') {
				$attributes['comment_status'] = $commentStatus;
			}
			else {
				return false;
			}
		}

		if (isset($params['allow_other_modify'])) {
			$allowOtherModify = trim($params['allow_other_modify']);
			if ($allowOtherModify !== '') {
				$attributes['allow_other_modify'] = $allowOtherModify;
			}
			else {
				return false;
			}
		}

		if (isset($params['hits'])) {
			$hits = (int) $params['hits'];
			if ($hits >= 0) {
				$attributes['hits'] = $hits;
			}
			else {
				return false;
			}
		}

		if (isset($params['praise_count'])) {
			$praiseCount = (int) $params['praise_count'];
			if ($praiseCount >= 0) {
				$attributes['praise_count'] = $praiseCount;
			}
			else {
				return false;
			}
		}

		if (isset($params['comment_count'])) {
			$commentCount = (int) $params['comment_count'];
			if ($commentCount >= 0) {
				$attributes['comment_count'] = $commentCount;
			}
			else {
				return false;
			}
		}

		if (isset($params['creator_id'])) {
			$creatorId = (int) $params['creator_id'];
			if ($creatorId > 0) {
				$attributes['creator_id'] = $creatorId;
			}
			else {
				return false;
			}
		}

		if (isset($params['creator_name'])) {
			$creatorName = trim($params['creator_name']);
			if ($creatorName !== '') {
				$attributes['creator_name'] = $creatorName;
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

		if ((isset($attributes['last_modifier_id']) && !isset($attributes['last_modifier_name']))
			|| (isset($attributes['last_modifier_name']) && !isset($attributes['last_modifier_id']))) {
			return false;
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

		$tableName = $this->getTblprefix() . TableNames::getPosts();
		$sql = $this->getCommandBuilder()->createUpdate($tableName, array_keys($attributes), '`post_id` = ?');
		$attributes['post_id'] = $postId;
		$rowCount = $this->update($sql, $attributes);
		return $rowCount;
	}

	/**
	 * 通过主键，编辑多条记录
	 * @param array|integer $postId
	 * @param array $params
	 * @return integer
	 */
	public function batchModifyByPk($postIds, array $params = array())
	{
		$postIds = Clean::positiveInteger($postIds);
		if ($postIds === false) {
			return false;
		}

		if (is_array($postIds)) {
			$postIds = implode(', ', $postIds);
		}

		$attributes = array();

		if (isset($params['sort'])) {
			$sort = (int) $params['sort'];
			if ($sort > 0) {
				$attributes['sort'] = $sort;
			}
			else {
				return false;
			}
		}

		if (isset($params['is_head'])) {
			$isHead = trim($params['is_head']);
			if ($isHead !== '') {
				$attributes['is_head'] = $isHead;
			}
			else {
				return false;
			}
		}

		if (isset($params['is_recommend'])) {
			$isRecommend = trim($params['is_recommend']);
			if ($isRecommend !== '') {
				$attributes['is_recommend'] = $isRecommend;
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

		if (isset($params['dt_publish_up'])) {
			$dtPublishUp = trim($params['dt_publish_up']);
			if ($dtPublishUp !== '') {
				$attributes['dt_publish_up'] = $dtPublishUp;
			}
			else {
				return false;
			}
		}

		if (isset($params['dt_publish_down'])) {
			$dtPublishDown = trim($params['dt_publish_down']);
			if ($dtPublishDown !== '') {
				$attributes['dt_publish_down'] = $dtPublishDown;
			}
			else {
				return false;
			}
		}

		if (isset($params['comment_status'])) {
			$commentStatus = trim($params['comment_status']);
			if ($commentStatus !== '') {
				$attributes['comment_status'] = $commentStatus;
			}
			else {
				return false;
			}
		}

		if (isset($params['allow_other_modify'])) {
			$allowOtherModify = trim($params['allow_other_modify']);
			if ($allowOtherModify !== '') {
				$attributes['allow_other_modify'] = $allowOtherModify;
			}
			else {
				return false;
			}
		}

		if (isset($params['trash'])) {
			$trash = trim($params['trash']);
			if ($trash !== '') {
				$attributes['trash'] = $trash;
			}
			else {
				return false;
			}
		}

		$rowCount = 0;

		if ($attributes === array()) {
			return $rowCount;
		}

		$tableName = $this->getTblprefix() . TableNames::getPosts();
		$condition = '`post_id` IN (' . $postIds . ')';
		$sql = $this->getCommandBuilder()->createUpdate($tableName, array_keys($attributes), $condition);
		$rowCount = $this->update($sql, $attributes);
		return $rowCount;
	}

	/**
	 * 通过主键，删除一条记录
	 * @param integer $postId
	 * @return integer
	 */
	public function removeByPk($postId)
	{
		if (($postId = (int) $postId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getPosts();
		$sql = $this->getCommandBuilder()->createDelete($tableName, '`post_id` = ?');
		$rowCount = $this->delete($sql, $postId);
		return $rowCount;
	}
}
