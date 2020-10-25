<?php

namespace backend\controllers\systems;

class UsersController extends \backend\controllers\BaseController
{

	/**
	 * 管理员管理
	 */
	function actionIndex()
	{
		$auth = \Yii::$app->authManager;
		$roles = $auth->getRoles();
		$this->_data['roles'] = $roles;
		return $this->render('index', $this->_data);
	}
}
