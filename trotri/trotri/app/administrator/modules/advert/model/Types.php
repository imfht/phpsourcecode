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
use tfc\saf\Text;
use advert\services\DataTypes;

/**
 * Types class file
 * 广告位置
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Types.php 1 2014-10-24 10:02:53Z Code Generator $
 * @package modules.advert.model
 * @since 1.0
 */
class Types extends BaseModel
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
			'type_id' => array(
				'__tid__' => 'main',
				'type' => 'hidden',
				'label' => Text::_('MOD_ADVERT_ADVERT_TYPES_TYPE_ID_LABEL'),
				'hint' => Text::_('MOD_ADVERT_ADVERT_TYPES_TYPE_ID_HINT'),
			),
			'type_name' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_ADVERT_ADVERT_TYPES_TYPE_NAME_LABEL'),
				'hint' => Text::_('MOD_ADVERT_ADVERT_TYPES_TYPE_NAME_HINT'),
				'required' => true,
			),
			'type_key' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_ADVERT_ADVERT_TYPES_TYPE_KEY_LABEL'),
				'hint' => Text::_('MOD_ADVERT_ADVERT_TYPES_TYPE_KEY_HINT'),
				'required' => true,
			),
			'picture' => array(
				'__tid__' => 'main',
				'__object__' => 'views\\bootstrap\\advert\\AdvertTypesRadioElement',
				'type' => 'radio',
				'label' => Text::_('MOD_ADVERT_ADVERT_TYPES_PICTURE_LABEL'),
				'hint' => Text::_('MOD_ADVERT_ADVERT_TYPES_PICTURE_HINT'),
				'options' => DataTypes::getPictureEnum(),
				'value' => DataTypes::PICTURE_DEFAULT,
			),
			'description' => array(
				'__tid__' => 'main',
				'type' => 'textarea',
				'label' => Text::_('MOD_ADVERT_ADVERT_TYPES_DESCRIPTION_LABEL'),
				'hint' => Text::_('MOD_ADVERT_ADVERT_TYPES_DESCRIPTION_HINT'),
			),
			'advert_count' => array(
				'label' => Text::_('MOD_ADVERT_ADVERT_TYPES_ADVERT_COUNT_LABEL'),
			),
			'adverts' => array(
				'label' => Text::_('MOD_ADVERT_URLS_ADVERTS_INDEX'),
			)
		);

		return $output;
	}

	/**
	 * 获取列表页“位置名”的A标签
	 * @param array $data
	 * @return string
	 */
	public function getTypeNameLink($data)
	{
		$params = array(
			'id' => $data['type_id'],
		);

		$url = $this->urlManager->getUrl($this->actNameView, $this->controller, $this->module, $params);
		$output = $this->html->a($data['type_name'], $url);
		return $output;
	}

	/**
	 * 通过“类型Key”，获取“类型名”
	 * @param string $typeKey
	 * @return string
	 */
	public function getTypeNameByTypeKey($typeKey)
	{
		return $this->getService()->getTypeNameByTypeKey($typeKey);
	}

	/**
	 * 通过类型Key，查询广告数
	 * @param string $typeKey
	 * @return integer
	 */
	public function getAdvertCount($typeKey)
	{
		$count = $this->getService()->getAdvertCount($typeKey);
		return $count;
	}

	/**
	 * 通过“示例图片”，获取“示例图片名”
	 * @param string $picture
	 * @return string
	 */
	public function getPictureLangByPicture($picture)
	{
		return $this->getService()->getPictureLangByPicture($picture);
	}
}
