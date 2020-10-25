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
use tfc\mvc\Mvc;
use tfc\saf\Text;
use member\services\DataSocial;

/**
 * Social class file
 * 会员详情
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Social.php 1 2014-12-01 15:43:12Z Code Generator $
 * @package modules.member.model
 * @since 1.0
 */
class Social extends BaseModel
{
	/**
	 * (non-PHPdoc)
	 * @see \library\BaseModel::getViewTabsRender()
	 */
	public function getViewTabsRender()
	{
		$output = array(
			'contact' => array(
				'tid' => 'contact',
				'prompt' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_VIEWTAB_CONTACT_PROMPT')
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
			'member_id' => array(
				'__tid__' => 'main',
				'type' => 'hidden',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_MEMBER_ID_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_MEMBER_ID_HINT'),
			),
			'login_name' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_LOGIN_NAME_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_LOGIN_NAME_HINT'),
				'required' => true,
			),
			'realname' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_REALNAME_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_REALNAME_HINT'),
			),
			'sex' => array(
				'__tid__' => 'main',
				'type' => 'radio',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_SEX_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_SEX_HINT'),
				'options' => DataSocial::getSexEnum(),
				'value' => DataSocial::SEX_MALE,
			),
			'birth_ymd' => array(
				'__tid__' => 'main',
				'type' => 'datetimepicker',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_BIRTH_YMD_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_BIRTH_YMD_HINT'),
				'format' => 'date'
			),
			'is_pub_birth' => array(
				'__tid__' => 'main',
				'type' => 'switch',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_IS_PUB_BIRTH_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_IS_PUB_BIRTH_HINT'),
				'options' => DataSocial::getIsPubBirthEnum(),
				'value' => DataSocial::IS_PUB_BIRTH_Y,
			),
			'birth_md' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_BIRTH_MD_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_BIRTH_MD_HINT'),
			),
			'anniversary' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_ANNIVERSARY_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_ANNIVERSARY_HINT'),
			),
			'is_pub_anniversary' => array(
				'__tid__' => 'main',
				'type' => 'switch',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_IS_PUB_ANNIVERSARY_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_IS_PUB_ANNIVERSARY_HINT'),
				'options' => DataSocial::getIsPubAnniversaryEnum(),
				'value' => DataSocial::IS_PUB_ANNIVERSARY_Y,
			),
			'head_portrait' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_HEAD_PORTRAIT_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_HEAD_PORTRAIT_HINT'),
			),
			'head_portrait_file' => array(
				'__tid__' => 'main',
				'type' => 'string',
				'label' => '',
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_HEAD_PORTRAIT_HINT'),
				'value' => '<div id="head_portrait_file" url="' . $urlManager->getUrl('ajaxupload', '', '', array('from' => 'picture')) . '" name="upload">' . Text::_('CFG_SYSTEM_GLOBAL_UPLOAD') . '</div>',
			),
			'introduce' => array(
				'__tid__' => 'main',
				'type' => 'textarea',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_INTRODUCE_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_INTRODUCE_HINT'),
			),
			'interests' => array(
				'__tid__' => 'main',
				'type' => 'checkbox',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_INTERESTS_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_INTERESTS_HINT'),
				'options' => DataSocial::getInterestsEnum(),
			),
			'is_pub_interests' => array(
				'__tid__' => 'main',
				'type' => 'switch',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_IS_PUB_INTERESTS_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_IS_PUB_INTERESTS_HINT'),
				'options' => DataSocial::getIsPubInterestsEnum(),
				'value' => DataSocial::IS_PUB_INTERESTS_Y,
			),
			'telephone' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_TELEPHONE_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_TELEPHONE_HINT'),
			),
			'mobiphone' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_MOBIPHONE_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_MOBIPHONE_HINT'),
			),
			'is_pub_mobiphone' => array(
				'__tid__' => 'main',
				'type' => 'switch',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_IS_PUB_MOBIPHONE_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_IS_PUB_MOBIPHONE_HINT'),
				'options' => DataSocial::getIsPubMobiphoneEnum(),
				'value' => DataSocial::IS_PUB_MOBIPHONE_Y,
			),
			'email' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_EMAIL_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_EMAIL_HINT'),
			),
			'is_pub_email' => array(
				'__tid__' => 'main',
				'type' => 'switch',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_IS_PUB_EMAIL_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_IS_PUB_EMAIL_HINT'),
				'options' => DataSocial::getIsPubEmailEnum(),
				'value' => DataSocial::IS_PUB_EMAIL_Y,
			),
			'live_country_id' => array(
				'__tid__' => 'main',
				'type' => 'select',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_LIVE_COUNTRY_ID_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_LIVE_COUNTRY_ID_HINT'),
			),
			'live_province_id' => array(
				'__tid__' => 'main',
				'type' => 'select',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_LIVE_PROVINCE_ID_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_LIVE_PROVINCE_ID_HINT'),
			),
			'live_city_id' => array(
				'__tid__' => 'main',
				'type' => 'select',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_LIVE_CITY_ID_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_LIVE_CITY_ID_HINT'),
			),
			'live_district_id' => array(
				'__tid__' => 'main',
				'type' => 'select',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_LIVE_DISTRICT_ID_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_LIVE_DISTRICT_ID_HINT'),
			),
			'live_country' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_LIVE_COUNTRY_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_LIVE_COUNTRY_HINT'),
			),
			'live_province' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_LIVE_PROVINCE_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_LIVE_PROVINCE_HINT'),
			),
			'live_city' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_LIVE_CITY_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_LIVE_CITY_HINT'),
			),
			'live_district' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_LIVE_DISTRICT_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_LIVE_DISTRICT_HINT'),
			),
			'live_street' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_LIVE_STREET_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_LIVE_STREET_HINT'),
			),
			'live_zipcode' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_LIVE_ZIPCODE_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_LIVE_ZIPCODE_HINT'),
			),
			'address_country_id' => array(
				'__tid__' => 'main',
				'type' => 'select',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_ADDRESS_COUNTRY_ID_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_ADDRESS_COUNTRY_ID_HINT'),
			),
			'address_province_id' => array(
				'__tid__' => 'main',
				'type' => 'select',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_ADDRESS_PROVINCE_ID_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_ADDRESS_PROVINCE_ID_HINT'),
			),
			'address_city_id' => array(
				'__tid__' => 'main',
				'type' => 'select',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_ADDRESS_CITY_ID_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_ADDRESS_CITY_ID_HINT'),
			),
			'address_district_id' => array(
				'__tid__' => 'main',
				'type' => 'select',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_ADDRESS_DISTRICT_ID_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_ADDRESS_DISTRICT_ID_HINT'),
			),
			'address_country' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_ADDRESS_COUNTRY_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_ADDRESS_COUNTRY_HINT'),
			),
			'address_province' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_ADDRESS_PROVINCE_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_ADDRESS_PROVINCE_HINT'),
			),
			'address_city' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_ADDRESS_CITY_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_ADDRESS_CITY_HINT'),
			),
			'address_district' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_ADDRESS_DISTRICT_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_ADDRESS_DISTRICT_HINT'),
			),
			'address_street' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_ADDRESS_STREET_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_ADDRESS_STREET_HINT'),
			),
			'address_zipcode' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_ADDRESS_ZIPCODE_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_ADDRESS_ZIPCODE_HINT'),
			),
			'qq' => array(
				'__tid__' => 'contact',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_QQ_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_QQ_HINT'),
			),
			'msn' => array(
				'__tid__' => 'contact',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_MSN_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_MSN_HINT'),
			),
			'skypeid' => array(
				'__tid__' => 'contact',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_SKYPEID_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_SKYPEID_HINT'),
			),
			'wangwang' => array(
				'__tid__' => 'contact',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_WANGWANG_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_WANGWANG_HINT'),
			),
			'weibo' => array(
				'__tid__' => 'contact',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_WEIBO_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_WEIBO_HINT'),
			),
			'blog' => array(
				'__tid__' => 'contact',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_BLOG_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_BLOG_HINT'),
			),
			'website' => array(
				'__tid__' => 'contact',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_WEBSITE_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_WEBSITE_HINT'),
			),
			'fax' => array(
				'__tid__' => 'contact',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_FAX_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_SOCIAL_FAX_HINT'),
			),
			'addresses' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_URLS_ADDRESSES_INDEX'),
				'hint' => Text::_('MOD_MEMBER_URLS_ADDRESSES_CREATE'),
			),
		);

		return $output;
	}

	/**
	 * 获取列表页“登录名：邮箱|用户名|手机号|第三方OpenID”的A标签
	 * @param array $data
	 * @return string
	 */
	public function getLoginNameLink($data)
	{
		$params = array(
			'id' => $data['member_id'],
		);

		$url = $this->urlManager->getUrl($this->actNameView, $this->controller, $this->module, $params);
		$output = $this->html->a($data['login_name'], $url);
		return $output;
	}

	/**
	 * 通过“性别”，获取“性别名”
	 * @param string $sex
	 * @return string
	 */
	public function getSexLangBySex($sex)
	{
		return $this->getService()->getSexLangBySex($sex);
	}

}
