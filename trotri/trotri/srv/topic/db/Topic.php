<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace topic\db;

use tdo\AbstractDb;
use topic\library\Constant;
use topic\library\TableNames;

/**
 * Topic class file
 * 业务层：数据库操作类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Topic.php 1 2014-11-04 16:50:14Z Code Generator $
 * @package topic.db
 * @since 1.0
 */
class Topic extends AbstractDb
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
		$tableName = $this->getTblprefix() . TableNames::getTopic();
		$sql = 'SELECT ' . $option . ' `topic_id`, `topic_name`, `topic_key`, `cover`, `meta_title`, `meta_keywords`, `meta_description`, `html_style`, `html_script`, `html_head`, `html_body`, `is_published`, `sort`, `use_header`, `use_footer`, `dt_created` FROM `' . $tableName . '`';

		$condition = '1';
		$attributes = array();

		if (isset($params['topic_name'])) {
			$topicName = trim($params['topic_name']);
			if ($topicName !== '') {
				$condition .= ' AND `topic_name` LIKE ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['topic_name'] = '%' . $topicName . '%';
			}
		}

		if (isset($params['topic_key'])) {
			$topicKey = trim($params['topic_key']);
			if ($topicKey !== '') {
				$condition .= ' AND `topic_key` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['topic_key'] = $topicKey;
			}
		}

		if (isset($params['is_published'])) {
			$isPublished = trim($params['is_published']);
			if ($isPublished !== '') {
				$condition .= ' AND `is_published` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['is_published'] = $isPublished;
			}
		}

		if (isset($params['use_header'])) {
			$useHeader = trim($params['use_header']);
			if ($useHeader !== '') {
				$condition .= ' AND `use_header` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['use_header'] = $useHeader;
			}
		}

		if (isset($params['use_footer'])) {
			$useFooter = trim($params['use_footer']);
			if ($useFooter !== '') {
				$condition .= ' AND `use_footer` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['use_footer'] = $useFooter;
			}
		}

		if (isset($params['topic_id'])) {
			$topicId = (int) $params['topic_id'];
			if ($topicId > 0) {
				$condition .= ' AND `topic_id` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['topic_id'] = $topicId;
			}
		}

		$sql = $commandBuilder->applyCondition($sql, $condition);
		$sql = $commandBuilder->applyOrder($sql, $order);
		$sql = $commandBuilder->applyLimit($sql, $limit, $offset);

		if ($option === 'SQL_CALC_FOUND_ROWS') {
			$ret = $this->fetchAllNoCache($sql, $attributes);
			if (isset($attributes['topic_name'])) {
				$attributes['topic_name'] = $topicName;
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
	 * @param integer $topicId
	 * @return array
	 */
	public function findByPk($topicId)
	{
		if (($topicId = (int) $topicId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getTopic();
		$sql = 'SELECT `topic_id`, `topic_name`, `topic_key`, `cover`, `meta_title`, `meta_keywords`, `meta_description`, `html_style`, `html_script`, `html_head`, `html_body`, `is_published`, `sort`, `use_header`, `use_footer`, `dt_created` FROM `' . $tableName . '` WHERE `topic_id` = ?';
		return $this->fetchAssoc($sql, $topicId);
	}

	/**
	 * 通过专题Key，查询一条记录
	 * @param string $topicKey
	 * @return array
	 */
	public function findByTopicKey($topicKey)
	{
		if (($topicKey = trim($topicKey)) === '') {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getTopic();
		$sql = 'SELECT `topic_id`, `topic_name`, `topic_key`, `cover`, `meta_title`, `meta_keywords`, `meta_description`, `html_style`, `html_script`, `html_head`, `html_body`, `is_published`, `sort`, `use_header`, `use_footer`, `dt_created` FROM `' . $tableName . '` WHERE `topic_key` = ?';
		return $this->fetchAssoc($sql, $topicKey);
	}

	/**
	 * 新增一条记录
	 * @param array $params
	 * @param boolean $ignore
	 * @return integer
	 */
	public function create(array $params = array(), $ignore = false)
	{
		$topicName = isset($params['topic_name']) ? trim($params['topic_name']) : '';
		$topicKey = isset($params['topic_key']) ? trim($params['topic_key']) : '';
		$cover = isset($params['cover']) ? trim($params['cover']) : '';
		$metaTitle = isset($params['meta_title']) ? trim($params['meta_title']) : '';
		$metaKeywords = isset($params['meta_keywords']) ? trim($params['meta_keywords']) : '';
		$metaDescription = isset($params['meta_description']) ? $params['meta_description'] : '';
		$htmlStyle = isset($params['html_style']) ? $params['html_style'] : '';
		$htmlScript = isset($params['html_script']) ? $params['html_script'] : '';
		$htmlHead = isset($params['html_head']) ? $params['html_head'] : '';
		$htmlBody = isset($params['html_body']) ? $params['html_body'] : '';
		$isPublished = isset($params['is_published']) ? trim($params['is_published']) : '';
		$sort = isset($params['sort']) ? (int) $params['sort'] : 0;
		$useHeader = isset($params['use_header']) ? trim($params['use_header']) : '';
		$useFooter = isset($params['use_footer']) ? trim($params['use_footer']) : '';
		$dtCreated = isset($params['dt_created']) ? trim($params['dt_created']) : '';

		if ($topicName === '' || $topicKey === '' || $cover === '' || $metaTitle === '' || $metaKeywords === '' || $metaDescription === '' || $htmlBody === '' || $sort <= 0) {
			return false;
		}

		if ($isPublished === '') {
			$isPublished = 'n';
		}

		if ($useHeader === '') {
			$useHeader = 'y';
		}

		if ($useFooter === '') {
			$useFooter = 'y';
		}

		if ($dtCreated === '') {
			$dtCreated = date('Y-m-d H:i:s');
		}

		$tableName = $this->getTblprefix() . TableNames::getTopic();
		$attributes = array(
			'topic_name' => $topicName,
			'topic_key' => $topicKey,
			'cover' => $cover,
			'meta_title' => $metaTitle,
			'meta_keywords' => $metaKeywords,
			'meta_description' => $metaDescription,
			'html_style' => $htmlStyle,
			'html_script' => $htmlScript,
			'html_head' => $htmlHead,
			'html_body' => $htmlBody,
			'is_published' => $isPublished,
			'sort' => $sort,
			'use_header' => $useHeader,
			'use_footer' => $useFooter,
			'dt_created' => $dtCreated,
		);

		$sql = $this->getCommandBuilder()->createInsert($tableName, array_keys($attributes), $ignore);
		$lastInsertId = $this->insert($sql, $attributes);
		return $lastInsertId;
	}

	/**
	 * 通过主键，编辑一条记录
	 * @param integer $topicId
	 * @param array $params
	 * @return integer
	 */
	public function modifyByPk($topicId, array $params = array())
	{
		if (($topicId = (int) $topicId) <= 0) {
			return false;
		}

		$attributes = array();

		if (isset($params['topic_name'])) {
			$topicName = trim($params['topic_name']);
			if ($topicName !== '') {
				$attributes['topic_name'] = $topicName;
			}
			else {
				return false;
			}
		}

		if (isset($params['topic_key'])) {
			$topicKey = trim($params['topic_key']);
			if ($topicKey !== '') {
				$attributes['topic_key'] = $topicKey;
			}
			else {
				return false;
			}
		}

		if (isset($params['cover'])) {
			$cover = trim($params['cover']);
			if ($cover !== '') {
				$attributes['cover'] = $cover;
			}
			else {
				return false;
			}
		}

		if (isset($params['meta_title'])) {
			$metaTitle = trim($params['meta_title']);
			if ($metaTitle !== '') {
				$attributes['meta_title'] = $metaTitle;
			}
			else {
				return false;
			}
		}

		if (isset($params['meta_keywords'])) {
			$metaKeywords = trim($params['meta_keywords']);
			if ($metaKeywords !== '') {
				$attributes['meta_keywords'] = $metaKeywords;
			}
			else {
				return false;
			}
		}

		if (isset($params['meta_description'])) {
			$metaDescription = $params['meta_description'];
			if ($metaDescription !== '') {
				$attributes['meta_description'] = $metaDescription;
			}
			else {
				return false;
			}
		}

		if (isset($params['html_style'])) {
			$attributes['html_style'] = $params['html_style'];
		}

		if (isset($params['html_script'])) {
			$attributes['html_script'] = $params['html_script'];
		}

		if (isset($params['html_head'])) {
			$attributes['html_head'] = $params['html_head'];
		}

		if (isset($params['html_body'])) {
			$attributes['html_body'] = $params['html_body'];
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

		if (isset($params['sort'])) {
			$sort = (int) $params['sort'];
			if ($sort > 0) {
				$attributes['sort'] = $sort;
			}
			else {
				return false;
			}
		}

		if (isset($params['use_header'])) {
			$useHeader = trim($params['use_header']);
			if ($useHeader !== '') {
				$attributes['use_header'] = $useHeader;
			}
			else {
				return false;
			}
		}

		if (isset($params['use_footer'])) {
			$useFooter = trim($params['use_footer']);
			if ($useFooter !== '') {
				$attributes['use_footer'] = $useFooter;
			}
			else {
				return false;
			}
		}

		$rowCount = 0;

		if ($attributes === array()) {
			return $rowCount;
		}

		$tableName = $this->getTblprefix() . TableNames::getTopic();
		$sql = $this->getCommandBuilder()->createUpdate($tableName, array_keys($attributes), '`topic_id` = ?');
		$attributes['topic_id'] = $topicId;
		$rowCount = $this->update($sql, $attributes);
		return $rowCount;
	}

	/**
	 * 通过主键，删除一条记录
	 * @param integer $topicId
	 * @return integer
	 */
	public function removeByPk($topicId)
	{
		if (($topicId = (int) $topicId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getTopic();
		$sql = $this->getCommandBuilder()->createDelete($tableName, '`topic_id` = ?');
		$rowCount = $this->delete($sql, $topicId);
		return $rowCount;
	}
}
