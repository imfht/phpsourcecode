<?php

namespace backend\controllers\api\systems;

class RolesController extends \backend\controllers\BaseController
{

	/**
	 * 角色列表
	 */
	function actionIndex()
	{
		$auth = \Yii::$app->authManager;

		$roles = $auth->getRoles();
		$items = [];
		foreach ($roles as $key => $value)
		{
			$items[] = $value;
		}
		$data = [
			'items' => $items,
			'total' => count($roles)
		];
		return $this->success($data);
	}

	/**
	 * 更新角色
	 */
	function actionUpdate()
	{
		$name = $this->post('name');
		$description = $this->post('description');
		if (empty($name))
		{
			return $this->failure('参数错误');
		}
		$auth = \Yii::$app->authManager;
		$role = $auth->getRole($name);
		if (empty($role))
		{
			return $this->failure('角色不存在');
		}
		$role->description = $description;
		$auth->update($name, $role);
		return $this->success('更新成功');
	}

	/**
	 * 创建角色
	 */
	function actionCreate()
	{
		$name = $this->post('name');
		$description = $this->post('description');
		if (empty($name))
		{
			return $this->failure('角色标识不能为空');
		}
		$auth = \Yii::$app->authManager;
		$role = $auth->createRole($name);
		$role->description = $description;
		$auth->add($role);
		return $this->success();
	}

	/**
	 * 删除角色
	 */
	function actionDelete()
	{
		$name = $this->post('name');
		if (empty($name))
		{
			return $this->failure('参数错误');
		}
		if ($name == 'admin')
		{
			return $this->failure('不可以删除管理员角色');
		}
		if ($name == 'auper_admin')
		{
			return $this->failure('不可以删除超级管理员角色');
		}
		$auth = \Yii::$app->authManager;
		$role = $auth->getRole($name);
		if (empty($role))
		{
			return $this->failure('角色不存在');
		}
		$auth->remove($role);
		return $this->success('删除成功');
	}

	/**
	 * 分配权限
	 */
	function actionSavePermissions()
	{
		//判断参数
		$name = $this->post('role_name');
		if (empty($name))
		{
			return $this->failure('角色不能为空');
		}
		if (in_array($name, ['admin', 'guest']))
		{
			return $this->failure('系统角色不可以操作');
		}
		//获取角色信息
		$auth = \Yii::$app->authManager;
		$role = $auth->getRole($name);
		if (empty($role))
		{
			return $this->failure('要更新的角色不存在');
		}

		//移除老数据
		$auth->removeChildren($role);

		//重新分配数据
		$permissions = $this->post('permissions');
		$all_permissions = \Yii::$app->params['permissions']; //取权限配置
		foreach ($permissions as $permission)
		{
			//拆分权限id
			list($main_id, $sub_id) = explode('_', $permission);

			//获取目标id动作列表
			$actions = $all_permissions[$main_id]['children'][$sub_id]['actions'];
			foreach ($actions as $action)
			{
				//是否存在
				$child = $auth->getPermission($action);
				if (empty($child))
				{
					//不存在则添加
					$child = $auth->createPermission($action);
					$auth->add($child);
				}
				//添加权限到角色
				$auth->addChild($role, $child);
			}
		}

		return $this->success();
	}

}
