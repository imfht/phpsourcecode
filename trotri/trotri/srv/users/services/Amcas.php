<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace users\services;

use libsrv\AbstractService;

/**
 * Amcas class file
 * 业务层：业务处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Amcas.php 1 2014-05-29 14:36:52Z Code Generator $
 * @package users.services
 * @since 1.0
 */
class Amcas extends AbstractService
{
	/**
	 * 获取所有的应用提示
	 * @return array
	 */
	public function findAppPrompts()
	{
		$rows = $this->getDb()->findAppPrompts();
		return $rows;
	}

	/**
	 * 通过父ID，获取所有的子事件
	 * @param integer $amcaPid
	 * @return array
	 */
	public function findAllByPid($amcaPid)
	{
		$rows = $this->getDb()->findAllByPid($amcaPid);
		return $rows;
	}

	/**
	 * 获取模块和控制器类型数据
	 * @param integer $appId
	 * @param string $padStr
	 * @return array
	 */
	public function findModCtrls($appId, $padStr = ' ---- ')
	{
		$data = array();

		$mods = $this->findAllByPid($appId);
		if ($mods && is_array($mods)) {
			foreach ($mods as $modRows) {
				$data[] = $modRows;

				$ctrls = $this->findAllByPid($modRows['amca_id']);
				if ($ctrls && is_array($ctrls)) {
					foreach ($ctrls as $ctrlRows) {
						$ctrlRows['amca_name'] = $padStr . $ctrlRows['amca_name'];
						$data[] = $ctrlRows;
					}
				}
			}
		}

		return $data;
	}

	/**
	 * 递归模式获取所有数据
	 * @return array
	 */
	public function findAllByRecur()
	{
		$data = array();

		$apps = $this->findAllByPid(0);
		if ($apps && is_array($apps)) {
			foreach ($apps as $appRow) {
				$appRow['rows'] = array();

				$mods = $this->findAllByPid($appRow['amca_id']);
				if ($mods && is_array($mods)) {
					foreach ($mods as $modRow) {
						$modRow['rows'] = array();

						$ctrls = $this->findAllByPid($modRow['amca_id']);
						if ($ctrls && is_array($ctrls)) {
							foreach ($ctrls as $ctrlRow) {
								$ctrlRow['rows'] = array();

								$acts = $this->findAllByPid($ctrlRow['amca_id']);
								if ($acts && is_array($acts)) {
									foreach ($acts as $actRow) {
										$ctrlRow['rows'][$actRow['amca_name']] = $actRow;
									}
								}

								$modRow['rows'][$ctrlRow['amca_name']] = $ctrlRow;
							}
						}

						$appRow['rows'][$modRow['amca_name']] = $modRow;
					}
				}

				$data[$appRow['amca_name']] = $appRow;
			}
		}

		return $data;
	}

	/**
	 * 通过“主键ID”，获取“事件名”
	 * @param integer $amcaId
	 * @return string
	 */
	public function getAmcaNameByAmcaId($amcaId)
	{
		$value = $this->getByPk('amca_name', $amcaId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“父ID”
	 * @param integer $amcaId
	 * @return integer
	 */
	public function getAmcaPidByAmcaId($amcaId)
	{
		$value = $this->getByPk('amca_pid', $amcaId);
		return ($value !== false) ? (int) $value : -1;
	}

	/**
	 * 通过“主键ID”，获取“提示”
	 * @param integer $amcaId
	 * @return string
	 */
	public function getPromptByAmcaId($amcaId)
	{
		$value = $this->getByPk('prompt', $amcaId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“排序”
	 * @param integer $amcaId
	 * @return integer
	 */
	public function getSortByAmcaId($amcaId)
	{
		$value = $this->getByPk('sort', $amcaId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“类型”
	 * @param integer $amcaId
	 * @return string
	 */
	public function getCategoryByAmcaId($amcaId)
	{
		$value = $this->getByPk('category', $amcaId);
		return $value ? $value : '';
	}

	/**
	 * 通过“类型”，获取“类型名”
	 * @param string $category
	 * @return string
	 */
	public function getCategoryLangByCategory($category)
	{
		$enum = DataAmcas::getCategoryEnum();
		return isset($enum[$category]) ? $enum[$category] : '';
	}

	/**
	 * 通过“主键ID”，获取“类型名”
	 * @param integer $amcaId
	 * @return string
	 */
	public function getCategoryLangByAmcaId($amcaId)
	{
		$category = $this->getCategoryByAmcaId($amcaId);
		return $this->getCategoryLangByCategory($category);
	}

	/**
	 * 通过“主键ID”，获取“父事件名”
	 * @param integer $amcaId
	 * @return string
	 */
	public function getAmcaPnameByAmcaId($amcaId)
	{
		$amcaPid = $this->getAmcaPidByAmcaId($amcaId);
		if ($amcaPid >= 0) {
			return $this->getAmcaNameByAmcaId($amcaPid);
		}

		return '';
	}

	/**
	 * 验证是否是应用类型
	 * @param string $category
	 * @return boolean
	 */
	public function isApp($category)
	{
		return ($category === DataAmcas::CATEGORY_APP);
	}

	/**
	 * 验证是否是模块类型
	 * @param string $category
	 * @return boolean
	 */
	public function isMod($category)
	{
		return ($category === DataAmcas::CATEGORY_MOD);
	}

	/**
	 * 验证是否是控制器类型
	 * @param string $category
	 * @return boolean
	 */
	public function isCtrl($category)
	{
		return ($category === DataAmcas::CATEGORY_CTRL);
	}

	/**
	 * 验证是否是行动类型
	 * @param string $category
	 * @return boolean
	 */
	public function isAct($category)
	{
		return ($category === DataAmcas::CATEGORY_ACT);
	}
}
