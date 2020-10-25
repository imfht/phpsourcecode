<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace users\db;

use tdo\AbstractDb;
use libsrv\Clean;
use users\library\Constant;
use users\library\TableNames;

/**
 * Users class file
 * 业务层：数据库操作类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Users.php 1 2014-08-07 10:09:58Z Code Generator $
 * @package users.db
 * @since 1.0
 */
class Users extends AbstractDb
{
	/**
	 * @var string 数据库配置名
	 */
	protected $_clusterName = Constant::DB_CLUSTER;

	/**
	 * 通过多个字段名和值，查询多条记录
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
		$usersTblName = $this->getTblprefix() . TableNames::getUsers();
		$userGroupsTblName = $this->getTblprefix() . TableNames::getUsergroups();

		$sql = 'SELECT ' . $option . ' `u`.`user_id`, `u`.`login_name`, `u`.`login_type`, `u`.`user_name`, `u`.`user_mail`, `u`.`user_phone`, `u`.`dt_registered`, `u`.`dt_last_login`, `u`.`dt_last_repwd`, `u`.`ip_registered`, `u`.`ip_last_login`, `u`.`ip_last_repwd`, `u`.`login_count`, `u`.`repwd_count`, `u`.`valid_mail`, `u`.`valid_phone`, `u`.`forbidden`, `u`.`trash` FROM `' . $usersTblName . '` AS `u`';
		if (isset($attributes['group_id'])) {
			$sql .= ' LEFT JOIN `' . $userGroupsTblName . '` AS `g` ON `u`.`user_id` = `g`.`user_id`';
		}

		$condition = '1';
		$attributes = array();

		if (isset($params['login_name'])) {
			$loginName = trim($params['login_name']);
			if ($loginName !== '') {
				$condition .= ' AND `u`.`login_name` LIKE ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['login_name'] = '%' . $loginName . '%';
			}
		}

		if (isset($params['login_type'])) {
			$loginType = trim($params['login_type']);
			if ($loginType !== '') {
				$condition .= ' AND `u`.`login_type` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['login_type'] = $loginType;
			}
		}

		if (isset($params['user_name'])) {
			$userName = trim($params['user_name']);
			if ($userName !== '') {
				$condition .= ' AND `u`.`user_name` LIKE ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['user_name'] = '%' . $userName . '%';
			}
			else {
				$condition .= ' AND `u`.`user_name` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['user_name'] = '';
			}
		}

		if (isset($params['user_mail'])) {
			$userMail = trim($params['user_mail']);
			if ($userMail !== '') {
				$condition .= ' AND `u`.`user_mail` LIKE ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['user_mail'] = '%' . $userMail . '%';
			}
			else {
				$condition .= ' AND `u`.`user_mail` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['user_mail'] = '';
			}
		}

		if (isset($params['user_phone'])) {
			$userPhone = trim($params['user_phone']);
			if ($userPhone !== '') {
				$condition .= ' AND `u`.`user_phone` LIKE ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['user_phone'] = '%' . $userPhone . '%';
			}
			else {
				$condition .= ' AND `u`.`user_phone` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['user_phone'] = '';
			}
		}

		if (isset($params['valid_mail'])) {
			$validMail = trim($params['valid_mail']);
			if ($validMail !== '') {
				$condition .= ' AND `u`.`valid_mail` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['valid_mail'] = $validMail;
			}
		}

		if (isset($params['valid_phone'])) {
			$validPhone = trim($params['valid_phone']);
			if ($validPhone !== '') {
				$condition .= ' AND `u`.`valid_phone` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['valid_phone'] = $validPhone;
			}
		}

		if (isset($params['forbidden'])) {
			$forbidden = trim($params['forbidden']);
			if ($forbidden !== '') {
				$condition .= ' AND `u`.`forbidden` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['forbidden'] = $forbidden;
			}
		}

		if (isset($params['trash'])) {
			$trash = trim($params['trash']);
			if ($trash !== '') {
				$condition .= ' AND `u`.`trash` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['trash'] = $trash;
			}
		}

		if (isset($params['group_id'])) {
			$groupId = (int) $params['group_id'];
			if ($groupId > 0) {
				$condition .= ' AND `g`.`group_id` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['group_id'] = $groupId;
			}
		}

		if (isset($params['dt_registered_ge'])) {
			$dtRegisteredGe = trim($params['dt_registered_ge']);
			if ($dtRegisteredGe !== '') {
				$condition .= ' AND `u`.`dt_registered` >= ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['dt_registered_ge'] = $dtRegisteredGe;
			}
		}

		if (isset($params['dt_registered_le'])) {
			$dtRegisteredLe = trim($params['dt_registered_le']);
			if ($dtRegisteredLe !== '') {
				$condition .= ' AND `u`.`dt_registered` <= ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['dt_registered_le'] = $dtRegisteredLe;
			}
		}

		if (isset($params['dt_last_login_ge'])) {
			$dtLastLoginGe = trim($params['dt_last_login_ge']);
			if ($dtLastLoginGe !== '') {
				$condition .= ' AND `u`.`dt_last_login` >= ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['dt_last_login_ge'] = $dtLastLoginGe;
			}
		}

		if (isset($params['dt_last_login_le'])) {
			$dtLastLoginLe = trim($params['dt_last_login_le']);
			if ($dtLastLoginLe !== '') {
				$condition .= ' AND `u`.`dt_last_login` <= ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['dt_last_login_le'] = $dtLastLoginLe;
			}
		}

		if (isset($params['login_count_ge'])) {
			$loginCountGe = (int) $params['login_count_ge'];
			if ($loginCountGe >= 0) {
				$condition .= ' AND `u`.`login_count` >= ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['login_count_ge'] = $loginCountGe;
			}
		}

		if (isset($params['login_count_le'])) {
			$loginCountLe = (int) $params['login_count_le'];
			if ($loginCountLe >= 0) {
				$condition .= ' AND `u`.`login_count` <= ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['login_count_le'] = $loginCountLe;
			}
		}

		if (isset($params['ip_registered'])) {
			$ipRegistered = (int) $params['ip_registered'];
			$condition .= ' AND `u`.`ip_registered` = ' . $commandBuilder::PLACE_HOLDERS;
			$attributes['ip_registered'] = $ipRegistered;
		}

		if (isset($params['user_id'])) {
			$userId = (int) $params['user_id'];
			if ($userId > 0) {
				$condition .= ' AND `u`.`user_id` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['user_id'] = $userId;
			}
		}

		$sql = $commandBuilder->applyCondition($sql, $condition);
		$sql = $commandBuilder->applyOrder($sql, $order);
		$sql = $commandBuilder->applyLimit($sql, $limit, $offset);

		if ($option === 'SQL_CALC_FOUND_ROWS') {
			$ret = $this->fetchAllNoCache($sql, $attributes);
			if (isset($attributes['login_name'])) {
				$attributes['login_name'] = $loginName;
			}
			if (isset($attributes['user_name'])) {
				$attributes['user_name'] = $userName;
			}
			if (isset($attributes['user_mail'])) {
				$attributes['user_mail'] = $userMail;
			}
			if (isset($attributes['user_phone'])) {
				$attributes['user_phone'] = $userPhone;
			}
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
	 * @param integer $userId
	 * @return array
	 */
	public function findByPk($userId)
	{
		if (($userId = (int) $userId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getUsers();
		$sql = 'SELECT `user_id`, `login_name`, `login_type`, `password`, `salt`, `user_name`, `user_mail`, `user_phone`, `dt_registered`, `dt_last_login`, `dt_last_repwd`, `ip_registered`, `ip_last_login`, `ip_last_repwd`, `login_count`, `repwd_count`, `valid_mail`, `valid_phone`, `forbidden`, `trash` FROM ' . $tableName . ' WHERE `user_id` = ?';
		return $this->fetchAssoc($sql, $userId);
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

		$tableName = $this->getTblprefix() . TableNames::getUsers();
		$sql = 'SELECT `user_id`, `login_name`, `login_type`, `password`, `salt`, `user_name`, `user_mail`, `user_phone`, `dt_registered`, `dt_last_login`, `dt_last_repwd`, `ip_registered`, `ip_last_login`, `ip_last_repwd`, `login_count`, `repwd_count`, `valid_mail`, `valid_phone`, `forbidden`, `trash` FROM ' . $tableName . ' WHERE `login_name` = ?';
		return $this->fetchAssoc($sql, $loginName);
	}

	/**
	 * 通过登录名，验证值是否在数据库表中是否存在
	 * @param string $loginName
	 * @return boolean
	 */
	public function loginNameExists($loginName)
	{
		if (($loginName = trim($loginName)) === '') {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getUsers();
		$sql = 'SELECT COUNT(*) FROM `' . $tableName . '` WHERE `login_name` = ?';
		$total = $this->fetchColumn($sql, $loginName);
		if ($total === false) {
			return false;
		}

		return ($total > 0);
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
		$userName = isset($params['user_name']) ? trim($params['user_name']) : '';
		$userMail = isset($params['user_mail']) ? trim($params['user_mail']) : '';
		$userPhone = isset($params['user_phone']) ? trim($params['user_phone']) : '';
		$dtRegistered = isset($params['dt_registered']) ? trim($params['dt_registered']) : '';
		$dtLastLogin = isset($params['dt_last_login']) ? trim($params['dt_last_login']) : '';
		$ipRegistered = isset($params['ip_registered']) ? (int) $params['ip_registered'] : 0;
		$ipLastLogin = isset($params['ip_last_login']) ? (int) $params['ip_last_login'] : 0;
		$loginCount = isset($params['login_count']) ? (int) $params['login_count'] : 0;
		$validMail = isset($params['valid_mail']) ? trim($params['valid_mail']) : '';
		$validPhone = isset($params['valid_phone']) ? trim($params['valid_phone']) : '';
		$forbidden = isset($params['forbidden']) ? trim($params['forbidden']) : '';
		$trash = 'n';

		if ($loginName === '' || $loginType === '' || $password === '' || $salt === '' || $userName === '' || $loginCount < 0) {
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

		$tableName = $this->getTblprefix() . TableNames::getUsers();
		$attributes = array(
			'login_name' => $loginName,
			'login_type' => $loginType,
			'password' => $password,
			'salt' => $salt,
			'user_name' => $userName,
			'user_mail' => $userMail,
			'user_phone' => $userPhone,
			'dt_registered' => $dtRegistered,
			'dt_last_login' => $dtLastLogin,
			'ip_registered' => $ipRegistered,
			'ip_last_login' => $ipLastLogin,
			'login_count' => $loginCount,
			'valid_mail' => $validMail,
			'valid_phone' => $validPhone,
			'forbidden' => $forbidden,
			'trash' => $trash,
		);

		$sql = $this->getCommandBuilder()->createInsert($tableName, array_keys($attributes), $ignore);
		$lastInsertId = $this->insert($sql, $attributes);
		return $lastInsertId;
	}

	/**
	 * 通过主键，编辑一条记录，禁止编辑“登录名”和“登录方式”
	 * @param integer $userId
	 * @param array $params
	 * @return integer
	 */
	public function modifyByPk($userId, array $params = array())
	{
		if (($userId = (int) $userId) <= 0) {
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

		if (isset($params['user_name'])) {
			$userName = trim($params['user_name']);
			if ($userName !== '') {
				$attributes['user_name'] = $userName;
			}
			else {
				return false;
			}
		}

		if (isset($params['user_mail'])) {
			$attributes['user_mail'] = trim($params['user_mail']);
		}

		if (isset($params['user_phone'])) {
			$attributes['user_phone'] = trim($params['user_phone']);
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
			if ($loginCount >= 0) {
				$attributes['login_count'] = $loginCount;
			}
			else {
				return false;
			}
		}

		if (isset($params['repwd_count'])) {
			$repwdCount = (int) $params['repwd_count'];
			if ($repwdCount >= 0) {
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

		$tableName = $this->getTblprefix() . TableNames::getUsers();
		$sql = $this->getCommandBuilder()->createUpdate($tableName, array_keys($attributes), '`user_id` = ?');
		$attributes['user_id'] = $userId;
		$rowCount = $this->update($sql, $attributes);
		return $rowCount;
	}

	/**
	 * 通过主键，编辑多条记录
	 * @param array|integer $userIds
	 * @param array $params
	 * @return integer
	 */
	public function batchModifyByPk($userIds, array $params = array())
	{
		$userIds = Clean::positiveInteger($userIds);
		if ($userIds === false) {
			return false;
		}

		if (is_array($userIds)) {
			$userIds = implode(', ', $userIds);
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

		$tableName = $this->getTblprefix() . TableNames::getUsers();
        $condition = '`user_id` IN (' . $userIds . ')';
        $sql = $this->getCommandBuilder()->createUpdate($tableName, array_keys($attributes), $condition);
        $rowCount = $this->update($sql, $attributes);
        return $rowCount;
	}

	/**
	 * 通过主键，删除一条记录
	 * @param integer $userId
	 * @return integer
	 */
	public function removeByPk($userId)
	{
		if (($userId = (int) $userId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getUsers();
		$sql = $this->getCommandBuilder()->createDelete($tableName, '`user_id` = ?');
		$rowCount = $this->delete($sql, $userId);
		return $rowCount;
	}
}
