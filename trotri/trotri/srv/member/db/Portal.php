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
use tfc\saf\Log;
use libsrv\Clean;
use member\library\Constant;
use member\library\TableNames;

/**
 * Portal class file
 * 业务层：数据库操作类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Portal.php 1 2014-11-26 16:58:35Z Code Generator $
 * @package member.db
 * @since 1.0
 */
class Portal extends AbstractDb
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
		$tableName = $this->getTblprefix() . TableNames::getPortal();
		$sql = 'SELECT ' . $option . ' `member_id`, `login_name`, `login_type`, `password`, `salt`, `member_name`, `member_mail`, `member_phone`, `relation_member_id`, `dt_registered`, `dt_last_login`, `dt_last_repwd`, `ip_registered`, `ip_last_login`, `ip_last_repwd`, `login_count`, `repwd_count`, `valid_mail`, `valid_phone`, `forbidden`, `trash` FROM `' . $tableName . '`';

		$condition = '1';
		$attributes = array();

		if (isset($params['login_name'])) {
			$loginName = trim($params['login_name']);
			if ($loginName !== '') {
				$condition .= ' AND `login_name` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['login_name'] = $loginName;
			}
		}

		if (isset($params['login_type'])) {
			$loginType = trim($params['login_type']);
			if ($loginType !== '') {
				$condition .= ' AND `login_type` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['login_type'] = $loginType;
			}
		}

		if (isset($params['member_name'])) {
			$memberName = trim($params['member_name']);
			$condition .= ' AND `member_name` = ' . $commandBuilder::PLACE_HOLDERS;
			$attributes['member_name'] = $memberName;
		}

		if (isset($params['member_mail'])) {
			$memberMail = trim($params['member_mail']);
			$condition .= ' AND `member_mail` = ' . $commandBuilder::PLACE_HOLDERS;
			$attributes['member_mail'] = $memberMail;
		}

		if (isset($params['member_phone'])) {
			$memberPhone = trim($params['member_phone']);
			$condition .= ' AND `member_phone` = ' . $commandBuilder::PLACE_HOLDERS;
			$attributes['member_phone'] = $memberPhone;
		}

		if (isset($params['valid_mail'])) {
			$validMail = trim($params['valid_mail']);
			if ($validMail !== '') {
				$condition .= ' AND `valid_mail` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['valid_mail'] = $validMail;
			}
		}

		if (isset($params['valid_phone'])) {
			$validPhone = trim($params['valid_phone']);
			if ($validPhone !== '') {
				$condition .= ' AND `valid_phone` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['valid_phone'] = $validPhone;
			}
		}

		if (isset($params['forbidden'])) {
			$forbidden = trim($params['forbidden']);
			if ($forbidden !== '') {
				$condition .= ' AND `forbidden` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['forbidden'] = $forbidden;
			}
		}

		if (isset($params['trash'])) {
			$trash = trim($params['trash']);
			if ($trash !== '') {
				$condition .= ' AND `trash` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['trash'] = $trash;
			}
		}

		if (isset($params['dt_registered_ge'])) {
			$dtRegisteredGe = trim($params['dt_registered_ge']);
			if ($dtRegisteredGe !== '') {
				$condition .= ' AND `dt_registered` >= ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['dt_registered_ge'] = $dtRegisteredGe;
			}
		}

		if (isset($params['dt_registered_le'])) {
			$dtRegisteredLe = trim($params['dt_registered_le']);
			if ($dtRegisteredLe !== '') {
				$condition .= ' AND `dt_registered` <= ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['dt_registered_le'] = $dtRegisteredLe;
			}
		}

		if (isset($params['dt_last_login_ge'])) {
			$dtLastLoginGe = trim($params['dt_last_login_ge']);
			if ($dtLastLoginGe !== '') {
				$condition .= ' AND `dt_last_login` >= ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['dt_last_login_ge'] = $dtLastLoginGe;
			}
		}

		if (isset($params['dt_last_login_le'])) {
			$dtLastLoginLe = trim($params['dt_last_login_le']);
			if ($dtLastLoginLe !== '') {
				$condition .= ' AND `dt_last_login` <= ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['dt_last_login_le'] = $dtLastLoginLe;
			}
		}

		if (isset($params['login_count_ge'])) {
			$loginCountGe = (int) $params['login_count_ge'];
			if ($loginCountGe >= 0) {
				$condition .= ' AND `login_count` >= ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['login_count_ge'] = $loginCountGe;
			}
		}

		if (isset($params['login_count_le'])) {
			$loginCountLe = (int) $params['login_count_le'];
			if ($loginCountLe >= 0) {
				$condition .= ' AND `login_count` <= ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['login_count_le'] = $loginCountLe;
			}
		}

		if (isset($params['ip_registered'])) {
			$ipRegistered = (int) $params['ip_registered'];
			$condition .= ' AND `ip_registered` = ' . $commandBuilder::PLACE_HOLDERS;
			$attributes['ip_registered'] = $ipRegistered;
		}

		if (isset($params['member_id'])) {
			$memberId = (int) $params['member_id'];
			if ($memberId > 0) {
				$condition .= ' AND `member_id` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['member_id'] = $memberId;
			}
		}

		$sql = $commandBuilder->applyCondition($sql, $condition);
		$sql = $commandBuilder->applyOrder($sql, $order);
		$sql = $commandBuilder->applyLimit($sql, $limit, $offset);

		if ($option === 'SQL_CALC_FOUND_ROWS') {
			$ret = $this->fetchAllNoCache($sql, $attributes);
			if (isset($attributes['ip_registered'])) {
				$attributes['ip_registered'] = long2ip($ipRegistered);
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

		$tableName = $this->getTblprefix() . TableNames::getPortal();
		$sql = 'SELECT `member_id`, `login_name`, `login_type`, `password`, `salt`, `member_name`, `member_mail`, `member_phone`, `relation_member_id`, `dt_registered`, `dt_last_login`, `dt_last_repwd`, `ip_registered`, `ip_last_login`, `ip_last_repwd`, `login_count`, `repwd_count`, `valid_mail`, `valid_phone`, `forbidden`, `trash` FROM `' . $tableName . '` WHERE `member_id` = ?';
		return $this->fetchAssoc($sql, $memberId);
	}

	/**
	 * 通过登录名，查询一条记录
	 * @param string $loginName
	 * @return array
	 */
	public function findByLoginName($loginName)
	{
		if (($loginName = trim($loginName)) === '') {
			return false;
		}

		$membersTblName = $this->getTblprefix() . TableNames::getMembers();
		$portalTblName = $this->getTblprefix() . TableNames::getPortal();

		$sql = 'SELECT `p`.`member_id`, `p`.`login_name`, `p`.`login_type`, `p`.`password`, `p`.`salt`, `p`.`member_name`, `p`.`member_mail`, `p`.`member_phone`, `p`.`relation_member_id`, `p`.`dt_registered`, `p`.`dt_last_login`, `p`.`dt_last_repwd`, `p`.`ip_registered`, `p`.`ip_last_login`, `p`.`ip_last_repwd`, `p`.`login_count`, `p`.`repwd_count`, `p`.`valid_mail`, `p`.`valid_phone`, `p`.`forbidden`, `p`.`trash`, `m`.`type_id`, `m`.`rank_id`, `m`.`experience`, `m`.`balance`, `m`.`points`, `m`.`consum`, `m`.`orders` FROM `' . $portalTblName . '` AS `p` LEFT JOIN `' . $membersTblName . '` AS `m` ON `p`.`member_id` = `m`.`member_id` WHERE `p`.`login_name` = ?';
		return $this->fetchAssoc($sql, $loginName);
	}

	/**
	 * 新增一条记录
	 * @param array $params
	 * @param boolean $ignore
	 * @return integer
	 */
	public function create(array $params = array(), $ignore = false)
	{
		$loginName = isset($params['login_name']) ? trim($params['login_name']) : '';
		$loginType = isset($params['login_type']) ? trim($params['login_type']) : '';
		$password = isset($params['password']) ? trim($params['password']) : '';
		$salt = isset($params['salt']) ? trim($params['salt']) : '';
		$memberName = isset($params['member_name']) ? trim($params['member_name']) : '';
		$memberMail = isset($params['member_mail']) ? trim($params['member_mail']) : '';
		$memberPhone = isset($params['member_phone']) ? trim($params['member_phone']) : '';
		$dtRegistered = isset($params['dt_registered']) ? trim($params['dt_registered']) : '';
		$dtLastLogin = isset($params['dt_last_login']) ? trim($params['dt_last_login']) : '';
		$dtLastRepwd = isset($params['dt_last_repwd']) ? trim($params['dt_last_repwd']) : '';
		$ipRegistered = isset($params['ip_registered']) ? (int) $params['ip_registered'] : 0;
		$ipLastLogin = isset($params['ip_last_login']) ? (int) $params['ip_last_login'] : 0;
		$ipLastRepwd = isset($params['ip_last_repwd']) ? (int) $params['ip_last_repwd'] : 0;
		$loginCount = isset($params['login_count']) ? (int) $params['login_count'] : 0;
		$repwdCount = isset($params['repwd_count']) ? (int) $params['repwd_count'] : 0;
		$validMail = isset($params['valid_mail']) ? trim($params['valid_mail']) : '';
		$validPhone = isset($params['valid_phone']) ? trim($params['valid_phone']) : '';
		$forbidden = isset($params['forbidden']) ? trim($params['forbidden']) : '';
		$relationMemberId = 0;
		$trash = 'n';

		if ($loginName === '' || $loginType === '' || $password === '' || $salt === '' || $memberName === ''
			|| $loginCount < 0) {
			return false;
		}

		if ($validMail === '') {
			$validMail = 'n';
		}

		if ($validPhone === '') {
			$validPhone = 'n';
		}

		if ($forbidden === '') {
			$forbidden = 'n';
		}

		if ($dtRegistered === '') {
			$dtRegistered = date('Y-m-d H:i:s');
		}

		if ($dtLastLogin === '') {
			$dtLastLogin = $dtRegistered;
		}

		$tableName = $this->getTblprefix() . TableNames::getPortal();
		$attributes = array(
			'login_name' => $loginName,
			'login_type' => $loginType,
			'password' => $password,
			'salt' => $salt,
			'member_name' => $memberName,
			'member_mail' => $memberMail,
			'member_phone' => $memberPhone,
			'relation_member_id' => $relationMemberId,
			'dt_registered' => $dtRegistered,
			'dt_last_login' => $dtLastLogin,
			'dt_last_repwd' => $dtLastRepwd,
			'ip_registered' => $ipRegistered,
			'ip_last_login' => $ipLastLogin,
			'ip_last_repwd' => $ipLastRepwd,
			'login_count' => $loginCount,
			'repwd_count' => $repwdCount,
			'valid_mail' => $validMail,
			'valid_phone' => $validPhone,
			'forbidden' => $forbidden,
			'trash' => $trash,
		);

		$sql = $this->getCommandBuilder()->createInsert($tableName, array_keys($attributes), $ignore);
		$lastInsertId = $this->insert($sql, $attributes);
		if (($lastInsertId = (int) $lastInsertId) <= 0) {
			return false;
		}

		$membersTblName = $this->getTblprefix() . TableNames::getMembers();
		$socialTblName = $this->getTblprefix() . TableNames::getSocial();
		$commands = array(
			array(
				'sql' => $this->getCommandBuilder()->createInsert($membersTblName, array('member_id', 'login_name', 'dt_created')),
				'params' => array('member_id' => $lastInsertId, 'login_name' => $loginName, 'dt_created' => date('Y-m-d H:i:s'))
			),
			array(
				'sql' => $this->getCommandBuilder()->createInsert($socialTblName, array('member_id', 'login_name')),
				'params' => array('member_id' => $lastInsertId, 'login_name' => $loginName)
			),
		);

		if ($this->doTransaction($commands)) {
			return $lastInsertId;
		}

		return false;
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

		if (isset($params['password'])) {
			$password = trim($params['password']);
			if ($password !== '') {
				$attributes['password'] = $password;
			}
			else {
				return false;
			}
		}

		if (isset($params['salt'])) {
			$salt = trim($params['salt']);
			if ($salt !== '') {
				$attributes['salt'] = $salt;
			}
			else {
				return false;
			}
		}

		if (isset($params['member_name'])) {
			$memberName = trim($params['member_name']);
			if ($memberName !== '') {
				$attributes['member_name'] = $memberName;
			}
			else {
				return false;
			}
		}

		if (isset($params['member_mail'])) {
			$attributes['member_mail'] = trim($params['member_mail']);
		}

		if (isset($params['member_phone'])) {
			$attributes['member_phone'] = trim($params['member_phone']);
		}

		if (isset($params['relation_member_id'])) {
			$relationMemberId = (int) $params['relation_member_id'];
			if ($relationMemberId >= 0) {
				$attributes['relation_member_id'] = $relationMemberId;
			}
			else {
				return false;
			}
		}

		if (isset($params['dt_last_login'])) {
			$dtLastLogin = trim($params['dt_last_login']);
			if ($dtLastLogin !== '') {
				$attributes['dt_last_login'] = $dtLastLogin;
			}
			else {
				return false;
			}
		}

		if (isset($params['dt_last_repwd'])) {
			$dtLastRepwd = trim($params['dt_last_repwd']);
			if ($dtLastRepwd !== '') {
				$attributes['dt_last_repwd'] = $dtLastRepwd;
			}
			else {
				return false;
			}
		}

		if (isset($params['ip_last_login'])) {
			$attributes['ip_last_login'] = (int) $params['ip_last_login'];
		}

		if (isset($params['ip_last_repwd'])) {
			$attributes['ip_last_repwd'] = (int) $params['ip_last_repwd'];
		}

		if (isset($params['login_count'])) {
			$loginCount = (int) $params['login_count'];
			if ($loginCount > 0) {
				$attributes['login_count'] = $loginCount;
			}
			else {
				return false;
			}
		}

		if (isset($params['repwd_count'])) {
			$repwdCount = (int) $params['repwd_count'];
			if ($repwdCount > 0) {
				$attributes['repwd_count'] = $repwdCount;
			}
			else {
				return false;
			}
		}

		if (isset($params['valid_mail'])) {
			$validMail = trim($params['valid_mail']);
			if ($validMail !== '') {
				$attributes['valid_mail'] = $validMail;
			}
			else {
				return false;
			}
		}

		if (isset($params['valid_phone'])) {
			$validPhone = trim($params['valid_phone']);
			if ($validPhone !== '') {
				$attributes['valid_phone'] = $validPhone;
			}
			else {
				return false;
			}
		}

		if (isset($params['forbidden'])) {
			$forbidden = trim($params['forbidden']);
			if ($forbidden !== '') {
				$attributes['forbidden'] = $forbidden;
			}
			else {
				return false;
			}
		}

		if (isset($params['trash'])) {
			$trash = trim($params['trash']);
			if ($trash !== '') {
				$attributes['trash'] = $trash;
			}
			else {
				return false;
			}
		}

		$rowCount = 0;

		if ($attributes === array()) {
			return $rowCount;
		}

		$tableName = $this->getTblprefix() . TableNames::getPortal();
		$sql = $this->getCommandBuilder()->createUpdate($tableName, array_keys($attributes), '`member_id` = ?');
		$attributes['member_id'] = $memberId;
		$rowCount = $this->update($sql, $attributes);
		return $rowCount;
	}

	/**
	 * 通过主键，编辑多条记录
	 * @param array|integer $memberIds
	 * @param array $params
	 * @return integer
	 */
	public function batchModifyByPk($memberIds, array $params = array())
	{
		$memberIds = Clean::positiveInteger($memberIds);
		if ($memberIds === false) {
			return false;
		}

		if (is_array($memberIds)) {
			$memberIds = implode(', ', $memberIds);
		}

		$attributes = array();

		if (isset($params['valid_mail'])) {
			$validMail = trim($params['valid_mail']);
			if ($validMail !== '') {
				$attributes['valid_mail'] = $validMail;
			}
			else {
				return false;
			}
		}

		if (isset($params['valid_phone'])) {
			$validPhone = trim($params['valid_phone']);
			if ($validPhone !== '') {
				$attributes['valid_phone'] = $validPhone;
			}
			else {
				return false;
			}
		}

		if (isset($params['forbidden'])) {
			$forbidden = trim($params['forbidden']);
			if ($forbidden !== '') {
				$attributes['forbidden'] = $forbidden;
			}
			else {
				return false;
			}
		}

		if (isset($params['trash'])) {
			$trash = trim($params['trash']);
			if ($trash !== '') {
				$attributes['trash'] = $trash;
			}
			else {
				return false;
			}
		}

		$rowCount = 0;

		if ($attributes === array()) {
			return $rowCount;
		}

		$tableName = $this->getTblprefix() . TableNames::getPortal();
		$condition = '`member_id` IN (' . $memberIds . ')';
		$sql = $this->getCommandBuilder()->createUpdate($tableName, array_keys($attributes), $condition);
		$rowCount = $this->update($sql, $attributes);
		return $rowCount;
	}

	/**
	 * 通过主键，删除一条记录
	 * @param integer $memberId
	 * @return integer
	 */
	public function removeByPk($memberId)
	{
		if (($memberId = (int) $memberId) <= 0) {
			return false;
		}

		$portalTblName = $this->getTblprefix() . TableNames::getPortal();
		$membersTblName = $this->getTblprefix() . TableNames::getMembers();
		$socialTblName = $this->getTblprefix() . TableNames::getSocial();

		$sql = 'SELECT `p`.*, `m`.*, `s`.* FROM `' . $portalTblName . '` AS `p` LEFT JOIN `' . $membersTblName . '` AS `m` ON `p`.`member_id` = `m`.`member_id` LEFT JOIN `' . $socialTblName . '` AS `s` ON `m`.`member_id` = `s`.`member_id` WHERE `m`.`member_id` = ?';
		$row = $this->fetchAssoc($sql, $memberId);
		if (!$row || !is_array($row) || !isset($row['member_id'])) {
			return false;
		}

		Log::info(sprintf(
			'Portal backup before remove: %s', serialize($row)
		), 0,  __METHOD__);

		$commands = array(
			array(
				'sql' => 'DELETE FROM `' . $portalTblName . '` WHERE `member_id` = ?',
				'params' => $memberId
			),
			array(
				'sql' => 'DELETE FROM `' . $membersTblName . '` WHERE `member_id` = ?',
				'params' => $memberId
			),
			array(
				'sql' => 'DELETE FROM `' . $socialTblName . '` WHERE `member_id` = ?',
				'params' => $memberId
			),
		);

		return $this->doTransaction($commands);
	}

}
