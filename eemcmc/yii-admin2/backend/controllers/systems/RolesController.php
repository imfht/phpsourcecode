<?php

namespace backend\controllers\systems;

use common\helpers\ArrayHelper;

class RolesController extends \backend\controllers\BaseController
{

	/**
	 * 角色列表
	 */
	function actionIndex()
	{
		return $this->render('index', $this->_data);
	}

	/**
	 * 角色权限分配
	 */
	public function actionPermissions($name)
	{
		$auth = \Yii::$app->authManager;

		//角色标识
		$this->_data['role_name'] = $name;

		//获取当前权限
		$my_permissions = $auth->getPermissionsByRole($name);
		$this->_data['my_permissions'] = ArrayHelper::map($my_permissions, 'name', 'name');

		//所有权限
		$permissions = \Yii::$app->params['permissions'];
		$this->_data['permissions'] = $permissions;

		return $this->render('permissions', $this->_data);
	}

}
