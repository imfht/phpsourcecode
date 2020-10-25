<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace member\db;

use tdo\AbstractDb;
use member\library\Constant;
use member\library\TableNames;

/**
 * Addresses class file
 * 业务层：数据库操作类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Addresses.php 1 2014-12-03 17:53:27Z Code Generator $
 * @package member.db
 * @since 1.0
 */
class Addresses extends AbstractDb
{
	/**
	 * @var string 数据库配置名
	 */
	protected $_clusterName = Constant::DB_CLUSTER;

	/**
	 * 查询全部记录
	 * @param integer $memberId
	 * @return array
	 */
	public function findAll($memberId)
	{
		if (($memberId = (int) $memberId) <= 0) {
			return false;
		}

		$commandBuilder = $this->getCommandBuilder();
		$tableName = $this->getTblprefix() . TableNames::getAddresses();
		$sql = 'SELECT `address_id`, `address_name`, `member_id`, `consignee`, `mobiphone`, `telephone`, `email`, `addr_country_id`, `addr_country`, `addr_province_id`, `addr_province`, `addr_city_id`, `addr_city`, `addr_district_id`, `addr_district`, `addr_street`, `addr_zipcode`, `when`, `is_default`, `dt_created`, `dt_last_modified` FROM `' . $tableName . '` WHERE `member_id` = ? ORDER BY `is_default` ASC, `dt_last_modified` DESC';
		return $this->fetchAll($sql, $memberId);
	}

	/**
	 * 通过主键，查询一条记录
	 * @param integer $addressId
	 * @return array
	 */
	public function findByPk($addressId)
	{
		if (($addressId = (int) $addressId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getAddresses();
		$sql = 'SELECT `address_id`, `address_name`, `member_id`, `consignee`, `mobiphone`, `telephone`, `email`, `addr_country_id`, `addr_country`, `addr_province_id`, `addr_province`, `addr_city_id`, `addr_city`, `addr_district_id`, `addr_district`, `addr_street`, `addr_zipcode`, `when`, `is_default`, `dt_created`, `dt_last_modified` FROM `' . $tableName . '` WHERE `address_id` = ?';
		return $this->fetchAssoc($sql, $addressId);
	}

	/**
	 * 新增一条记录
	 * @param array $params
	 * @param boolean $ignore
	 * @return integer
	 */
	public function create(array $params = array(), $ignore = false)
	{
		$addressName = isset($params['address_name']) ? trim($params['address_name']) : '';
		$memberId = isset($params['member_id']) ? (int) $params['member_id'] : 0;
		$consignee = isset($params['consignee']) ? trim($params['consignee']) : '';
		$mobiphone = isset($params['mobiphone']) ? trim($params['mobiphone']) : '';
		$telephone = isset($params['telephone']) ? trim($params['telephone']) : '';
		$email = isset($params['email']) ? trim($params['email']) : '';
		$addrCountryId = isset($params['addr_country_id']) ? (int) $params['addr_country_id'] : 0;
		$addrCountry = isset($params['addr_country']) ? trim($params['addr_country']) : '';
		$addrProvinceId = isset($params['addr_province_id']) ? (int) $params['addr_province_id'] : 0;
		$addrProvince = isset($params['addr_province']) ? trim($params['addr_province']) : '';
		$addrCityId = isset($params['addr_city_id']) ? (int) $params['addr_city_id'] : 0;
		$addrCity = isset($params['addr_city']) ? trim($params['addr_city']) : '';
		$addrDistrictId = isset($params['addr_district_id']) ? (int) $params['addr_district_id'] : 0;
		$addrDistrict = isset($params['addr_district']) ? trim($params['addr_district']) : '';
		$addrStreet = isset($params['addr_street']) ? trim($params['addr_street']) : '';
		$addrZipcode = isset($params['addr_zipcode']) ? trim($params['addr_zipcode']) : '';
		$when = isset($params['when']) ? trim($params['when']) : '';
		$isDefault = isset($params['is_default']) ? trim($params['is_default']) : '';
		$dtCreated = isset($params['dt_created']) ? trim($params['dt_created']) : '';

		if ($addressName === '' || $memberId <= 0 || $consignee === ''
			|| $addrCountryId <= 0 || $addrCountry === '' || $addrProvinceId <= 0 || $addrProvince === ''
			|| $addrCityId <= 0 || $addrCity === '' || $addrDistrictId <= 0 || $addrDistrict === '' || $addrStreet === '') {
			return false;
		}

		if ($mobiphone === '' && $telephone === '') {
			return false;
		}

		if ($when === '') {
			$when = 'anyone';
		}

		if ($isDefault === '') {
			$isDefault = 'n';
		}

		if ($dtCreated === '') {
			$dtCreated = date('Y-m-d H:i:s');
		}

		$dtLastModified = $dtCreated;

		$tableName = $this->getTblprefix() . TableNames::getAddresses();
		$attributes = array(
			'address_name' => $addressName,
			'member_id' => $memberId,
			'consignee' => $consignee,
			'mobiphone' => $mobiphone,
			'telephone' => $telephone,
			'email' => $email,
			'addr_country_id' => $addrCountryId,
			'addr_country' => $addrCountry,
			'addr_province_id' => $addrProvinceId,
			'addr_province' => $addrProvince,
			'addr_city_id' => $addrCityId,
			'addr_city' => $addrCity,
			'addr_district_id' => $addrDistrictId,
			'addr_district' => $addrDistrict,
			'addr_street' => $addrStreet,
			'addr_zipcode' => $addrZipcode,
			'when' => $when,
			'is_default' => $isDefault,
			'dt_created' => $dtCreated,
			'dt_last_modified' => $dtLastModified,
		);

		$sql = $this->getCommandBuilder()->createInsert($tableName, array_keys($attributes), $ignore);
		$lastInsertId = $this->insert($sql, $attributes);
		if ($lastInsertId > 0 && $isDefault === 'y') {
			$this->update('UPDATE `' . $tableName . '` SET `is_default` = ? WHERE `member_id` = ? AND `address_id` != ?', array('is_default' => 'n', 'member_id' => $memberId, 'address_id' => $lastInsertId));
		}

		return $lastInsertId;
	}

	/**
	 * 通过主键，编辑一条记录
	 * @param integer $addressId
	 * @param array $params
	 * @return integer
	 */
	public function modifyByPk($addressId, array $params = array())
	{
		if (($addressId = (int) $addressId) <= 0) {
			return false;
		}

		$row = $this->findByPk($addressId);
		if (!$row || !is_array($row) || !isset($row['member_id']) || !isset($row['mobiphone']) || !isset($row['telephone'])) {
			return false;
		}

		$attributes = array();

		if (isset($params['address_name'])) {
			$addressName = trim($params['address_name']);
			if ($addressName !== '') {
				$attributes['address_name'] = $addressName;
			}
			else {
				return false;
			}
		}

		if (isset($params['consignee'])) {
			$consignee = trim($params['consignee']);
			if ($consignee !== '') {
				$attributes['consignee'] = $consignee;
			}
			else {
				return false;
			}
		}

		if (isset($params['mobiphone'])) {
			$attributes['mobiphone'] = trim($params['mobiphone']);
		}

		if (isset($params['telephone'])) {
			$attributes['telephone'] = trim($params['telephone']);
		}

		if ((isset($attributes['mobiphone']) && $attributes['mobiphone'] === '')
			&& (isset($attributes['telephone']) && $attributes['telephone'] === '')) {
			return false;
		}

		if (isset($attributes['mobiphone']) && $attributes['mobiphone'] === '') {
			if (!isset($attributes['telephone']) && $row['telephone'] === '') {
				return false;
			}
		}

		if (isset($attributes['telephone']) && $attributes['telephone'] === '') {
			if (!isset($attributes['mobiphone']) && $row['mobiphone'] === '') {
				return false;
			}
		}

		if (isset($params['email'])) {
			$attributes['email'] = trim($params['email']);
		}

		if (isset($params['addr_country_id'])) {
			$addrCountryId = (int) $params['addr_country_id'];
			if ($addrCountryId > 0) {
				$attributes['addr_country_id'] = $addrCountryId;
			}
			else {
				return false;
			}
		}

		if (isset($params['addr_country'])) {
			$addrCountry = trim($params['addr_country']);
			if ($addrCountry !== '') {
				$attributes['addr_country'] = $addrCountry;
			}
			else {
				return false;
			}
		}

		if (isset($params['addr_province_id'])) {
			$addrProvinceId = (int) $params['addr_province_id'];
			if ($addrProvinceId > 0) {
				$attributes['addr_province_id'] = $addrProvinceId;
			}
			else {
				return false;
			}
		}

		if (isset($params['addr_province'])) {
			$addrProvince = trim($params['addr_province']);
			if ($addrProvince !== '') {
				$attributes['addr_province'] = $addrProvince;
			}
			else {
				return false;
			}
		}

		if (isset($params['addr_city_id'])) {
			$addrCityId = (int) $params['addr_city_id'];
			if ($addrCityId > 0) {
				$attributes['addr_city_id'] = $addrCityId;
			}
			else {
				return false;
			}
		}

		if (isset($params['addr_city'])) {
			$addrCity = trim($params['addr_city']);
			if ($addrCity !== '') {
				$attributes['addr_city'] = $addrCity;
			}
			else {
				return false;
			}
		}

		if (isset($params['addr_district_id'])) {
			$addrDistrictId = (int) $params['addr_district_id'];
			if ($addrDistrictId > 0) {
				$attributes['addr_district_id'] = $addrDistrictId;
			}
			else {
				return false;
			}
		}

		if (isset($params['addr_district'])) {
			$addrDistrict = trim($params['addr_district']);
			if ($addrDistrict !== '') {
				$attributes['addr_district'] = $addrDistrict;
			}
			else {
				return false;
			}
		}

		if (isset($params['addr_street'])) {
			$addrStreet = trim($params['addr_street']);
			if ($addrStreet !== '') {
				$attributes['addr_street'] = $addrStreet;
			}
			else {
				return false;
			}
		}

		if (isset($params['addr_zipcode'])) {
			$addrZipcode = trim($params['addr_zipcode']);
			if ($addrZipcode !== '') {
				$attributes['addr_zipcode'] = $addrZipcode;
			}
			else {
				return false;
			}
		}

		if (isset($params['when'])) {
			$when = trim($params['when']);
			if ($when !== '') {
				$attributes['when'] = $when;
			}
			else {
				return false;
			}
		}

		if (isset($params['is_default'])) {
			$isDefault = trim($params['is_default']);
			if ($isDefault !== '') {
				$attributes['is_default'] = $isDefault;
			}
			else {
				return false;
			}
		}

		if (isset($params['dt_last_modified'])) {
			$dtLastModified = trim($params['dt_last_modified']);
			if ($dtLastModified !== '') {
				$attributes['dt_last_modified'] = $dtLastModified;
			}
			else {
				return false;
			}
		}
		else {
			$attributes['dt_last_modified'] = date('Y-m-d H:i:s');
		}

		$rowCount = 0;

		if ($attributes === array()) {
			return $rowCount;
		}

		$tableName = $this->getTblprefix() . TableNames::getAddresses();
		$sql = $this->getCommandBuilder()->createUpdate($tableName, array_keys($attributes), '`address_id` = ?');
		$attributes['address_id'] = $addressId;
		$rowCount = $this->update($sql, $attributes);
		if ($rowCount > 0 && isset($attributes['is_default']) && $attributes['is_default'] === 'y') {
			$this->update('UPDATE `' . $tableName . '` SET `is_default` = ? WHERE `member_id` = ? AND `address_id` != ?', array('is_default' => 'n', 'member_id' => $row['member_id'], 'address_id' => $addressId));
		}

		return $rowCount;
	}

	/**
	 * 通过主键，删除一条记录
	 * @param integer $addressId
	 * @return integer
	 */
	public function removeByPk($addressId)
	{
		if (($addressId = (int) $addressId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getAddresses();
		$sql = $this->getCommandBuilder()->createDelete($tableName, '`address_id` = ?');
		$rowCount = $this->delete($sql, $addressId);
		return $rowCount;
	}
}
