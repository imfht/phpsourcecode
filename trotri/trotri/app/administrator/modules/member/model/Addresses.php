<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\member\model;

use library\BaseModel;
use tfc\ap\Ap;
use tfc\saf\Text;
use member\services\DataAddresses;

/**
 * Addresses class file
 * 会员收货地址
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Addresses.php 1 2014-12-04 14:57:46Z Code Generator $
 * @package modules.member.model
 * @since 1.0
 */
class Addresses extends BaseModel
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
				'prompt' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_VIEWTAB_SYSTEM_PROMPT')
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
		$output = array(
			'address_id' => array(
				'__tid__' => 'main',
				'type' => 'hidden',
				'label' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_ADDRESS_ID_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_ADDRESS_ID_HINT'),
			),
			'address_name' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_ADDRESS_NAME_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_ADDRESS_NAME_HINT'),
				'disable' => true
			),
			'member_id' => array(
				'__tid__' => 'main',
				'type' => 'hidden',
				'label' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_MEMBER_ID_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_MEMBER_ID_HINT'),
			),
			'consignee' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_CONSIGNEE_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_CONSIGNEE_HINT'),
				'required' => true,
			),
			'mobiphone' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_MOBIPHONE_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_MOBIPHONE_HINT'),
			),
			'telephone' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_TELEPHONE_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_TELEPHONE_HINT'),
			),
			'email' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_EMAIL_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_EMAIL_HINT'),
			),
			'addr_country_id' => array(
				'__tid__' => 'main',
				'type' => 'select',
				'label' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_ADDR_COUNTRY_ID_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_ADDR_COUNTRY_ID_HINT'),
			),
			'addr_country' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_ADDR_COUNTRY_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_ADDR_COUNTRY_HINT'),
			),
			'addr_province_id' => array(
				'__tid__' => 'main',
				'type' => 'select',
				'label' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_ADDR_PROVINCE_ID_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_ADDR_PROVINCE_ID_HINT'),
			),
			'addr_province' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_ADDR_PROVINCE_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_ADDR_PROVINCE_HINT'),
			),
			'addr_city_id' => array(
				'__tid__' => 'main',
				'type' => 'select',
				'label' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_ADDR_CITY_ID_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_ADDR_CITY_ID_HINT'),
			),
			'addr_city' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_ADDR_CITY_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_ADDR_CITY_HINT'),
			),
			'addr_district_id' => array(
				'__tid__' => 'main',
				'type' => 'select',
				'label' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_ADDR_DISTRICT_ID_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_ADDR_DISTRICT_ID_HINT'),
			),
			'addr_district' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_ADDR_DISTRICT_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_ADDR_DISTRICT_HINT'),
			),
			'addr_street' => array(
				'__tid__' => 'main',
				'type' => 'textarea',
				'label' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_ADDR_STREET_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_ADDR_STREET_HINT'),
				'required' => true,
			),
			'addr_zipcode' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_ADDR_ZIPCODE_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_ADDR_ZIPCODE_HINT'),
			),
			'when' => array(
				'__tid__' => 'main',
				'type' => 'radio',
				'label' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_WHEN_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_WHEN_HINT'),
				'options' => DataAddresses::getWhenEnum(),
				'value' => DataAddresses::WHEN_ANYONE,
			),
			'is_default' => array(
				'__tid__' => 'main',
				'type' => 'switch',
				'label' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_IS_DEFAULT_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_IS_DEFAULT_HINT'),
				'options' => DataAddresses::getIsDefaultEnum(),
				'value' => DataAddresses::IS_DEFAULT_Y,
			),
			'dt_created' => array(
				'__tid__' => 'system',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_DT_CREATED_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_DT_CREATED_HINT'),
				'disabled' => true,
			),
			'dt_last_modified' => array(
				'__tid__' => 'system',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_DT_LAST_MODIFIED_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_ADDRESSES_DT_LAST_MODIFIED_HINT'),
				'disabled' => true,
			),
		);

		return $output;
	}

	/**
	 * 获取列表页“地址名”的A标签
	 * @param array $data
	 * @return string
	 */
	public function getAddressNameLink($data)
	{
		$params = array(
			'id' => $data['address_id'],
		);

		$url = $this->urlManager->getUrl($this->actNameView, $this->controller, $this->module, $params);
		$output = $this->html->a($data['address_name'], $url);
		return $output;
	}

	/**
	 * 获取member_id值
	 * @return integer
	 */
	public function getMemberId()
	{
		$memberId = Ap::getRequest()->getInteger('member_id');
		if ($memberId <= 0) {
			$id = Ap::getRequest()->getInteger('id');
			$memberId = $this->getService()->getMemberIdByAddressId($id);
		}

		return $memberId;
	}

	/**
	 * 查询全部记录
	 * @param integer $memberId
	 * @return array
	 */
	public function findAll($memberId)
	{
		$ret = $this->callFetchMethod($this->getService(), 'findAll', array($memberId));
		return $ret;
	}

	/**
	 * 获取“收货最佳时间”
	 * @param string $when
	 * @return string
	 */
	public function getWhenLangByWhen($when)
	{
		$ret = $this->getService()->getWhenLangByWhen($when);
		return $ret;
	}
}
