<?php

namespace Common\Controller;

use Common\Controller\AppframeController;

class AdminbaseController extends AppframeController {

	function _initialize() {
		parent::_initialize();
		$this->assign("js_debug", APP_DEBUG ? "?v=" . time() : "");
		if (!!is_login()) {
			$user = session('user');
			if (!$this->check_access($user['role_id'])) {
				$this->error("您没有访问权限！");
				exit();
			}
			$this->assign("admin", $user);
		} else {
			$this->redirect('admin/public/login');
		}
		$this->initMenu();
	}

	/**
	 * 初始化后台菜单
	 */
	public function initMenu() {
		$Menu = F("Menu");
		if (!$Menu) {
			$model = new \Admin\Model\MenuModel();
			$model->menu_cache();
		}
	}

	/**
	 *  排序 排序字段为listorders数组 POST 排序字段为：listorder
	 */
	protected function listorders($model) {
		if (!is_object($model)) {
			return false;
		}
		$pk = $model->getPk(); //获取主键名称
		$ids = $_POST['listorders'];
		foreach ($ids as $key => $r) {
			$data['listorder'] = $r;
			$model->where(array($pk => $key))->save($data);
		}
		return true;
	}

	protected function page($Total_Size = 1, $Page_Size = 0, $Current_Page = 1, $listRows = 6, $PageParam = '', $PageLink = '', $Static = FALSE) {
		if ($Page_Size == 0) {
			$Page_Size = C("PAGE_LISTROWS");
		}
		if (empty($PageParam)) {
			$PageParam = C("VAR_PAGE");
		}
		$Page = new \Common\Lib\Util\Page($Total_Size, $Page_Size, $Current_Page, $listRows, $PageParam, $PageLink, $Static);
		$Page->SetPager('Admin', '{first}{prev}&nbsp;{liststart}{list}{listend}&nbsp;{next}{last}', array("listlong" => "6", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));
		return $Page;
	}

	/**
	 * 获取菜单导航
	 * @param type $app
	 * @param type $model
	 * @param type $action
	 */
	public static function getMenu() {
		$menuid = (int) I('get.menuid');
		$menuid = $menuid ? $menuid : cookie("menuid", "", array("prefix" => ""));
		//cookie("menuid",$menuid);
		$db = D("Menu");
		$info = $db->cache(true, 60)->where(array("id" => $menuid))->getField("id,action,app,model,parentid,data,type,name");
		$find = $db->cache(true, 60)->where(array("parentid" => $menuid, "status" => 1))->getField("id,action,app,model,parentid,data,type,name");
		if ($find) {
			array_unshift($find, $info[$menuid]);
		} else {
			$find = $info;
		}
		foreach ($find as $k => $v) {
			$find[$k]['data'] = $find[$k]['data'] . "&menuid=$menuid";
		}
		return $find;
	}

	/**
	 * 当前位置
	 * @param $id 菜单id
	 */
	final public static function current_pos($id) {
		$menudb = M("Menu");
		$r = $menudb->where(array('id' => $id))->find();
		$str = '';
		if ($r['parentid']) {
			$str = self::current_pos($r['parentid']);
		}
		return $str . $r['name'] . ' > ';
	}

	private function check_access($roleid) {
		/* 如果用户角色是1，则无需判断 */
		if ($roleid == 1) {
			return true;
		}
		$group = GROUP_NAME;
		$model = MODULE_NAME;
		$action = ACTION_NAME;
		return M("Access")->where("role_id=$roleid and g='$group' and m='$model' and a='$action'")->count();
	}

}
