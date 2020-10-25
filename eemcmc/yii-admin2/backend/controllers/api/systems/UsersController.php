<?php

namespace backend\controllers\api\systems;

use backend\models\User;
use common\helpers\ArrayHelper;

class UsersController extends \backend\controllers\BaseController
{

	/**
	 * 管理员列表
	 */
	function actionIndex($page = 1, $page_size = 20)
	{
		$get = $this->request->get();
		//创建对象
		$user = User::find()->page($page, $page_size);

		//排序
		if (!empty($get['sort_field']) && !empty($get['sort_type']))
		{
			$user->orderBy("{$get['sort_field']} {$get['sort_type']}");
		}

		$users = $user->all();
		foreach ($users as &$value)
		{
			$new_value = $value->toArray();

			$roles = $value->getRoles();
			$new_value['roles'] = ArrayHelper::map($roles, 'name', 'name');
			$new_value['role_names'] = implode(', ', ArrayHelper::map($roles, 'name', 'description'));
			$value = $new_value;
		}
		$data = [
			'items' => $users,
			'total' => $user->count()
		];
		return $this->success($data);
	}

	/**
	 * 
	 * @return type
	 */
	function actionUpdate()
	{
		//获取参数
		$id = $this->post('id');
		$username = $this->post('username');
		$realname = $this->post('realname');
		$password = $this->post('password');
		$roles = $this->post('roles');

		//判断管理员id是否正确
		$user = User::findOne($id);
		if (empty($user))
		{
			return $this->failure('管理员不存在');
		}

		//判断要修改的昵称是否已存在
		if ($user->username != $username)
		{
			$user_obj = User::findByUsername($username);
			if (!empty($user_obj) && $user_obj->id != $user->id)
			{
				return $this->failure('管理员用户名已存在');
			}
		}

		//修改专辑
		$attributes = [
			'username' => $username,
			'realname' => $realname,
		];
		if (!empty($password))
		{
			$attributes['password_hash'] = $password;
		}
		$user->attributes = $attributes;
		$user->save();
		if ($user->hasErrors())
		{
			return $this->failure($user->getError());
		}

		//更新角色
		$auth = \Yii::$app->authManager;
		$auth->revokeAll($user->id);
		foreach ($roles as $role_name)
		{
			$role = $auth->getRole($role_name);
			$auth->assign($role, $user->id);
		}
		return $this->success();
	}

	/**
	 * 创建管理员
	 */
	function actionCreate()
	{
		$username = $this->post('username');
		$realname = $this->post('realname');
		$password = $this->post('password');
		$roles = $this->post('roles');

		//是否选择角色
		if (empty($roles))
		{
			return $this->failure('至少选择一个角色');
		}

		//是否存在的管理员
		$user = User::findByUsername($username);
		if (!empty($user))
		{
			return $this->failure('该管理员已存在');
		}

		//组织管理员对象属性
		$attributes = [
			'username' => $username,
			'realname' => $realname,
			'password_hash' => $password,
		];

		//创建管理员
		$user = User::create($attributes);
		if ($user->hasErrors())
		{
			return $this->failure($user->getError());
		}

		//关联角色
		$auth = \Yii::$app->authManager;
		foreach ($roles as $role_name)
		{
			$role = $auth->getRole($role_name);
			$auth->assign($role, $user->id);
		}

		return $this->success();
	}

	/**
	 * 删除管理员
	 */
	function actionDelete()
	{
		$id = $this->post('id');
		$user = User::findOne($id);
		if (empty($user))
		{
			return $this->failure('要删除的管理员不存在');
		}
		$user->delete();
		return $this->success();
	}

}
