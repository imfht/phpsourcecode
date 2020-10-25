<?php

namespace Admin\Model;

use Admin\Lib\AdminUser;
use CigoAdminLib\Lib\Admin;
use CigoAdminLib\Logic\UserCtrlLogic;
use CigoAdminLib\Model\UserCtrlModel;

class ManagerModel extends UserCtrlModel {
	/* 用户模型自动验证 */
	protected $_validate = array(
		array('username', 'require', '用户名不能为空!', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('username', '6,20', '用户名长度为6-20个字符', self::EXISTS_VALIDATE, 'length'),
		array('username', '', '用户名已存在', self::EXISTS_VALIDATE, 'unique'),

		array('nickname', 'require', '昵称不能为空!', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('nickname', '1,16', '昵称长度为1-16个字符', self::EXISTS_VALIDATE, 'length'),
		array('nickname', '', '昵称被占用', self::EXISTS_VALIDATE, 'unique'),
	);

	/* 用户模型自动完成 */
	protected $_auto = array(
		array('create_time', NOW_TIME, self::MODEL_INSERT),
		array('create_ip', 'get_client_ip', self::MODEL_INSERT, 'function', 1),
		array('last_log_time', NOW_TIME, self::MODEL_BOTH),
		array('last_log_ip', 'get_client_ip', self::MODEL_BOTH, 'function', 1)
	);

	/**
	 * 修改昵称
	 *
	 * @param $params
	 *
	 * @return array
	 */
	public function modifyNickName($params) {
		if (!isset($_POST['nickname']) || empty($_POST['nickname'])) {
			return array(
				Admin::DATA_TAG_RES => FALSE,
				Admin::DATA_TAG_INFO => '请输入新昵称！'
			);
		}
		if (!isset($_POST['password']) || empty($_POST['password'])) {
			return array(
				Admin::DATA_TAG_RES => FALSE,
				Admin::DATA_TAG_INFO => '请输入用户密码！'
			);
		}

		$logUserInfo = session(MODULE_NAME . UserCtrlLogic::DATA_TAG_USERINFO);

		// 密码验证
		$result = $this->doLogIn(array(
			UserCtrlLogic::DATA_TAG_USERNAME => $logUserInfo[UserCtrlLogic::DATA_TAG_USERNAME],
			UserCtrlLogic::DATA_TAG_PASSWORD => $params['password']
		));
		if (!$result[Admin::DATA_TAG_RES]) {
			return $result;
		}

		//更新昵称
		$condition = array(
			'id' => $logUserInfo[UserCtrlLogic::DATA_TAG_ID],
			'username' => $logUserInfo[UserCtrlLogic::DATA_TAG_USERNAME],
			'password' => encryptUserPwd($params[UserCtrlLogic::DATA_TAG_PASSWORD])
		);
		$data = $this->validate(array(
			array('nickname', 'require', '昵称不能为空!', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
			array('nickname', '1,16', '昵称长度为1-16个字符', self::EXISTS_VALIDATE, 'length'),
			array('nickname', '', '昵称被占用', self::EXISTS_VALIDATE, 'unique')
		))->create(array(
			'id' => $logUserInfo[UserCtrlLogic::DATA_TAG_ID],
			'nickname' => $params['nickname']
		));
		if (!$data) {
			return array(
				Admin::DATA_TAG_RES => FALSE,
				Admin::DATA_TAG_INFO => $this->getError()
			);
		}
		$result = $this->where($condition)->save($data);

		//判断结果
		if ($result) {
			//保存用户信息到Session
			session(MODULE_NAME . UserCtrlLogic::DATA_TAG_USERINFO, array(
				UserCtrlLogic::DATA_TAG_ID => $logUserInfo[UserCtrlLogic::DATA_TAG_ID],
				UserCtrlLogic::DATA_TAG_USERNAME => $logUserInfo[UserCtrlLogic::DATA_TAG_USERNAME],
				UserCtrlLogic::DATA_TAG_NICKNAME => $params['nickname']
			));
			//保存登陆时间
			session(MODULE_NAME . UserCtrlLogic::DATA_TAG_LOGTIME, time());

			return array(
				Admin::DATA_TAG_RES => TRUE,
				Admin::DATA_TAG_INFO => '昵称修改成功！'
			);
		} else {
			return array(
				Admin::DATA_TAG_RES => FALSE,
				Admin::DATA_TAG_INFO => '昵称修改失败！'
			);
		}
	}

	/**
	 * 修改密码
	 *
	 * @param $params 参数
	 *
	 * @return array
	 */
	public function modifyPwd($params) {
		if (!isset($_POST['oldPwd']) || empty($_POST['oldPwd'])) {
			return array(
				Admin::DATA_TAG_RES => FALSE,
				Admin::DATA_TAG_INFO => '请输入原密码！'
			);
		}
		if (!isset($_POST['newPwd']) || empty($_POST['newPwd'])) {
			return array(
				Admin::DATA_TAG_RES => FALSE,
				Admin::DATA_TAG_INFO => '请输入新密码！'
			);
		}
		if (!isset($_POST['repeat']) || empty($_POST['repeat'])) {
			return array(
				Admin::DATA_TAG_RES => FALSE,
				Admin::DATA_TAG_INFO => '请确认新密码！'
			);
		}
		if (trim($_POST['repeat']) !== trim($_POST['newPwd'])) {
			return array(
				Admin::DATA_TAG_RES => FALSE,
				Admin::DATA_TAG_INFO => '两次新密码输入不一致！'
			);
		}

		$logUserInfo = session(MODULE_NAME . UserCtrlLogic::DATA_TAG_USERINFO);

		// 密码验证
		$result = $this->doLogIn(array(
			UserCtrlLogic::DATA_TAG_USERNAME => $logUserInfo[UserCtrlLogic::DATA_TAG_USERNAME],
			UserCtrlLogic::DATA_TAG_PASSWORD => $params['oldPwd']
		));
		if (!$result[Admin::DATA_TAG_RES]) {
			return array(
				Admin::DATA_TAG_RES => FALSE,
				Admin::DATA_TAG_INFO => '旧密码错误！'
			);
		}

		//检查密码是否修改
		if ($params['newPwd'] == $params['oldPwd']) {
			return array(
				Admin::DATA_TAG_RES => FALSE,
				Admin::DATA_TAG_INFO => '密码未做修改，请尝试重新修改！'
			);
		}

		//检查密码格式
		if (!formatCheckPassword($params['newPwd'])) {
			return array(
				Admin::DATA_TAG_RES => FALSE,
				Admin::DATA_TAG_INFO => '密码格式错误<br/>6～20个大小写字母和数字组成！'
			);
		}

		//更新密码
		$condition = array(
			'id' => $logUserInfo[UserCtrlLogic::DATA_TAG_ID],
			'username' => $logUserInfo[UserCtrlLogic::DATA_TAG_USERNAME]
		);

		$data = $this->validate(array(

			array('password', 'require', '密码不能为空!', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH)
		))->create(array(
			'id' => $logUserInfo[UserCtrlLogic::DATA_TAG_ID],
			'password' => encryptUserPwd($params['newPwd'])
		));
		if (!$data) {
			return array(
				Admin::DATA_TAG_RES => FALSE,
				Admin::DATA_TAG_INFO => $this->getError()
			);
		}
		$result = $this->where($condition)->save($data);

		//判断结果
		if ($result) {
			$userApi = new AdminUser();
			$userApi->doLogOut();

			return array(
				Admin::DATA_TAG_RES => TRUE,
				Admin::DATA_TAG_INFO => '密码修改成功！'
			);
		} else {
			return array(
				Admin::DATA_TAG_RES => FALSE,
				Admin::DATA_TAG_INFO => '密码修改失败！'
			);
		}
	}


	function saveUserInfoToSession($userInfo = array()) {
		//保存用户信息到Session
		session(MODULE_NAME . UserCtrlLogic::DATA_TAG_USERINFO, array(
			UserCtrlLogic::DATA_TAG_ID => $userInfo['id'],
			UserCtrlLogic::DATA_TAG_USERNAME => $userInfo['username'],
			UserCtrlLogic::DATA_TAG_NICKNAME => $userInfo['nickname']
		));

		//保存登陆时间
		session(MODULE_NAME . UserCtrlLogic::DATA_TAG_LOGTIME, time());
	}

	function doLogIn($userInfo) {
		$condition = array(
			'username' => $userInfo[UserCtrlLogic::DATA_TAG_USERNAME]
		);
		$result = $this->where($condition)->find();
		if ($result) {
			if (encryptUserPwd($userInfo[UserCtrlLogic::DATA_TAG_PASSWORD]) != $result['password']) {
				return $this->tipLogPwdError();
			}

			$this->saveUserInfoToSession($result);
			return $this->tipLogSuccess();
		} else {
			return $this->tipLogUserNotExist();
		}
	}
}
