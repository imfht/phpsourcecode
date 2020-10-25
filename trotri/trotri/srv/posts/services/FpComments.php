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
use tfc\validator;
use libsrv\Service;
use libsrv\Clean;
use posts\library\Lang;

/**
 * FpComments class file
 * 业务层：表单数据处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: FpComments.php 1 2014-10-31 11:14:54Z Code Generator $
 * @package posts.services
 * @since 1.0
 */
class FpComments extends FormProcessor
{
	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_process()
	 */
	protected function _process(array $params = array())
	{
		if ($this->isInsert()) {
			if (!$this->required($params, 'comment_pid', 'post_id', 'content', 'author_name', 'author_mail')) {
				return false;
			}
		}

		$this->isValids($params, 'comment_pid', 'post_id', 'content', 'author_name', 'author_mail', 'author_url', 'is_published', 'good_count', 'bad_count', 'creator_id', 'creator_name', 'last_modifier_id', 'last_modifier_name', 'dt_created', 'dt_last_modified', 'ip_created', 'ip_last_modified');
		return !$this->hasError();
	}

	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_cleanPreProcess()
	 */
	protected function _cleanPreProcess(array $params)
	{
		if ($this->isInsert()) {
			if (isset($params['last_modifier_id'])) { unset($params['last_modifier_id']); }
			if (isset($params['last_modifier_name'])) { unset($params['last_modifier_name']); }

			$params['dt_created'] = $params['dt_last_modified'] = date('Y-m-d H:i:s');
			$params['ip_created'] = $params['ip_last_modified'] = Clean::ip2long(Ap::getRequest()->getClientIp());
			$params['good_count'] = $params['bad_count'] = 0;

			$postId = isset($params['post_id']) ? (int) $params['post_id'] : 0;
			if ($postId <= 0) {
				$this->addError('post_id', Lang::_('SRV_FILTER_POST_COMMENTS_POST_ID_EXISTS'));
				return false;
			}

			$row = Service::getInstance('Posts', 'posts')->findByPk($postId);
			if (!$row || !is_array($row) || !isset($row['comment_status'])) {
				$this->addError('post_id', Lang::_('SRV_FILTER_POST_COMMENTS_POST_ID_EXISTS'));
				return false;
			}

			switch (true) {
				case $row['comment_status'] === DataPosts::COMMENT_STATUS_DRAFT:
					$params['is_published'] = DataComments::IS_PUBLISHED_N;
					break;
				case $row['comment_status'] === DataPosts::COMMENT_STATUS_PUBLISH:
					$params['is_published'] = DataComments::IS_PUBLISHED_Y;
					break;
				default:
					$this->addError('post_id', Lang::_('SRV_FILTER_POST_COMMENTS_POST_ID_POWER'));
					return false;
			}
		}
		else {
			if (isset($params['creator_id'])) { unset($params['creator_id']); }
			if (isset($params['creator_name'])) { unset($params['creator_name']); }
			if (isset($params['dt_created'])) { unset($params['dt_created']); }
			if (isset($params['ip_created'])) { unset($params['ip_created']); }
			$params['dt_last_modified'] = date('Y-m-d H:i:s');
			$params['ip_last_modified'] = Clean::ip2long(Ap::getRequest()->getClientIp());
		}

		$rules = array(
			'comment_pid' => 'intval',
			'post_id' => 'intval',
			'author_name' => 'trim',
			'author_mail' => 'trim',
			'author_url' => 'trim',
			'is_published' => 'trim',
			'good_count' => 'intval',
			'bad_count' => 'intval',
			'creator_id' => 'intval',
			'creator_name' => 'trim',
			'last_modifier_id' => 'intval',
			'last_modifier_name' => 'trim',
			'dt_created' => 'trim',
			'dt_last_modified' => 'trim',
			'ip_created' => 'intval',
			'ip_last_modified' => 'intval',
		);

		$ret = $this->clean($rules, $params);
		return $ret;
	}

	/**
	 * 获取“评论内容”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getContentRule($value)
	{
		return array(
			'NotEmpty' => new validator\NotEmptyValidator($value, true, Lang::_('SRV_FILTER_POST_COMMENTS_CONTENT_NOTEMPTY')),
		);
	}

	/**
	 * 获取“评论作者名”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getAuthorNameRule($value)
	{
		return array(
			'NotEmpty' => new validator\NotEmptyValidator($value, true, Lang::_('SRV_FILTER_POST_COMMENTS_AUTHOR_NAME_NOTEMPTY')),
		);
	}

	/**
	 * 获取“评论作者邮箱”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getAuthorMailRule($value)
	{
		return array(
			'NotEmpty' => new validator\NotEmptyValidator($value, true, Lang::_('SRV_FILTER_POST_COMMENTS_AUTHOR_MAIL_NOTEMPTY')),
			'Mail' => new validator\MailValidator($value, true, Lang::_('SRV_FILTER_POST_COMMENTS_AUTHOR_MAIL_MAIL')),
		);
	}

	/**
	 * 获取“是否发表”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getIsPublishedRule($value)
	{
		$enum = DataComments::getIsPublishedEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_POST_COMMENTS_IS_PUBLISHED_INARRAY'), implode(', ', $enum))),
		);
	}

}
