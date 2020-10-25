<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace topic\services;

use libsrv\FormProcessor;
use tfc\saf\Log;
use tfc\validator;
use libapp\ErrorNo;
use topic\library\Lang;
use topic\library\TableNames;

/**
 * FpTopic class file
 * 业务层：表单数据处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: FpTopic.php 1 2014-11-04 16:50:14Z Code Generator $
 * @package topic.services
 * @since 1.0
 */
class FpTopic extends FormProcessor
{
	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_process()
	 */
	protected function _process(array $params = array())
	{
		if ($this->isInsert()) {
			if (!$this->required($params, 'topic_name', 'topic_key', 'cover', 'meta_title', 'meta_keywords', 'meta_description', 'html_body', 'sort')) {
				return false;
			}
		}

		$this->isValids($params,
			'topic_name', 'topic_key', 'cover', 'meta_title', 'meta_keywords', 'meta_description',
			'html_style', 'html_script', 'html_head', 'html_body', 'is_published', 'sort', 'use_header', 'use_footer', 'dt_created');
		return !$this->hasError();
	}

	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_cleanPreProcess()
	 */
	protected function _cleanPreProcess(array $params)
	{
		if ($this->isUpdate()) {
			if (isset($params['topic_key'])) {
				$row = $this->_object->findByPk($this->id);
				if (!$row || !is_array($row) || !isset($row['topic_key'])) {
					Log::warning(sprintf(
						'FpTopic is unable to find the result by id "%d"', $this->id
					), ErrorNo::ERROR_DB_SELECT,  __METHOD__);

					return false;
				}

				$topicKey = trim($params['topic_key']);
				if ($topicKey === $row['topic_key']) {
					unset($params['topic_key']);
				}
			}
		}

		$rules = array(
			'topic_name' => 'trim',
			'topic_key' => 'trim',
			'cover' => 'trim',
			'meta_title' => 'trim',
			'meta_keywords' => 'trim',
			'is_published' => 'trim',
			'sort' => 'intval',
			'use_header' => 'trim',
			'use_footer' => 'trim',
			'dt_created' => 'trim',
		);

		$ret = $this->clean($rules, $params);
		return $ret;
	}

	/**
	 * 获取“专题名”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getTopicNameRule($value)
	{
		return array(
			'MinLength' => new validator\MinLengthValidator($value, 1, Lang::_('SRV_FILTER_TOPIC_TOPIC_NAME_MINLENGTH')),
			'MaxLength' => new validator\MaxLengthValidator($value, 100, Lang::_('SRV_FILTER_TOPIC_TOPIC_NAME_MAXLENGTH')),
		);
	}

	/**
	 * 获取“专题Key”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getTopicKeyRule($value)
	{
		return array(
			'AlphaNum' => new validator\AlphaNumValidator($value, true, Lang::_('SRV_FILTER_TOPIC_TOPIC_KEY_ALPHANUM')),
			'MinLength' => new validator\MinLengthValidator($value, 2, Lang::_('SRV_FILTER_TOPIC_TOPIC_KEY_MINLENGTH')),
			'MaxLength' => new validator\MaxLengthValidator($value, 20, Lang::_('SRV_FILTER_TOPIC_TOPIC_KEY_MAXLENGTH')),
			'DbExists' => new validator\DbExistsValidator($value, false, Lang::_('SRV_FILTER_TOPIC_TOPIC_KEY_UNIQUE'), $this->getDbProxy(), TableNames::getTopic(), 'topic_key'),
		);
	}

	/**
	 * 获取“封面大图”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getCoverRule($value)
	{
		return array(
			'NotEmpty' => new validator\NotEmptyValidator($value, true, Lang::_('SRV_FILTER_TOPIC_COVER_NOTEMPTY')),
		);
	}

	/**
	 * 获取“SEO标题”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getMetaTitleRule($value)
	{
		return array(
			'MinLength' => new validator\MinLengthValidator($value, 2, Lang::_('SRV_FILTER_TOPIC_META_TITLE_MINLENGTH')),
			'MaxLength' => new validator\MaxLengthValidator($value, 50, Lang::_('SRV_FILTER_TOPIC_META_TITLE_MAXLENGTH')),
		);
	}

	/**
	 * 获取“SEO关键字”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getMetaKeywordsRule($value)
	{
		return array(
			'MinLength' => new validator\MinLengthValidator($value, 2, Lang::_('SRV_FILTER_TOPIC_META_KEYWORDS_MINLENGTH')),
			'MaxLength' => new validator\MaxLengthValidator($value, 50, Lang::_('SRV_FILTER_TOPIC_META_KEYWORDS_MAXLENGTH')),
		);
	}

	/**
	 * 获取“SEO描述”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getMetaDescriptionRule($value)
	{
		return array(
			'MinLength' => new validator\MinLengthValidator($value, 2, Lang::_('SRV_FILTER_TOPIC_META_DESCRIPTION_MINLENGTH')),
			'MaxLength' => new validator\MaxLengthValidator($value, 120, Lang::_('SRV_FILTER_TOPIC_META_DESCRIPTION_MAXLENGTH')),
		);
	}

	/**
	 * 获取“页面内容”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getHtmlBodyRule($value)
	{
		return array(
			'NotEmpty' => new validator\NotEmptyValidator($value, true, Lang::_('SRV_FILTER_TOPIC_HTML_BODY_NOTEMPTY')),
		);
	}

	/**
	 * 获取“是否发表”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getIsPublishedRule($value)
	{
		$enum = DataTopic::getIsPublishedEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_TOPIC_IS_PUBLISHED_INARRAY'), implode(', ', $enum))),
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
			'Integer' => new validator\IntegerValidator($value, true, Lang::_('SRV_FILTER_TOPIC_SORT_INTEGER')),
		);
	}

	/**
	 * 获取“使用公共的页头”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getUseHeaderRule($value)
	{
		$enum = DataTopic::getUseHeaderEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_TOPIC_USE_HEADER_INARRAY'), implode(', ', $enum))),
		);
	}

	/**
	 * 获取“使用公共的页脚”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getUseFooterRule($value)
	{
		$enum = DataTopic::getUseFooterEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_TOPIC_USE_FOOTER_INARRAY'), implode(', ', $enum))),
		);
	}

}
