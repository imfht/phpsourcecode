<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace users\services;

use libsrv\AbstractService;
use tfc\util\String;
use libsrv\Clean;
use users\library\Constant;
use users\library\Plugin;

/**
 * Users class file
 * 业务层：业务处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Users.php 1 2014-08-07 10:09:58Z Code Generator $
 * @package users.services
 * @since 1.0
 */
class Users extends AbstractService
{
	/**
	 * @var instance of users\services\Usergroups
	 */
	protected $_userGroups = null;

	/**
	 * 构造方法：初始化数据库操作类
	 */
	public function __construct()
	{
		parent::__construct();

		$this->_userGroups = new Usergroups();
	}

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
		$limit = min(max((int) $limit, 1), Constant::FIND_MAX_LIMIT);
		$offset = max((int) $offset, 0);

		if (isset($params['ip_registered'])) {
			$ipRegistered = trim($params['ip_registered']); unset($params['ip_registered']);
			if ($ipRegistered !== '') {
				$ipRegistered = (strpos($ipRegistered, '.') !== false) ? Clean::ip2long($ipRegistered) : (int) $ipRegistered;
				if ($ipRegistered !== false) {
					$params['ip_registered'] = $ipRegistered;
				}
			}
		}

		$rows = $this->getDb()->findAll($params, $order, $limit, $offset, $option);
		return $rows;
	}

	/**
	 * 通过主键，查询一条记录
	 * @param integer $userId
	 * @return array
	 */
	public function findByPk($userId)
	{
		$row = $this->getDb()->findByPk($userId);
		if ($row && is_array($row) && isset($row['user_id'])) {
			$groupIds = $this->_userGroups->findGroupIdsByUserId($row['user_id']);
			$row['group_ids'] = is_array($groupIds) ? $groupIds : array();

			$dispatcher = Plugin::getInstance();
			$dispatcher->trigger('onAfterFind', array(__METHOD__, &$row));
		}

		return $row;
	}

	/**
	 * 通过登录名，查询一条记录
	 * @param string $loginName
	 * @return array
	 */
	public function findByLoginName($loginName)
	{
		$row = $this->getDb()->findByLoginName($loginName);
		if ($row && is_array($row) && isset($row['user_id'])) {
			$groupIds = $this->_userGroups->findGroupIdsByUserId($row['user_id']);
			$row['group_ids'] = is_array($groupIds) ? $groupIds : array();
		}

		return $row;
	}

	/**
	 * 验证值登录名是否存在
	 * @param string $loginName
	 * @return boolean
	 */
	public function loginNameExists($loginName)
	{
		return $this->getDb()->loginNameExists($loginName);
	}

	/**
	 * (non-PHPdoc)
	 * @see \libsrv\AbstractService::create()
	 */
	public function create(array $params = array(), $ignore = false)
	{
		$userId = parent::create($params, $ignore);
		if (($userId = (int) $userId) <= 0) {
			return false;
		}

		$groupIds = $this->getFormProcessor()->group_ids;
		if (is_array($groupIds)) {
			$this->_userGroups->modify($userId, $groupIds);
		}

		$dispatcher = Plugin::getInstance();
		$dispatcher->trigger('onAfterSave', array(__METHOD__, &$params, $userId));

		return $userId;
	}

	/**
	 * (non-PHPdoc)
	 * @see \libsrv\AbstractService::modifyByPk()
	 */
	public function modifyByPk($value, array $params = array())
	{
		$rowCount = parent::modifyByPk($value, $params);
		if ($rowCount === false) {
			return false;
		}

		$groupIds = $this->getFormProcessor()->group_ids;
		if (is_array($groupIds)) {
			$rowCount = $this->_userGroups->modify($value, $groupIds);
		}

		$dispatcher = Plugin::getInstance();
		$dispatcher->trigger('onAfterSave', array(__METHOD__, &$params, $value));

		return true;
	}

	/**
	 * 通过主键，编辑多条记录
	 * @param array|integer $values
	 * @param array $params
	 * @return integer
	 */
	public function batchModifyByPk($values, array $params = array())
	{
		$rowCount = $this->getDb()->batchModifyByPk($values, $params);
		return $rowCount;
	}

	/**
	 * 通过主键，将一条记录移至回收站
	 * @param integer $value
	 * @return integer
	 */
	public function trashByPk($value)
	{
		return $this->batchModifyByPk($value, array('trash' => DataUsers::TRASH_Y));
	}

	/**
	 * 通过主键，将多条记录移至回收站
	 * @param array $values
	 * @return integer
	 */
	public function batchTrashByPk(array $values)
	{
		return $this->batchModifyByPk($values, array('trash' => DataUsers::TRASH_Y));
	}

	/**
	 * 通过主键，从回收站还原一条记录
	 * @param integer $value
	 * @return integer
	 */
	public function restoreByPk($value)
	{
		return $this->batchModifyByPk($value, array('trash' => DataUsers::TRASH_N));
	}

	/**
	 * 通过主键，将多条记录移至回收站
	 * @param array $values
	 * @return integer
	 */
	public function batchRestoreByPk(array $values)
	{
		return $this->batchModifyByPk($values, array('trash' => DataUsers::TRASH_N));
	}

	/**
	 * 通过“主键ID”，获取“登录名”
	 * @param integer $userId
	 * @return string
	 */
	public function getLoginNameByUserId($userId)
	{
		$value = $this->getByPk('login_name', $userId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“登录方式”
	 * @param integer $userId
	 * @return string
	 */
	public function getLoginTypeByUserId($userId)
	{
		$value = $this->getByPk('login_type', $userId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“登录密码”
	 * @param integer $userId
	 * @return string
	 */
	public function getPasswordByUserId($userId)
	{
		$value = $this->getByPk('password', $userId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“随机附加混淆码”
	 * @param integer $userId
	 * @return string
	 */
	public function getSaltByUserId($userId)
	{
		$value = $this->getByPk('salt', $userId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“用户名”
	 * @param integer $userId
	 * @return string
	 */
	public function getUserNameByUserId($userId)
	{
		$value = $this->getByPk('user_name', $userId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“邮箱”
	 * @param integer $userId
	 * @return string
	 */
	public function getUserMailByUserId($userId)
	{
		$value = $this->getByPk('user_mail', $userId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“手机号”
	 * @param integer $userId
	 * @return string
	 */
	public function getUserPhoneByUserId($userId)
	{
		$value = $this->getByPk('user_phone', $userId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“注册时间”
	 * @param integer $userId
	 * @return string
	 */
	public function getDtRegisteredByUserId($userId)
	{
		$value = $this->getByPk('dt_registered', $userId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“上次登录时间”
	 * @param integer $userId
	 * @return string
	 */
	public function getDtLastLoginByUserId($userId)
	{
		$value = $this->getByPk('dt_last_login', $userId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“上次更新密码时间”
	 * @param integer $userId
	 * @return string
	 */
	public function getDtLastRepwdByUserId($userId)
	{
		$value = $this->getByPk('dt_last_repwd', $userId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“注册IP”
	 * @param integer $userId
	 * @return integer
	 */
	public function getIpRegisteredByUserId($userId)
	{
		$value = $this->getByPk('ip_registered', $userId);
		return $value ? long2ip((int) $value) : false;
	}

	/**
	 * 通过“主键ID”，获取“上次登录IP”
	 * @param integer $userId
	 * @return integer
	 */
	public function getIpLastLoginByUserId($userId)
	{
		$value = $this->getByPk('ip_last_login', $userId);
		return $value ? long2ip((int) $value) : false;
	}

	/**
	 * 通过“主键ID”，获取“上次更新密码IP”
	 * @param integer $userId
	 * @return integer
	 */
	public function getIpLastRepwdByUserId($userId)
	{
		$value = $this->getByPk('ip_last_repwd', $userId);
		return $value ? long2ip((int) $value) : false;
	}

	/**
	 * 通过“主键ID”，获取“总登录次数”
	 * @param integer $userId
	 * @return integer
	 */
	public function getLoginCountByUserId($userId)
	{
		$value = $this->getByPk('login_count', $userId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“总更新密码次数”
	 * @param integer $userId
	 * @return integer
	 */
	public function getRepwdCountByUserId($userId)
	{
		$value = $this->getByPk('repwd_count', $userId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“是否已验证邮箱”
	 * @param integer $userId
	 * @return string
	 */
	public function getValidMailByUserId($userId)
	{
		$value = $this->getByPk('valid_mail', $userId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“是否已验证手机号”
	 * @param integer $userId
	 * @return string
	 */
	public function getValidPhoneByUserId($userId)
	{
		$value = $this->getByPk('valid_phone', $userId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“是否禁用”
	 * @param integer $userId
	 * @return string
	 */
	public function getForbiddenByUserId($userId)
	{
		$value = $this->getByPk('forbidden', $userId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“是否删除”
	 * @param integer $userId
	 * @return string
	 */
	public function getTrashByUserId($userId)
	{
		$value = $this->getByPk('trash', $userId);
		return $value ? $value : '';
	}

	/**
	 * 获取“是否已验证邮箱”
	 * @param string $validMail
	 * @return string
	 */
	public function getValidMailLangByValidMail($validMail)
	{
		$enum = DataUsers::getValidMailEnum();
		return isset($enum[$validMail]) ? $enum[$validMail] : '';
	}

	/**
	 * 获取“是否已验证手机号”
	 * @param string $validPhone
	 * @return string
	 */
	public function getValidPhoneLangByValidPhone($validPhone)
	{
		$enum = DataUsers::getValidPhoneEnum();
		return isset($enum[$validPhone]) ? $enum[$validPhone] : '';
	}

	/**
	 * 获取“是否禁用”
	 * @param string $forbidden
	 * @return string
	 */
	public function getForbiddenLangByForbidden($forbidden)
	{
		$enum = DataUsers::getForbiddenEnum();
		return isset($enum[$forbidden]) ? $enum[$forbidden] : '';
	}

	/**
	 * 通过“主键ID”，获取“所属用户分组ID”
	 * @param integer $userId
	 * @return array
	 */
	public function getGroupIdsByUserId($userId)
	{
		$row = $this->findByPk($userId);
		if ($row && is_array($row) && isset($row['group_ids'])) {
			return $row['group_ids'];
		}

		return array();
	}

	/**
	 * 获取用户登录随机附加混淆码
	 * @return string
	 */
	public function getSalt()
	{
		return String::randStr(6);
	}

	/**
	 * 加密用户登录密码
	 * @param string $pwd
	 * @param string $salt
	 * @return string
	 */
	public function encrypt($pwd, $salt = '')
	{
		return md5($salt . substr(md5($pwd), 3));
	}

	/**
	 * 通过登录名自动识别登录方式
	 * @param string $loginName
	 * @return string
	 */
	public function getLoginType($loginName)
	{
		if (strpos($loginName, '@')) {
			return DataUsers::LOGIN_TYPE_MAIL;
		}

		if (is_numeric($loginName)) {
			return DataUsers::LOGIN_TYPE_PHONE;
		}

		return DataUsers::LOGIN_TYPE_NAME;
	}

	/**
	 * 是否通过邮箱登录
	 * @param string $loginType
	 * @return boolean
	 */
	public function isMailLogin($loginType)
	{
		return $loginType === DataUsers::LOGIN_TYPE_MAIL;
	}

	/**
	 * 是否通过手机号登录
	 * @param string $loginType
	 * @return boolean
	 */
	public function isPhoneLogin($loginType)
	{
		return $loginType === DataUsers::LOGIN_TYPE_PHONE;
	}

}
