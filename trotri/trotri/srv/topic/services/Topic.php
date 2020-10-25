<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace topic\services;

use libsrv\AbstractService;
use topic\library\Constant;

/**
 * Topic class file
 * 业务层：业务处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Topic.php 1 2014-11-04 16:50:14Z Code Generator $
 * @package topic.services
 * @since 1.0
 */
class Topic extends AbstractService
{
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
			$order = 'sort ASC, dt_created DESC';
		}

		$params['is_published'] = DataTopic::IS_PUBLISHED_Y;

		$rows = $this->findAll($params, $order, $limit, $offset, $option);
		if ($option === 'SQL_CALC_FOUND_ROWS') {
			if ($rows && is_array($rows)) {
				if (isset($rows['attributes']) && is_array($rows['attributes'])) {
					if (isset($rows['attributes']['is_published'])) {
						unset($rows['attributes']['is_published']);
					}
				}
				if (isset($rows['order']) && $rows['order'] === DataTopic::ORDER_BY_SORT) {
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

		$rows = $this->getDb()->findAll($params, $order, $limit, $offset, $option);
		return $rows;
	}

	/**
	 * 通过主键，查询一条记录
	 * @param integer $topicId
	 * @param boolean $usable
	 * @return array
	 */
	public function findByPk($topicId, $usable = false)
	{
		$row = $this->getDb()->findByPk($topicId);
		if ($row && is_array($row) && isset($row['topic_id'])) {
			if ($usable) {
				if ($row['is_published'] !== DataTopic::IS_PUBLISHED_Y) {
					return array();
				}
			}

			return $row;
		}

		return array();
	}

	/**
	 * 通过专题Key，查询一条记录
	 * @param string $topicKey
	 * @param boolean $usable
	 * @return array
	 */
	public function findByTopicKey($topicKey, $usable = false)
	{
		$row = $this->getDb()->findByTopicKey($topicKey);
		if ($row && is_array($row) && isset($row['topic_id'])) {
			if ($usable) {
				if ($row['is_published'] !== DataTopic::IS_PUBLISHED_Y) {
					return array();
				}
			}

			$row['use_header'] = ($row['use_header'] === DataTopic::USE_HEADER_Y) ? true : false;
			$row['use_footer'] = ($row['use_footer'] === DataTopic::USE_FOOTER_Y) ? true : false;

			return $row;
		}

		return array();
	}

	/**
	 * 通过“主键ID”，获取“专题名”
	 * @param integer $topicId
	 * @return string
	 */
	public function getTopicNameByTopicId($topicId)
	{
		$value = $this->getByPk('topic_name', $topicId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“专题Key”
	 * @param integer $topicId
	 * @return string
	 */
	public function getTopicKeyByTopicId($topicId)
	{
		$value = $this->getByPk('topic_key', $topicId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“封面大图”
	 * @param integer $topicId
	 * @return string
	 */
	public function getCoverByTopicId($topicId)
	{
		$value = $this->getByPk('cover', $topicId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“SEO标题”
	 * @param integer $topicId
	 * @return string
	 */
	public function getMetaTitleByTopicId($topicId)
	{
		$value = $this->getByPk('meta_title', $topicId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“SEO关键字”
	 * @param integer $topicId
	 * @return string
	 */
	public function getMetaKeywordsByTopicId($topicId)
	{
		$value = $this->getByPk('meta_keywords', $topicId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“SEO描述”
	 * @param integer $topicId
	 * @return string
	 */
	public function getMetaDescriptionByTopicId($topicId)
	{
		$value = $this->getByPk('meta_description', $topicId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“CSS-style代码”
	 * @param integer $topicId
	 * @return string
	 */
	public function getHtmlStyleByTopicId($topicId)
	{
		$value = $this->getByPk('html_style', $topicId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“JavaScript代码”
	 * @param integer $topicId
	 * @return string
	 */
	public function getHtmlScriptByTopicId($topicId)
	{
		$value = $this->getByPk('html_script', $topicId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“Head代码”
	 * @param integer $topicId
	 * @return string
	 */
	public function getHtmlHeadByTopicId($topicId)
	{
		$value = $this->getByPk('html_head', $topicId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“页面内容”
	 * @param integer $topicId
	 * @return string
	 */
	public function getHtmlBodyByTopicId($topicId)
	{
		$value = $this->getByPk('html_body', $topicId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“是否发表”
	 * @param integer $topicId
	 * @return string
	 */
	public function getIsPublishedByTopicId($topicId)
	{
		$value = $this->getByPk('is_published', $topicId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“排序”
	 * @param integer $topicId
	 * @return integer
	 */
	public function getSortByTopicId($topicId)
	{
		$value = $this->getByPk('sort', $topicId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“使用公共的页头”
	 * @param integer $topicId
	 * @return string
	 */
	public function getUseHeaderByTopicId($topicId)
	{
		$value = $this->getByPk('use_header', $topicId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“使用公共的页脚”
	 * @param integer $topicId
	 * @return string
	 */
	public function getUseFooterByTopicId($topicId)
	{
		$value = $this->getByPk('use_footer', $topicId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“创建时间”
	 * @param integer $topicId
	 * @return string
	 */
	public function getDtCreatedByTopicId($topicId)
	{
		$value = $this->getByPk('dt_created', $topicId);
		return $value ? $value : '';
	}

}
