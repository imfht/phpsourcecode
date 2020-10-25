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
use member\library\Lang;

/**
 * FpSocial class file
 * 业务层：表单数据处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: FpSocial.php 1 2014-12-01 11:37:11Z Code Generator $
 * @package member.services
 * @since 1.0
 */
class FpSocial extends FormProcessor
{
	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_process()
	 */
	protected function _process(array $params = array())
	{
		if ($this->isInsert()) {
			return false;
		}

		$this->isValids($params,
			'login_name', 'realname', 'sex', 'birth_ymd', 'is_pub_birth', 'anniversary', 'is_pub_anniversary', 'head_portrait',
			'introduce', 'interests', 'is_pub_interests', 'telephone', 'mobiphone', 'is_pub_mobiphone', 'email', 'is_pub_email',
			'live_country_id', 'live_province_id', 'live_city_id', 'live_district_id',
			'live_country', 'live_province', 'live_city', 'live_district', 'live_street', 'live_zipcode',
			'address_country_id', 'address_province_id', 'address_city_id', 'address_district_id',
			'address_country', 'address_province', 'address_city', 'address_district', 'address_street', 'address_zipcode',
			'qq', 'msn', 'skypeid', 'wangwang', 'weibo', 'blog', 'website', 'fax');
		return !$this->hasError();
	}

	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_cleanPreProcess()
	 */
	protected function _cleanPreProcess(array $params)
	{
		$rules = array(
			'realname' => 'trim',
			'sex' => 'trim',
			'birth_ymd' => 'trim',
			'is_pub_birth' => 'trim',
			'anniversary' => 'trim',
			'is_pub_anniversary' => 'trim',
			'head_portrait' => 'trim',
			'introduce' => 'trim',
			'is_pub_interests' => 'trim',
			'telephone' => 'trim',
			'mobiphone' => 'trim',
			'is_pub_mobiphone' => 'trim',
			'email' => 'trim',
			'is_pub_email' => 'trim',
			'live_country_id' => 'intval',
			'live_province_id' => 'intval',
			'live_city_id' => 'intval',
			'live_district_id' => 'intval',
			'live_country' => 'trim',
			'live_province' => 'trim',
			'live_city' => 'trim',
			'live_district' => 'trim',
			'live_street' => 'trim',
			'live_zipcode' => 'trim',
			'address_country_id' => 'intval',
			'address_province_id' => 'intval',
			'address_city_id' => 'intval',
			'address_district_id' => 'intval',
			'address_country' => 'trim',
			'address_province' => 'trim',
			'address_city' => 'trim',
			'address_district' => 'trim',
			'address_street' => 'trim',
			'address_zipcode' => 'trim',
			'qq' => 'intval',
			'msn' => 'trim',
			'skypeid' => 'trim',
			'wangwang' => 'trim',
			'weibo' => 'trim',
			'blog' => 'trim',
			'website' => 'trim',
			'fax' => 'trim',
		);

		$ret = $this->clean($rules, $params);
		return $ret;
	}

	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_cleanPostProcess()
	 */
	protected function _cleanPostProcess()
	{
		if (isset($this->sex)) {
			$enum = DataSocial::getSexEnum();
			if (!isset($enum[$this->sex])) {
				$this->sex = null;
			}
		}

		if (isset($this->interests)) {
			$enum = DataSocial::getInterestsEnum();
			$newInterests = array();
			$oldInterests = (array) $this->interests;
			foreach ($oldInterests as $value) {
				if (($value = trim($value)) === '') {
					continue;
				}

				if (isset($enum[$value])) {
					$newInterests[] = $value;
				}
			}

			$this->interests = implode(',', array_unique($newInterests));
		}

		if (isset($this->birth_ymd)) {
			$birthYmd = date('Y-m-d', strtotime($this->birth_ymd));
			if ($birthYmd !== $this->birth_ymd) {
				unset($this->birth_ymd);
			}
			else {
				$this->birth_md = date('md', strtotime($this->birth_ymd));
			}
		}

		if (isset($this->is_pub_birth)) {
			$enum = DataSocial::getIsPubBirthEnum();
			if (!isset($enum[$this->is_pub_birth])) {
				unset($enum[$this->is_pub_birth]);
			}
		}

		if (isset($this->is_pub_anniversary)) {
			$enum = DataSocial::getIsPubAnniversaryEnum();
			if (!isset($enum[$this->is_pub_anniversary])) {
				unset($enum[$this->is_pub_anniversary]);
			}
		}

		if (isset($this->is_pub_interests)) {
			$enum = DataSocial::getIsPubInterestsEnum();
			if (!isset($enum[$this->is_pub_interests])) {
				unset($enum[$this->is_pub_interests]);
			}
		}

		if (isset($this->is_pub_mobiphone)) {
			$enum = DataSocial::getIsPubMobiphoneEnum();
			if (!isset($enum[$this->is_pub_mobiphone])) {
				unset($enum[$this->is_pub_mobiphone]);
			}
		}

		if (isset($this->is_pub_email)) {
			$enum = DataSocial::getIsPubEmailEnum();
			if (!isset($enum[$this->is_pub_email])) {
				unset($enum[$this->is_pub_email]);
			}
		}

		return true;
	}

	/**
	 * 获取“真实姓名”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getRealnameRule($value)
	{
		return array(
			'MaxLength' => new validator\MaxLengthValidator($value, 20, Lang::_('SRV_FILTER_MEMBER_SOCIAL_REALNAME_MAXLENGTH')),
		);
	}

	/**
	 * 获取“备用手机号”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getMobiphoneRule($value)
	{
		if ($value === '') { return array(); }

		return array(
			'Phone' => new validator\PhoneValidator($value, true, Lang::_('SRV_FILTER_MEMBER_SOCIAL_MOBIPHONE_PHONE')),
		);
	}

	/**
	 * 获取“备用邮箱”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getEmailRule($value)
	{
		if ($value === '') { return array(); }

		return array(
			'Mail' => new validator\MailValidator($value, true, Lang::_('SRV_FILTER_MEMBER_SOCIAL_EMAIL_MAIL')),
		);
	}

}
