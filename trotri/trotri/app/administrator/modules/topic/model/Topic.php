<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\topic\model;

use library\BaseModel;
use tfc\mvc\Mvc;
use tfc\util\String;
use tfc\saf\Text;
use topic\services\DataTopic;

/**
 * Topic class file
 * 专题管理
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Topic.php 1 2014-11-04 17:38:30Z Code Generator $
 * @package modules.topic.model
 * @since 1.0
 */
class Topic extends BaseModel
{
	/**
	 * (non-PHPdoc)
	 * @see \library\BaseModel::getViewTabsRender()
	 */
	public function getViewTabsRender()
	{
		$output = array(
			'system' => array(
				'tid' => 'system',
				'prompt' => Text::_('MOD_TOPIC_TOPIC_VIEWTAB_SYSTEM_PROMPT')
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
		$output = array(
			'topic_id' => array(
				'__tid__' => 'main',
				'type' => 'hidden',
				'label' => Text::_('MOD_TOPIC_TOPIC_TOPIC_ID_LABEL'),
				'hint' => Text::_('MOD_TOPIC_TOPIC_TOPIC_ID_HINT'),
			),
			'topic_name' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_TOPIC_TOPIC_TOPIC_NAME_LABEL'),
				'hint' => Text::_('MOD_TOPIC_TOPIC_TOPIC_NAME_HINT'),
				'required' => true,
			),
			'topic_key' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_TOPIC_TOPIC_TOPIC_KEY_LABEL'),
				'hint' => Text::_('MOD_TOPIC_TOPIC_TOPIC_KEY_HINT'),
				'required' => true,
			),
			'cover' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_TOPIC_TOPIC_COVER_LABEL'),
				'hint' => Text::_('MOD_TOPIC_TOPIC_COVER_HINT'),
				'required' => true,
			),
			'cover_file' => array(
				'__tid__' => 'main',
				'type' => 'string',
				'label' => '',
				'hint' => '',
				'value' => '<div id="cover_file" url="' . $urlManager->getUrl('ajaxupload', '', '') . '" name="upload">' . Text::_('CFG_SYSTEM_GLOBAL_UPLOAD') . '</div>',
			),
			'meta_title' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_TOPIC_TOPIC_META_TITLE_LABEL'),
				'hint' => Text::_('MOD_TOPIC_TOPIC_META_TITLE_HINT'),
				'required' => true,
			),
			'meta_keywords' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_TOPIC_TOPIC_META_KEYWORDS_LABEL'),
				'hint' => Text::_('MOD_TOPIC_TOPIC_META_KEYWORDS_HINT'),
				'required' => true,
			),
			'meta_description' => array(
				'__tid__' => 'main',
				'type' => 'textarea',
				'label' => Text::_('MOD_TOPIC_TOPIC_META_DESCRIPTION_LABEL'),
				'hint' => Text::_('MOD_TOPIC_TOPIC_META_DESCRIPTION_HINT'),
				'required' => true,
			),
			'html_style' => array(
				'__tid__' => 'main',
				'type' => 'textarea',
				'label' => Text::_('MOD_TOPIC_TOPIC_HTML_STYLE_LABEL'),
				'hint' => Text::_('MOD_TOPIC_TOPIC_HTML_STYLE_HINT'),
			),
			'html_script' => array(
				'__tid__' => 'main',
				'type' => 'textarea',
				'label' => Text::_('MOD_TOPIC_TOPIC_HTML_SCRIPT_LABEL'),
				'hint' => Text::_('MOD_TOPIC_TOPIC_HTML_SCRIPT_HINT'),
			),
			'html_head' => array(
				'__tid__' => 'main',
				'type' => 'textarea',
				'label' => Text::_('MOD_TOPIC_TOPIC_HTML_HEAD_LABEL'),
				'hint' => Text::_('MOD_TOPIC_TOPIC_HTML_HEAD_HINT'),
			),
			'html_body' => array(
				'__tid__' => 'main',
				'type' => 'textarea',
				'label' => Text::_('MOD_TOPIC_TOPIC_HTML_BODY_LABEL'),
				'hint' => Text::_('MOD_TOPIC_TOPIC_HTML_BODY_HINT'),
				'required' => true,
				'rows' => 20
			),
			'is_published' => array(
				'__tid__' => 'main',
				'type' => 'switch',
				'label' => Text::_('MOD_TOPIC_TOPIC_IS_PUBLISHED_LABEL'),
				'hint' => Text::_('MOD_TOPIC_TOPIC_IS_PUBLISHED_HINT'),
				'options' => DataTopic::getIsPublishedEnum(),
				'value' => DataTopic::IS_PUBLISHED_Y,
			),
			'sort' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_TOPIC_TOPIC_SORT_LABEL'),
				'hint' => Text::_('MOD_TOPIC_TOPIC_SORT_HINT'),
				'required' => true,
			),
			'use_header' => array(
				'__tid__' => 'main',
				'type' => 'switch',
				'label' => Text::_('MOD_TOPIC_TOPIC_USE_HEADER_LABEL'),
				'hint' => Text::_('MOD_TOPIC_TOPIC_USE_HEADER_HINT'),
				'options' => DataTopic::getUseHeaderEnum(),
				'value' => DataTopic::USE_HEADER_Y,
			),
			'use_footer' => array(
				'__tid__' => 'main',
				'type' => 'switch',
				'label' => Text::_('MOD_TOPIC_TOPIC_USE_FOOTER_LABEL'),
				'hint' => Text::_('MOD_TOPIC_TOPIC_USE_FOOTER_HINT'),
				'options' => DataTopic::getUseFooterEnum(),
				'value' => DataTopic::USE_FOOTER_Y,
			),
			'dt_created' => array(
				'__tid__' => 'system',
				'type' => 'text',
				'label' => Text::_('MOD_TOPIC_TOPIC_DT_CREATED_LABEL'),
				'hint' => Text::_('MOD_TOPIC_TOPIC_DT_CREATED_HINT'),
				'disabled' => true,
			),
		);

		return $output;
	}

	/**
	 * 获取列表页“专题名”的A标签
	 * @param array $data
	 * @return string
	 */
	public function getTopicNameLink($data)
	{
		$params = array(
			'id' => $data['topic_id'],
		);

		$url = $this->urlManager->getUrl($this->actNameView, $this->controller, $this->module, $params);
		$output = $this->html->a($data['topic_name'], $url);
		return $output;
	}

	/**
	 * 新增一条记录
	 * @param array $params
	 * @param boolean $ignore
	 * @return array
	 */
	public function create(array $params = array(), $ignore = false)
	{
		if (isset($params['html_style'])) {
			$params['html_style'] = String::stripslashes($params['html_style']);
		}

		if (isset($params['html_script'])) {
			$params['html_script'] = String::stripslashes($params['html_script']);
		}

		if (isset($params['html_head'])) {
			$params['html_head'] = String::stripslashes($params['html_head']);
		}

		if (isset($params['html_body'])) {
			$params['html_body'] = String::stripslashes($params['html_body']);
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
		if (isset($params['html_style'])) {
			$params['html_style'] = String::stripslashes($params['html_style']);
		}

		if (isset($params['html_script'])) {
			$params['html_script'] = String::stripslashes($params['html_script']);
		}

		if (isset($params['html_head'])) {
			$params['html_head'] = String::stripslashes($params['html_head']);
		}

		if (isset($params['html_body'])) {
			$params['html_body'] = String::stripslashes($params['html_body']);
		}

		return parent::modifyByPk($id, $params);
	}
}
