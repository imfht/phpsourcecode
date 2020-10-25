<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\posts\model;

use library\BaseModel;
use tfc\saf\Text;
use libapp\Model;
use posts\services\DataComments;

/**
 * Comments class file
 * 文档评论
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Comments.php 1 2014-10-31 11:14:54Z Code Generator $
 * @package modules.posts.model
 * @since 1.0
 */
class Comments extends BaseModel
{
	/**
	 * (non-PHPdoc)
	 * @see \library\BaseModel::getViewTabsRender()
	 */
	public function getViewTabsRender()
	{
		$output = array(
		);

		return $output;
	}

	/**
	 * (non-PHPdoc)
	 * @see \library\BaseModel::getElementsRender()
	 */
	public function getElementsRender()
	{
		$output = array(
			'comment_id' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POST_COMMENTS_COMMENT_ID_LABEL'),
				'hint' => Text::_('MOD_POSTS_POST_COMMENTS_COMMENT_ID_HINT'),
			),
			'comment_pid' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POST_COMMENTS_COMMENT_PID_LABEL'),
				'hint' => Text::_('MOD_POSTS_POST_COMMENTS_COMMENT_PID_HINT'),
				'required' => true,
			),
			'post_id' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POST_COMMENTS_POST_ID_LABEL'),
				'hint' => Text::_('MOD_POSTS_POST_COMMENTS_POST_ID_HINT'),
				'required' => true,
			),
			'post_title' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POSTS_TITLE_LABEL'),
				'hint' => '',
			),
			'content' => array(
				'__tid__' => 'main',
				'type' => 'textarea',
				'label' => Text::_('MOD_POSTS_POST_COMMENTS_CONTENT_LABEL'),
				'hint' => Text::_('MOD_POSTS_POST_COMMENTS_CONTENT_HINT'),
				'required' => true,
			),
			'author_name' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POST_COMMENTS_AUTHOR_NAME_LABEL'),
				'hint' => Text::_('MOD_POSTS_POST_COMMENTS_AUTHOR_NAME_HINT'),
				'required' => true,
			),
			'author_mail' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POST_COMMENTS_AUTHOR_MAIL_LABEL'),
				'hint' => Text::_('MOD_POSTS_POST_COMMENTS_AUTHOR_MAIL_HINT'),
				'required' => true,
			),
			'author_url' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POST_COMMENTS_AUTHOR_URL_LABEL'),
				'hint' => Text::_('MOD_POSTS_POST_COMMENTS_AUTHOR_URL_HINT'),
				'required' => true,
			),
			'is_published' => array(
				'__tid__' => 'main',
				'type' => 'switch',
				'label' => Text::_('MOD_POSTS_POST_COMMENTS_IS_PUBLISHED_LABEL'),
				'hint' => Text::_('MOD_POSTS_POST_COMMENTS_IS_PUBLISHED_HINT'),
				'options' => DataComments::getIsPublishedEnum(),
				'value' => DataComments::IS_PUBLISHED_Y,
			),
			'good_count' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POST_COMMENTS_GOOD_COUNT_LABEL'),
				'hint' => Text::_('MOD_POSTS_POST_COMMENTS_GOOD_COUNT_HINT'),
				'required' => true,
			),
			'bad_count' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POST_COMMENTS_BAD_COUNT_LABEL'),
				'hint' => Text::_('MOD_POSTS_POST_COMMENTS_BAD_COUNT_HINT'),
				'required' => true,
			),
			'creator_id' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POST_COMMENTS_CREATOR_ID_LABEL'),
				'hint' => Text::_('MOD_POSTS_POST_COMMENTS_CREATOR_ID_HINT'),
				'required' => true,
			),
			'creator_name' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POST_COMMENTS_CREATOR_NAME_LABEL'),
				'hint' => Text::_('MOD_POSTS_POST_COMMENTS_CREATOR_NAME_HINT'),
				'required' => true,
			),
			'last_modifier_id' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POST_COMMENTS_LAST_MODIFIER_ID_LABEL'),
				'hint' => Text::_('MOD_POSTS_POST_COMMENTS_LAST_MODIFIER_ID_HINT'),
				'required' => true,
			),
			'last_modifier_name' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POST_COMMENTS_LAST_MODIFIER_NAME_LABEL'),
				'hint' => Text::_('MOD_POSTS_POST_COMMENTS_LAST_MODIFIER_NAME_HINT'),
				'required' => true,
			),
			'dt_created' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POST_COMMENTS_DT_CREATED_LABEL'),
				'hint' => Text::_('MOD_POSTS_POST_COMMENTS_DT_CREATED_HINT'),
				'required' => true,
			),
			'dt_last_modified' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POST_COMMENTS_DT_LAST_MODIFIED_LABEL'),
				'hint' => Text::_('MOD_POSTS_POST_COMMENTS_DT_LAST_MODIFIED_HINT'),
				'required' => true,
			),
			'ip_created' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POST_COMMENTS_IP_CREATED_LABEL'),
				'hint' => Text::_('MOD_POSTS_POST_COMMENTS_IP_CREATED_HINT'),
				'required' => true,
			),
			'ip_last_modified' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POST_COMMENTS_IP_LAST_MODIFIED_LABEL'),
				'hint' => Text::_('MOD_POSTS_POST_COMMENTS_IP_LAST_MODIFIED_HINT'),
				'required' => true,
			),
			'dt_created_le' => array(
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POST_COMMENTS_DT_CREATED_LE_LABEL'),
			),
			'dt_created_ge' => array(
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POST_COMMENTS_DT_CREATED_GE_LABEL'),
			),
		);

		return $output;
	}

	/**
	 * 获取列表页“ID”的A标签
	 * @param array $data
	 * @return string
	 */
	public function getCommentIdLink($data)
	{
		$params = array(
			'id' => $data['comment_id'],
		);

		$url = $this->urlManager->getUrl($this->actNameView, $this->controller, $this->module, $params);
		$output = $this->html->a($data['comment_id'], $url);
		return $output;
	}

	/**
	 * (non-PHPdoc)
	 * @see \library\BaseModel::findByPk()
	 */
	public function findByPk($value)
	{
		$ret = parent::findByPk($value);
		if (isset($ret['data']) && is_array($ret['data'])) {
			if (isset($ret['data']['post_id'])) {
				$ret['data']['post_title'] = $this->getPostTitleByPostId($ret['data']['post_id']);
			}

			if (isset($ret['data']['ip_created'])) {
				$ret['data']['ip_created'] = long2ip($ret['data']['ip_created']);
			}

			if (isset($ret['data']['ip_last_modified'])) {
				$ret['data']['ip_last_modified'] = long2ip($ret['data']['ip_last_modified']);
			}
		}

		return $ret;
	}

	/**
	 * 通过“文档ID”，获取“文档标题”
	 * @param integer $postId
	 * @return string
	 */
	public function getPostTitleByPostId($postId)
	{
		return Model::getInstance('Posts')->getTitleByPostId($postId);
	}

}
