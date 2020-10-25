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
use posts\library\Constant;

/**
 * Modules class file
 * 业务层：业务处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Modules.php 1 2014-10-12 21:14:11Z Code Generator $
 * @package posts.services
 * @since 1.0
 */
class Modules extends AbstractService
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
	public function findAll(array $params = array(), $order = '', $limit = 0, $offset = 0, $option = '')
	{
		$limit = min(max((int) $limit, 1), Constant::FIND_MAX_LIMIT);
		$offset = max((int) $offset, 0);

		$rows = $this->getDb()->findAll($params, $order, $limit, $offset, $option);
		return $rows;
	}

	/**
	 * 获取所有的模型名称
	 * @return array
	 */
	public function getModuleNames()
	{
		$rows = $this->getDb()->getModuleNames();
		return $rows;
	}

	/**
	 * 获取所有的文档扩展字段
	 * @return array
	 */
	public function getFields()
	{
		$data = array();

		$rows = $this->getDb()->getFields();
		if ($rows && is_array($rows)) {
			foreach ($rows as $id => $fields) {
				if (($id = (int) $id) <= 0) {
					continue;
				}

				$data[$id] = $this->cleanFields($fields, false);
			}
		}

		return $data;
	}

	/**
	 * 通过主键，查询一条记录
	 * @param integer $moduleId
	 * @return array
	 */
	public function findByPk($moduleId)
	{
		$row = $this->getDb()->findByPk($moduleId);
		if ($row && is_array($row) && isset($row['fields'])) {
			$row['fields'] = $this->cleanFields($row['fields'], false);
		}

		return $row;
	}

	/**
	 * 清理“文档扩展字段”，除去不规则的行，并转成数组
	 * @param string $lines
	 * @param boolean $returnString
	 * @return array|string
	 */
	public function cleanFields($lines, $returnString = true)
	{
		$data = array();

		$lines = explode("\n", $lines);
		foreach ($lines as $line) {
			$temp = explode('|', $line);
			if (count($temp) < 2) {
				continue;
			}

			if (($name = trim($temp[0])) === '') {
				continue;
			}

			if (($label = trim($temp[1])) === '') {
				continue;
			}

			if (!preg_match('/^_\w+$/i', $name)) {
				continue;
			}

			$data[$name] = array(
				'label' => $label,
				'hint' => (isset($temp[2]) ? trim($temp[2]) : '')
			);
		}

		if ($returnString) {
			$lines = '';
			foreach ($data as $name => $row) {
				$lines .= $name . '|' . $row['label'] . (($row['hint'] !== '') ? ('|' . $row['hint']) : '') . "\n";
			}

			$data = rtrim($lines);
		}

		return $data;
	}

	/**
	 * 通过“主键ID”，获取“模型名称”
	 * @param integer $moduleId
	 * @return string
	 */
	public function getModuleNameByModuleId($moduleId)
	{
		$value = $this->getByPk('module_name', $moduleId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“文档扩展字段”
	 * @param integer $moduleId
	 * @return array
	 */
	public function getFieldsByModuleId($moduleId)
	{
		$value = $this->getByPk('fields', $moduleId);
		return $value ? $value : array();
	}

	/**
	 * 通过“主键ID”，获取“是否禁用”
	 * @param integer $moduleId
	 * @return string
	 */
	public function getForbiddenByModuleId($moduleId)
	{
		$value = $this->getByPk('forbidden', $moduleId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“描述”
	 * @param integer $moduleId
	 * @return string
	 */
	public function getDescriptionByModuleId($moduleId)
	{
		$value = $this->getByPk('description', $moduleId);
		return $value ? $value : '';
	}

}
