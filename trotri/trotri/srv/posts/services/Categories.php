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
use libsrv\Service;
use posts\library\Lang;

/**
 * Categories class file
 * 业务层：业务处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Categories.php 1 2014-10-13 21:17:13Z Code Generator $
 * @package posts.services
 * @since 1.0
 */
class Categories extends AbstractService
{
	/**
	 * 递归方式获取所有的类别，默认用|—填充子类别左边用于和父类别错位（可用于Table列表）
	 * @param integer $catPid
	 * @param string $padStr
	 * @param string $leftPad
	 * @param string $rightPad
	 * @return array
	 */
	public function findLists($catPid = 0, $padStr = '|—', $leftPad = '', $rightPad = null)
	{
		$rows = $this->findAllByPid($catPid);
		if (!$rows || !is_array($rows)) {
			return array();
		}

		$tmpLeftPad = is_string($leftPad) ? $leftPad . $padStr : null;
		$tmpRightPad = is_string($rightPad) ? $rightPad . $padStr : null;

		$data = array();
		foreach ($rows as $row) {
			$row['category_name'] = $leftPad . $row['category_name'] . $rightPad;
			$data[] = $row;

			$tmpRows = $this->findLists($row['category_id'], $padStr, $tmpLeftPad, $tmpRightPad);
			$data = array_merge($data, $tmpRows);
		}

		return $data;
	}

	/**
	 * 递归方式获取所有的类别名，默认用空格填充子类别左边用于和父类别错位
	 * （只返回ID和类别名的键值对）（可用于Select表单的Option选项）
	 * @param integer $catPid
	 * @param string $padStr
	 * @param string $leftPad
	 * @param string $rightPad
	 * @return array
	 */
	public function getOptions($catPid = -1, $padStr = '&nbsp;&nbsp;&nbsp;&nbsp;', $leftPad = '', $rightPad = null)
	{
		if ($catPid === -1) {
			$tmpLeftPad = is_string($leftPad) ? $leftPad . $padStr : null;
			$tmpRightPad = is_string($rightPad) ? $rightPad . $padStr : null;

			$data = array(0 => Lang::_('SRV_ENUM_POST_CATEGORIES_CATEGORY_TOP'));
			$data += $this->getOptions(0, $padStr, $tmpLeftPad, $tmpRightPad);
			return $data;
		}

		$data = array();

		$rows = $this->findLists($catPid, $padStr, $leftPad, $rightPad);
		if (is_array($rows)) {
			foreach ($rows as $row) {
				if (!isset($row['category_id']) || !isset($row['category_name'])) {
					continue;
				}

				$categoryId = (int) $row['category_id'];
				$data[$categoryId] = $row['category_name'];
			}
		}

		return $data;
	}

	/**
	 * 通过父ID，获取所有的类别
	 * @param integer $catPid
	 * @return array
	 */
	public function findAllByPid($catPid)
	{
		$rows = $this->getDb()->findAllByPid($catPid);
		return $rows;
	}

	/**
	 * 通过主键，查询一条记录
	 * @param integer $categoryId
	 * @return array
	 */
	public function findByPk($categoryId)
	{
		$row = $this->getDb()->findByPk($categoryId);
		return $row;
	}

	/**
	 * 通过类别ID，查询文档数
	 * @param integer $categoryId
	 * @return integer
	 */
	public function getPostCount($categoryId)
	{
		return Service::getInstance('Posts', 'posts')->countByCategoryId($categoryId);
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
	 * 通过“主键ID”，获取“类别名”
	 * @param integer $categoryId
	 * @return string
	 */
	public function getCategoryNameByCategoryId($categoryId)
	{
		$value = $this->getByPk('category_name', $categoryId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“所属父类别”
	 * @param integer $categoryId
	 * @return integer
	 */
	public function getCategoryPidByCategoryId($categoryId)
	{
		$value = $this->getByPk('category_pid', $categoryId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“别名”
	 * @param integer $categoryId
	 * @return string
	 */
	public function getAliasByCategoryId($categoryId)
	{
		$value = $this->getByPk('alias', $categoryId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“SEO标题”
	 * @param integer $categoryId
	 * @return string
	 */
	public function getMetaTitleByCategoryId($categoryId)
	{
		$value = $this->getByPk('meta_title', $categoryId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“SEO关键字”
	 * @param integer $categoryId
	 * @return string
	 */
	public function getMetaKeywordsByCategoryId($categoryId)
	{
		$value = $this->getByPk('meta_keywords', $categoryId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“SEO描述”
	 * @param integer $categoryId
	 * @return string
	 */
	public function getMetaDescriptionByCategoryId($categoryId)
	{
		$value = $this->getByPk('meta_description', $categoryId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“封页模板名”
	 * @param integer $categoryId
	 * @return string
	 */
	public function getTplHomeByCategoryId($categoryId)
	{
		$value = $this->getByPk('tpl_home', $categoryId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“列表模板名”
	 * @param integer $categoryId
	 * @return string
	 */
	public function getTplListByCategoryId($categoryId)
	{
		$value = $this->getByPk('tpl_list', $categoryId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“文档模板名”
	 * @param integer $categoryId
	 * @return string
	 */
	public function getTplViewByCategoryId($categoryId)
	{
		$value = $this->getByPk('tpl_view', $categoryId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“排序”
	 * @param integer $categoryId
	 * @return integer
	 */
	public function getSortByCategoryId($categoryId)
	{
		$value = $this->getByPk('sort', $categoryId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“描述”
	 * @param integer $categoryId
	 * @return string
	 */
	public function getDescriptionByCategoryId($categoryId)
	{
		$value = $this->getByPk('description', $categoryId);
		return $value ? $value : '';
	}

}
