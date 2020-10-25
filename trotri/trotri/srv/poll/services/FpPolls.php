<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace poll\services;

use libsrv\FormProcessor;
use tfc\validator;
use tfc\saf\Log;
use libapp\ErrorNo;
use poll\library\Lang;
use poll\library\TableNames;
use member\services\DataMembers;

/**
 * FpPolls class file
 * 业务层：表单数据处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: FpPolls.php 1 2014-12-05 17:47:10Z Code Generator $
 * @package poll.services
 * @since 1.0
 */
class FpPolls extends FormProcessor
{
	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_process()
	 */
	protected function _process(array $params = array())
	{
		if ($this->isInsert()) {
			if (!$this->required($params, 'poll_name', 'poll_key', 'allow_unregistered', 'join_type')) {
				return false;
			}
		}

		$this->isValids($params,
			'poll_name', 'poll_key', 'allow_unregistered', 'm_rank_ids', 'join_type', 'interval',
			'is_published', 'dt_publish_up', 'dt_publish_down',
			'is_visible', 'is_multiple', 'max_choices', 'description', 'ext_info', 'dt_created');
		return !$this->hasError();
	}

	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_cleanPreProcess()
	 */
	protected function _cleanPreProcess(array $params)
	{
		if ($this->isInsert()) {
			if (!isset($params['m_rank_ids'])) {
				$params['m_rank_ids'] = array();
			}

			if (!is_array($params['m_rank_ids'])) {
				$params['m_rank_ids'] = (array) $params['m_rank_ids'];
			}
		}
		else {
			if (isset($params['m_rank_ids']) && !is_array($params['m_rank_ids'])) {
				$params['m_rank_ids'] = (array) $params['m_rank_ids'];
			}

			if (isset($params['poll_key'])) {
				$row = $this->_object->findByPk($this->id);
				if (!$row || !is_array($row) || !isset($row['poll_key'])) {
					Log::warning(sprintf(
						'FpPolls is unable to find the result by id "%d"', $this->id
					), ErrorNo::ERROR_DB_SELECT,  __METHOD__);

					return false;
				}

				$pollKey = trim($params['poll_key']);
				if ($pollKey === $row['poll_key']) {
					unset($params['poll_key']);
				}
			}
		}

		$rules = array(
			'poll_name' => 'trim',
			'poll_key' => 'trim',
			'allow_unregistered' => 'trim',
			'join_type' => 'trim',
			'interval' => 'intval',
			'is_published' => 'trim',
			'dt_publish_up' => 'trim',
			'dt_publish_down' => 'trim',
			'is_visible' => 'trim',
			'is_multiple' => 'trim',
			'max_choices' => 'intval',
			'description' => 'trim',
			'ext_info' => 'trim',
			'dt_created' => 'trim',
		);

		$ret = $this->clean($rules, $params);
		return $ret;
	}

	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_cleanPostProcess()
	 */
	protected function _cleanPostProcess()
	{
		if (isset($this->m_rank_ids)) {
			$this->m_rank_ids = implode(',', $this->m_rank_ids);
		}

		return true;
	}

	/**
	 * 获取“投票名”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getPollNameRule($value)
	{
		return array(
			'MinLength' => new validator\MinLengthValidator($value, 2, Lang::_('SRV_FILTER_POLLS_POLL_NAME_MINLENGTH')),
			'MaxLength' => new validator\MaxLengthValidator($value, 50, Lang::_('SRV_FILTER_POLLS_POLL_NAME_MAXLENGTH')),
		);
	}

	/**
	 * 获取“投票Key”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getPollKeyRule($value)
	{
		return array(
			'AlphaNum' => new validator\AlphaNumValidator($value, true, Lang::_('SRV_FILTER_POLLS_POLL_KEY_ALPHANUM')),
			'MinLength' => new validator\MinLengthValidator($value, 2, Lang::_('SRV_FILTER_POLLS_POLL_KEY_MINLENGTH')),
			'MaxLength' => new validator\MaxLengthValidator($value, 20, Lang::_('SRV_FILTER_POLLS_POLL_KEY_MAXLENGTH')),	
			'DbExists' => new validator\DbExistsValidator($value, false, Lang::_('SRV_FILTER_POLLS_POLL_KEY_UNIQUE'), $this->getDbProxy(), TableNames::getPolls(), 'poll_key'),
		);
	}

	/**
	 * 获取“是否允许非会员参加”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getAllowUnregisteredRule($value)
	{
		$enum = DataPolls::getAllowUnregisteredEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_POLLS_ALLOW_UNREGISTERED_INARRAY'), implode(', ', $enum))),
		);
	}

	/**
	 * 获取“允许参与会员成长度”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getMRankIdsRule($value)
	{
		$enum = DataMembers::getRanksEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), Lang::_('SRV_FILTER_POLLS_M_RANK_IDS_INARRAY')),
		);
	}

	/**
	 * 获取“参与方式”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getJoinTypeRule($value)
	{
		$enum = DataPolls::getJoinTypeEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_POLLS_JOIN_TYPE_INARRAY'), implode(', ', $enum))),
		);
	}

	/**
	 * 获取“间隔几秒可再次参与”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getIntervalRule($value)
	{
		return array(
			'NonNegativeInteger' => new validator\NonNegativeIntegerValidator($value, true, Lang::_('SRV_FILTER_POLLS_INTERVAL_NONNEGATIVEINTEGER')),
		);
	}

	/**
	 * 获取“是否开放”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getIsPublishedRule($value)
	{
		$enum = DataPolls::getIsPublishedEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_POLLS_IS_PUBLISHED_INARRAY'), implode(', ', $enum))),
		);
	}

	/**
	 * 获取“开始时间”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getDtPublishUpRule($value)
	{
		if ($value === '') {
			return array();
		}

		return array(
			'DateTime' => new validator\DateTimeValidator($value, true, Lang::_('SRV_FILTER_POLLS_DT_PUBLISH_UP_DATETIME')),
		);
	}

	/**
	 * 获取“结束时间”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getDtPublishDownRule($value)
	{
		if ($value === '' || $value === '0000-00-00 00:00:00') {
			return array();
		}

		return array(
			'DateTime' => new validator\DateTimeValidator($value, true, Lang::_('SRV_FILTER_POLLS_DT_PUBLISH_DOWN_DATETIME')),
		);
	}

	/**
	 * 获取“是否展示结果”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getIsVisibleRule($value)
	{
		$enum = DataPolls::getIsVisibleEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_POLLS_IS_VISIBLE_INARRAY'), implode(', ', $enum))),
		);
	}

	/**
	 * 获取“是否多选”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getIsMultipleRule($value)
	{
		$enum = DataPolls::getIsMultipleEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_POLLS_IS_MULTIPLE_INARRAY'), implode(', ', $enum))),
		);
	}

	/**
	 * 获取“最多可选数量”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getMaxChoicesRule($value)
	{
		return array(
			'NonNegativeInteger' => new validator\NonNegativeIntegerValidator($value, true, Lang::_('SRV_FILTER_POLLS_MAX_CHOICES_NONNEGATIVEINTEGER')),
		);
	}

}
