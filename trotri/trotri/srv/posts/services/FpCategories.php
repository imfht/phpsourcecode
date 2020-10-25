<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace posts\services;

use libsrv\FormProcessor;
use tfc\validator;
use tfc\saf\Log;
use libapp\ErrorNo;
use posts\library\Lang;
use posts\library\TableNames;

/**
 * FpCategories class file
 * 业务层：表单数据处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: FpCategories.php 1 2014-10-13 21:17:13Z Code Generator $
 * @package posts.services
 * @since 1.0
 */
class FpCategories extends FormProcessor
{
	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_process()
	 */
	protected function _process(array $params = array())
	{
		if ($this->isInsert()) {
			if (!$this->required($params,
				'category_pid', 'category_name', 'meta_title', 'meta_keywords', 'meta_description',
				'tpl_home', 'tpl_list', 'tpl_view', 'sort', 'description')) {
				return false;
			}
		}

		$this->isValids($params,
			'category_pid', 'category_name', 'alias',
			'meta_title', 'meta_keywords', 'meta_description',
			'tpl_home', 'tpl_list', 'tpl_view', 'sort', 'description');
		return !$this->hasError();
	}

	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_cleanPreProcess()
	 */
	protected function _cleanPreProcess(array $params)
	{
		if ($this->isUpdate()) {
			$row = $this->_object->findByPk($this->id);
			if (!$row || !is_array($row) || !isset($row['category_id']) || !isset($row['category_pid']) || !isset($row['category_name'])) {
				Log::warning(sprintf(
					'FpCategories is unable to find the result by id "%d"', $this->id
				), ErrorNo::ERROR_DB_SELECT,  __METHOD__);

				return false;
			}

			if (isset($params['category_name'])) {
				$newCategoryName = trim($params['category_name']);
				$oldCategoryName = $row['category_name'];
				if (isset($params['category_pid'])) {
					$newCategoryPid = (int) $params['category_pid'];
					$oldCategoryPid = (int) $row['category_pid'];
					if ($newCategoryPid === $oldCategoryPid && $newCategoryName === $oldCategoryName) {
						unset($params['category_pid'], $params['category_name']);
					}
				}
				else {
					if ($newCategoryName === $oldCategoryName) {
						unset($params['category_name']);
					}
				}
			}
		}

		$rules = array(
			'category_name' => 'trim',
			'category_pid' => 'intval',
			'alias' => 'trim',
			'meta_title' => 'trim',
			'meta_keywords' => 'trim',
			'meta_description' => 'trim',
			'tpl_home' => 'trim',
			'tpl_list' => 'trim',
			'tpl_view' => 'trim',
			'sort' => 'intval',
		);

		$ret = $this->clean($rules, $params);
		return $ret;
	}

	/**
	 * 获取“所属父类别”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getCategoryPidRule($value)
	{
		if ($value === 0) {
			return array();
		}

		return array(
			'NotEqual' => new validator\NotEqualValidator($value, $this->id, Lang::_('SRV_FILTER_POST_CATEGORIES_CATEGORY_PID_NOTEQUAL')),
			'DbExists' => new validator\DbExistsValidator($value, true, Lang::_('SRV_FILTER_POST_CATEGORIES_CATEGORY_PID_EXISTS'), $this->getDbProxy(), TableNames::getCategories(), 'category_id')
		);
	}

	/**
	 * 获取“类别名”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getCategoryNameRule($value)
	{
		return array(
			'MinLength' => new validator\MinLengthValidator($value, 2, Lang::_('SRV_FILTER_POST_CATEGORIES_CATEGORY_NAME_MINLENGTH')),
			'MaxLength' => new validator\MaxLengthValidator($value, 20, Lang::_('SRV_FILTER_POST_CATEGORIES_CATEGORY_NAME_MAXLENGTH')),
			'DbExists2' => new validator\DbExists2Validator($value, false, Lang::_('SRV_FILTER_POST_CATEGORIES_CATEGORY_NAME_UNIQUE'), $this->getDbProxy(), TableNames::getCategories(), 'category_name', 'category_pid', $this->category_pid)
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
			'MaxLength' => new validator\MaxLengthValidator($value, 120, Lang::_('SRV_FILTER_POST_CATEGORIES_ALIAS_MAXLENGTH')),
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
			'MinLength' => new validator\MinLengthValidator($value, 2, Lang::_('SRV_FILTER_POST_CATEGORIES_META_TITLE_MINLENGTH')),
			'MaxLength' => new validator\MaxLengthValidator($value, 50, Lang::_('SRV_FILTER_POST_CATEGORIES_META_TITLE_MAXLENGTH')),
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
			'MinLength' => new validator\MinLengthValidator($value, 2, Lang::_('SRV_FILTER_POST_CATEGORIES_META_KEYWORDS_MINLENGTH')),
			'MaxLength' => new validator\MaxLengthValidator($value, 50, Lang::_('SRV_FILTER_POST_CATEGORIES_META_KEYWORDS_MAXLENGTH')),
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
			'MinLength' => new validator\MinLengthValidator($value, 2, Lang::_('SRV_FILTER_POST_CATEGORIES_META_DESCRIPTION_MINLENGTH')),
			'MaxLength' => new validator\MaxLengthValidator($value, 120, Lang::_('SRV_FILTER_POST_CATEGORIES_META_DESCRIPTION_MAXLENGTH')),
		);
	}

	/**
	 * 获取“封页模板名”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getTplHomeRule($value)
	{
		return array(
			'Regex' => new validator\RegexValidator($value, '/^[\w\.\/]+$/i', Lang::_('SRV_FILTER_POST_CATEGORIES_TPL_HOME_REGEX')),
			'MinLength' => new validator\MinLengthValidator($value, 1, Lang::_('SRV_FILTER_POST_CATEGORIES_TPL_HOME_MINLENGTH')),
			'MaxLength' => new validator\MaxLengthValidator($value, 50, Lang::_('SRV_FILTER_POST_CATEGORIES_TPL_HOME_MAXLENGTH')),
		);
	}

	/**
	 * 获取“列表模板名”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getTplListRule($value)
	{
		return array(
			'Regex' => new validator\RegexValidator($value, '/^[\w\.\/]+$/i', Lang::_('SRV_FILTER_POST_CATEGORIES_TPL_LIST_REGEX')),
			'MinLength' => new validator\MinLengthValidator($value, 1, Lang::_('SRV_FILTER_POST_CATEGORIES_TPL_LIST_MINLENGTH')),
			'MaxLength' => new validator\MaxLengthValidator($value, 50, Lang::_('SRV_FILTER_POST_CATEGORIES_TPL_LIST_MAXLENGTH')),
		);
	}

	/**
	 * 获取“文档模板名”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getTplViewRule($value)
	{
		return array(
			'Regex' => new validator\RegexValidator($value, '/^[\w\.\/]+$/i', Lang::_('SRV_FILTER_POST_CATEGORIES_TPL_VIEW_REGEX')),
			'MinLength' => new validator\MinLengthValidator($value, 1, Lang::_('SRV_FILTER_POST_CATEGORIES_TPL_VIEW_MINLENGTH')),
			'MaxLength' => new validator\MaxLengthValidator($value, 50, Lang::_('SRV_FILTER_POST_CATEGORIES_TPL_VIEW_MAXLENGTH')),
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
			'Integer' => new validator\IntegerValidator($value, true, Lang::_('SRV_FILTER_POST_CATEGORIES_SORT_INTEGER')),
		);
	}

}
