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
use tfc\validator;
use menus\library\Lang;
use menus\library\TableNames;

/**
 * FpMenus class file
 * 业务层：表单数据处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: FpMenus.php 1 2014-10-22 14:27:46Z Code Generator $
 * @package menus.services
 * @since 1.0
 */
class FpMenus extends FormProcessor
{
	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_process()
	 */
	protected function _process(array $params = array())
	{
		if ($this->isInsert()) {
			if (!$this->required($params, 'menu_pid', 'menu_name', 'menu_url', 'type_key', 'sort')) {
				return false;
			}
		}

		$this->isValids($params,
			'menu_name', 'menu_pid', 'menu_url', 'type_key', 'picture', 'alias', 'description', 'allow_unregistered', 'is_hide', 'sort',
			'attr_target', 'attr_title', 'attr_rel', 'attr_class', 'attr_style', 'dt_created', 'dt_last_modified');
		return !$this->hasError();
	}

	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_cleanPreProcess()
	 */
	protected function _cleanPreProcess(array $params)
	{
		if ($this->isInsert()) {
			$params['dt_created'] = $params['dt_last_modified'] = date('Y-m-d H:i:s');
		}
		else {
			$params['dt_last_modified'] = date('Y-m-d H:i:s');
			if (isset($params['dt_created'])) { unset($params['dt_created']); }
		}

		$rules = array(
			'menu_pid' => 'intval',
			'menu_name' => 'trim',
			'menu_url' => 'trim',
			'type_key' => 'trim',
			'picture' => 'trim',
			'alias' => 'trim',
			'allow_unregistered' => 'trim',
			'is_hide' => 'trim',
			'sort' => 'intval',
		);

		$ret = $this->clean($rules, $params);
		return $ret;
	}

	/**
	 * 获取“父菜单ID”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getMenuPidRule($value)
	{
		if ($value === 0) {
			return array();
		}

		return array(
			'NotEqual' => new validator\NotEqualValidator($value, $this->id, Lang::_('SRV_FILTER_MENUS_MENU_PID_NOTEQUAL')),
			'DbExists' => new validator\DbExistsValidator($value, true, Lang::_('SRV_FILTER_MENUS_MENU_PID_EXISTS'), $this->getDbProxy(), TableNames::getMenus(), 'menu_id'),
		);
	}

	/**
	 * 获取“菜单名”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getMenuNameRule($value)
	{
		return array(
			'MinLength' => new validator\MinLengthValidator($value, 1, Lang::_('SRV_FILTER_MENUS_MENU_NAME_MINLENGTH')),
			'MaxLength' => new validator\MaxLengthValidator($value, 100, Lang::_('SRV_FILTER_MENUS_MENU_NAME_MAXLENGTH')),
		);
	}

	/**
	 * 获取“菜单链接”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getMenuUrlRule($value)
	{
		return array(
			'NotEmpty' => new validator\NotEmptyValidator($value, true, Lang::_('SRV_FILTER_MENUS_MENU_URL_NOTEMPTY')),
		);
	}

	/**
	 * 获取“类型Key”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getTypeKeyRule($value)
	{
		return array(
			'DbExists' => new validator\DbExistsValidator($value, true, Lang::_('SRV_FILTER_MENUS_TYPE_KEY_EXISTS'), $this->getDbProxy(), TableNames::getTypes(), 'type_key'),
		);
	}

	/**
	 * 获取“别名”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getAliasRule($value)
	{
		return array(
			'MaxLength' => new validator\MaxLengthValidator($value, 100, Lang::_('SRV_FILTER_MENUS_ALIAS_MAXLENGTH')),
		);
	}

	/**
	 * 获取“允许非会员访问”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getAllowUnregisteredRule($value)
	{
		$enum = DataMenus::getAllowUnregisteredEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_MENUS_ALLOW_UNREGISTERED_INARRAY'), implode(', ', $enum))),
		);
	}

	/**
	 * 获取“是否隐藏”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getIsHideRule($value)
	{
		$enum = DataMenus::getIsHideEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_MENUS_IS_HIDE_INARRAY'), implode(', ', $enum))),
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
			'Integer' => new validator\IntegerValidator($value, true, Lang::_('SRV_FILTER_MENUS_SORT_INTEGER')),
		);
	}

}
