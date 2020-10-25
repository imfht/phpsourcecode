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
use poll\library\Lang;
use poll\library\TableNames;

/**
 * FpPolloptions class file
 * 业务层：表单数据处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: FpPolloptions.php 1 2014-12-06 21:49:14Z Code Generator $
 * @package poll.services
 * @since 1.0
 */
class FpPolloptions extends FormProcessor
{
	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_process()
	 */
	protected function _process(array $params = array())
	{
		if ($this->isInsert()) {
			if (!$this->required($params, 'option_name', 'poll_id')) {
				return false;
			}
		}

		$this->isValids($params, 'option_name', 'poll_id', 'votes', 'sort');
		return !$this->hasError();
	}

	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_cleanPreProcess()
	 */
	protected function _cleanPreProcess(array $params)
	{
		$rules = array(
			'option_name' => 'trim',
			'poll_id' => 'intval',
			'votes' => 'intval',
			'sort' => 'intval',
		);

		$ret = $this->clean($rules, $params);
		return $ret;
	}

	/**
	 * 获取“选项”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getOptionNameRule($value)
	{
		return array(
			'NotEmpty' => new validator\NotEmptyValidator($value, true, Lang::_('SRV_FILTER_POLLOPTIONS_OPTION_NAME_NOTEMPTY')),
		);
	}

	/**
	 * 获取“投票名”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getPollIdRule($value)
	{
		return array(
			'DbExists' => new validator\DbExistsValidator($value, true, Lang::_('SRV_FILTER_POLLOPTIONS_POLL_ID_EXISTS'), $this->getDbProxy(), TableNames::getPolls(), 'poll_id'),
		);
	}

	/**
	 * 获取“票数”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getVotesRule($value)
	{
		return array(
			'NonNegativeInteger' => new validator\NonNegativeIntegerValidator($value, true, Lang::_('SRV_FILTER_POLLOPTIONS_VOTES_NONNEGATIVEINTEGER')),
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
			'Integer' => new validator\IntegerValidator($value, true, Lang::_('SRV_FILTER_POLLOPTIONS_SORT_INTEGER')),
		);
	}

}
