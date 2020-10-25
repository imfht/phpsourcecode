<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace advert\services;

use libsrv\AbstractService;
use advert\library\Constant;
use advert\library\Plugin;

/**
 * Adverts class file
 * 业务层：业务处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Adverts.php 1 2014-10-26 12:07:53Z Code Generator $
 * @package advert.services
 * @since 1.0
 */
class Adverts extends AbstractService
{
	/**
	 * 查询第一条记录
	 * @param string $typeKey
	 * @return array
	 */
	public function getRow($typeKey)
	{
		if (($typeKey = trim($typeKey)) === '') {
			return array();
		}

		$rows = $this->findRows($typeKey, 1);
		if ($rows && is_array($rows)) {
			$row = array_shift($rows);
			return $row;
		}

		return array();
	}

	/**
	 * 查询多条记录：不包含分页信息
	 * @param string $typeKey
	 * @param integer $limit
	 * @param integer $offset
	 * @return array
	 */
	public function findRows($typeKey, $limit = 0, $offset = 0)
	{
		if (($typeKey = trim($typeKey)) === '') {
			return array();
		}

		$order = DataAdverts::ORDER_BY_SORT;
		$nowTime = date('Y-m-d H:i:s');

		$params = array(
			'type_key' => $typeKey,
			'is_published' => DataAdverts::IS_PUBLISHED_Y,
			'dt_publish_up' => $nowTime,
			'dt_publish_down' => $nowTime
		);

		$rows = $this->findAll($params, $order, $limit, $offset);
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
		$limit = min(max((int) $limit, 0), Constant::FIND_MAX_LIMIT);
		$offset = max((int) $offset, 0);

		$rows = $this->getDb()->findAll($params, $order, $limit, $offset, $option);
		return $rows;
	}

	/**
	 * 通过主键，查询一条记录
	 * @param integer $advertId
	 * @return array
	 */
	public function findByPk($advertId)
	{
		$row = $this->getDb()->findByPk($advertId);
		return $row;
	}

	/**
	 * (non-PHPdoc)
	 * @see \libsrv\AbstractService::create()
	 */
	public function create(array $params = array(), $ignore = false)
	{
		$dispatcher = Plugin::getInstance();
		$dispatcher->trigger('onBeforeSave', array(__METHOD__, &$params));

		return parent::create($params, $ignore);
	}

	/**
	 * (non-PHPdoc)
	 * @see \libsrv\AbstractService::modifyByPk()
	 */
	public function modifyByPk($value, array $params = array())
	{
		$dispatcher = Plugin::getInstance();
		$dispatcher->trigger('onBeforeSave', array(__METHOD__, &$params, $value));

		return parent::modifyByPk($value, $params);
	}

	/**
	 * 通过位置Key，查询记录数
	 * @param string $typeKey
	 * @return integer
	 */
	public function countByTypeKey($typeKey)
	{
		$count = $this->getDb()->countByTypeKey($typeKey);
		return $count;
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
			$rowCount += $this->modifyByPk($pk, array($columnName => $value));
		}

		return $rowCount;
	}

	/**
	 * 通过“展现方式”，获取“展现方式名”
	 * @param string $showType
	 * @return string
	 */
	public function getShowTypeLangByShowType($showType)
	{
		$enum = DataAdverts::getShowTypeEnum();
		return isset($enum[$showType]) ? $enum[$showType] : '';
	}

	/**
	 * 通过“主键ID”，获取“广告名”
	 * @param integer $advertId
	 * @return string
	 */
	public function getAdvertNameByAdvertId($advertId)
	{
		$value = $this->getByPk('advert_name', $advertId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“位置Key”
	 * @param integer $advertId
	 * @return string
	 */
	public function getTypeKeyByAdvertId($advertId)
	{
		$value = $this->getByPk('type_key', $advertId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“描述”
	 * @param integer $advertId
	 * @return string
	 */
	public function getDescriptionByAdvertId($advertId)
	{
		$value = $this->getByPk('description', $advertId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“是否发表”
	 * @param integer $advertId
	 * @return string
	 */
	public function getIsPublishedByAdvertId($advertId)
	{
		$value = $this->getByPk('is_published', $advertId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“开始发表时间”
	 * @param integer $advertId
	 * @return string
	 */
	public function getDtPublishUpByAdvertId($advertId)
	{
		$value = $this->getByPk('dt_publish_up', $advertId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“结束发表时间”
	 * @param integer $advertId
	 * @return string
	 */
	public function getDtPublishDownByAdvertId($advertId)
	{
		$value = $this->getByPk('dt_publish_down', $advertId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“排序”
	 * @param integer $advertId
	 * @return integer
	 */
	public function getSortByAdvertId($advertId)
	{
		$value = $this->getByPk('sort', $advertId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“展现方式”
	 * @param integer $advertId
	 * @return string
	 */
	public function getShowTypeByAdvertId($advertId)
	{
		$value = $this->getByPk('show_type', $advertId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“展现代码”
	 * @param integer $advertId
	 * @return string
	 */
	public function getShowCodeByAdvertId($advertId)
	{
		$value = $this->getByPk('show_code', $advertId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“文字内容”
	 * @param integer $advertId
	 * @return string
	 */
	public function getTitleByAdvertId($advertId)
	{
		$value = $this->getByPk('title', $advertId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“广告链接”
	 * @param integer $advertId
	 * @return string
	 */
	public function getAdvertUrlByAdvertId($advertId)
	{
		$value = $this->getByPk('advert_url', $advertId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“图片|Flash链接”
	 * @param integer $advertId
	 * @return string
	 */
	public function getAdvertSrcByAdvertId($advertId)
	{
		$value = $this->getByPk('advert_src', $advertId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“辅图链接”
	 * @param integer $advertId
	 * @return string
	 */
	public function getAdvertSrc2ByAdvertId($advertId)
	{
		$value = $this->getByPk('advert_src2', $advertId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“图片替换文字”
	 * @param integer $advertId
	 * @return string
	 */
	public function getAttrAltByAdvertId($advertId)
	{
		$value = $this->getByPk('attr_alt', $advertId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“图片|Flash宽度”
	 * @param integer $advertId
	 * @return integer
	 */
	public function getAttrWidthByAdvertId($advertId)
	{
		$value = $this->getByPk('attr_width', $advertId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“图片|Flash高度”
	 * @param integer $advertId
	 * @return integer
	 */
	public function getAttrHeightByAdvertId($advertId)
	{
		$value = $this->getByPk('attr_height', $advertId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“文字大小”
	 * @param integer $advertId
	 * @return string
	 */
	public function getAttrFontsizeByAdvertId($advertId)
	{
		$value = $this->getByPk('attr_fontsize', $advertId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“Target属性”
	 * @param integer $advertId
	 * @return string
	 */
	public function getAttrTargetByAdvertId($advertId)
	{
		$value = $this->getByPk('attr_target', $advertId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“创建时间”
	 * @param integer $advertId
	 * @return string
	 */
	public function getDtCreatedByAdvertId($advertId)
	{
		$value = $this->getByPk('dt_created', $advertId);
		return $value ? $value : '';
	}

	/**
	 * 验证是否是代码展现方式
	 * @param string $showType
	 * @return boolean
	 */
	public function isShowTypeCode($showType)
	{
		return ($showType === DataAdverts::SHOW_TYPE_CODE);
	}

	/**
	 * 验证是否是图片展现方式
	 * @param string $showType
	 * @return boolean
	 */
	public function isShowTypeImage($showType)
	{
		return ($showType === DataAdverts::SHOW_TYPE_IMAGE);
	}

	/**
	 * 验证是否是文字展现方式
	 * @param string $showType
	 * @return boolean
	 */
	public function isShowTypeText($showType)
	{
		return ($showType === DataAdverts::SHOW_TYPE_TEXT);
	}

	/**
	 * 验证是否是Flash展现方式
	 * @param string $showType
	 * @return boolean
	 */
	public function isShowTypeFlash($showType)
	{
		return ($showType === DataAdverts::SHOW_TYPE_FLASH);
	}

}
