<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\advert\model;

use library\BaseModel;
use tfc\ap\Ap;
use tfc\mvc\Mvc;
use tfc\util\String;
use tfc\saf\Text;
use libapp\Model;
use advert\services\DataAdverts;

/**
 * Adverts class file
 * 广告管理
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Adverts.php 1 2014-10-26 19:08:03Z Code Generator $
 * @package modules.advert.model
 * @since 1.0
 */
class Adverts extends BaseModel
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
				'prompt' => Text::_('MOD_ADVERT_ADVERTS_VIEWTAB_ADVANCED_PROMPT')
			),
			'system' => array(
				'tid' => 'system',
				'prompt' => Text::_('MOD_ADVERT_ADVERTS_VIEWTAB_SYSTEM_PROMPT')
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
			'advert_id' => array(
				'__tid__' => 'main',
				'type' => 'hidden',
				'label' => Text::_('MOD_ADVERT_ADVERTS_ADVERT_ID_LABEL'),
				'hint' => Text::_('MOD_ADVERT_ADVERTS_ADVERT_ID_HINT'),
			),
			'advert_name' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_ADVERT_ADVERTS_ADVERT_NAME_LABEL'),
				'hint' => Text::_('MOD_ADVERT_ADVERTS_ADVERT_NAME_HINT'),
				'required' => true,
			),
			'type_name' => array(
				'__tid__' => 'main',
				'type' => 'string',
				'label' => Text::_('MOD_ADVERT_ADVERT_TYPES_TYPE_NAME_LABEL'),
			),
			'type_key' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_ADVERT_ADVERTS_TYPE_KEY_LABEL'),
				'hint' => Text::_('MOD_ADVERT_ADVERTS_TYPE_KEY_HINT'),
				'readonly' => true,
			),
			'description' => array(
				'__tid__' => 'main',
				'type' => 'textarea',
				'label' => Text::_('MOD_ADVERT_ADVERTS_DESCRIPTION_LABEL'),
				'hint' => Text::_('MOD_ADVERT_ADVERTS_DESCRIPTION_HINT'),
			),
			'is_published' => array(
				'__tid__' => 'main',
				'type' => 'switch',
				'label' => Text::_('MOD_ADVERT_ADVERTS_IS_PUBLISHED_LABEL'),
				'hint' => Text::_('MOD_ADVERT_ADVERTS_IS_PUBLISHED_HINT'),
				'options' => DataAdverts::getIsPublishedEnum(),
				'value' => DataAdverts::IS_PUBLISHED_Y,
			),
			'dt_publish_up' => array(
				'__tid__' => 'main',
				'type' => 'datetimepicker',
				'label' => Text::_('MOD_ADVERT_ADVERTS_DT_PUBLISH_UP_LABEL'),
				'hint' => Text::_('MOD_ADVERT_ADVERTS_DT_PUBLISH_UP_HINT'),
				'value' => $nowTime
			),
			'dt_publish_down' => array(
				'__tid__' => 'main',
				'type' => 'datetimepicker',
				'label' => Text::_('MOD_ADVERT_ADVERTS_DT_PUBLISH_DOWN_LABEL'),
				'hint' => Text::_('MOD_ADVERT_ADVERTS_DT_PUBLISH_DOWN_HINT'),
			),
			'sort' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_ADVERT_ADVERTS_SORT_LABEL'),
				'hint' => Text::_('MOD_ADVERT_ADVERTS_SORT_HINT'),
				'required' => true,
				'value' => 1000
			),
			'show_type' => array(
				'__tid__' => 'advanced',
				'type' => 'radio',
				'label' => Text::_('MOD_ADVERT_ADVERTS_SHOW_TYPE_LABEL'),
				'hint' => Text::_('MOD_ADVERT_ADVERTS_SHOW_TYPE_HINT'),
				'options' => DataAdverts::getShowTypeEnum(),
				'value' => DataAdverts::SHOW_TYPE_IMAGE,
			),
			'show_code' => array(
				'__tid__' => 'advanced',
				'type' => 'textarea',
				'label' => Text::_('MOD_ADVERT_ADVERTS_SHOW_CODE_LABEL'),
				'hint' => Text::_('MOD_ADVERT_ADVERTS_SHOW_CODE_HINT'),
				'required' => true,
				'rows' => 16
			),
			'title' => array(
				'__tid__' => 'advanced',
				'type' => 'text',
				'label' => Text::_('MOD_ADVERT_ADVERTS_TITLE_LABEL'),
				'hint' => Text::_('MOD_ADVERT_ADVERTS_TITLE_HINT'),
				'required' => true,
			),
			'advert_url' => array(
				'__tid__' => 'advanced',
				'type' => 'textarea',
				'label' => Text::_('MOD_ADVERT_ADVERTS_ADVERT_URL_LABEL'),
				'hint' => Text::_('MOD_ADVERT_ADVERTS_ADVERT_URL_HINT'),
				'required' => true,
				'rows' => 3
			),
			'advert_src' => array(
				'__tid__' => 'advanced',
				'type' => 'text',
				'label' => Text::_('MOD_ADVERT_ADVERTS_ADVERT_SRC_LABEL'),
				'hint' => Text::_('MOD_ADVERT_ADVERTS_ADVERT_SRC_HINT'),
				'required' => true,
			),
			'advert_src_file' => array(
				'__tid__' => 'advanced',
				'type' => 'string',
				'label' => '',
				'hint' => '',
				'value' => '<div id="advert_src_file" url="' . $urlManager->getUrl('ajaxupload', '', '') . '" name="upload">' . Text::_('CFG_SYSTEM_GLOBAL_UPLOAD') . '</div>',
			),
			'advert_src2' => array(
				'__tid__' => 'advanced',
				'type' => 'text',
				'label' => Text::_('MOD_ADVERT_ADVERTS_ADVERT_SRC2_LABEL'),
				'hint' => Text::_('MOD_ADVERT_ADVERTS_ADVERT_SRC2_HINT'),
			),
			'advert_src2_file' => array(
				'__tid__' => 'advanced',
				'type' => 'string',
				'label' => '',
				'hint' => '',
				'value' => '<div id="advert_src2_file" url="' . $urlManager->getUrl('ajaxupload', '', '') . '" name="upload">' . Text::_('CFG_SYSTEM_GLOBAL_UPLOAD') . '</div>',
			),
			'attr_alt' => array(
				'__tid__' => 'advanced',
				'type' => 'text',
				'label' => Text::_('MOD_ADVERT_ADVERTS_ATTR_ALT_LABEL'),
				'hint' => Text::_('MOD_ADVERT_ADVERTS_ATTR_ALT_HINT'),
				'required' => true,
			),
			'attr_width' => array(
				'__tid__' => 'advanced',
				'type' => 'text',
				'label' => Text::_('MOD_ADVERT_ADVERTS_ATTR_WIDTH_LABEL'),
				'hint' => Text::_('MOD_ADVERT_ADVERTS_ATTR_WIDTH_HINT'),
				'value' => 0
			),
			'attr_height' => array(
				'__tid__' => 'advanced',
				'type' => 'text',
				'label' => Text::_('MOD_ADVERT_ADVERTS_ATTR_HEIGHT_LABEL'),
				'hint' => Text::_('MOD_ADVERT_ADVERTS_ATTR_HEIGHT_HINT'),
				'value' => 0
			),
			'attr_fontsize' => array(
				'__tid__' => 'advanced',
				'type' => 'text',
				'label' => Text::_('MOD_ADVERT_ADVERTS_ATTR_FONTSIZE_LABEL'),
				'hint' => Text::_('MOD_ADVERT_ADVERTS_ATTR_FONTSIZE_HINT'),
			),
			'attr_target' => array(
				'__tid__' => 'advanced',
				'type' => 'text',
				'label' => Text::_('MOD_ADVERT_ADVERTS_ATTR_TARGET_LABEL'),
				'hint' => Text::_('MOD_ADVERT_ADVERTS_ATTR_TARGET_HINT'),
				'value' => '_blank'
			),
			'dt_created' => array(
				'__tid__' => 'system',
				'type' => 'text',
				'label' => Text::_('MOD_ADVERT_ADVERTS_DT_CREATED_LABEL'),
				'hint' => Text::_('MOD_ADVERT_ADVERTS_DT_CREATED_HINT'),
				'disabled' => true,
			),
		);

		return $output;
	}

	/**
	 * 获取列表页“广告名”的A标签
	 * @param array $data
	 * @return string
	 */
	public function getAdvertNameLink($data)
	{
		$params = array(
			'id' => $data['advert_id'],
		);

		$url = $this->urlManager->getUrl($this->actNameView, $this->controller, $this->module, $params);
		$output = $this->html->a($data['advert_name'], $url);
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
		if (isset($params['show_code'])) {
			$params['show_code'] = String::stripslashes($params['show_code']);
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
		if (isset($params['show_code'])) {
			$params['show_code'] = String::stripslashes($params['show_code']);
		}

		return parent::modifyByPk($id, $params);
	}

	/**
	 * 获取type_key值
	 * @return string
	 */
	public function getTypeKey()
	{
		$typeKey = Ap::getRequest()->getTrim('type_key');
		if ($typeKey === '') {
			$id = Ap::getRequest()->getInteger('id');
			$typeKey = $this->getService()->getTypeKeyByAdvertId($id);
		}

		return $typeKey;
	}

	/**
	 * 通过“位置Key”获取“位置名”
	 * @param string $typeKey
	 * @return string
	 */
	public function getTypeNameByTypeKey($typeKey)
	{
		return Model::getInstance('Types')->getTypeNameByTypeKey($typeKey);
	}

	/**
	 * 通过“展现方式”，获取“展现方式名”
	 * @param string $showType
	 * @return string
	 */
	public function getShowTypeLangByShowType($showType)
	{
		return $this->getService()->getShowTypeLangByShowType($showType);
	}
}
