<?php
namespace Admin\Controller;
// 角色模块
class RoleController extends CommonController {

	function _filter(&$map) {
		$map['name'] = array('like', "%" . $_POST['name'] . "%");
	}

	
	
	
	
	/**
	 +----------------------------------------------------------
	 * 增加组操作权限
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return void
	 +----------------------------------------------------------
	 * @throws FcsException
	 +----------------------------------------------------------
	 */
	public function setUser() {
		$id = I('groupUserId');
		$groupId = I('groupId');
		$group = D("Role");
		$group->delGroupUser($groupId);
		$group->delGroupUsers($groupId, $id);
		$result = $group->setGroupUsers($groupId, $id);
		if ($result === false) {
			
			$this->mtReturn(300, '更改用户分组失败！');
			
		} else {
			$this->mtReturn(201, '更改用户分组成功！');
			
		}
	}

	/**
	 +----------------------------------------------------------
	 * 组操作权限列表
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return void
	 +----------------------------------------------------------
	 * @throws FcsException
	 +----------------------------------------------------------
	 */
	public function user() {
		//读取系统的用户列表
		$user = D("Member");
		$list2 = $user->field('uid,nickname')->select();
		//echo $user->getlastsql();
		//dump(	$user);
		foreach ($list2 as $vo) {
			
			$userList[$vo['uid']] ='<span class="label label-primary">' .$vo['nickname'].'</span>';
		}

		$group = D("Role");
		$list = $group->field('id,name')->select();
		foreach ($list as $vo) {
			$groupList[$vo['id']] = $vo['name'];
		}
		$this->assign("groupList", $groupList);

		//获取当前用户组信息
		$groupId = isset($_GET['id']) ? $_GET['id'] : '';
		$groupUserList = array();
		if (!empty($groupId)) {
			$this->assign("selectGroupId", $groupId);
			//获取当前组的用户列表
			$list = $group->getGroupUserList($groupId);
			
			foreach ($list as $vo) {
				$groupUserList[$vo['uid']] = $vo['uid'];
			}
		}
		$this->assign('groupUserList', $groupUserList);
		$this->assign('userList', $userList);
		
		$this->display();

		return;
	}

	public function _before_edit() {
		$Group = D('Role');
		//查找满足条件的列表数据
		$classTree = $Group->field('id,name,pid')->select();
		$list = list_to_tree($classTree,'id','pid','_child',0);
		$this->assign('list', $list);
	}

	public function _before_add() {
		$Group = D('Role');
		//查找满足条件的列表数据
		$classTree = $Group->field('id,name,pid')->select();
		$list = list_to_tree($classTree,'id','pid','_child',0);
		$this->assign('list', $list);
	}

	public function select() {
		$map = $this->_search();
		//创建数据对象
		$Group = D('Role');
		//查找满足条件的列表数据
		$classTree = $Group->field('id,name,pid')->select();
		$list = list_to_tree($classTree,'id','pid','_child',0);
		$this->assign('list', $list);
		$this->display();
		return;
	}

	public function access() {
		//获取当前用户组项目权限信息
		$groupId = I('get.groupId','');
		$menu = array();
		$currentMenu = array();
		$node = D("Node");
		$menu = $node->getAllNode();
		
		
		
		$pid = $node->getByPid(0);
		
		$Group = D('Role');
		$currentMenuList = $Group->getGroupAllList($groupId);
		
		
		foreach ($currentMenuList as $value) {
			$currentMenu[] = $value["id"];
		}
		
		$this->assign('list', $menu);
		$this->assign("nodeid", $pid['id']);
		$this->assign('groupId', $groupId);
		$this->assign('currentMenu', $currentMenu);
		$this->display();
		
	}

	public function setGroupAll() {
		$groupId = I('groupId');
		$ids = I('c');
		$group = D("Role");
		$group->delGroupAllNode($groupId);
		$result = $group->setGroupAll($groupId, $ids);

		if ($result === false) {
			$this->mtReturn(300, '授权失败！');
			
		} else {
			$this->mtReturn(201, '授权成功！');
			
		}
	}





	



}

?>