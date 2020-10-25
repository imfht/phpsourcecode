<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace builders\services;

use libsrv\DynamicService;

/**
 * Builders class file
 * 业务层：业务处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Builders.php 1 2014-05-26 19:25:19Z Code Generator $
 * @package builders.services
 * @since 1.0
 */
class Builders extends DynamicService
{
	/**
	 * @var string 表名
	 */
	protected $_tableName = 'builders';

	/**
	 * (non-PHPdoc)
	 * @see \libsrv\DynamicService::findByPk()
	 */
	public function findByPk($value)
	{
		$row = parent::findByPk($value);
		if ($row && isset($row['index_row_btns'])) {
			$row['index_row_btns'] = explode(',', $row['index_row_btns']);
		}

		return $row;
	}

	/**
	 * 获取所有的表名
	 * @return array
	 */
	public function getTblNames()
	{
		$data = array();

		$rows = $this->findColumnsByAttributes(array('tbl_name'), array('trash' => 'n'), '', 0, 0, '');
		if ($rows && is_array($rows)) {
			foreach ($rows as $row) {
				$data[] = $row['tbl_name'];
			}
		}

		return $data;
	}

	/**
	 * 通过“生成代码ID”获取“生成代码名”
	 * @param integer $builderId
	 * @return string
	 */
	public function getBuilderNameByBuilderId($builderId)
	{
		$builderName = $this->getByPk('builder_name', $builderId);
		return $builderName ? $builderName : '';
	}

	/**
	 * 获取“是否生成扩展表”
	 * @param string $tblProfile
	 * @return string
	 */
	public function getTblProfileLangByTblProfile($tblProfile)
	{
		$enum = DataBuilders::getTblProfileEnum();
		return isset($enum[$tblProfile]) ? $enum[$tblProfile] : '';
	}
}
