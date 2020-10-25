<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace users\db;

use tdo\AbstractDb;
use users\library\Constant;
use users\library\TableNames;

/**
 * Amcas class file
 * 业务层：数据库操作类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Amcas.php 1 2014-05-29 14:36:52Z Code Generator $
 * @package users.db
 * @since 1.0
 */
class Amcas extends AbstractDb
{
	/**
	 * @var string 数据库配置名
	 */
	protected $_clusterName = Constant::DB_CLUSTER;

	/**
	 * 获取所有的应用提示
	 * @return array
	 */
	public function findAppPrompts()
	{
		$tableName = $this->getTblprefix() . TableNames::getAmcas();
		$sql = 'SELECT `amca_id`, `prompt` FROM `' . $tableName . '` WHERE `category` = ? ORDER BY `sort`';
		return $this->fetchPairs($sql, 'app');
	}

	/**
	 * 通过父ID，获取所有的子事件
	 * @param integer $amcaPid
	 * @return array
	 */
	public function findAllByPid($amcaPid)
	{
		if (($amcaPid = (int) $amcaPid) < 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getAmcas();
		$sql = 'SELECT `amca_id`, `amca_pid`, `amca_name`, `prompt`, `sort`, `category` FROM `' . $tableName . '` WHERE `amca_pid` = ? ORDER BY `sort`';
		return $this->fetchAll($sql, $amcaPid);
	}

	/**
	 * 通过主键，查询一条记录
	 * @param integer $amcaId
	 * @return array
	 */
	public function findByPk($amcaId)
	{
		if (($amcaId = (int) $amcaId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getAmcas();
		$sql = 'SELECT `amca_id`, `amca_name`, `amca_pid`, `prompt`, `sort`, `category` FROM `' . $tableName . '` WHERE `amca_id` = ?';
		return $this->fetchAssoc($sql, $amcaId);
	}

	/**
	 * 新增一条记录
	 * @param array $params
	 * @param boolean $ignore
	 * @return integer
	 */
	public function create(array $params = array(), $ignore = false)
	{
		$amcaName = isset($params['amca_name']) ? trim($params['amca_name']) : '';
		$amcaPid = isset($params['amca_pid']) ? (int) $params['amca_pid'] : 0;
		$prompt = isset($params['prompt']) ? trim($params['prompt']) : '';
		$sort = isset($params['sort']) ? (int) $params['sort'] : 0;
		$category = isset($params['category']) ? trim($params['category']) : '';

		if ($amcaName === '' || $amcaPid < 0 || $category === '') {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getAmcas();
		$attributes = array(
			'amca_name' => $amcaName,
			'amca_pid' => $amcaPid,
			'prompt' => $prompt,
			'sort' => $sort,
			'category' => $category,
		);

		$sql = $this->getCommandBuilder()->createInsert($tableName, array_keys($attributes), $ignore);
		$lastInsertId = $this->insert($sql, $attributes);
		return $lastInsertId;
	}

	/**
	 * 通过主键，编辑一条记录
	 * @param integer $amcaId
	 * @param array $params
	 * @return integer
	 */
	public function modifyByPk($amcaId, array $params = array())
	{
		if (($amcaId = (int) $amcaId) <= 0) {
			return false;
		}

		$attributes = array();

		if (isset($params['amca_name'])) {
			$amcaName = trim($params['amca_name']);
			if ($amcaName !== '') {
				$attributes['amca_name'] = $amcaName;
			}
			else {
				return false;
			}
		}

		if (isset($params['amca_pid'])) {
			$amcaPid = (int) $params['amca_pid'];
			if ($amcaPid >= 0) {
				$attributes['amca_pid'] = $amcaPid;
			}
			else {
				return false;
			}
		}

		if (isset($params['prompt'])) {
			$prompt = trim($params['prompt']);
			if ($prompt !== '') {
				$attributes['prompt'] = $prompt;
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

		if (isset($params['category'])) {
			$category = trim($params['category']);
			if ($category !== '') {
				$attributes['category'] = $category;
			}
			else {
				return false;
			}
		}

		$rowCount = 0;

		if ($attributes === array()) {
			return $rowCount;
		}

		$tableName = $this->getTblprefix() . TableNames::getAmcas();
		$sql = $this->getCommandBuilder()->createUpdate($tableName, array_keys($attributes), '`amca_id` = ?');
		$attributes['amca_id'] = $amcaId;
		$rowCount = $this->update($sql, $attributes);
		return $rowCount;
	}

	/**
	 * 通过主键，删除一条记录
	 * @param integer $amcaId
	 * @return integer
	 */
	public function removeByPk($amcaId)
	{
		if (($amcaId = (int) $amcaId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getAmcas();
		$sql = $this->getCommandBuilder()->createDelete($tableName, '`amca_id` = ? OR `amca_pid` = ?');
		$rowCount = $this->delete($sql, array($amcaId, $amcaId));
		return $rowCount;
	}
}
