<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace member\services;

use libsrv\AbstractService;
use libsrv\Service;
use member\library\Constant;

/**
 * Social class file
 * 业务层：业务处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Social.php 1 2014-12-01 11:37:11Z Code Generator $
 * @package member.services
 * @since 1.0
 */
class Social extends AbstractService
{
	/**
	 * 查询多条记录
	 * @param array $params
	 * @param string $order
	 * @param integer $limit
	 * @param integer $offset
	 * @param string $option
	 * @return array
	 */
	public function findAll(array $params = array(), $order = '', $limit = 0, $offset = 0, $option = '')
	{
		$limit = min(max((int) $limit, 1), Constant::FIND_MAX_LIMIT);
		$offset = max((int) $offset, 0);

		$rows = $this->getDb()->findAll($params, $order, $limit, $offset, $option);
		return $rows;
	}

	/**
	 * 通过主键，查询一条记录
	 * @param integer $memberId
	 * @return array
	 */
	public function findByPk($memberId)
	{
		$row = parent::findByPk($memberId);
		if (is_array($row) && isset($row['member_id'])) {
			$row['interests'] = ($row['interests'] !== '') ? explode(',', $row['interests']) : array();
			if (!$row['qq']) $row['qq'] = '';
			if ($row['birth_ymd'] === '0000-00-00') $row['birth_ymd'] = '';
		}

		return $row;
	}

	/**
	 * 通过主键，编辑一条记录
	 * @param integer|array $value
	 * @param array $params
	 * @return integer
	 */
	public function modifyByPk($value, array $params = array())
	{
		$srv = Service::getInstance('Regions', 'system');
		$columnNames = array(
			'live_country_id'     => 'live_country',
			'live_province_id'    => 'live_province',
			'live_city_id'        => 'live_city',
			'live_district_id'    => 'live_district',
			'address_country_id'  => 'address_country',
			'address_province_id' => 'address_province',
			'address_city_id'     => 'address_city',
			'address_district_id' => 'address_district'
		);

		foreach ($columnNames as $columnId => $columnName) {
			if (isset($params[$columnId])) {
				$params[$columnName] = $srv->getRegionNameByRegionId($params[$columnId]);
			}
		}

		if (isset($params['interests'])) {
			if (is_string($params['interests'])) {
				$params['interests'] = explode(',', $params['interests']);
			}
		}

		return parent::modifyByPk($value, $params);
	}

	/**
	 * 通过“性别”，获取“性别名”
	 * @param string $sex
	 * @return string
	 */
	public function getSexLangBySex($sex)
	{
		$enum = DataSocial::getSexEnum();
		return isset($enum[$sex]) ? $enum[$sex] : '';
	}

	/**
	 * 通过“主键ID”，获取“登录名：邮箱|用户名|手机号|第三方OpenID”
	 * @param integer $memberId
	 * @return string
	 */
	public function getLoginNameByMemberId($memberId)
	{
		$value = $this->getByPk('login_name', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“真实姓名”
	 * @param integer $memberId
	 * @return string
	 */
	public function getRealnameByMemberId($memberId)
	{
		$value = $this->getByPk('realname', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“性别”
	 * @param integer $memberId
	 * @return string
	 */
	public function getSexByMemberId($memberId)
	{
		$value = $this->getByPk('sex', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“出生日”
	 * @param integer $memberId
	 * @return string
	 */
	public function getBirthYmdByMemberId($memberId)
	{
		$value = $this->getByPk('birth_ymd', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“生日”
	 * @param integer $memberId
	 * @return string
	 */
	public function getBirthMdByMemberId($memberId)
	{
		$value = $this->getByPk('birth_md', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“是否公开生日”
	 * @param integer $memberId
	 * @return string
	 */
	public function getIsPubBirthByMemberId($memberId)
	{
		$value = $this->getByPk('is_pub_birth', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“纪念日”
	 * @param integer $memberId
	 * @return string
	 */
	public function getAnniversaryByMemberId($memberId)
	{
		$value = $this->getByPk('anniversary', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“是否公开纪念日”
	 * @param integer $memberId
	 * @return string
	 */
	public function getIsPubAnniversaryByMemberId($memberId)
	{
		$value = $this->getByPk('is_pub_anniversary', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“头像URL”
	 * @param integer $memberId
	 * @return string
	 */
	public function getHeadPortraitByMemberId($memberId)
	{
		$value = $this->getByPk('head_portrait', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“自我介绍”
	 * @param integer $memberId
	 * @return string
	 */
	public function getIntroduceByMemberId($memberId)
	{
		$value = $this->getByPk('introduce', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“兴趣爱好”
	 * @param integer $memberId
	 * @return string
	 */
	public function getInterestsByMemberId($memberId)
	{
		$value = $this->getByPk('interests', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“是否公开兴趣爱好”
	 * @param integer $memberId
	 * @return string
	 */
	public function getIsPubInterestsByMemberId($memberId)
	{
		$value = $this->getByPk('is_pub_interests', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“固定电话”
	 * @param integer $memberId
	 * @return string
	 */
	public function getTelephoneByMemberId($memberId)
	{
		$value = $this->getByPk('telephone', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“备用手机号”
	 * @param integer $memberId
	 * @return string
	 */
	public function getMobiphoneByMemberId($memberId)
	{
		$value = $this->getByPk('mobiphone', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“是否公开手机号”
	 * @param integer $memberId
	 * @return string
	 */
	public function getIsPubMobiphoneByMemberId($memberId)
	{
		$value = $this->getByPk('is_pub_mobiphone', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“备用邮箱”
	 * @param integer $memberId
	 * @return string
	 */
	public function getEmailByMemberId($memberId)
	{
		$value = $this->getByPk('email', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“是否公开邮箱”
	 * @param integer $memberId
	 * @return string
	 */
	public function getIsPubEmailByMemberId($memberId)
	{
		$value = $this->getByPk('is_pub_email', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“家乡-国家”
	 * @param integer $memberId
	 * @return string
	 */
	public function getLiveCountryByMemberId($memberId)
	{
		$value = $this->getByPk('live_country', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“家乡-省”
	 * @param integer $memberId
	 * @return string
	 */
	public function getLiveProvinceByMemberId($memberId)
	{
		$value = $this->getByPk('live_province', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“家乡-城市”
	 * @param integer $memberId
	 * @return string
	 */
	public function getLiveCityByMemberId($memberId)
	{
		$value = $this->getByPk('live_city', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“家乡-区域”
	 * @param integer $memberId
	 * @return string
	 */
	public function getLiveDistrictByMemberId($memberId)
	{
		$value = $this->getByPk('live_district', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“家乡-街道门牌号”
	 * @param integer $memberId
	 * @return string
	 */
	public function getLiveStreetByMemberId($memberId)
	{
		$value = $this->getByPk('live_street', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“家乡-邮编”
	 * @param integer $memberId
	 * @return string
	 */
	public function getLiveZipcodeByMemberId($memberId)
	{
		$value = $this->getByPk('live_zipcode', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“住址-国家”
	 * @param integer $memberId
	 * @return string
	 */
	public function getAddressCountryByMemberId($memberId)
	{
		$value = $this->getByPk('address_country', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“住址-省”
	 * @param integer $memberId
	 * @return string
	 */
	public function getAddressProvinceByMemberId($memberId)
	{
		$value = $this->getByPk('address_province', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“住址-城市”
	 * @param integer $memberId
	 * @return string
	 */
	public function getAddressCityByMemberId($memberId)
	{
		$value = $this->getByPk('address_city', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“住址-区域”
	 * @param integer $memberId
	 * @return string
	 */
	public function getAddressDistrictByMemberId($memberId)
	{
		$value = $this->getByPk('address_district', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“住址-街道门牌号”
	 * @param integer $memberId
	 * @return string
	 */
	public function getAddressStreetByMemberId($memberId)
	{
		$value = $this->getByPk('address_street', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“住址-邮编”
	 * @param integer $memberId
	 * @return string
	 */
	public function getAddressZipcodeByMemberId($memberId)
	{
		$value = $this->getByPk('address_zipcode', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“QQ”
	 * @param integer $memberId
	 * @return integer
	 */
	public function getQqByMemberId($memberId)
	{
		$value = $this->getByPk('qq', $memberId);
		return $value ? (int) $value : '';
	}

	/**
	 * 通过“主键ID”，获取“MSN”
	 * @param integer $memberId
	 * @return string
	 */
	public function getMsnByMemberId($memberId)
	{
		$value = $this->getByPk('msn', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“Skype”
	 * @param integer $memberId
	 * @return string
	 */
	public function getSkypeidByMemberId($memberId)
	{
		$value = $this->getByPk('skypeid', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“旺旺”
	 * @param integer $memberId
	 * @return string
	 */
	public function getWangwangByMemberId($memberId)
	{
		$value = $this->getByPk('wangwang', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“微博”
	 * @param integer $memberId
	 * @return string
	 */
	public function getWeiboByMemberId($memberId)
	{
		$value = $this->getByPk('weibo', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“博客”
	 * @param integer $memberId
	 * @return string
	 */
	public function getBlogByMemberId($memberId)
	{
		$value = $this->getByPk('blog', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“网站”
	 * @param integer $memberId
	 * @return string
	 */
	public function getWebsiteByMemberId($memberId)
	{
		$value = $this->getByPk('website', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“传真”
	 * @param integer $memberId
	 * @return string
	 */
	public function getFaxByMemberId($memberId)
	{
		$value = $this->getByPk('fax', $memberId);
		return $value ? $value : '';
	}

}
