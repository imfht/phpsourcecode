<?php
/**
 * 名称：AuthorizationController
 * 功能：后台权限管理
 * 作者: Qiyuan<mqycn@126.com>
 * 更新：http://git.oschina.net/mqycn/thinkAuthorization/
 */

namespace Admin\Controller;

class AuthorizationController extends _AdminController {

	/**
	 * 权限分组列表
	 */
	public function index() {
		$AuthGroup = M("AuthGroup");
		$list = $AuthGroup->select();
		$this->assign("list", $list);

		unset($AuthGroup, $list);

		$this->display();
	}

	/**
	 * 添加权限分组
	 */
	public function Add() {
		$this->Edit(true);
	}

	/**
	 * 编辑权限分组
	 */
	public function Edit($addNew = false) {
		$gid = $this->_gid();

		//初始化权限分组
		if ($addNew === false) {
			if ($gid == 0) {
				$this->error("要编辑的权限组不存在");
			}
			$AuthGroup = M("AuthGroup");
			$group = $AuthGroup->find($gid);
			if (!$group) {
				$this->error("要编辑的权限组不存在");
			}
			unset($AuthGroup);

			$group['ag_auth'] = unserialize($group['ag_auth']);

		} else {
			$group = array("ag_id" => 0, "ag_auth" => array());
		}

		$authlist = $this->_GetAuthList($group['ag_auth']);

		$this->assign("group", $group);
		$this->assign("authlist", $authlist);

		unset($group, $authlist);

		$this->display("Edit");
	}

	/**
	 * 保存权限分组设置
	 */
	public function Save() {

		$AuthGroup = M("AuthGroup");

		if (!$AuthGroup->autoCheckToken($_POST)) {
			unset($AuthGroup);
			$this->error("请不要非法提交");
		}

		$gid = $this->_gid();
		$authArray = $_POST['ag_auth'];
		$ag_auth = serialize($authArray);
		$ag_name = htmlspecialchars($_POST['ag_name']);
		$ag_login = htmlspecialchars($_POST['ag_login']);
		if ($gid != 0) {
			if (!$AuthGroup->find($gid)) {
				unset($AuthGroup);
				$this->error("要操作的分组不存在");
			}
		}
		
		if('' == $ag_name){
			unset($AuthGroup);
			$this->error("分组名称不能为空");
		}

		$findgroup = $AuthGroup->where(array("ag_name" => $ag_name))->find();

		if ($findgroup) {
			if ($findgroup['ag_id'] != $gid) {
				$this->error("对不起，分组[" . $ag_name . "]已经存在，请不要重复添加");
			}
		}
		unset($findgroup);

		$group = array(
			"ag_name" => $ag_name,
			"ag_auth" => $ag_auth,
			"ag_login" => $ag_login,
		);

		if ($gid === 0) {
			$gid = $AuthGroup->add($group);
		} else {
			$group['ag_id'] = $gid;
			$AuthGroup->save($group);
		}

		S('auth_group_' . $gid, $authArray);
		unset($AuthGroup, $group, $authArray);

		if ($gid === 0) {
			$this->success("保存成功", "index");
		} else {
			$this->success("保存成功", "Edit/gid/" . $gid);
		}
	}

	/**
	 * 删除权限分组
	 */
	public function Delete() {
		$gid = $this->_gid();
		if ($gid < 2) {
			$this->error("内置分组不能删除");
		} else {
			$AuthGroup = M("AuthGroup");
			$where = array(
				"ag_id" => $gid,
			);
			if ($AuthGroup->where($where)->delete()) {
				$this->success("删除成功");
			} else {
				$this->success("删除失败，请重试");
			}
			unset($AuthGroup);
		}
	}

	/**
	 * 更新 Controller 和 Action
	 */
	public function Update() {

		//ThinkPHP版本号
		$ver = explode(".", THINK_VERSION);
		$mode = "3.2";
		if ('3' == $ver[0]) {
			switch ($ver[1]) {
			case "0":
			case "1":
				$mode = "3.0";
				break;
			}
		} else {
			$this->error("权限更新：暂时只支持ThinkPHP3.x");
		}

		//获取模块
		$AuthModule = M("AuthModule");
		if ($mode == "3.2") {
			//ThinkPHP3.2和以上
			foreach ($AuthModule->select() as $module) {
				$this->_Update32($module);
			}
		} else {
			//ThinkPHP3.0和3.1
			$module = $AuthModule->find();
			if ($module) {
				$this->_Update30($module);
			}
		}
		unset($AuthModule);
		$this->success("更新完毕", U("FriendlyName"));
	}

	/**
	 * 对扫描到的Controller 和 Action 自定义名称
	 */
	public function FriendlyName() {
		$authlist = $this->_GetAuthList();
		$this->assign("authlist", $authlist);
		unset($authlist);
		$this->display();
	}

	/**
	 * 保存 自定义名称
	 */
	public function SaveFriendlyName() {
		$this->_SaveFriendly("AuthModule", "am_id", "am_name");
		$this->_SaveFriendly("AuthController", "ac_id", "ac_name");
		$this->_SaveFriendly("AuthAction", "aa_id", "aa_name");
		$this->success("保存自定义名称成功", U("FriendlyName"));
	}

	/**
	 * 兼容ThinkPHP3.0
	 */
	private function _Update30($module) {
		$module['am_path'] = "admin";
		$this->_UpdateCore($module, "Lib/Action/", "Action", false);
	}

	/**
	 * 兼容ThinkPHP3.2
	 */
	private function _Update32($module) {
		$this->_UpdateCore($module, $module['am_path'] . "/Controller/", "Controller", true);
	}

	/**
	 * 扫描Controller
	 */
	private function _UpdateCore($module, $controllerPath, $controllerAddon, $useNamespace) {
		$AuthController = M("AuthController");

		//所有 Controller 都不可用
		$where = array("ac_mid" => $module['am_id']);
		$data = array("ac_error" => 1);
		$AuthController->where($where)->save($data);

		//遍历 Controller 目录下的所有控制器
		$count = 0;
		$path = APP_PATH . $controllerPath;

		foreach (scandir($path) as $fileName) {
			if ($controllerAddon . ".class.php" == substr($fileName, -10 - strlen($controllerAddon))) {
				$filePath = $path . $fileName;
				$count++;
				$controllerName = substr($fileName, 0, -10 - strlen($controllerAddon));
				if (substr($controllerName, 0, 1) != "_") {

					//根据控制器文件查询数据库中的控制器
					$where = array(
						"ac_mid" => $module["am_id"],
						"ac_path" => $controllerName,
					);
					$controller = $AuthController->where($where)->find();
					if (!$controller) {
						$controller = $where;
						$controller['ac_name'] = $controllerName;
						$controller['ac_error'] = 0;
						$controller["ac_id"] = $AuthController->add($controller);
					} else {
						//控制器存在，更新为可用
						$controller['ac_error'] = 0;
						$AuthController->save($controller);
					}

					//载入类，设置类名称，准备下一步分析
					require_once $filePath;
					if ($useNamespace) {
						$controller['class'] = $module['am_path'] . "\\Controller\\" . $controller["ac_path"];
					} else {
						$controller['class'] = $controller["ac_path"];
					}
					$controller['class'] .= $controllerAddon;
					$this->_UpdateAction($module, $controller);
				}
			}
		}
		unset($AuthController);

		//对不存在控制器的模块，设置为 1
		$module['am_error'] = $count == 0 ? 1 : 0;
		M("AuthModule")->save($module);
		
	}

	/**
	 * 扫描Action
	 */
	private function _UpdateAction($module, $controller) {
		$AuthAction = M("AuthAction");

		//所有 Action 都不可用
		$where = array("aa_cid" => $controller['ac_id']);
		$data = array("aa_error" => 1);
		$AuthAction->where($where)->save($data);

		//遍历类，如果存在未定义的方法，则添加数据库
		$hideMothod = array(

			//3.2 内置
			"display", "show", "fetch", "buildHtml",
			"theme", "assign", "get", "error",
			"success", "ajaxReturn", "redirect",

			//3.0内置
			"getActionName", "isAjax", "fetch",

			//自定义

		);

		//遍历类的方法
		foreach (get_class_methods($controller['class']) as $method) {

			if (substr($method, 0, 1) == "_" || in_array($method, $hideMothod)) {
				// _开头， 或者 hideMothod 列表中的方法
			} else {

				//根据控制器文件查询数据库中的控制器
				$where = array(
					"aa_cid" => $controller["ac_id"],
					"aa_path" => $method,
				);
				$action = $AuthAction->where($where)->find();
				if (!$action) {
					$action = $where;
					$action['aa_name'] = $method;
					$action['aa_error'] = 0;
					$action['aa_key'] = strtolower($module['am_path'] . "/" . $controller['ac_path'] . "/" . $method);
					$action["aa_id"] = $AuthAction->add($action);
				} else {
					//控制器存在，更新为可用
					$action['aa_error'] = 0;
					$AuthAction->save($action);
				}
			}
		}
		unset($AuthAction);
	}

	/**
	 * 输出权限列表
	 */
	private function _GetAuthList($_group = array()) {
		//列出所有权限组
		$authlist = array();
		$AuthModule = M("AuthModule");
		$AuthController = M("AuthController");
		$AuthAction = M("AuthAction");

		foreach ($AuthModule->field("am_id,am_path,am_name")->select() as $module) {
			$module['list'] = array();
			foreach ($AuthController->where("ac_mid='" . $module['am_id'] . "'")->field("ac_id,ac_path,ac_name")->select() as $controller) {
				foreach ($AuthAction->where("aa_cid='" . $controller['ac_id'] . "'")->field("aa_id,aa_key,aa_path,aa_name")->select() as $action) {
					$action['selected'] = in_array($action['aa_key'], $_group);
					$controller['list'][] = $action;
				}
				$module['list'][] = $controller;
			}
			$authlist[] = $module;
		}

		unset($AuthModule, $AuthController, $AuthAction);
		return $authlist;
	}

	/**
	 * 循环保存自定义名称（核心）
	 */
	private function _SaveFriendly($model, $id, $name) {

		$_ID = $_POST[$id];
		$_Name = $_POST[$name];
		if (is_array($_ID) && is_array($_Name)) {
			$_Model = M($model);
			foreach ($_ID as $index => $_id) {
				if (is_numeric($_id)) {
					$_name = htmlspecialchars($_Name[$index]);
					$item = array(
						$id => $_id,
						$name => $_name,
					);
					$_Model->save($item);
				}
			}
			unset($_Model, $item);
		}
	}

	/**
	 * 快速获取 分组编号
	 */
	private function _gid() {
		$gid = isset($_REQUEST['gid']) ? $_REQUEST['gid'] : 0;
		if (!is_numeric($gid)) {
			$gid = 0;
		} else {
			$gid = (int) $gid;
		}
		return $gid;
	}

}