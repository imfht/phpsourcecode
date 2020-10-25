<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace member\services;

use libsrv\FormProcessor;
use tfc\saf\Log;
use tfc\validator;
use libapp\ErrorNo;
use member\library\Lang;
use member\library\TableNames;

/**
 * FpRanks class file
 * 业务层：表单数据处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: FpRanks.php 1 2014-11-26 14:16:14Z Code Generator $
 * @package member.services
 * @since 1.0
 */
class FpRanks extends FormProcessor
{
	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_process()
	 */
	protected function _process(array $params = array())
	{
		if ($this->isInsert()) {
			if (!$this->required($params, 'rank_name', 'experience', 'sort')) {
				return false;
			}
		}

		$this->isValids($params, 'rank_name', 'experience', 'sort', 'description');
		return !$this->hasError();
	}

	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_cleanPreProcess()
	 */
	protected function _cleanPreProcess(array $params)
	{
		if ($this->isUpdate()) {
			if (isset($params['rank_name'])) {
				$row = $this->_object->findByPk($this->id);
				if (!$row || !is_array($row) || !isset($row['rank_name'])) {
					Log::warning(sprintf(
						'FpRanks is unable to find the result by id "%d"', $this->id
					), ErrorNo::ERROR_DB_SELECT,  __METHOD__);

					return false;
				}

				$rankName = trim($params['rank_name']);
				if ($rankName === $row['rank_name']) {
					unset($params['rank_name']);
				}
			}
		}

		$rules = array(
			'rank_name' => 'trim',
			'experience' => 'intval',
			'sort' => 'intval',
			'description' => 'trim',
		);

		$ret = $this->clean($rules, $params);
		return $ret;
	}

	/**
	 * 获取“成长度名”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getRankNameRule($value)
	{
		return array(
			'MinLength' => new validator\MinLengthValidator($value, 2, Lang::_('SRV_FILTER_MEMBER_RANKS_RANK_NAME_MINLENGTH')),
			'MaxLength' => new validator\MaxLengthValidator($value, 50, Lang::_('SRV_FILTER_MEMBER_RANKS_RANK_NAME_MAXLENGTH')),
			'DbExists' => new validator\DbExistsValidator($value, false, Lang::_('SRV_FILTER_MEMBER_RANKS_RANK_NAME_UNIQUE'), $this->getDbProxy(), TableNames::getRanks(), 'rank_name')
		);
	}

	/**
	 * 获取“需要成长值”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getExperienceRule($value)
	{
		return array(
			'NonNegativeInteger' => new validator\NonNegativeIntegerValidator($value, true, Lang::_('SRV_FILTER_MEMBER_RANKS_EXPERIENCE_NONNEGATIVEINTEGER')),
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
			'Integer' => new validator\IntegerValidator($value, true, Lang::_('SRV_FILTER_MEMBER_RANKS_SORT_INTEGER')),
		);
	}

}
