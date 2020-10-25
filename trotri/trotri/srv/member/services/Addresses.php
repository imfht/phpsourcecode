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

/**
 * Addresses class file
 * 业务层：业务处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Addresses.php 1 2014-12-03 17:53:27Z Code Generator $
 * @package member.services
 * @since 1.0
 */
class Addresses extends AbstractService
{
	/**
	 * 查询全部记录
	 * @param integer $memberId
	 * @return array
	 */
	public function findAll($memberId)
	{
		$rows = $this->getDb()->findAll($memberId);
		return $rows;
	}

	/**
	 * 通过主键，查询一条记录
	 * @param integer $addressId
	 * @return array
	 */
	public function findByPk($addressId)
	{
		$row = $this->getDb()->findByPk($addressId);
		return $row;
	}

	/**
	 * 通过“主键ID”，获取“地址名”
	 * @param integer $addressId
	 * @return string
	 */
	public function getAddressNameByAddressId($addressId)
	{
		$value = $this->getByPk('address_name', $addressId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“会员ID”
	 * @param integer $addressId
	 * @return integer
	 */
	public function getMemberIdByAddressId($addressId)
	{
		$value = $this->getByPk('member_id', $addressId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“收货人姓名”
	 * @param integer $addressId
	 * @return string
	 */
	public function getConsigneeByAddressId($addressId)
	{
		$value = $this->getByPk('consignee', $addressId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“手机号码”
	 * @param integer $addressId
	 * @return string
	 */
	public function getMobiphoneByAddressId($addressId)
	{
		$value = $this->getByPk('mobiphone', $addressId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“电话号码”
	 * @param integer $addressId
	 * @return string
	 */
	public function getTelephoneByAddressId($addressId)
	{
		$value = $this->getByPk('telephone', $addressId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“邮箱”
	 * @param integer $addressId
	 * @return string
	 */
	public function getEmailByAddressId($addressId)
	{
		$value = $this->getByPk('email', $addressId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“国家”
	 * @param integer $addressId
	 * @return integer
	 */
	public function getAddrCountryIdByAddressId($addressId)
	{
		$value = $this->getByPk('addr_country_id', $addressId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“国家”
	 * @param integer $addressId
	 * @return string
	 */
	public function getAddrCountryByAddressId($addressId)
	{
		$value = $this->getByPk('addr_country', $addressId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“省”
	 * @param integer $addressId
	 * @return integer
	 */
	public function getAddrProvinceIdByAddressId($addressId)
	{
		$value = $this->getByPk('addr_province_id', $addressId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“省”
	 * @param integer $addressId
	 * @return string
	 */
	public function getAddrProvinceByAddressId($addressId)
	{
		$value = $this->getByPk('addr_province', $addressId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“城市”
	 * @param integer $addressId
	 * @return integer
	 */
	public function getAddrCityIdByAddressId($addressId)
	{
		$value = $this->getByPk('addr_city_id', $addressId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“城市”
	 * @param integer $addressId
	 * @return string
	 */
	public function getAddrCityByAddressId($addressId)
	{
		$value = $this->getByPk('addr_city', $addressId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“区域”
	 * @param integer $addressId
	 * @return integer
	 */
	public function getAddrDistrictIdByAddressId($addressId)
	{
		$value = $this->getByPk('addr_district_id', $addressId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“区域”
	 * @param integer $addressId
	 * @return string
	 */
	public function getAddrDistrictByAddressId($addressId)
	{
		$value = $this->getByPk('addr_district', $addressId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“详细地址”
	 * @param integer $addressId
	 * @return string
	 */
	public function getAddrStreetByAddressId($addressId)
	{
		$value = $this->getByPk('addr_street', $addressId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“邮编”
	 * @param integer $addressId
	 * @return string
	 */
	public function getAddrZipcodeByAddressId($addressId)
	{
		$value = $this->getByPk('addr_zipcode', $addressId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“收货最佳时间”
	 * @param integer $addressId
	 * @return string
	 */
	public function getWhenByAddressId($addressId)
	{
		$value = $this->getByPk('when', $addressId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“是否默认地址”
	 * @param integer $addressId
	 * @return string
	 */
	public function getIsDefaultByAddressId($addressId)
	{
		$value = $this->getByPk('is_default', $addressId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“创建时间”
	 * @param integer $addressId
	 * @return string
	 */
	public function getDtCreatedByAddressId($addressId)
	{
		$value = $this->getByPk('dt_created', $addressId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“上次编辑时间”
	 * @param integer $addressId
	 * @return string
	 */
	public function getDtLastModifiedByAddressId($addressId)
	{
		$value = $this->getByPk('dt_last_modified', $addressId);
		return $value ? $value : '';
	}

	/**
	 * 获取“收货最佳时间”
	 * @param string $when
	 * @return string
	 */
	public function getWhenLangByWhen($when)
	{
		$enum = DataAddresses::getWhenEnum();
		return isset($enum[$when]) ? $enum[$when] : '';
	}
}
