<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace advert\db;

use tdo\AbstractDb;
use advert\library\Constant;
use advert\library\TableNames;

/**
 * Adverts class file
 * 业务层：数据库操作类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Adverts.php 1 2014-10-25 20:49:44Z Code Generator $
 * @package advert.db
 * @since 1.0
 */
class Adverts extends AbstractDb
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
		$tableName = $this->getTblprefix() . TableNames::getAdverts();
		$sql = 'SELECT ' . $option . ' `advert_id`, `advert_name`, `type_key`, `description`, `is_published`, `dt_publish_up`, `dt_publish_down`, `sort`, `show_type`, `show_code`, `title`, `advert_url`, `advert_src`, `advert_src2`, `attr_alt`, `attr_width`, `attr_height`, `attr_fontsize`, `attr_target`, `dt_created` FROM `' . $tableName . '`';

		$condition = '1';
		$attributes = array();

		if (isset($params['type_key'])) {
			$typeKey = trim($params['type_key']);
			if ($typeKey !== '') {
				$condition .= ' AND `type_key` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['type_key'] = $typeKey;
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

		if (isset($params['show_type'])) {
			$showType = trim($params['show_type']);
			if ($showType !== '') {
				$condition .= ' AND `show_type` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['show_type'] = $showType;
			}
		}

		if (isset($params['advert_url'])) {
			$advertUrl = trim($params['advert_url']);
			if ($advertUrl !== '') {
				$condition .= ' AND `advert_url` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['advert_url'] = $advertUrl;
			}
		}

		if (isset($params['advert_src'])) {
			$advertSrc = trim($params['advert_src']);
			if ($advertSrc !== '') {
				$condition .= ' AND `advert_src` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['advert_src'] = $advertSrc;
			}
		}

		if (isset($params['advert_src2'])) {
			$advertSrc2 = trim($params['advert_src2']);
			if ($advertSrc2 !== '') {
				$condition .= ' AND `advert_src2` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['advert_src2'] = $advertSrc2;
			}
		}

		$sql = $commandBuilder->applyCondition($sql, $condition);
		$sql = $commandBuilder->applyOrder($sql, $order);
		$sql = $commandBuilder->applyLimit($sql, $limit, $offset);
		if ($option === 'SQL_CALC_FOUND_ROWS') {
			$ret = $this->fetchAllNoCache($sql, $attributes);
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
	 * @param integer $advertId
	 * @return array
	 */
	public function findByPk($advertId)
	{
		if (($advertId = (int) $advertId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getAdverts();
		$sql = 'SELECT `advert_id`, `advert_name`, `type_key`, `description`, `is_published`, `dt_publish_up`, `dt_publish_down`, `sort`, `show_type`, `show_code`, `title`, `advert_url`, `advert_src`, `advert_src2`, `attr_alt`, `attr_width`, `attr_height`, `attr_fontsize`, `attr_target`, `dt_created` FROM `' . $tableName . '` WHERE `advert_id` = ?';
		return $this->fetchAssoc($sql, $advertId);
	}

	/**
	 * 通过类型Key，查询记录数
	 * @param string $typeKey
	 * @return integer
	 */
	public function countByTypeKey($typeKey)
	{
		if (($typeKey = trim($typeKey)) === '') {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getAdverts();
		$sql = 'SELECT COUNT(*) FROM `' . $tableName . '` WHERE `type_key` = ?';
		return $this->fetchColumn($sql, $typeKey);
	}

	/**
	 * 新增一条记录
	 * @param array $params
	 * @param boolean $ignore
	 * @return integer
	 */
	public function create(array $params = array(), $ignore = false)
	{
		$advertName = isset($params['advert_name']) ? trim($params['advert_name']) : '';
		$typeKey = isset($params['type_key']) ? trim($params['type_key']) : '';
		$description = isset($params['description']) ? $params['description'] : '';
		$isPublished = isset($params['is_published']) ? trim($params['is_published']) : '';
		$dtPublishUp = isset($params['dt_publish_up']) ? trim($params['dt_publish_up']) : '';
		$dtPublishDown = isset($params['dt_publish_down']) ? trim($params['dt_publish_down']) : '';
		$sort = isset($params['sort']) ? (int) $params['sort'] : 0;
		$showType = isset($params['show_type']) ? trim($params['show_type']) : '';
		$showCode = isset($params['show_code']) ? $params['show_code'] : '';
		$title = isset($params['title']) ? trim($params['title']) : '';
		$advertUrl = isset($params['advert_url']) ? trim($params['advert_url']) : '';
		$advertSrc = isset($params['advert_src']) ? trim($params['advert_src']) : '';
		$advertSrc2 = isset($params['advert_src2']) ? trim($params['advert_src2']) : '';
		$attrAlt = isset($params['attr_alt']) ? trim($params['attr_alt']) : '';
		$attrWidth = isset($params['attr_width']) ? (int) $params['attr_width'] : 0;
		$attrHeight = isset($params['attr_height']) ? (int) $params['attr_height'] : 0;
		$attrFontsize = isset($params['attr_fontsize']) ? trim($params['attr_fontsize']) : '';
		$attrTarget = isset($params['attr_target']) ? trim($params['attr_target']) : '';
		$dtCreated = isset($params['dt_created']) ? trim($params['dt_created']) : '';

		if ($advertName === '' || $typeKey === '' || $sort <= 0 || $showType === '' || $showCode === '') {
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

		if ($dtCreated === '') {
			$dtCreated = date('Y-m-d H:i:s');
		}

		$tableName = $this->getTblprefix() . TableNames::getAdverts();
		$attributes = array(
			'advert_name' => $advertName,
			'type_key' => $typeKey,
			'description' => $description,
			'is_published' => $isPublished,
			'dt_publish_up' => $dtPublishUp,
			'dt_publish_down' => $dtPublishDown,
			'sort' => $sort,
			'show_type' => $showType,
			'show_code' => $showCode,
			'title' => $title,
			'advert_url' => $advertUrl,
			'advert_src' => $advertSrc,
			'advert_src2' => $advertSrc2,
			'attr_alt' => $attrAlt,
			'attr_width' => $attrWidth,
			'attr_height' => $attrHeight,
			'attr_fontsize' => $attrFontsize,
			'attr_target' => $attrTarget,
			'dt_created' => $dtCreated,
		);

		$sql = $this->getCommandBuilder()->createInsert($tableName, array_keys($attributes), $ignore);
		$lastInsertId = $this->insert($sql, $attributes);
		return $lastInsertId;
	}

	/**
	 * 通过主键，编辑一条记录
	 * @param integer $advertId
	 * @param array $params
	 * @return integer
	 */
	public function modifyByPk($advertId, array $params = array())
	{
		if (($advertId = (int) $advertId) <= 0) {
			return false;
		}

		$attributes = array();

		if (isset($params['advert_name'])) {
			$advertName = trim($params['advert_name']);
			if ($advertName !== '') {
				$attributes['advert_name'] = $advertName;
			}
			else {
				return false;
			}
		}

		if (isset($params['type_key'])) {
			$typeKey = trim($params['type_key']);
			if ($typeKey !== '') {
				$attributes['type_key'] = $typeKey;
			}
			else {
				return false;
			}
		}

		if (isset($params['description'])) {
			$attributes['description'] = $params['description'];
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

		if (isset($params['sort'])) {
			$sort = (int) $params['sort'];
			if ($sort > 0) {
				$attributes['sort'] = $sort;
			}
			else {
				return false;
			}
		}

		if (isset($params['show_type'])) {
			$showType = trim($params['show_type']);
			if ($showType !== '') {
				$attributes['show_type'] = $showType;
			}
			else {
				return false;
			}
		}

		if (isset($params['show_code'])) {
			$showCode = $params['show_code'];
			if ($showCode !== '') {
				$attributes['show_code'] = $showCode;
			}
			else {
				return false;
			}
		}

		if (isset($params['title'])) {
			$attributes['title'] = trim($params['title']);
		}

		if (isset($params['advert_url'])) {
			$attributes['advert_url'] = trim($params['advert_url']);
		}

		if (isset($params['advert_src'])) {
			$attributes['advert_src'] = trim($params['advert_src']);
		}

		if (isset($params['advert_src2'])) {
			$attributes['advert_src2'] = trim($params['advert_src2']);
		}

		if (isset($params['attr_alt'])) {
			$attributes['attr_alt'] = trim($params['attr_alt']);
		}

		if (isset($params['attr_width'])) {
			$attrWidth = (int) $params['attr_width'];
			if ($attrWidth >= 0) {
				$attributes['attr_width'] = $attrWidth;
			}
			else {
				return false;
			}
		}

		if (isset($params['attr_height'])) {
			$attrHeight = (int) $params['attr_height'];
			if ($attrHeight >= 0) {
				$attributes['attr_height'] = $attrHeight;
			}
			else {
				return false;
			}
		}

		if (isset($params['attr_fontsize'])) {
			$attributes['attr_fontsize'] = trim($params['attr_fontsize']);
		}

		if (isset($params['attr_target'])) {
			$attributes['attr_target'] = trim($params['attr_target']);
		}

		if (isset($params['dt_created'])) {
			$attributes['dt_created'] = trim($params['dt_created']);
		}

		$rowCount = 0;

		if ($attributes === array()) {
			return $rowCount;
		}

		$tableName = $this->getTblprefix() . TableNames::getAdverts();
		$sql = $this->getCommandBuilder()->createUpdate($tableName, array_keys($attributes), '`advert_id` = ?');
		$attributes['advert_id'] = $advertId;
		$rowCount = $this->update($sql, $attributes);
		return $rowCount;
	}

	/**
	 * 通过主键，删除一条记录
	 * @param integer $advertId
	 * @return integer
	 */
	public function removeByPk($advertId)
	{
		if (($advertId = (int) $advertId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getAdverts();
		$sql = $this->getCommandBuilder()->createDelete($tableName, '`advert_id` = ?');
		$rowCount = $this->delete($sql, $advertId);
		return $rowCount;
	}
}
