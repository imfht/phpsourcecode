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

use libsrv\FormProcessor;
use tfc\validator;
use tfc\saf\Log;
use libsrv\Service;
use libapp\ErrorNo;
use member\library\Lang;

/**
 * FpAddresses class file
 * 业务层：表单数据处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: FpAddresses.php 1 2014-12-03 17:53:27Z Code Generator $
 * @package member.services
 * @since 1.0
 */
class FpAddresses extends FormProcessor
{
	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_process()
	 */
	protected function _process(array $params = array())
	{
		if ($this->isInsert()) {
			if (!$this->required($params,
				'member_id', 'consignee', 'mobiphone', 'telephone', 'addr_street')) {
				return false;
			}
		}

		$this->isValids($params,
			'member_id', 'consignee', 'mobiphone', 'telephone', 'email',
			'addr_country_id', 'addr_country', 'addr_province_id', 'addr_province',
			'addr_city_id', 'addr_city', 'addr_district_id', 'addr_district', 'addr_street', 'addr_zipcode',
			'when', 'is_default', 'dt_created', 'dt_last_modified');
		return !$this->hasError();
	}

	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_cleanPreProcess()
	 */
	protected function _cleanPreProcess(array $params)
	{
		if ($this->isInsert()) {
			if (isset($params['dt_last_modified'])) { unset($params['dt_last_modified']); }

			$params['dt_created'] = $params['dt_last_modified'] = date('Y-m-d H:i:s');
		}
		else {
			if (isset($params['dt_created'])) { unset($params['dt_created']); }

			$params['dt_last_modified'] = date('Y-m-d H:i:s');
		}

		$rules = array(
			'member_id' => 'intval',
			'consignee' => 'trim',
			'mobiphone' => 'trim',
			'telephone' => 'trim',
			'email' => 'trim',
			'addr_country_id' => 'intval',
			'addr_province_id' => 'intval',
			'addr_city_id' => 'intval',
			'addr_district_id' => 'intval',
			'addr_street' => 'trim',
			'addr_zipcode' => 'intval',
			'when' => 'trim',
			'is_default' => 'trim',
			'dt_created' => 'trim',
			'dt_last_modified' => 'trim',
		);

		$ret = $this->clean($rules, $params);
		return $ret;
	}

	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_cleanPostProcess()
	 */
	public function _cleanPostProcess()
	{
		if (isset($this->when)) {
			$enum = DataAddresses::getWhenEnum();
			if (!isset($enum[$this->when])) {
				$this->when = DataAddresses::WHEN_ANYONE;
			}
		}

		if (isset($this->is_default)) {
			$enum = DataAddresses::getIsDefaultEnum();
			if (!isset($enum[$this->is_default])) {
				$this->is_default = DataAddresses::IS_DEFAULT_N;
			}
		}

		if ($this->isUpdate()) {
			$row = $this->_object->findByPk($this->id);
			if (!$row || !is_array($row) || !isset($row['address_id']) || !isset($row['mobiphone']) || !isset($row['telephone'])) {
				Log::warning(sprintf(
					'FpAddresses is unable to find the result by id "%d"', $this->id
				), ErrorNo::ERROR_DB_SELECT,  __METHOD__);

				return false;
			}

			if (isset($this->mobiphone) && $this->mobiphone === '') {
				if (!isset($this->telephone) && $row['telephone'] === '') {
					$this->addError('mobiphone', Lang::_('SRV_FILTER_MEMBER_ADDRESSES_MOBIPHONE_TELEPHONE_NOTEMPTY'));
					return false;
				}
			}

			if (isset($this->telephone) && $this->telephone === '') {
				if (!isset($this->mobiphone) && $row['mobiphone'] === '') {
					$this->addError('telephone', Lang::_('SRV_FILTER_MEMBER_ADDRESSES_MOBIPHONE_TELEPHONE_NOTEMPTY'));
					return false;
				}
			}

			$consignee = isset($this->consignee) ? $this->consignee : $row['consignee'];
		}

		if ((isset($this->mobiphone) && $this->mobiphone === '')
			&& (isset($this->telephone) && $this->telephone === '')) {
			$this->addError('mobiphone', Lang::_('SRV_FILTER_MEMBER_ADDRESSES_MOBIPHONE_TELEPHONE_NOTEMPTY'));
			$this->addError('telephone', Lang::_('SRV_FILTER_MEMBER_ADDRESSES_MOBIPHONE_TELEPHONE_NOTEMPTY'));
			return false;
		}

		if ($this->isInsert()) {
			$consignee = $this->consignee;
		}

		if ($this->isUpdate()) {
			if (!isset($this->addr_country_id) && !isset($this->addr_province_id) && !isset($this->addr_city_id) && !isset($this->addr_district_id)) {
				$this->address_name = $consignee . '-' . $row['addr_city'];
				return true;
			}
		}

		if (!isset($this->addr_country_id)) {
			$this->addr_country_id = 1;
		}

		if (!isset($this->addr_province_id)) {
			$this->addr_province_id = 0;
		}

		if (!isset($this->addr_city_id)) {
			$this->addr_city_id = 0;
		}

		if (!isset($this->addr_district_id)) {
			$this->addr_district_id = 0;
		}

		$addrCountryId  = $this->addr_country_id;
		$addrProvinceId = $this->addr_province_id;
		$addrCityId     = $this->addr_city_id;
		$addrDistrictId = $this->addr_district_id;

		$srv = Service::getInstance('Regions', 'system');

		$this->addr_country = $this->addr_province = $this->addr_city = $this->addr_district = '';

		$row = $srv->findByPk($addrCountryId);
		if ($row && is_array($row) && isset($row['region_pid'], $row['region_name'], $row['region_type'])) {
			if (((int) $row['region_pid'] === 0) && ((int) $row['region_type'] === 0)) {
				$this->addr_country = $row['region_name'];
			}
		}

		if ($this->addr_country === '') {
			$this->addError('addr_country_id', Lang::_('SRV_FILTER_MEMBER_ADDRESSES_ADDR_ID_INTEGER'));
			return false;
		}

		$row = $srv->findByPk($addrProvinceId);
		if ($row && is_array($row) && isset($row['region_pid'], $row['region_name'], $row['region_type'])) {
			if (((int) $row['region_pid'] === $addrCountryId) && ((int) $row['region_type'] === 1)) {
				$this->addr_province = $row['region_name'];
			}
		}

		if ($this->addr_province === '') {
			$this->addError('addr_province_id', Lang::_('SRV_FILTER_MEMBER_ADDRESSES_ADDR_ID_INTEGER'));
			return false;
		}

		$row = $srv->findByPk($addrCityId);
		if ($row && is_array($row) && isset($row['region_pid'], $row['region_name'], $row['region_type'])) {
			if (((int) $row['region_pid'] === $addrProvinceId) && ((int) $row['region_type'] === 2)) {
				$this->addr_city = $row['region_name'];
			}
		}

		if ($this->addr_city === '') {
			$this->addError('addr_city_id', Lang::_('SRV_FILTER_MEMBER_ADDRESSES_ADDR_ID_INTEGER'));
			return false;
		}

		$row = $srv->findByPk($addrDistrictId);
		if ($row && is_array($row) && isset($row['region_pid'], $row['region_name'], $row['region_type'])) {
			if (((int) $row['region_pid'] === $addrCityId) && ((int) $row['region_type'] === 3)) {
				$this->addr_district = $row['region_name'];
			}
		}

		if ($this->addr_district === '') {
			$this->addError('addr_district_id', Lang::_('SRV_FILTER_MEMBER_ADDRESSES_ADDR_ID_INTEGER'));
			return false;
		}

		$this->address_name = $consignee . '-' . $this->addr_city;
		return true;
	}

	/**
	 * 获取“收货人姓名”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getConsigneeRule($value)
	{
		return array(
			'NotEmpty' => new validator\NotEmptyValidator($value, true, Lang::_('SRV_FILTER_MEMBER_ADDRESSES_CONSIGNEE_NOTEMPTY')),
			'MaxLength' => new validator\MaxLengthValidator($value, 50, Lang::_('SRV_FILTER_MEMBER_ADDRESSES_CONSIGNEE_MAXLENGTH')),
		);
	}

	/**
	 * 获取“手机号码”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getMobiphoneRule($value)
	{
		if ($value === '') { return array(); }

		return array(
			'Phone' => new validator\PhoneValidator($value, true, Lang::_('SRV_FILTER_MEMBER_ADDRESSES_MOBIPHONE_PHONE')),
		);
	}

	/**
	 * 获取“邮箱”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getEmailRule($value)
	{
		if ($value === '') { return array(); }

		return array(
			'Mail' => new validator\MailValidator($value, true, Lang::_('SRV_FILTER_MEMBER_ADDRESSES_EMAIL_MAIL')),
		);
	}

	/**
	 * 获取“国家”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getAddrCountryIdRule($value)
	{
		return array(
			'Integer' => new validator\IntegerValidator($value, true, Lang::_('SRV_FILTER_MEMBER_ADDRESSES_ADDR_ID_INTEGER')),
		);
	}

	/**
	 * 获取“省”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getAddrProvinceIdRule($value)
	{
		return array(
			'Integer' => new validator\IntegerValidator($value, true, Lang::_('SRV_FILTER_MEMBER_ADDRESSES_ADDR_ID_INTEGER')),
		);
	}

	/**
	 * 获取“城市”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getAddrCityIdRule($value)
	{
		return array(
			'Integer' => new validator\IntegerValidator($value, true, Lang::_('SRV_FILTER_MEMBER_ADDRESSES_ADDR_ID_INTEGER')),
		);
	}

	/**
	 * 获取“区域”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getAddrDistrictIdRule($value)
	{
		return array(
			'Integer' => new validator\IntegerValidator($value, true, Lang::_('SRV_FILTER_MEMBER_ADDRESSES_ADDR_ID_INTEGER')),
		);
	}

	/**
	 * 获取“详细地址”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getAddrStreetRule($value)
	{
		return array(
			'MinLength' => new validator\MinLengthValidator($value, 5, Lang::_('SRV_FILTER_MEMBER_ADDRESSES_ADDR_STREET_MINLENGTH')),
			'MaxLength' => new validator\MaxLengthValidator($value, 120, Lang::_('SRV_FILTER_MEMBER_ADDRESSES_ADDR_STREET_MAXLENGTH')),
		);
	}

}
