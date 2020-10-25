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
 * Social class file
 * 业务层：数据库操作类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Social.php 1 2014-12-01 11:37:11Z Code Generator $
 * @package member.db
 * @since 1.0
 */
class Social extends AbstractDb
{
	/**
	 * @var string 数据库配置名
	 */
	protected $_clusterName = Constant::DB_CLUSTER;

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
		$commandBuilder = $this->getCommandBuilder();
		$portalTblName = $this->getTblprefix() . TableNames::getPortal();
		$socialTblName = $this->getTblprefix() . TableNames::getSocial();
		$sql = 'SELECT ' . $option . ' `p`.`member_id`, `p`.`login_name`, `p`.`member_name`, `p`.`member_mail`, `p`.`member_phone`, `s`.* FROM `' . $socialTblName . '` AS `s` LEFT JOIN `' . $portalTblName . '` AS `p` ON `s`.`member_id` = `p`.`member_id`';

		$condition = '`p`.`trash` = ' . $commandBuilder::PLACE_HOLDERS;
		$attributes = array('trash' => 'n');

		if (isset($params['login_name'])) {
			$loginName = trim($params['login_name']);
			if ($loginName !== '') {
				$condition .= ' AND `p`.`login_name` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['login_name'] = $loginName;
			}
		}

		if (isset($params['login_type'])) {
			$loginType = trim($params['login_type']);
			if ($loginType !== '') {
				$condition .= ' AND `p`.`login_type` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['login_type'] = $loginType;
			}
		}

		if (isset($params['member_name'])) {
			$memberName = trim($params['member_name']);
			$condition .= ' AND `p`.`member_name` = ' . $commandBuilder::PLACE_HOLDERS;
			$attributes['member_name'] = $memberName;
		}

		if (isset($params['member_mail'])) {
			$memberMail = trim($params['member_mail']);
			$condition .= ' AND `p`.`member_mail` = ' . $commandBuilder::PLACE_HOLDERS;
			$attributes['member_mail'] = $memberMail;
		}

		if (isset($params['member_phone'])) {
			$memberPhone = trim($params['member_phone']);
			$condition .= ' AND `p`.`member_phone` = ' . $commandBuilder::PLACE_HOLDERS;
			$attributes['member_phone'] = $memberPhone;
		}

		if (isset($params['realname'])) {
			$realname = trim($params['realname']);
			if ($realname !== '') {
				$condition .= ' AND `s`.`realname` LIKE ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['realname'] = '%' . $realname . '%';
			}
		}

		if (isset($params['sex'])) {
			$sex = trim($params['sex']);
			if ($sex !== '') {
				$condition .= ' AND `s`.`sex` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['sex'] = $sex;
			}
		}

		if (isset($params['birth_md'])) {
			$birthMd = trim($params['birth_md']);
			if ($birthMd !== '') {
				$condition .= ' AND `s`.`birth_md` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['birth_md'] = $birthMd;
			}
		}

		if (isset($params['anniversary'])) {
			$anniversary = trim($params['anniversary']);
			if ($anniversary !== '') {
				$condition .= ' AND `s`.`anniversary` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['anniversary'] = $anniversary;
			}
		}

		if (isset($params['qq'])) {
			$qq = (int) $params['qq'];
			if ($qq > 0) {
				$condition .= ' AND `s`.`qq` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['qq'] = $qq;
			}
		}

		if (isset($params['member_id'])) {
			$memberId = (int) $params['member_id'];
			if ($memberId > 0) {
				$condition .= ' AND `s`.`member_id` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['member_id'] = $memberId;
			}
		}

		$sql = $commandBuilder->applyCondition($sql, $condition);
		$sql = $commandBuilder->applyOrder($sql, $order);
		$sql = $commandBuilder->applyLimit($sql, $limit, $offset);

		if ($option === 'SQL_CALC_FOUND_ROWS') {
			$ret = $this->fetchAllNoCache($sql, $attributes);
			if (isset($attributes['realname'])) {
				$attributes['realname'] = $realname;
			}
			if (is_array($ret)) {
				$ret['attributes'] = $attributes;
				$ret['order']      = $order;
				$ret['limit']      = $limit;
				$ret['offset']     = $offset;
			}
		}
		else {
			$ret = $this->fetchAll($sql, $attributes);
		}

		return $ret;
	}

	/**
	 * 通过主键，查询一条记录
	 * @param integer $memberId
	 * @return array
	 */
	public function findByPk($memberId)
	{
		if (($memberId = (int) $memberId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getSocial();
		$sql = 'SELECT * FROM `' . $tableName . '` WHERE `member_id` = ?';
		return $this->fetchAssoc($sql, $memberId);
	}

	/**
	 * 通过主键，编辑一条记录
	 * @param integer $memberId
	 * @param array $params
	 * @return integer
	 */
	public function modifyByPk($memberId, array $params = array())
	{
		if (($memberId = (int) $memberId) <= 0) {
			return false;
		}

		$attributes = array();

		if (isset($params['realname'])) {
			$attributes['realname'] = trim($params['realname']);
		}

		if (isset($params['sex'])) {
			$sex = trim($params['sex']);
			if ($sex !== '') {
				$attributes['sex'] = $sex;
			}
			else {
				return false;
			}
		}

		if (isset($params['birth_ymd'])) {
			$birthYmd = trim($params['birth_ymd']);
			if ($birthYmd !== '') {
				$attributes['birth_ymd'] = $birthYmd;
			}
		}

		if (isset($params['is_pub_birth'])) {
			$isPubBirth = trim($params['is_pub_birth']);
			if ($isPubBirth !== '') {
				$attributes['is_pub_birth'] = $isPubBirth;
			}
			else {
				return false;
			}
		}

		if (isset($params['birth_md'])) {
			$attributes['birth_md'] = trim($params['birth_md']);
		}

		if (isset($params['anniversary'])) {
			$attributes['anniversary'] = trim($params['anniversary']);
		}

		if (isset($params['is_pub_anniversary'])) {
			$isPubAnniversary = trim($params['is_pub_anniversary']);
			if ($isPubAnniversary !== '') {
				$attributes['is_pub_anniversary'] = $isPubAnniversary;
			}
			else {
				return false;
			}
		}

		if (isset($params['head_portrait'])) {
			$attributes['head_portrait'] = trim($params['head_portrait']);
		}

		if (isset($params['introduce'])) {
			$attributes['introduce'] = trim($params['introduce']);
		}

		if (isset($params['interests'])) {
			$attributes['interests'] = trim($params['interests']);
		}

		if (isset($params['is_pub_interests'])) {
			$isPubInterests = trim($params['is_pub_interests']);
			if ($isPubInterests !== '') {
				$attributes['is_pub_interests'] = $isPubInterests;
			}
			else {
				return false;
			}
		}

		if (isset($params['telephone'])) {
			$attributes['telephone'] = trim($params['telephone']);
		}

		if (isset($params['mobiphone'])) {
			$attributes['mobiphone'] = trim($params['mobiphone']);
		}

		if (isset($params['is_pub_mobiphone'])) {
			$isPubMobiphone = trim($params['is_pub_mobiphone']);
			if ($isPubMobiphone !== '') {
				$attributes['is_pub_mobiphone'] = $isPubMobiphone;
			}
			else {
				return false;
			}
		}

		if (isset($params['email'])) {
			$attributes['email'] = trim($params['email']);
		}

		if (isset($params['is_pub_email'])) {
			$isPubEmail = trim($params['is_pub_email']);
			if ($isPubEmail !== '') {
				$attributes['is_pub_email'] = $isPubEmail;
			}
			else {
				return false;
			}
		}

		if (isset($params['live_country_id'])) {
			$attributes['live_country_id'] = (int) $params['live_country_id'];
		}

		if (isset($params['live_country'])) {
			$attributes['live_country'] = trim($params['live_country']);
		}

		if (isset($params['live_province_id'])) {
			$attributes['live_province_id'] = (int) $params['live_province_id'];
		}

		if (isset($params['live_province'])) {
			$attributes['live_province'] = trim($params['live_province']);
		}

		if (isset($params['live_city_id'])) {
			$attributes['live_city_id'] = (int) $params['live_city_id'];
		}

		if (isset($params['live_city'])) {
			$attributes['live_city'] = trim($params['live_city']);
		}

		if (isset($params['live_district_id'])) {
			$attributes['live_district_id'] = (int) $params['live_district_id'];
		}

		if (isset($params['live_district'])) {
			$attributes['live_district'] = trim($params['live_district']);
		}

		if (isset($params['live_street'])) {
			$attributes['live_street'] = trim($params['live_street']);
		}

		if (isset($params['live_zipcode'])) {
			$attributes['live_zipcode'] = trim($params['live_zipcode']);
		}

		if (isset($params['address_country_id'])) {
			$attributes['address_country_id'] = (int) $params['address_country_id'];
		}

		if (isset($params['address_country'])) {
			$attributes['address_country'] = trim($params['address_country']);
		}

		if (isset($params['address_province_id'])) {
			$attributes['address_province_id'] = (int) $params['address_province_id'];
		}

		if (isset($params['address_province'])) {
			$attributes['address_province'] = trim($params['address_province']);
		}

		if (isset($params['address_city_id'])) {
			$attributes['address_city_id'] = (int) $params['address_city_id'];
		}

		if (isset($params['address_city'])) {
			$attributes['address_city'] = trim($params['address_city']);
		}

		if (isset($params['address_district_id'])) {
			$attributes['address_district_id'] = (int) $params['address_district_id'];
		}

		if (isset($params['address_district'])) {
			$attributes['address_district'] = trim($params['address_district']);
		}

		if (isset($params['address_street'])) {
			$attributes['address_street'] = trim($params['address_street']);
		}

		if (isset($params['address_zipcode'])) {
			$attributes['address_zipcode'] = trim($params['address_zipcode']);
		}

		if (isset($params['qq'])) {
			$attributes['qq'] = (int) $params['qq'];
		}

		if (isset($params['msn'])) {
			$attributes['msn'] = trim($params['msn']);
		}

		if (isset($params['skypeid'])) {
			$attributes['skypeid'] = trim($params['skypeid']);
		}

		if (isset($params['wangwang'])) {
			$attributes['wangwang'] = trim($params['wangwang']);
		}

		if (isset($params['weibo'])) {
			$attributes['weibo'] = trim($params['weibo']);
		}

		if (isset($params['blog'])) {
			$attributes['blog'] = trim($params['blog']);
		}

		if (isset($params['website'])) {
			$attributes['website'] = trim($params['website']);
		}

		if (isset($params['fax'])) {
			$attributes['fax'] = trim($params['fax']);
		}

		$rowCount = 0;

		if ($attributes === array()) {
			return $rowCount;
		}

		$tableName = $this->getTblprefix() . TableNames::getSocial();
		$sql = $this->getCommandBuilder()->createUpdate($tableName, array_keys($attributes), '`member_id` = ?');
		$attributes['member_id'] = $memberId;
		$rowCount = $this->update($sql, $attributes);
		return $rowCount;
	}
}
