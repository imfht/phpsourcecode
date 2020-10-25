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
 * Validators class file
 * 业务层：业务处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Validators.php 1 2014-05-28 11:06:31Z Code Generator $
 * @package builders.services
 * @since 1.0
 */
class Validators extends DynamicService
{
	/**
	 * @var string 表名
	 */
	protected $_tableName = 'builder_field_validators';

	/**
	 * 获取验证时对比值类型
	 * @param string $optionCategory
	 * @return string
	 */
	public function getOptionCategoryLangByOptionCategory($optionCategory)
	{
		$enum = DataValidators::getOptionCategoryEnum();
		return isset($enum[$optionCategory]) ? $enum[$optionCategory] : '';
	}

	/**
	 * 获取验证时对比值类型
	 * @param string $when
	 * @return string
	 */
	public function getWhenLangByWhen($when)
	{
		$enum = DataValidators::getWhenEnum();
		return isset($enum[$when]) ? $enum[$when] : '';
	}
}
