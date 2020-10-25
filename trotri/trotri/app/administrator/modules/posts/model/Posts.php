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
use tfc\ap\Ap;
use tfc\mvc\Mvc;
use tfc\util\String;
use tfc\saf\Text;
use tfc\auth\Identity;
use libapp\Model;
use posts\services\DataPosts;

/**
 * Posts class file
 * 文档管理
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Posts.php 1 2014-10-18 01:27:03Z Code Generator $
 * @package modules.posts.model
 * @since 1.0
 */
class Posts extends BaseModel
{
	/**
	 * (non-PHPdoc)
	 * @see \library\BaseModel::getViewTabsRender()
	 */
	public function getViewTabsRender()
	{
		$output = array(
			'advanced' => array(
				'tid' => 'advanced',
				'prompt' => Text::_('MOD_POSTS_POSTS_VIEWTAB_ADVANCED_PROMPT')
			),
			'profile' => array(
				'tid' => 'profile',
				'prompt' => Text::_('MOD_POSTS_POSTS_VIEWTAB_PROFILE_PROMPT')
			),
			'system' => array(
				'tid' => 'system',
				'prompt' => Text::_('MOD_POSTS_POSTS_VIEWTAB_SYSTEM_PROMPT')
			),
		);

		return $output;
	}

	/**
	 * (non-PHPdoc)
	 * @see \library\BaseModel::getElementsRender()
	 */
	public function getElementsRender()
	{
		$urlManager = Mvc::getView()->getUrlManager();
		$nowTime = date('Y-m-d H:i:s');
		$output = array(
			'post_id' => array(
				'__tid__' => 'main',
				'type' => 'hidden',
				'label' => Text::_('MOD_POSTS_POSTS_POST_ID_LABEL'),
				'hint' => Text::_('MOD_POSTS_POSTS_POST_ID_HINT'),
			),
			'title' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POSTS_TITLE_LABEL'),
				'hint' => Text::_('MOD_POSTS_POSTS_TITLE_HINT'),
				'required' => true,
			),
			'alias' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POSTS_ALIAS_LABEL'),
				'hint' => Text::_('MOD_POSTS_POSTS_ALIAS_HINT'),
			),
			'picture' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POSTS_PICTURE_LABEL'),
				'hint' => Text::_('MOD_POSTS_POSTS_PICTURE_HINT'),
			),
			'picture_file' => array(
				'__tid__' => 'main',
				'type' => 'string',
				'label' => '',
				'hint' => Text::_('MOD_POSTS_POSTS_PICTURE_HINT'),
				'value' => '<div id="picture_file" url="' . $urlManager->getUrl('ajaxupload', '', '', array('from' => 'picture')) . '" name="upload">' . Text::_('CFG_SYSTEM_GLOBAL_UPLOAD') . '</div>',
			),
			'content' => array(
				'__tid__' => 'main',
				'type' => 'ckeditor',
				'id' => 'content',
				'height' => '960px',
				'toolbar' => 'post',
				'url' => $urlManager->getUrl('ajaxupload', '', '', array('from' => 'ckeditor'))
			),
			'keywords' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POSTS_KEYWORDS_LABEL'),
				'hint' => Text::_('MOD_POSTS_POSTS_KEYWORDS_HINT'),
			),
			'description' => array(
				'__tid__' => 'main',
				'type' => 'textarea',
				'label' => Text::_('MOD_POSTS_POSTS_DESCRIPTION_LABEL'),
				'hint' => Text::_('MOD_POSTS_POSTS_DESCRIPTION_HINT'),
			),
			'sort' => array(
				'__tid__' => 'advanced',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POSTS_SORT_LABEL'),
				'hint' => Text::_('MOD_POSTS_POSTS_SORT_HINT'),
				'required' => true,
				'value' => 10000
			),
			'category_id' => array(
				'__tid__' => 'main',
				'type' => 'select',
				'label' => Text::_('MOD_POSTS_POSTS_CATEGORY_ID_LABEL'),
				'hint' => Text::_('MOD_POSTS_POSTS_CATEGORY_ID_HINT'),
			),
			'category_name' => array(
				'__tid__' => 'main',
				'type' => 'hidden',
				'label' => Text::_('MOD_POSTS_POSTS_CATEGORY_NAME_LABEL'),
				'hint' => Text::_('MOD_POSTS_POSTS_CATEGORY_NAME_HINT'),
			),
			'module_id' => array(
				'__tid__' => 'profile',
				'type' => 'select',
				'label' => Text::_('MOD_POSTS_POSTS_MODULE_ID_LABEL'),
				'hint' => Text::_('MOD_POSTS_POSTS_MODULE_ID_HINT'),
			),
			'password' => array(
				'__tid__' => 'advanced',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POSTS_PASSWORD_LABEL'),
				'hint' => Text::_('MOD_POSTS_POSTS_PASSWORD_HINT'),
			),
			'is_head' => array(
				'__tid__' => 'advanced',
				'type' => 'switch',
				'label' => Text::_('MOD_POSTS_POSTS_IS_HEAD_LABEL'),
				'hint' => Text::_('MOD_POSTS_POSTS_IS_HEAD_HINT'),
				'options' => DataPosts::getIsHeadEnum(),
				'value' => DataPosts::IS_HEAD_N,
			),
			'is_recommend' => array(
				'__tid__' => 'advanced',
				'type' => 'switch',
				'label' => Text::_('MOD_POSTS_POSTS_IS_RECOMMEND_LABEL'),
				'hint' => Text::_('MOD_POSTS_POSTS_IS_RECOMMEND_HINT'),
				'options' => DataPosts::getIsRecommendEnum(),
				'value' => DataPosts::IS_RECOMMEND_N,
			),
			'is_jump' => array(
				'__tid__' => 'advanced',
				'type' => 'switch',
				'label' => Text::_('MOD_POSTS_POSTS_IS_JUMP_LABEL'),
				'hint' => Text::_('MOD_POSTS_POSTS_IS_JUMP_HINT'),
				'options' => DataPosts::getIsJumpEnum(),
				'value' => DataPosts::IS_JUMP_N,
			),
			'jump_url' => array(
				'__tid__' => 'advanced',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POSTS_JUMP_URL_LABEL'),
				'hint' => Text::_('MOD_POSTS_POSTS_JUMP_URL_HINT'),
				'required' => true,
			),
			'is_published' => array(
				'__tid__' => 'main',
				'type' => 'switch',
				'label' => Text::_('MOD_POSTS_POSTS_IS_PUBLISHED_LABEL'),
				'hint' => Text::_('MOD_POSTS_POSTS_IS_PUBLISHED_HINT'),
				'options' => DataPosts::getIsPublishedEnum(),
				'value' => DataPosts::IS_PUBLISHED_Y,
			),
			'dt_publish_up' => array(
				'__tid__' => 'advanced',
				'type' => 'datetimepicker',
				'label' => Text::_('MOD_POSTS_POSTS_DT_PUBLISH_UP_LABEL'),
				'hint' => Text::_('MOD_POSTS_POSTS_DT_PUBLISH_UP_HINT'),
				'value' => $nowTime
			),
			'dt_publish_down' => array(
				'__tid__' => 'advanced',
				'type' => 'datetimepicker',
				'label' => Text::_('MOD_POSTS_POSTS_DT_PUBLISH_DOWN_LABEL'),
				'hint' => Text::_('MOD_POSTS_POSTS_DT_PUBLISH_DOWN_HINT'),
			),
			'comment_status' => array(
				'__tid__' => 'advanced',
				'type' => 'radio',
				'label' => Text::_('MOD_POSTS_POSTS_COMMENT_STATUS_LABEL'),
				'hint' => Text::_('MOD_POSTS_POSTS_COMMENT_STATUS_HINT'),
				'options' => DataPosts::getCommentStatusEnum(),
				'value' => DataPosts::COMMENT_STATUS_PUBLISH,
			),
			'allow_other_modify' => array(
				'__tid__' => 'advanced',
				'type' => 'switch',
				'label' => Text::_('MOD_POSTS_POSTS_ALLOW_OTHER_MODIFY_LABEL'),
				'hint' => Text::_('MOD_POSTS_POSTS_ALLOW_OTHER_MODIFY_HINT'),
				'options' => DataPosts::getAllowOtherModifyEnum(),
				'value' => DataPosts::ALLOW_OTHER_MODIFY_Y,
			),
			'hits' => array(
				'__tid__' => 'system',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POSTS_HITS_LABEL'),
				'hint' => Text::_('MOD_POSTS_POSTS_HITS_HINT'),
				'value' => 0
			),
			'praise_count' => array(
				'__tid__' => 'system',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POSTS_PRAISE_COUNT_LABEL'),
				'hint' => Text::_('MOD_POSTS_POSTS_PRAISE_COUNT_HINT'),
				'value' => 0
			),
			'comment_count' => array(
				'__tid__' => 'system',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POSTS_COMMENT_COUNT_LABEL'),
				'hint' => Text::_('MOD_POSTS_POSTS_COMMENT_COUNT_HINT'),
				'value' => 0
			),
			'creator_id' => array(
				'__tid__' => 'system',
				'type' => 'hidden',
				'label' => Text::_('MOD_POSTS_POSTS_CREATOR_ID_LABEL'),
				'hint' => Text::_('MOD_POSTS_POSTS_CREATOR_ID_HINT'),
			),
			'creator_name' => array(
				'__tid__' => 'system',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POSTS_CREATOR_NAME_LABEL'),
				'hint' => Text::_('MOD_POSTS_POSTS_CREATOR_NAME_HINT'),
				'disabled' => true,
			),
			'last_modifier_id' => array(
				'__tid__' => 'system',
				'type' => 'hidden',
				'label' => Text::_('MOD_POSTS_POSTS_LAST_MODIFIER_ID_LABEL'),
				'hint' => Text::_('MOD_POSTS_POSTS_LAST_MODIFIER_ID_HINT'),
			),
			'last_modifier_name' => array(
				'__tid__' => 'system',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POSTS_LAST_MODIFIER_NAME_LABEL'),
				'hint' => Text::_('MOD_POSTS_POSTS_LAST_MODIFIER_NAME_HINT'),
				'disabled' => true,
			),
			'dt_created' => array(
				'__tid__' => 'system',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POSTS_DT_CREATED_LABEL'),
				'hint' => Text::_('MOD_POSTS_POSTS_DT_CREATED_HINT'),
				'disabled' => true,
			),
			'dt_last_modified' => array(
				'__tid__' => 'system',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POSTS_DT_LAST_MODIFIED_LABEL'),
				'hint' => Text::_('MOD_POSTS_POSTS_DT_LAST_MODIFIED_HINT'),
				'disabled' => true,
			),
			'ip_created' => array(
				'__tid__' => 'system',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POSTS_IP_CREATED_LABEL'),
				'hint' => Text::_('MOD_POSTS_POSTS_IP_CREATED_HINT'),
				'disabled' => true,
			),
			'ip_last_modified' => array(
				'__tid__' => 'system',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POSTS_IP_LAST_MODIFIED_LABEL'),
				'hint' => Text::_('MOD_POSTS_POSTS_IP_LAST_MODIFIED_HINT'),
				'disabled' => true,
			),
			'trash' => array(
				'__tid__' => 'main',
				'type' => 'switch',
				'label' => Text::_('MOD_POSTS_POSTS_TRASH_LABEL'),
				'hint' => Text::_('MOD_POSTS_POSTS_TRASH_HINT'),
				'options' => DataPosts::getTrashEnum(),
				'value' => DataPosts::TRASH_N,
			),
			'order' => array(
				'type' => 'select',
				'label' => Text::_('CFG_SYSTEM_GLOBAL_ORDER'),
				'options' => array(
					'hits DESC' => Text::_('MOD_POSTS_POSTS_HITS_LABEL'),
					'praise_count DESC' => Text::_('MOD_POSTS_POSTS_PRAISE_COUNT_LABEL'),
					'comment_count DESC' => Text::_('MOD_POSTS_POSTS_COMMENT_COUNT_LABEL'),
					'dt_created DESC' => Text::_('MOD_POSTS_POSTS_DT_CREATED_LABEL'),
					'sort' => Text::_('MOD_POSTS_POSTS_SORT_LABEL'),
				)
			),
			'dt_created_ge' => array(
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POSTS_DT_CREATED_GE_LABEL'),
			),
			'dt_created_le' => array(
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POSTS_DT_CREATED_LE_LABEL'),
			),
			'dt_last_modified_ge' => array(
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POSTS_DT_LAST_MODIFIED_GE_LABEL'),
			),
			'dt_last_modified_le' => array(
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POSTS_DT_LAST_MODIFIED_LE_LABEL'),
			),
		);

		if (Mvc::$action === 'modify' || Mvc::$action === 'view') {
			$id = Ap::getRequest()->getInteger('id');
			if ($id > 0) {
				$fields = $this->getModuleFieldsByPostId($id);
				foreach ($fields as $name => $field) {
					$fields[$name]['__tid__'] = 'profile';
					$fields[$name]['type'] = 'textarea';
				}

				$output = array_merge($output, $fields);
			}
		}

		return $output;
	}

	/**
	 * 获取列表页“文档标题”的A标签
	 * @param array $data
	 * @return string
	 */
	public function getTitleLink($data)
	{
		$params = array(
			'id' => $data['post_id'],
		);

		$url = $this->urlManager->getUrl($this->actNameView, $this->controller, $this->module, $params);
		$output = $this->html->a($data['title'], $url);
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
	 * (non-PHPdoc)
	 * @see \library\BaseModel::search()
	 */
	public function search(array $params = array(), $order = '', $limit = null, $offset = null)
	{
		if (isset($params['alias']) && $params['alias'] === '') {
			unset($params['alias']);
		}

		if (isset($params['keywords']) && $params['keywords'] === '') {
			unset($params['keywords']);
		}

		if (isset($params['jump_url']) && $params['jump_url'] === '') {
			unset($params['jump_url']);
		}

		if (isset($params['ip_created']) && $params['ip_created'] === '') {
			unset($params['ip_created']);
		}

		if (isset($params['ip_last_modified']) && $params['ip_last_modified'] === '') {
			unset($params['ip_last_modified']);
		}

		if ($order === '') {
			$order = 'sort';
		}

		return parent::search($params, $order, $limit, $offset);
	}

	/**
	 * 新增一条记录
	 * @param array $params
	 * @param boolean $ignore
	 * @return array
	 */
	public function create(array $params = array(), $ignore = false)
	{
		$params['creator_id'] = Identity::getUserId();
		if (isset($params['content'])) {
			$params['content'] = String::stripslashes($params['content']);
		}

		return parent::create($params, $ignore);
	}

	/**
	 * 通过主键，编辑一条记录
	 * @param integer $id
	 * @param array $params
	 * @return array
	 */
	public function modifyByPk($id, array $params = array())
	{
		$params['last_modifier_id'] = Identity::getUserId();
		if (isset($params['content'])) {
			$params['content'] = String::stripslashes($params['content']);
		}

		return parent::modifyByPk($id, $params);
	}

	/**
	 * 通过“主键ID”，获取“模型名称”
	 * @param integer $moduleId
	 * @return string
	 */
	public function getModuleNameByModuleId($moduleId)
	{
		return Model::getInstance('Modules')->getModuleNameByModuleId($moduleId);
	}

	/**
	 * 获取“是否头条”
	 * @param string $isHead
	 * @return string
	 */
	public function getIsHeadLangByIsHead($isHead)
	{
		$ret = $this->getService()->getIsHeadLangByIsHead($isHead);
		return $ret;
	}

	/**
	 * 获取“是否推荐”
	 * @param string $isRecommend
	 * @return string
	 */
	public function getIsRecommendLangByIsRecommend($isRecommend)
	{
		$ret = $this->getService()->getIsRecommendLangByIsRecommend($isRecommend);
		return $ret;
	}

	/**
	 * 获取“是否发表”
	 * @param string $isPublished
	 * @return string
	 */
	public function getIsPublishedLangByIsPublished($isPublished)
	{
		$ret = $this->getService()->getIsPublishedLangByIsPublished($isPublished);
		return $ret;
	}

	/**
	 * 获取“评论设置”
	 * @param string $commentStatus
	 * @return string
	 */
	public function getCommentStatusLangByCommentStatus($commentStatus)
	{
		$ret = $this->getService()->getCommentStatusLangByCommentStatus($commentStatus);
		return $ret;
	}

	/**
	 * 通过“主键ID”，获取“文档标题”
	 * @param integer $postId
	 * @return string
	 */
	public function getTitleByPostId($postId)
	{
		$ret = $this->getService()->getTitleByPostId($postId);
		return $ret;
	}

	/**
	 * 通过“主键ID”，获取“文档扩展字段”
	 * @param integer $postId
	 * @return array
	 */
	public function getModuleFieldsByPostId($postId)
	{
		$ret = $this->getService()->getModuleFieldsByPostId($postId);
		return $ret;
	}

	/**
	 * 递归方式获取所有的类别名，默认用空格填充子类别左边用于和父类别错位
	 * （只返回ID和类别名的键值对）（可用于Select表单的Option选项）
	 * @param integer $categoryPid
	 * @param string $padStr
	 * @param string $leftPad
	 * @param string $rightPad
	 * @return array
	 */
	public function getCategoryNames($categoryPid = 0, $padStr = '&nbsp;&nbsp;&nbsp;&nbsp;', $leftPad = '', $rightPad = null)
	{
		return Model::getInstance('Categories')->getOptions($categoryPid, $padStr, $leftPad, $rightPad);
	}

	/**
	 * 获取所有的模型名称
	 * @return array
	 */
	public function getModuleNames()
	{
		return Model::getInstance('Modules')->getModuleNames();
	}

	/**
	 * 获取所有的文档扩展字段
	 * @return array
	 */
	public function getFields()
	{
		return Model::getInstance('Modules')->getFields();
	}
}
