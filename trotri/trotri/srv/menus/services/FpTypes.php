<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace menus\services;

use libsrv\FormProcessor;
use tfc\saf\Log;
use tfc\validator;
use libapp\ErrorNo;
use menus\library\Lang;
use menus\library\TableNames;

/**
 * FpTypes class file
 * 业务层：表单数据处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: FpTypes.php 1 2014-10-22 10:08:47Z Code Generator $
 * @package menus.services
 * @since 1.0
 */
class FpTypes extends FormProcessor
{
	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_process()
	 */
	protected function _process(array $params = array())
	{
		if ($this->isInsert()) {
			if (!$this->required($params, 'type_key', 'type_name')) {
				return false;
			}
		}

		$this->isValids($params, 'type_key', 'type_name', 'description');
		return !$this->hasError();
	}

	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_cleanPreProcess()
	 */
	protected function _cleanPreProcess(array $params)
	{
		if ($this->isUpdate()) {
			if (isset($params['type_key'])) {
				$row = $this->_object->findByPk($this->id);
				if (!$row || !is_array($row) || !isset($row['type_key'])) {
					Log::warning(sprintf(
						'FpTypes is unable to find the result by id "%d"', $this->id
					), ErrorNo::ERROR_DB_SELECT,  __METHOD__);

					return false;
				}

				$typeKey = trim($params['type_key']);
				if ($typeKey === $row['type_key']) {
					unset($params['type_key']);
				}
			}
		}

		$rules = array(
			'type_key' => 'trim',
			'type_name' => 'trim',
		);

		$ret = $this->clean($rules, $params);
		return $ret;
	}

	/**
	 * 获取“类型Key”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getTypeKeyRule($value)
	{
		return array(
			'AlphaNum' => new validator\AlphaNumValidator($value, true, Lang::_('SRV_FILTER_MENU_TYPES_TYPE_KEY_ALPHANUM')),
			'MinLength' => new validator\MinLengthValidator($value, 2, Lang::_('SRV_FILTER_MENU_TYPES_TYPE_KEY_MINLENGTH')),
			'MaxLength' => new validator\MaxLengthValidator($value, 20, Lang::_('SRV_FILTER_MENU_TYPES_TYPE_KEY_MAXLENGTH')),
			'DbExists' => new validator\DbExistsValidator($value, false, Lang::_('SRV_FILTER_MENU_TYPES_TYPE_KEY_UNIQUE'), $this->getDbProxy(), TableNames::getTypes(), 'type_key'),
		);
	}

	/**
	 * 获取“类型名”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getTypeNameRule($value)
	{
		return array(
			'MinLength' => new validator\MinLengthValidator($value, 2, Lang::_('SRV_FILTER_MENU_TYPES_TYPE_NAME_MINLENGTH')),
			'MaxLength' => new validator\MaxLengthValidator($value, 50, Lang::_('SRV_FILTER_MENU_TYPES_TYPE_NAME_MAXLENGTH')),
		);
	}

}
