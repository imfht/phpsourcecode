<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace advert\services;

use libsrv\FormProcessor;
use tfc\validator;
use advert\library\Lang;
use advert\library\TableNames;

/**
 * FpAdverts class file
 * 业务层：表单数据处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: FpAdverts.php 1 2014-10-26 12:07:53Z Code Generator $
 * @package advert.services
 * @since 1.0
 */
class FpAdverts extends FormProcessor
{
	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_process()
	 */
	protected function _process(array $params = array())
	{
		if ($this->isInsert()) {
			if (!$this->required($params, 'advert_name', 'type_key', 'sort', 'show_type', 'show_code')) {
				return false;
			}
		}

		$this->isValids($params,
			'advert_name', 'type_key', 'description', 'is_published', 'dt_publish_up', 'dt_publish_down', 'sort',
			'show_type', 'show_code', 'title', 'advert_url', 'advert_src', 'advert_src2',
			'attr_alt', 'attr_width', 'attr_height', 'attr_fontsize', 'attr_target', 'dt_created');
		return !$this->hasError();
	}

	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_cleanPreProcess()
	 */
	protected function _cleanPreProcess(array $params)
	{
		if ($this->isInsert()) {
			$params['dt_created'] = date('Y-m-d H:i:s');
		}
		else {
			if (isset($params['dt_created'])) { unset($params['dt_created']); }
		}

		$rules = array(
			'advert_name' => 'trim',
			'type_key' => 'trim',
			'is_published' => 'trim',
			'dt_publish_up' => 'trim',
			'dt_publish_down' => 'trim',
			'sort' => 'intval',
			'show_type' => 'trim',
			'title' => 'trim',
			'advert_url' => 'trim',
			'advert_src' => 'trim',
			'advert_src2' => 'trim',
			'attr_alt' => 'trim',
			'attr_width' => 'intval',
			'attr_height' => 'intval',
			'attr_fontsize' => 'trim',
			'attr_target' => 'trim',
		);

		$ret = $this->clean($rules, $params);
		return $ret;
	}

	/**
	 * 获取“广告名”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getAdvertNameRule($value)
	{
		return array(
			'MinLength' => new validator\MinLengthValidator($value, 1, Lang::_('SRV_FILTER_ADVERTS_ADVERT_NAME_MINLENGTH')),
			'MaxLength' => new validator\MaxLengthValidator($value, 100, Lang::_('SRV_FILTER_ADVERTS_ADVERT_NAME_MAXLENGTH')),
		);
	}

	/**
	 * 获取“位置Key”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getTypeKeyRule($value)
	{
		return array(
			'DbExists' => new validator\DbExistsValidator($value, true, Lang::_('SRV_FILTER_ADVERTS_TYPE_KEY_EXISTS'), $this->getDbProxy(), TableNames::getTypes(), 'type_key'),
		);
	}

	/**
	 * 获取“是否发表”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getIsPublishedRule($value)
	{
		$enum = DataAdverts::getIsPublishedEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_ADVERTS_IS_PUBLISHED_INARRAY'), implode(', ', $enum))),
		);
	}

	/**
	 * 获取“开始发表时间”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getDtPublishUpRule($value)
	{
		if ($value === '') {
			return array();
		}

		return array(
			'DateTime' => new validator\DateTimeValidator($value, true, Lang::_('SRV_FILTER_ADVERTS_DT_PUBLISH_UP_DATETIME')),
		);
	}

	/**
	 * 获取“结束发表时间”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getDtPublishDownRule($value)
	{
		if ($value === '' || $value === '0000-00-00 00:00:00') {
			return array();
		}

		return array(
			'DateTime' => new validator\DateTimeValidator($value, true, Lang::_('SRV_FILTER_ADVERTS_DT_PUBLISH_DOWN_DATETIME')),
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
			'Integer' => new validator\IntegerValidator($value, true, Lang::_('SRV_FILTER_ADVERTS_SORT_INTEGER')),
		);
	}

	/**
	 * 获取“展现方式”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getShowTypeRule($value)
	{
		$enum = DataAdverts::getShowTypeEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_ADVERTS_SHOW_TYPE_INARRAY'), implode(', ', $enum))),
		);
	}

	/**
	 * 获取“展现代码”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getShowCodeRule($value)
	{
		return array(
			'NotEmpty' => new validator\NotEmptyValidator($value, true, Lang::_('SRV_FILTER_ADVERTS_SHOW_CODE_NOTEMPTY')),
		);
	}

	/**
	 * 获取“文字内容”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getTitleRule($value)
	{
		if (!$this->_object->isShowTypeText($this->show_type)) {
			return array();
		}

		return array(
			'NotEmpty' => new validator\NotEmptyValidator($value, true, Lang::_('SRV_FILTER_ADVERTS_TITLE_NOTEMPTY')),
		);
	}

	/**
	 * 获取“广告链接”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getAdvertUrlRule($value)
	{
		if ($this->_object->isShowTypeFlash($this->show_type) || $this->_object->isShowTypeCode($this->show_type)) {
			return array();
		}

		return array(
			'NotEmpty' => new validator\NotEmptyValidator($value, true, Lang::_('SRV_FILTER_ADVERTS_ADVERT_URL_NOTEMPTY')),
		);
	}

	/**
	 * 获取“图片|Flash链接”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getAdvertSrcRule($value)
	{
		if ($this->_object->isShowTypeText($this->show_type) || $this->_object->isShowTypeCode($this->show_type)) {
			return array();
		}

		return array(
			'NotEmpty' => new validator\NotEmptyValidator($value, true, Lang::_('SRV_FILTER_ADVERTS_ADVERT_SRC_NOTEMPTY')),
		);
	}

	/**
	 * 获取“图片替换文字”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getAttrAltRule($value)
	{
		if (!$this->_object->isShowTypeImage($this->show_type)) {
			return array();
		}

		return array(
			'NotEmpty' => new validator\NotEmptyValidator($value, true, Lang::_('SRV_FILTER_ADVERTS_ATTR_ALT_NOTEMPTY')),
		);
	}

}
