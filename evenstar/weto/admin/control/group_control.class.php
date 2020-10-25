<?php

/*
 * Copyright (C) xiuno.com
 */

!defined('FRAMEWORK_PATH') && exit('FRAMEWORK_PATH not defined.');

include BBS_PATH.'admin/control/admin_control.class.php';

class group_control extends admin_control {
	
	function __construct(&$conf) {
		parent::__construct($conf);
		$this->_checked['bbs'] = ' class="checked"';
		$this->check_admin_group();
	}
	
	// 列表
	public function on_index() {
		$this->on_list();
	}	
	
	public function on_list() {
		$this->_title[] = '管理用户组';
		$this->_nav[] = '<a href="./">管理用户组</a>';
		
		// hook admin_group_update_before.php
		
		$groups = $this->group->count();
		$grouplist = $this->group->index_fetch(array(), array(), 0, 100);
		
		// 显示数据初始化
		$grouplist1 = $grouplist2 = $grouplist3 = array();
		$newgroupid = 0;
		$newgroupid2 = 1001;
		foreach($grouplist as &$group) {
			if($group['groupid'] < 8) {
				$grouplist1[$group['groupid']] = $group;
			} elseif($group['groupid'] > 1000) {
				$group['groupid'] > 1000 && $group['groupid'] > $newgroupid2 && $newgroupid2 =  $group['groupid'];
				$grouplist2[$group['groupid']] = $group;
			} else {
				$group['groupid'] > $newgroupid && $newgroupid = $group['groupid'];
				$grouplist3[$group['groupid']] = $group;
			}
			$this->group->format($group);
		}
		$newgroupid++;
		$newgroupid2++;
		
		$this->view->assign('newgroupid', $newgroupid);
		$this->view->assign('newgroupid2', $newgroupid2);
		$this->view->assign('grouplist1', $grouplist1);
		$this->view->assign('grouplist2', $grouplist2);
		$this->view->assign('grouplist3', $grouplist3);
		$this->view->assign('grouplist', $grouplist);
		
		// hook admin_group_list_view_before.php
		
		$this->view->display('group_list.htm');
	}
	
	// 保存 on_list 数据
	public function on_save() {
		// 更新 grouplist
		$error = array();
		
		if($this->form_submit()) {
			
			$namearr = core::gpc('name', 'P');
			$creditsfromarr = core::gpc('creditsfrom', 'P');
			$creditstoarr = core::gpc('creditsto', 'P');

			// update group
			foreach((array)$namearr as $groupid=>$v) {
				$error[$groupid] = array();
				$name = $namearr[$groupid];
				$creditsfrom = intval($creditsfromarr[$groupid]);
				$creditsto = intval($creditstoarr[$groupid]);
				
				// 错误检查
				$error[$groupid]['name'] = $this->group->check_name($name);
				$error[$groupid]['creditsto'] = $groupid > 10 && $groupid < 1000 ? $this->group->check_creditsto($creditsto) : '';
				
				if(!array_filter($error[$groupid])) {
					$group = $this->group->read($groupid);
					$group['name'] = $name;
					$group['creditsfrom'] = $creditsfrom;
					$group['creditsto'] = $creditsto;
					$this->group->update($group);
				}
			}
			
			// add new group
			$newnamearr = core::gpc('newname', 'P');
			$newcreditsfromarr = core::gpc('newcreditsfrom', 'P');
			$newcreditstoarr = core::gpc('newcreditsto', 'P');
			foreach((array)$newnamearr as $groupid=>$v) {
				$error[$groupid] = array();
				$name = $newnamearr[$groupid];
				$creditsfrom = intval($newcreditsfromarr[$groupid]);
				$creditsto = intval($newcreditstoarr[$groupid]);
				
				// 错误检查
				$error[$groupid]['creditsto'] = $groupid > 10 && $groupid < 1000 && $name ? $this->group->check_creditsto($creditsto) : '';
				
				if($name && !array_filter($error[$groupid])) {
					$group = array();
					$group['groupid'] = $groupid;
					$group['name'] = $name;
					$group['creditsfrom'] = $creditsfrom;
					$group['creditsto'] = $creditsto;
					$group['maxcredits'] = 0;
					$group['maxgolds'] = 0;
					$group['allowread'] = 1;
					$group['allowthread'] = 0;
					$group['allowpost'] = 0;
					$group['allowreply'] = 0;
					$group['allowattach'] = 0;
					$group['allowdown'] = 0;
					$group['allowtop'] = 0;
					$group['allowdigest'] = 0;
					$group['allowupdate'] = 0;
					$group['allowdelete'] = 0;
					$group['allowmove'] = 0;
					$group['allowbanuser'] = 0;
					$group['allowdeleteuser'] = 0;
					$group['allowviewip'] = 0;
					$this->group->create($group);
				}
			}
			
			$this->runtime->xupdate('grouparr');
			// hook admin_group_save_submit_after.php
		}
		
		$this->message($error);
	}
	
	// 预留
	public function on_detail() {
		$this->_title[] = '用户组详情';
		$this->_nav[] = '用户组详情';
		
		$groupid = intval(core::gpc('groupid'));

		$group = $this->group->read($groupid);
		$this->check_group_exists($group);
		
		$input = $error = array();
		if($this->form_submit()) {
			
			$name = core::gpc('name', 'P');
			$creditsfrom = intval(core::gpc('creditsfrom', 'P'));
			$creditsto = intval(core::gpc('creditsto', 'P'));
			$maxcredits = intval(core::gpc('maxcredits', 'P'));
			$maxgolds = intval(core::gpc('maxgolds', 'P'));

			// 错误检查
			$error['name'] = $this->group->check_name($name);
			$error['creditsto'] = ($groupid > 10 && $groupid < 1000) ? $this->group->check_creditsto($creditsto) : '';

			if(!array_filter($error)) {
				$group['name'] = $name;
				$group['creditsfrom'] = $creditsfrom;
				$group['creditsto'] = $creditsto;
				$group['maxcredits'] = $maxcredits;
				$group['maxgolds'] = $maxgolds;
				
				$group['allowread'] = intval(core::gpc('allowread', 'P'));
				$group['allowthread'] = intval(core::gpc('allowthread', 'P'));
				$group['allowpost'] = intval(core::gpc('allowpost', 'P'));
				$group['allowattach'] = intval(core::gpc('allowattach', 'P'));
				$group['allowdown'] = intval(core::gpc('allowdown', 'P'));
				$group['allowtop'] = intval(core::gpc('allowtop', 'P'));
				$group['allowmove'] = intval(core::gpc('allowmove', 'P'));
				$group['allowdigest'] = intval(core::gpc('allowdigest', 'P'));
				$group['allowupdate'] = intval(core::gpc('allowupdate', 'P'));
				$group['allowdelete'] = intval(core::gpc('allowdelete', 'P'));
				$group['allowbanuser'] = intval(core::gpc('allowbanuser', 'P'));
				$group['allowdeleteuser'] = intval(core::gpc('allowdeleteuser', 'P'));
				$group['allowviewip'] = intval(core::gpc('allowviewip', 'P'));
				
				// hook admin_group_detail_update_before.php
				$this->group->update($group);
			
				$this->runtime->xupdate('grouparr');
				$error = array();	// 设置为空。
			}
		}
		
		$input['allowread'] = form::get_radio_yes_no('allowread', $group['allowread']);
		$input['allowthread'] = form::get_radio_yes_no('allowthread', $group['allowthread']);
		$input['allowpost'] = form::get_radio_yes_no('allowpost', $group['allowpost']);
		$input['allowattach'] = form::get_radio_yes_no('allowattach', $group['allowattach']);
		$input['allowdown'] = form::get_radio_yes_no('allowdown', $group['allowdown']);
		$input['allowtop'] = form::get_radio_yes_no('allowtop', $group['allowtop']);
		$input['allowdigest'] = form::get_radio_yes_no('allowdigest', $group['allowdigest']);
		$input['allowupdate'] = form::get_radio_yes_no('allowupdate', $group['allowupdate']);
		$input['allowmove'] = form::get_radio_yes_no('allowmove', $group['allowmove']);
		$input['allowdelete'] = form::get_radio_yes_no('allowdelete', $group['allowdelete']);
		$input['allowbanuser'] = form::get_radio_yes_no('allowbanuser', $group['allowbanuser']);
		$input['allowdeleteuser'] = form::get_radio_yes_no('allowdeleteuser', $group['allowdeleteuser']);
		$input['allowviewip'] = form::get_radio_yes_no('allowviewip', $group['allowviewip']);
		
		// hook admin_group_detail_view_before.php
		$this->group->format($group);
		$this->view->assign('groupid', $groupid);
		$this->view->assign('group', $group);
		$this->view->assign('input', $input);
		$this->view->assign('error', $error);
		$this->view->display('group_detail.htm');
	}
		
	public function on_delete() {
		$this->_title[] = '删除用户组';
		$this->_nav[] = '删除用户组';
		
		$groupid = intval(core::gpc('groupid'));
		if($groupid <= 11) {
			$this->message('该用户组不能删除。');
		}

		$group = $this->group->read($groupid);
		$this->check_group_exists($group);
		
		$this->group->delete($groupid);
		
		$this->runtime->xupdate('grouparr');
		
		// 调整用户组所有用户，自动升级的时候调整，此处不调整。
		// $uids = $this->user->fetch_index_id();
		
		// hook admin_group_delete_after.php
		
		$this->location("?group-list.htm");
	}

	private function check_group_exists($group) {
		if(empty($group)) {
			$this->message('group不存在！可能已经被删除。');
		}
	}
	
	//hook admin_group_control_after.php
}
?>