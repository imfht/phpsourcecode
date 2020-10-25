<?php
class PermissionAction extends Action{
	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array()
		);
		B('Authenticate', $action);
	}
	
	Public function module(){
		$module = M('ControlModule');
		$p = isset($_GET['p']) ? intval($_GET['p']) : 1 ;
		
		$list = $module->page($p.',10')->select();
		$this->assign('moduleList',$list);
		import("@.ORG.Page");
		$count = $module->count();
		$Page = new Page($count,10);

		$show = $Page->show();
		
		$this->assign('page',$show);
		$this->alert = parseAlert();
		$this->display();
		
	}
	
	public function module_add(){
		if($_POST['submit']){
			$module = D('ControlModule');
			if($module->create()){
				if($module->add()){
					alert('success', L('ADD_THE_MODULE_SUCCESSFULLY'),$_SERVER['HTTP_REFERER']);
				}else{
					alert('error', L('ADD_MODULE_FAILED_PLEASE_CONTACT_YOUR_ADMINISTRATOR'),$_SERVER['HTTP_REFERER']);
				}
			}else{
				alert('error', $module->getError(),$_SERVER['HTTP_REFERER']);
			}
		}else{
			$this->alert = parseAlert();
			$this->display();
		}
	}
	
	public function module_delete(){
		$module = M('ControlModule');
		if($_POST['module_list']){
			if($module->where('module_id in (%s)', join($_POST['module_list'],','))->delete()){
				alert('success', L('MODULE_WAS_REMOVED_SUCCESSFULLY'),$_SERVER['HTTP_REFERER']);
			}else{
				alert('error', L('MODULE_WAS_REMOVED_FAILURE'),$_SERVER['HTTP_REFERER']);
			}
		}elseif($module->where('module_id =' . $_GET['id'])->delete()){
			alert('success', L('MODULE_WAS_REMOVED_SUCCESSFULLY'),$_SERVER['HTTP_REFERER']);
		}else{
			alert('error', L('MODULE_WAS_REMOVED_FAILURE_PLEASE_CONTACT_YOUR_ADMINISTRATOR'),$_SERVER['HTTP_REFERER']);
		}
	}
	
	public function module_edit(){
		$module = M('ControlModule');
		if($_GET['id']){
			$this->vo = $module->where('module_id =' . $_GET['id'])->find();
			$this->display();
		}elseif($_POST['name']){
			$module = D('ControlModule');
			if($module->create()){
				$data['name'] = $_POST['name'];
				$data['description'] = $_POST['description'];
				if($module->where('module_id =' . $_POST['module_id'])->save($data)){
					alert('success', L('MODULE_MODIFICATION_OF_SUCCESS'),$_SERVER['HTTP_REFERER']);
				}else{
					alert('error', L('COUNTLESS_ACCORDING_TO_THE_CHANGE_FAILED_TO_MODIFY_MODULE'),$_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error', L('MODIFY_THE_ERROR_PLEASE_CONTACT_YOUR_ADMINISTRATOR'),$_SERVER['HTTP_REFERER']);
			}
		}
	}
	
	public function index(){
		
		$module = M('ControlModule');
		$this->moduleList = $module->select();
		
		$control = M('Control');
		$where = $_GET['module_id']?'module_id = ' . $_GET['module_id']:'';
		$p = isset($_GET['p']) ? intval($_GET['p']) : 1 ;
		$list = $control->where($where)->page($p.',15')->select();
		$this->assign('controlList',$list);
		import("@.ORG.Page");
		$count = $control->where($where)->count();
		$Page = new Page($count,15);
		
		$Page->parameter = $_GET['module_id']?'module_id/' . $_GET['module_id']:'';
		$show = $Page->show();		
		$this->assign('page',$show);
		$this->alert = parseAlert();
		$this->display();
		
	}
	
	public function control_add(){	
		if($_POST['name']){
			$control = D('Control');
			
			if(!isset($_POST['url']) or $_POST['url']==''){
				$this->error(L('URL_NOT_EMPTY'));
			}elseif($control->create()){
				$url =explode('/',$_POST['url']);
				$m = $url[0];
				$a = $url[1];
				$data['m'] = $m;
				$data['a'] = $a;
				$data['name'] = $_POST['name'];
				$data['description'] = $_POST['description'];
				$data['parameter'] = $_POST['parameter'];
				$data['module_id'] = $_POST['module_id'];

				if($control->add($data)){
					alert('success', L('ADD_OPERATION_IS_SUCCESSFUL'),$_SERVER['HTTP_REFERER']);
				}else{
					alert('error', L('ADD_OPERATION_IS_FAILURE'),$_SERVER['HTTP_REFERER']);
				}
			}
		}else{
			$module = M('ControlModule');
			$this->moduleList = $module->select();
			$this->display();
		}
		
	}
	
	public function control_edit(){
		$control = D('Control');
		if($_POST['name']){
			if(!isset($_POST['url']) or $_POST['url']==''){
				$this->error(L('URL_NOT_EMPTY'));
			}elseif($control->create()){
				$url =explode('/',$_POST['url']);
				$m = $url[0];
				$a = $url[1];
				$data['m'] = $m;
				$data['a'] = $a;
				$data['name'] = $_POST['name'];
				$data['description'] = $_POST['description'];
				$data['parameter'] = $_POST['parameter'];
				$data['module_id'] = $_POST['module_id'];
				if($control->where('control_id =' . $_POST['control_id'])->save($data)){
					alert('success', L('OPERATION_IS_CHANGED'),$_SERVER['HTTP_REFERER']);
				}else{
					echo $control->getLastSql(); die();
					alert('error', L('COUNTLESS_ACCORDING_TO_THE_CHANGE_OPERATION_CHANGE_FAILURE'),$_SERVER['HTTP_REFERER']);
				}
			}
		}else{
			$module = M('ControlModule');
			$this->moduleList = $module->select();
			if($_GET['id']){
				$this->vo = $control->where('control_id = ' . $_GET['id'])->find();
				$this->display();
			}else{
				alert('error', L('MODIFIED_FAILURE_PARAMETER_ERROR'),$_SERVER['HTTP_REFERER']);
			}
		}
	}
	
	public function control_delete(){
		$control = M('Control');
		if($_POST['control_list']){
			if($control->where('control_id in (%s)', join($_POST['control_list'],','))->delete()){
				alert('success', L('DELETED SUCCESSFULLY'),$_SERVER['HTTP_REFERER']);
			}else{
				alert('error', L('DELETE FAILED'),$_SERVER['HTTP_REFERER']);
			}
		}elseif($_GET['id']){
			if($control->where('control_id =' . $_GET['id'])->delete()){
				alert('success', L('DELETED SUCCESSFULLY'),$_SERVER['HTTP_REFERER']); 
			} else {
				alert('error', L('DELETE FAILED'),$_SERVER['HTTP_REFERER']);
			}
		}else{
			alert('error', L('PLEASE_SELECT_ITEMS_TO_DELETE'),$_SERVER['HTTP_REFERER']);
		}
	}
	
	public function authorize(){
		if($_GET['by'] == 'permission'){
			if($_POST['control_id']){
				$roleList = $_POST['roleList'];
				$permission = M('UserPermission');
				$data['control_id'] = $_POST['control_id'];
		
				$temp = $permission->where('control_id =' . $_POST['control_id'])->select();
				$idList = array();

				foreach($temp as $value){
					$idList[] = $value['role_id'];
				}
				$add_permission = array_diff($roleList,$idList);
				$delete_permission = array_diff($idList,$roleList);
				if(!empty($add_permission)){
					foreach($add_permission as $value){
						$data['role_id'] = $value;
						if($permission->add($data)){
						}else{
							alert('error', L('OPERATION_SAVE_FAILED'),$_SERVER['HTTP_REFERER']);
						}
					}
				}
				if(!empty($delete_permission)){
					foreach($delete_permission as $value){
						if($permission->where('control_id = %d and role_id = %d', $_POST['control_id'],$value)->delete()){
						}else{
							alert('error', L('OPERATION_SAVE_FAILED'),$_SERVER['HTTP_REFERER']);
						}
					}
				}
				alert('success', L('OPERATION_SAVE_SUCCESSED'),$_SERVER['HTTP_REFERER']);
				
			}else{
				if($_GET['control_id']){
					$where = isset($_GET['control_id'])?'control_id =' . $_GET['control_id']:'';
					$role = M('UserRole');
					$role_list = array();
					$permission = M('UserPermission');
					$department = M('UserDepartment');
					$permissionList = $permission->where($where)->select();
					$department_temp = $department->select();	
					$department_list = getSubDepartment(0,$department_temp,'');  //按部门查询岗位
					foreach($department_list as $key=>$value){
						$role_list = $role->where('department_id = %d',$value['department_id'])->select();
						foreach($role_list as $key2=>$value2){
							foreach($permissionList as $key3=>$value3){
								if($value2['role_id'] == $value3['role_id']) $role_list[$key2]['checked'] = 'checked';
							}
						}
						$department_list[$key]['role'] = $role_list;
						
					}
					$this->temp = $_GET['control_id'];
					$this->roleList = $department_list;
					$this->display();
				}else{
					alert('error', L('OPERATION_SAVE_FAILED'),$_SERVER['HTTP_REFERER']);
				}
				
			}
		}elseif($_GET['by'] == 'user'){
			if($_POST['role_id']){
				$controlList = $_POST['controlList'];
				$permission = M('UserPermission');
				$data['role_id'] = $_POST['role_id'];
		
				$temp = $permission->where('role_id =' . $_POST['role_id'])->select();
				$idList = array();
				
				foreach($temp as $value){
					$idList[] = $value['control_id'];
				}
				$add_permission = array_diff($controlList,$idList);
				$delete_permission = array_diff($idList,$controlList);
				if(!empty($add_permission)){
					foreach($add_permission as $value){
						$data['control_id'] = $value;
						if($permission->add($data)){
						}else{
							alert('error', L('OPERATION_SAVE_FAILED'),$_SERVER['HTTP_REFERER']);
						}
					}
				}
				if(!empty($delete_permission)){
					foreach($delete_permission as $value){
						if($permission->where('control_id = %d and role_id = %d', $value, $_POST['role_id'])->delete()){
						}else{
							alert('error', L('OPERATION_SAVE_FAILED'),$_SERVER['HTTP_REFERER']);
						}
					}
				}
				alert('success', L('OPERATION_SAVE_SUCCESSED'),$_SERVER['HTTP_REFERER']);
				
			}else{
				if($_GET['role_id']){
					$where = isset($_GET['role_id'])?'role_id =' . $_GET['role_id']:'';
					$module = M('ControlModule');
					$permission = M('UserPermission');
					$control = M('Control');
					$controlList = $module->select();
					$existsList = $permission->where($where)->select();
					foreach($controlList as $key=>$value){
						$controls = $control->where('module_id = %d', $value['module_id'])->select();
						foreach($existsList as $key2=>$value2){
							foreach($controls as $key3=>$value3){
								if($value2['control_id'] == $value3['control_id']){
									$controls[$key3]['checked'] = 'checked';
								}
							}
						}
						$controlList[$key]['control'] = $controls;
					}
					$role = M('UserRole');
					$this->temp = $role->where('role_id =' . $_GET['role_id'])->find();
					$this->controlList = $controlList;
					$this->display('User:authorize');
				}else{
					alert('error', L('SAVE_FAILED_PARAMETER_ERRORS'), $_SERVER['HTTP_REFERER']);
				}
			}
		}
	}
	
	public function user_authorize(){
		if($this->isAjax() && $_GET['auth']){			
			$position_id = isset($_GET['position_id']) ? $_GET['position_id'] : 0;
			if($position_id != 0){
				$per = explode(',',$_GET['perlist']);
				$m_permission = M('Permission');
				$owned_permission = $m_permission->where('position_id = %d', $position_id)->getField('url', true);
				if(!empty($owned_permission)){
					$add_permission = array_diff($per,$owned_permission); 				//需要增加的
					$delete_permission = array_diff($owned_permission,$per);			//需要删除的
				} else {
					$add_permission = $per;
				}
				if(!empty($add_permission)){
					$data['position_id'] = $position_id;
					foreach($add_permission as $key=>$value){
						$data['url'] = $value;
						if(0>=$m_permission->add($data)){
							$this->ajaxReturn(L('PART_OF_THE_AUTHORIZATION_FAILED'),'info',1);
						}
					}
				}
				if(!empty($delete_permission)){
					$map['url'] = array('in',$delete_permission);
					$a = $m_permission->where('position_id = %d', $position_id)->where($map)->delete();
					
					//改变首页widget权限
					$user_list = D('RoleView')->where('position.position_id = %d', $position_id)->select();
					foreach($user_list as $v){
						$dashboard = unserialize($v['dashboard']);
						if(!empty($dashboard)){
							foreach($dashboard as $kk=>$vv){
								//如果没有获取相应权限，则去除对应权限的首页图表
								//权限图表：销售漏斗、客户来源、财务月度统计、财务年度对比
								if(in_array('business/index',$delete_permission) && $vv['widget'] == 'Salesfunnel'){
									unset($dashboard[$kk]);
								}
								if(in_array('customer/index',$delete_permission) && $vv['widget'] == 'Customerorigin'){
									unset($dashboard[$kk]);
								}
								if(in_array('finance/index',$delete_permission) && ($vv['widget'] == 'Receivemonthly' || $vv['widget'] == 'Receiveyearcomparison')){
									unset($dashboard[$kk]);
								}
							}
							$newDashboard = serialize($dashboard);
							M('user')->where('user_id = %d', $v['user_id'])->setField('dashboard',$newDashboard);
						}
					}
					
					if($a<=0){
						$this->ajaxReturn(L('PART_OF_THE_AUTHORIZATION_FAILED'),'info',1);
					}
				}
				$this->ajaxReturn(L('OPERATION_IS_CHANGED'),'info',1);
			}else{
				$this->ajaxReturn( L('PLEASE_RETRY_AFTER_LOGIN_AGAIN'),'info',1);
			}
			
		} elseif($_GET['position_id']) {
			$m_permission = M('Permission');
			
			$owned_permission = $m_permission->where('position_id = %d', $_GET['position_id'])->getField('url', true);
			$this->owned_permission = $owned_permission;
			$this->position_id = $_GET['position_id'];
			$this->alert = parseAlert();
			$this->display();
		} else{
			alert('error', L('PLEASE_CHOOSE_TO_AUTHORIZE_JOBS'), $_SERVER['HTTP_REFERER']);
		}
		
	}
}