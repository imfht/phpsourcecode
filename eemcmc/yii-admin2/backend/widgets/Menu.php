<?php

namespace backend\widgets;

/**
 * 用户头部信息组件
 *
 * @author ken <vb2005xu@qq.com>
 */
class Menu extends \common\widgets\BaseWidget
{

	/**
	 * 所有菜单数组
	 * @var array
	 */
	public $all_menus = [];

	/**
	 * 获取当前角色菜单
	 * @return array
	 */
	private function _getMenus()
	{
		//非管理员判断权限显示菜单
		$menus = [];
		foreach ($this->all_menus as $key => $menu)
		{
			if (isset($menu['children']))
			{

				foreach ($menu['children'] as $key => &$child)
				{
					if (!$this->controller->user->can($child['action']))
					{
						unset($menu['children'][$key]);
					}
				}
				if (!empty($menu['children']))
				{
					$menus[] = $menu;
				}
			}
			else
			{
				if ($this->controller->user->can($menu['action']))
				{
					$menus[] = $menu;
				}
			}
		}
		return $menus;
	}

	/**
	 * 执行
	 * @return view
	 */
	public function run()
	{
		//取所有菜单列表
		$this->all_menus = \Yii::$app->params['menus'];

		//判断是否管理员
		if ($this->controller->user->identity && $this->controller->user->identity->isAdmin())
		{
			//管理员显示所有菜单
			$menus = $this->all_menus;
		}
		else
		{
			$menus = $this->_getMenus();
		}
		$this->_data['menus'] = $menus;

		//取当前控制器
		$controller = explode('/', $this->controller->uniqueId);
		$this->_data['controller'] = $controller[0];
		if (count($controller) >= 2)
		{
			$this->_data['action'] = "{$controller[0]}/{$controller[1]}";
		}
		else
		{
			$this->_data['action'] = $this->controller->action->uniqueId;
		}
		return $this->render('menu', $this->_data);
	}

}
