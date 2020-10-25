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
use tfc\ap\Ap;
use tfc\saf\Log;
use tfc\validator;
use libsrv\Service;
use libsrv\Clean;
use libapp\ErrorNo;
use posts\library\Lang;
use posts\library\TableNames;

/**
 * FpPosts class file
 * 业务层：表单数据处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: FpPosts.php 1 2014-10-17 11:27:20Z Code Generator $
 * @package posts.services
 * @since 1.0
 */
class FpPosts extends FormProcessor
{
	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_process()
	 */
	protected function _process(array $params = array())
	{
		if ($this->isInsert()) {
			if (!$this->required($params, 'title', 'category_id', 'module_id', 'creator_id')) {
				return false;
			}
		}
		else {
			if (!$this->required($params, 'last_modifier_id')) {
				return false;
			}
		}

		$this->isValids($params,
			'title', 'alias', 'content', 'keywords', 'description', 'sort', 'category_id', 'category_name', 'module_id',
			'password', 'picture', 'is_head', 'is_recommend', 'is_jump', 'jump_url', 'is_published', 'dt_publish_up', 'dt_publish_down',
			'comment_status', 'allow_other_modify', 'hits', 'praise_count', 'comment_count',
			'creator_id', 'creator_name', 'last_modifier_id', 'last_modifier_name', 'dt_created', 'dt_last_modified', 'ip_created', 'ip_last_modified', 'trash');
		return !$this->hasError();
	}

	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_cleanPreProcess()
	 */
	protected function _cleanPreProcess(array $params)
	{
		if (isset($params['trash'])) { unset($params['trash']); }
		if (isset($params['category_name'])) { unset($params['category_name']); }
		if (isset($params['creator_name'])) { unset($params['creator_name']); }
		if (isset($params['last_modifier_name'])) { unset($params['last_modifier_name']); }

		if ($this->isInsert()) {
			if (isset($params['last_modifier_id'])) { unset($params['last_modifier_id']); }
			if (isset($params['last_modifier_name'])) { unset($params['last_modifier_name']); }

			$params['dt_created'] = $params['dt_last_modified'] = date('Y-m-d H:i:s');
			$params['ip_created'] = $params['ip_last_modified'] = Clean::ip2long(Ap::getRequest()->getClientIp());

			if (!isset($params['sort'])) {
				$params['sort'] = 10000;
			}
		}
		else {
			$row = $this->_object->findByPk($this->id);
			if (!$row || !is_array($row) || !isset($row['creator_id']) || !isset($row['allow_other_modify'])) {
				Log::warning(sprintf(
					'FpPosts is unable to find the result by id "%d"', $this->id
				), ErrorNo::ERROR_DB_SELECT,  __METHOD__);

				return false;
			}

			$creatorId = isset($row['creator_id']) ? (int) $row['creator_id'] : 0;
			$lastModifierId = isset($params['last_modifier_id']) ? (int) $params['last_modifier_id'] : 0;
			if ($creatorId !== $lastModifierId) {
				if ($row['allow_other_modify'] !== DataPosts::ALLOW_OTHER_MODIFY_Y) {
					$this->addError('allow_other_modify', Lang::_('SRV_FILTER_POSTS_ALLOW_OTHER_MODIFY_POWER'));
				}
			}

			if (isset($params['creator_id'])) { unset($params['creator_id']); }
			if (isset($params['creator_name'])) { unset($params['creator_name']); }
			if (isset($params['dt_created'])) { unset($params['dt_created']); }
			if (isset($params['ip_created'])) { unset($params['ip_created']); }
			if (isset($params['module_id'])) { unset($params['module_id']); }
			$params['dt_last_modified'] = date('Y-m-d H:i:s');
			$params['ip_last_modified'] = Clean::ip2long(Ap::getRequest()->getClientIp());
		}

		$rules = array(
			'title' => 'trim',
			'alias' => 'trim',
			'keywords' => 'trim',
			'sort' => 'intval',
			'category_id' => 'intval',
			'module_id' => 'intval',
			'password' => 'trim',
			'picture' => 'trim',
			'is_head' => 'trim',
			'is_recommend' => 'trim',
			'is_jump' => 'trim',
			'jump_url' => 'trim',
			'is_published' => 'trim',
			'dt_publish_up' => 'trim',
			'dt_publish_down' => 'trim',
			'comment_status' => 'trim',
			'allow_other_modify' => 'trim',
			'hits' => 'intval',
			'praise_count' => 'intval',
			'comment_count' => 'intval',
			'creator_id' => 'intval',
			'last_modifier_id' => 'intval',
		);

		$ret = $this->clean($rules, $params);
		return $ret;
	}

	/**
	 * 获取“文档标题”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getTitleRule($value)
	{
		return array(
			'MinLength' => new validator\MinLengthValidator($value, 1, Lang::_('SRV_FILTER_POSTS_TITLE_MINLENGTH')),
			'MaxLength' => new validator\MaxLengthValidator($value, 50, Lang::_('SRV_FILTER_POSTS_TITLE_MAXLENGTH')),
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
			'MaxLength' => new validator\MaxLengthValidator($value, 120, Lang::_('SRV_FILTER_POSTS_ALIAS_MAXLENGTH')),
		);
	}

	/**
	 * 获取“关键字”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getKeywordsRule($value)
	{
		return array(
			'MaxLength' => new validator\MaxLengthValidator($value, 50, Lang::_('SRV_FILTER_POSTS_KEYWORDS_MAXLENGTH')),
		);
	}

	/**
	 * 获取“内容摘要”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getDescriptionRule($value)
	{
		return array(
			'MaxLength' => new validator\MaxLengthValidator($value, 240, Lang::_('SRV_FILTER_POSTS_DESCRIPTION_MAXLENGTH')),
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
			'Integer' => new validator\IntegerValidator($value, true, Lang::_('SRV_FILTER_POSTS_SORT_INTEGER')),
		);
	}

	/**
	 * 获取“所属类别”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getCategoryIdRule($value)
	{
		$columnName = 'category_id';

		if (($value = (int) $value) <= 0) {
			$this->addError($columnName, Lang::_('SRV_FILTER_POSTS_CATEGORY_ID_EXISTS'));
			return array();
		}

		$categoryName = Service::getInstance('Categories', 'posts')->getCategoryNameByCategoryId($value);
		if ($categoryName === '') {
			$this->addError($columnName, Lang::_('SRV_FILTER_POSTS_CATEGORY_ID_EXISTS'));
			return array();
		}

		$this->$columnName = $value;
		$this->category_name = $categoryName;

		return array();
	}

	/**
	 * 获取“所属模型”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getModuleIdRule($value)
	{
		return array(
			'DbExists' => new validator\DbExistsValidator($value, true, Lang::_('SRV_FILTER_POSTS_MODULE_ID_EXISTS'), $this->getDbProxy(), TableNames::getModules(), 'module_id')
		);
	}

	/**
	 * 获取“访问密码”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getPasswordRule($value)
	{
		return array(
			'MaxLength' => new validator\MaxLengthValidator($value, 20, Lang::_('SRV_FILTER_POSTS_PASSWORD_MAXLENGTH')),
		);
	}

	/**
	 * 获取“是否头条”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getIsHeadRule($value)
	{
		$enum = DataPosts::getIsHeadEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_POSTS_IS_HEAD_INARRAY'), implode(', ', $enum))),
		);
	}

	/**
	 * 获取“是否推荐”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getIsRecommendRule($value)
	{
		$enum = DataPosts::getIsRecommendEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_POSTS_IS_RECOMMEND_INARRAY'), implode(', ', $enum))),
		);
	}

	/**
	 * 获取“是否跳转”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getIsJumpRule($value)
	{
		$enum = DataPosts::getIsJumpEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_POSTS_IS_JUMP_INARRAY'), implode(', ', $enum))),
		);
	}

	/**
	 * 获取“跳转链接”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getJumpUrlRule($value)
	{
		if ($this->is_jump === DataPosts::IS_JUMP_N && $value === '') {
			return array();
		}

		return array(
			'NotEmpty' => new validator\NotEmptyValidator($value, true, Lang::_('SRV_FILTER_POSTS_JUMP_URL_NOTEMPTY')),
			'Url' => new validator\UrlValidator($value, true, Lang::_('SRV_FILTER_POSTS_JUMP_URL_URL')),
		);
	}

	/**
	 * 获取“是否发表”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getIsPublishedRule($value)
	{
		$enum = DataPosts::getIsPublishedEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_POSTS_IS_PUBLISHED_INARRAY'), implode(', ', $enum))),
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
			'DateTime' => new validator\DateTimeValidator($value, true, Lang::_('SRV_FILTER_POSTS_DT_PUBLISH_UP_DATETIME')),
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
			'DateTime' => new validator\DateTimeValidator($value, true, Lang::_('SRV_FILTER_POSTS_DT_PUBLISH_DOWN_DATETIME')),
		);
	}

	/**
	 * 获取“评论设置”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getCommentStatusRule($value)
	{
		$enum = DataPosts::getCommentStatusEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_POSTS_COMMENT_STATUS_INARRAY'), implode(', ', $enum))),
		);
	}

	/**
	 * 获取“允许其他人编辑”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getAllowOtherModifyRule($value)
	{
		$enum = DataPosts::getAllowOtherModifyEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_POSTS_ALLOW_OTHER_MODIFY_INARRAY'), implode(', ', $enum))),
		);
	}

	/**
	 * 获取“访问次数”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getHitsRule($value)
	{
		return array(
			'NonNegativeInteger' => new validator\NonNegativeIntegerValidator($value, true, Lang::_('SRV_FILTER_POSTS_HITS_NONNEGATIVEINTEGER')),
		);
	}

	/**
	 * 获取“赞美次数”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getPraiseCountRule($value)
	{
		return array(
			'NonNegativeInteger' => new validator\NonNegativeIntegerValidator($value, true, Lang::_('SRV_FILTER_POSTS_PRAISE_COUNT_NONNEGATIVEINTEGER')),
		);
	}

	/**
	 * 获取“评论次数”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getCommentCountRule($value)
	{
		return array(
			'NonNegativeInteger' => new validator\NonNegativeIntegerValidator($value, true, Lang::_('SRV_FILTER_POSTS_COMMENT_COUNT_NONNEGATIVEINTEGER')),
		);
	}

	/**
	 * 获取“创建人ID”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getCreatorIdRule($value)
	{
		$columnName = 'creator_id';

		if (($value = (int) $value) <= 0) {
			$this->addError($columnName, Lang::_('SRV_FILTER_POSTS_CREATOR_ID_EXISTS'));
		}

		$userName = Service::getInstance('Users', 'users')->getUserNameByUserId($value);
		if ($userName === '') {
			$this->addError($columnName, Lang::_('SRV_FILTER_POSTS_CREATOR_ID_EXISTS'));
		}

		$this->$columnName = $value;
		$this->creator_name = $userName;

		return array();
	}

	/**
	 * 获取“上次编辑人ID”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getLastModifierIdRule($value)
	{
		$columnName = 'last_modifier_id';

		if (($value = (int) $value) <= 0) {
			$this->addError($columnName, Lang::_('SRV_FILTER_POSTS_LAST_MODIFIER_ID_EXISTS'));
		}

		$userName = Service::getInstance('Users', 'users')->getUserNameByUserId($value);
		if ($userName === '') {
			$this->addError($columnName, Lang::_('SRV_FILTER_POSTS_LAST_MODIFIER_ID_EXISTS'));
		}

		$this->$columnName = $value;
		$this->last_modifier_name = $userName;

		return array();
	}

	/**
	 * 获取“创建时间”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getDtCreatedRule($value)
	{
		return array(
			'DateTime' => new validator\DateTimeValidator($value, true, Lang::_('SRV_FILTER_POSTS_DT_CREATED_DATETIME')),
		);
	}

	/**
	 * 获取“上次编辑时间”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getDtLastModifiedRule($value)
	{
		return array(
			'DateTime' => new validator\DateTimeValidator($value, true, Lang::_('SRV_FILTER_POSTS_DT_LAST_MODIFIED_DATETIME')),
		);
	}

	/**
	 * 获取“是否删除”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getTrashRule($value)
	{
		$enum = DataPosts::getTrashEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_POSTS_TRASH_INARRAY'), implode(', ', $enum))),
		);
	}

}
