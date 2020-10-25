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
use posts\library\Constant;
use posts\library\TableNames;

/**
 * Categories class file
 * 业务层：数据库操作类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Categories.php 1 2014-10-13 21:17:13Z Code Generator $
 * @package posts.db
 * @since 1.0
 */
class Categories extends AbstractDb
{
	/**
	 * @var string 数据库配置名
	 */
	protected $_clusterName = Constant::DB_CLUSTER;

	/**
	 * 通过父ID，获取所有的类别
	 * @param integer $catPid
	 * @return array
	 */
	public function findAllByPid($catPid)
	{
		if (($catPid = (int) $catPid) < 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getCategories();
		$sql = 'SELECT `category_id`, `category_name`, `category_pid`, `alias`, `meta_title`, `meta_keywords`, `meta_description`, `tpl_home`, `tpl_list`, `tpl_view`, `sort`, `description` FROM `' . $tableName . '` WHERE `category_pid` = ? ORDER BY `sort`';
		return $this->fetchAll($sql, $catPid);
	}

	/**
	 * 通过主键，查询一条记录
	 * @param integer $categoryId
	 * @return array
	 */
	public function findByPk($categoryId)
	{
		if (($categoryId = (int) $categoryId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getCategories();
		$sql = 'SELECT `category_id`, `category_name`, `category_pid`, `alias`, `meta_title`, `meta_keywords`, `meta_description`, `tpl_home`, `tpl_list`, `tpl_view`, `sort`, `description` FROM `' . $tableName . '` WHERE `category_id` = ?';
		return $this->fetchAssoc($sql, $categoryId);
	}

	/**
	 * 新增一条记录
	 * @param array $params
	 * @param boolean $ignore
	 * @return integer
	 */
	public function create(array $params = array(), $ignore = false)
	{
		$categoryName = isset($params['category_name']) ? trim($params['category_name']) : '';
		$categoryPid = isset($params['category_pid']) ? (int) $params['category_pid'] : 0;
		$alias = isset($params['alias']) ? trim($params['alias']) : '';
		$metaTitle = isset($params['meta_title']) ? trim($params['meta_title']) : '';
		$metaKeywords = isset($params['meta_keywords']) ? trim($params['meta_keywords']) : '';
		$metaDescription = isset($params['meta_description']) ? trim($params['meta_description']) : '';
		$tplHome = isset($params['tpl_home']) ? trim($params['tpl_home']) : '';
		$tplList = isset($params['tpl_list']) ? trim($params['tpl_list']) : '';
		$tplView = isset($params['tpl_view']) ? trim($params['tpl_view']) : '';
		$sort = isset($params['sort']) ? (int) $params['sort'] : 0;
		$description = isset($params['description']) ? $params['description'] : '';

		if ($categoryName === '' || $categoryPid < 0 || $metaTitle === '' || $metaKeywords === '' || $metaDescription === ''
			|| $tplHome === '' || $tplList === '' || $tplView === '' || $sort < 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getCategories();
		$attributes = array(
			'category_name' => $categoryName,
			'category_pid' => $categoryPid,
			'alias' => $alias,
			'meta_title' => $metaTitle,
			'meta_keywords' => $metaKeywords,
			'meta_description' => $metaDescription,
			'tpl_home' => $tplHome,
			'tpl_list' => $tplList,
			'tpl_view' => $tplView,
			'sort' => $sort,
			'description' => $description,
		);

		$sql = $this->getCommandBuilder()->createInsert($tableName, array_keys($attributes), $ignore);
		$lastInsertId = $this->insert($sql, $attributes);
		return $lastInsertId;
	}

	/**
	 * 通过主键，编辑一条记录
	 * @param integer $categoryId
	 * @param array $params
	 * @return integer
	 */
	public function modifyByPk($categoryId, array $params = array())
	{
		if (($categoryId = (int) $categoryId) <= 0) {
			return false;
		}

		$attributes = array();

		if (isset($params['category_name'])) {
			$categoryName = trim($params['category_name']);
			if ($categoryName !== '') {
				$attributes['category_name'] = $categoryName;
			}
			else {
				return false;
			}
		}

		if (isset($params['category_pid'])) {
			$categoryPid = (int) $params['category_pid'];
			if ($categoryPid >= 0) {
				if ($categoryPid !== $categoryId) {
					$attributes['category_pid'] = $categoryPid;
				}
				else {
					return false;
				}
			}
			else {
				return false;
			}
		}

		if (isset($params['alias'])) {
			$attributes['alias'] = trim($params['alias']);
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
			$metaDescription = trim($params['meta_description']);
			if ($metaDescription !== '') {
				$attributes['meta_description'] = $metaDescription;
			}
			else {
				return false;
			}
		}

		if (isset($params['tpl_home'])) {
			$tplHome = trim($params['tpl_home']);
			if ($tplHome !== '') {
				$attributes['tpl_home'] = $tplHome;
			}
			else {
				return false;
			}
		}

		if (isset($params['tpl_list'])) {
			$tplList = trim($params['tpl_list']);
			if ($tplList !== '') {
				$attributes['tpl_list'] = $tplList;
			}
			else {
				return false;
			}
		}

		if (isset($params['tpl_view'])) {
			$tplView = trim($params['tpl_view']);
			if ($tplView !== '') {
				$attributes['tpl_view'] = $tplView;
			}
			else {
				return false;
			}
		}

		if (isset($params['sort'])) {
			$sort = (int) $params['sort'];
			if ($sort >= 0) {
				$attributes['sort'] = $sort;
			}
			else {
				return false;
			}
		}

		if (isset($params['description'])) {
			$attributes['description'] = $params['description'];
		}

		$rowCount = 0;

		if ($attributes === array()) {
			return $rowCount;
		}

		$tableName = $this->getTblprefix() . TableNames::getCategories();
		$sql = $this->getCommandBuilder()->createUpdate($tableName, array_keys($attributes), '`category_id` = ?');
		$attributes['category_id'] = $categoryId;
		$rowCount = $this->update($sql, $attributes);
		return $rowCount;
	}

	/**
	 * 通过主键，删除一条记录
	 * @param integer $categoryId
	 * @return integer
	 */
	public function removeByPk($categoryId)
	{
		if (($categoryId = (int) $categoryId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getCategories();
		$sql = $this->getCommandBuilder()->createDelete($tableName, '`category_id` = ?');
		$rowCount = $this->delete($sql, $categoryId);
		return $rowCount;
	}
}
