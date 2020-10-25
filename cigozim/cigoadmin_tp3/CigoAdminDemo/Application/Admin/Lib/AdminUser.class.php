<?php

namespace Admin\Lib;

use Admin\Model\ManagerModel;
use CigoAdminLib\Logic\UserCtrlLogic;

class AdminUser extends UserCtrlLogic {

	/**
	 * API调用模型实例
	 *
	 * @access protected
	 * @var object
	 */
	protected $model;

	/**
	 * 构造方法，实例化操作模型
	 */
	public function __construct() {
		$this->model = new ManagerModel();
	}

	/**
	 * 修改昵称
	 *
	 * @param $params
	 *
	 * @return array
	 */
	public function modifyNickName($params) {
		return $this->model->modifyNickName($params);
	}

	/**
	 * 修改密码
	 *
	 * @param $params 参数
	 *
	 * @return array
	 */
	public function modifyPwd($params) {
		return $this->model->modifyPwd($params);
	}

	protected function checkIfTimeOut() {
		if (time() - session(MODULE_NAME . UserCtrlLogic::DATA_TAG_LOGTIME) > 3600) {
			return false;
		}
		return true;
	}
}
