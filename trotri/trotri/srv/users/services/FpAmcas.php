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
 * FpAmcas class file
 * 业务层：表单数据处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: FpAmcas.php 1 2014-05-29 14:36:52Z Code Generator $
 * @package users.services
 * @since 1.0
 */
class FpAmcas extends FormProcessor
{
	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_process()
	 */
	protected function _process(array $params = array())
	{
		if ($this->isInsert()) {
			if (!$this->required($params, 'amca_name', 'amca_pid')) {
				return false;
			}
		}

		if (isset($params['amca_pid'])) {
			$amcaPid = (int) $params['amca_pid'];
			if (!$this->isValid('amca_pid', $amcaPid, $this->getAmcaPidRule($amcaPid))) {
				return false;
			}
		}
		else {
			$amcaPid = $this->_object->getAmcaPidByAmcaId($this->id);
			if ($amcaPid < 0) {
				return false;
			}

			$this->amca_pid = $amcaPid;
		}

		$this->isValids($params, 'amca_name', 'prompt', 'sort', 'category');
		return !$this->hasError();
	}

	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_cleanPreProcess()
	 */
	protected function _cleanPreProcess(array $params)
	{
		if (!isset($params['category'])) {
			$params['category'] = DataAmcas::CATEGORY_MOD;
		}

		$rules = array(
			'amca_name' => 'trim',
			'amca_pid' => 'intval',
			'prompt' => 'trim',
			// 'sort' => 'intval',
			'category' => 'trim',
		);

		$ret = $this->clean($rules, $params);
		return $ret;
	}

	/**
	 * 验证“父ID”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getAmcaPidRule($value)
	{
		return array(
			'DbExists' => new validator\DbExistsValidator($value, true, Lang::_('SRV_FILTER_USER_AMCAS_AMCA_PID_EXISTS'), $this->getDbProxy(), TableNames::getAmcas(), 'amca_id')
		);
	}

	/**
	 * 获取“事件名”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getAmcaNameRule($value)
	{
		if ($this->isUpdate()) {
			if ($this->_object->getAmcaNameByAmcaId($this->id) === $value) {
				return array();
			}
		}

		return array(
			'Alpha' => new validator\AlphaValidator($value, true, Lang::_('SRV_FILTER_USER_AMCAS_AMCA_NAME_ALPHA')),
			'MinLength' => new validator\MinLengthValidator($value, 2, Lang::_('SRV_FILTER_USER_AMCAS_AMCA_NAME_MINLENGTH')),
			'MaxLength' => new validator\MaxLengthValidator($value, 16, Lang::_('SRV_FILTER_USER_AMCAS_AMCA_NAME_MAXLENGTH')),
			'DbExists2' => new validator\DbExists2Validator($value, false, Lang::_('SRV_FILTER_USER_AMCAS_AMCA_NAME_UNIQUE'), $this->getDbProxy(), TableNames::getAmcas(), 'amca_name', 'amca_pid', $this->amca_pid)
		);
	}

	/**
	 * 获取“提示”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getPromptRule($value)
	{
		return array(
			'MinLength' => new validator\MinLengthValidator($value, 2, Lang::_('SRV_FILTER_USER_AMCAS_PROMPT_MINLENGTH')),
			'MaxLength' => new validator\MaxLengthValidator($value, 50, Lang::_('SRV_FILTER_USER_AMCAS_PROMPT_MAXLENGTH')),
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
			'Integer' => new validator\NumericValidator($value, true, Lang::_('SRV_FILTER_USER_AMCAS_SORT_INTEGER')),
		);
	}

	/**
	 * 获取“类型”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getCategoryRule($value)
	{
		return array(
			'Equal' => new validator\EqualValidator($value, DataAmcas::CATEGORY_MOD, Lang::_('SRV_FILTER_USER_AMCAS_CATEGORY_EQUAL')),
		);
	}

}
