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

use libsrv\FormProcessor;
use tfc\validator;
use users\library\Lang;
use users\library\TableNames;

/**
 * FpGroups class file
 * 业务层：表单数据处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: FpGroups.php 1 2014-05-30 11:00:05Z Code Generator $
 * @package users.services
 * @since 1.0
 */
class FpGroups extends FormProcessor
{
	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_process()
	 */
	protected function _process(array $params = array())
	{
		if ($this->isInsert()) {
			if (!$this->required($params, 'group_name', 'group_pid', 'sort', 'description')) {
				return false;
			}
		}

		$this->isValids($params, 'group_name', 'group_pid', 'sort', 'description');
		return !$this->hasError();
	}

	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_cleanPreProcess()
	 */
	protected function _cleanPreProcess(array $params)
	{
		if (isset($params['permission'])) {
			unset($params['permission']);
		}

		$rules = array(
			'group_name' => 'trim',
			'group_pid' => 'intval',
			// 'sort' => 'intval',
		);

		$ret = $this->clean($rules, $params);
		return $ret;
	}

	/**
	 * 获取“组名”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getGroupNameRule($value)
	{
		if ($this->isUpdate()) {
			if ($this->_object->getGroupNameByGroupId($this->id) === $value) {
				return array();
			}
		}

		return array(
			'MinLength' => new validator\MinLengthValidator($value, 2, Lang::_('SRV_FILTER_USER_GROUPS_GROUP_NAME_MINLENGTH')),
			'MaxLength' => new validator\MaxLengthValidator($value, 50, Lang::_('SRV_FILTER_USER_GROUPS_GROUP_NAME_MAXLENGTH')),
			'DbExists' => new validator\DbExistsValidator($value, false, Lang::_('SRV_FILTER_USER_GROUPS_GROUP_NAME_UNIQUE'), $this->getDbProxy(), TableNames::getGroups(), 'group_name')
		);
	}

	/**
	 * 获取“父ID”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getGroupPidRule($value)
	{
		return array(
			'DbExists' => new validator\DbExistsValidator($value, true, Lang::_('SRV_FILTER_USER_GROUPS_GROUP_PID_EXISTS'), $this->getDbProxy(), TableNames::getGroups(), 'group_id'),
		);
	}

	/**
	 * 获取“排序”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getSortRule($value)
	{
		return array(
			'Integer' => new validator\NumericValidator($value, true, Lang::_('SRV_FILTER_USER_GROUPS_SORT_INTEGER')),
		);
	}

}
