<?php
class TaskAction extends Action{

	public function _initialize(){
		$action = array(
			'permission'=>array('tips'),
			'allow'=>array('close','revert','open','changecontent','analytics')
		);
		B('Authenticate', $action);
	}

	public function add(){
		if ($this->isPost()) {
			$m_task = M('Task');
			if ($task = $m_task->create()) {
				$task['create_date'] = time();
				$task['update_date'] = time();
				$task['due_date'] = isset($_POST['due_date']) ? strtotime($_POST['due_date']) : time();
				$task['owner_role_id'] = $_POST['owner_role_id_str'];
				if(!$_POST['subject']) alert('error', L('NEED_TASK_TITLE'),  $_SERVER['HTTP_REFERER']);
				$send_email_array = ($_POST['owner_role_id_str']).($_POST['about_roles']);
				if($send_email_array){
					$owner_role_id_array = explode(',', $send_email_array);
					$creator = getUserByRoleId(session('role_id'));
					
					if ($task_id = $m_task->add($task)) {
						$message_content = L('MESSAGE_CONTENT' ,array(U('task/view','id='.$task_id),$_POST['subject'], $creator['user_name'], $creator['department_name'], $creator['role_name'], $_POST['due_date'] ,$_POST['priority'], $_POST['description']));
						$email_content = L('EMAIL_CONTENT', array($_POST['subject'] ,$creator['user_name'] ,$creator['department_name'] ,$creator['role_name'] ,$_POST['due_date'] ,$_POST['priority'] , $_POST['description']));
						$module = isset($_POST['module']) ? $_POST['module'] : '';
						if($module != ''){
							switch ($module) {
								case 'contacts' : $m_r = M('RContactsTask'); $module_id = 'contacts_id'; break;
								case 'leads' : $m_r = M('RLeadsTask'); $module_id = 'leads_id'; break;
								case 'customer' : $m_r = M('RCustomerTask'); $module_id = 'customer_id'; break;
								case 'product' : $m_r = M('RProductTask'); $module_id = 'product_id'; break;
								case 'business' : $m_r = M('RBusinessTask'); $module_id = 'business_id'; break;
							}
							if ($_POST['module_id']) {
								$data[$module_id] = intval($_POST['module_id']);
								$data['task_id'] = $task_id;
								$rs = $m_r->add($data);
								if ($rs<=0) {
									alert('error', L('RELATED_FAILED'), $_SERVER['HTTP_REFERER']);
								}
							}
						}
						
						foreach(array_unique($owner_role_id_array) as $k => $v){
							if($v && $v != session('role_id')) {
								if(intval($_POST['message_alert']) == 1) {
									sendMessage($v,$message_content,1);
								}
								if(intval($_POST['email_alert']) == 1){
									sysSendEmail($v,L('EMAIL_TITLE'),$email_content);
								}
							}
						}
					} else {
						alert('error', L('FAILED_ADD'),  $_SERVER['HTTP_REFERER']);
					}
					$refer_url = $_POST['refer_url'];
					if($_POST['submit'] == L('SAVE')) {
						if($refer_url){
							alert('success', L('SUCCESS_ADD'), $refer_url);
						}else{
							alert('success', L('SUCCESS_ADD'), U('task/index'));
						}
					} elseif($_POST['submit'] == L('SAVE AND NEW')) {
						alert('success', L('SUCCESS_ADD'), U('task/add'));
					} else {
						if($refer_url){
							alert('success', L('SUCCESS_ADD'), $refer_url);
						}else{
							alert('success', L('SUCCESS_ADD'), U('task/index'));
						}
					}
				} else {
					$this -> error(L('SELECT_TASK_EXECUTOR'));
				}
			} else {
				$this->error(L('ADDING FAILS CONTACT THE ADMINISTRATOR' ,array(L('TASK'))));
			}
		} elseif($_GET['r'] && $_GET['module'] && $_GET['id']) {
			$this->r = $_GET['r'];
			$this->module = $_GET['module'];
			$this->id = $_GET['id'];
			$this->refer_url = $_SERVER['HTTP_REFERER'];
			$this->display('Task:add_dialog');
		}  else {
			$this->alert = parseAlert();
			$this->display();
		}
	}

	public function edit(){
		$task_id = $_POST['task_id'] ? intval($_POST['task_id']) : intval($_GET['id']);
		$task = M('Task')->where('task_id = %d', $task_id)->find();
		$below_ids = getSubRoleId(false);
		if(empty($task)){
			$this->error(L('PARAMETER_ERROR'));
		}elseif (!in_array($task['creator_role_id'],$below_ids) && $task['creator_role_id'] != session('role_id')){
			alert('error',L('DO NOT HAVE PRIVILEGES'),$_SERVER['HTTP_REFERER']);
		}
		if($_POST['owner_name']){
			$d_task = D('Task');
			$d_task->create();
			$d_task->due_date = strtotime($_POST['due_date']);
			$d_task->update_time = time();

			$is_updated = false;
			$module = isset($_POST['module']) ? $_POST['module'] : '';
			if ($module != '') {
				switch ($module) {
					case 'contacts' : $m_r = M('RContactsTask'); $module_id = 'contacts_id'; break;
					case 'leads' : $m_r = M('RLeadsTask'); $module_id = 'leads_id'; break;
					case 'customer' : $m_r = M('RCustomerTask'); $module_id = 'customer_id'; break;
					case 'product' : $m_r = M('RProductTask'); $module_id = 'product_id'; break;
					case 'business' : $m_r = M('RBusinessTask'); $module_id = 'business_id'; break;
				}
				if ($_POST['module_id']) {
					if (!$m_r->where('task_id = %d and '.$module.'_id = %d', $task_id, intval($_POST['module_id']))->find()) {
						$r_module = array('Business'=>'RBusinessTask', 'Contacts'=>'RContactsTask', 'Customer'=>'RCustomerTask', 'Product'=>'RProductTask','Leads'=>'RLeadsTask');
						foreach ($r_module as $key=>$value) {
							$r_m = M($value);
							$r_m->where('task_id = %d', $task_id)->delete();
						}
						$data[$module_id] = intval($_POST['module_id']);
						$data['task_id'] = $task_id;
						$rs = $m_r->add($data);
						if ($rs<=0) {
							alert('error', L('RELATED_FAILED'), $_SERVER['HTTP_REFERER']);
						}
						$is_updated = true;
					}
				} else {
					$this -> error(L('SELECT_CORRESPOND_OPTION'));
				}
			}
			if ($d_task->save()) $is_updated = true;
			if($is_updated){
				alert('success', L('MODIFY_TASK_SUCCESS'), U('task/view', 'id='.$task_id));
			}else{
				alert('error', L('DATA_DID_NOT_CHANGE_MODIFY_FAILED'), $_SERVER['HTTP_REFERER']);
			}
		}elseif($_GET['id']){
			if($task['isclose'] == 1){
				alert('error',L('TASK_HAS_BEEN_CLOSED_CAN_NOT_MODIFY'),$_SERVER['HTTP_REFERER']);
			}
			if(is_array($task)){
				$task['owner_name'] = D('RoleView')->where('role.role_id in (%s)', '0'.$task['owner_role_id'].'0')->select();
				$task['creator'] = getUserByRoleId($task['creator_role_id']);
				$task['about_roles_id'] = D('RoleView')->where('role.role_id in (%s)', '0'.$task['about_roles'].'0')->select();
				$r_module = array('Business'=>'RBusinessTask', 'Contacts'=>'RContactsTask', 'Customer'=>'RCustomerTask', 'Product'=>'RProductTask','Leads'=>'RLeadsTask');
				foreach ($r_module as $key=>$value) {
					$r_m = M($value);
					
					if($module_id = $r_m->where('task_id = %d', trim($_GET['id']))->getField($key . '_id')){
						if($key == 'Leads') {
							$leads = M($key)->where($key.'_id = %d', $module_id)->find();
							$name = $leads['first_name'].$leads['last_name']. ' ' . $leads['company'];
						} else {
							$name = M($key)->where($key.'_id = %d', $module_id)->getField('name');
						}
						$module = M($key)->where($key.'_id = %d', $module_id)->find();
						$task['module']=array('module_name'=>$key,'name'=>$name,'module_id'=>$module_id);
						break;
					}
				}
				
				$this->task = $task;
				$this->alert = parseAlert();
				$this->display();
			} else {
				alert('error', L('TASK_NOT_EXIST'),$_SERVER['HTTP_REFERER']);
			}
		}else{
			$this->error(L('PARAMETER_ERROR'));
		}
	}
	
	public function delete(){
		$m_task = M('Task');
		if($this->isPost()){
			$task_ids = is_array($_POST['task_id']) ? implode(',', $_POST['task_id']) : '';
			if ('' == $task_ids) {
				alert('error', L('NOT CHOOSE ANY'),$_SERVER['HTTP_REFERER']);
			} else {
				if(!session('?admin')){
					foreach($_POST['task_id'] as $key => $value){
						if(!$m_task->where('creator_role_id = %d and task_id = %d', session('role_id'), $value) -> find()){
							alert('error', L('YOU_DO_NOT_HAVE_FULL_RIGHTS_TO_OPERATE'), $_SERVER['HTTP_REFERER']);
						}
					}
				}
				$data = array('is_deleted'=>1, 'delete_role_id'=>session('role_id'), 'delete_time'=>time());
				if($m_task->where('task_id in (%s)', $task_ids)->save($data)){	
					alert('success', L('DELETED SUCCESSFULLY'),U('Task/index'));
				} else {
					alert('error', L('DELETE FAILED CONTACT THE ADMINISTRATOR'),$_SERVER['HTTP_REFERER']);
				}
			}
		} elseif ($_GET['id']) {
			$task = $m_task->where('task_id = %d', $_GET['id'])->find();
			if (is_array($task)) {
				if($task['creator_role_id'] == session('role_id') || session('?admin')){
					$data = array('is_deleted'=>1, 'delete_role_id'=>session('role_id'), 'delete_time'=>time());
					if($m_task->where('task_id = %d', $_GET['id'])->save($data)){
						if($_GET['redirect']){
							alert('success', L('DELETED SUCCESSFULLY'),U('Task/index'));
						} else {
							alert('success', L('DELETED SUCCESSFULLY'), $_SERVER['HTTP_REFERER']);
						}
					}else{
						alert('error', L('DELETE FAILED CONTACT THE ADMINISTRATOR'), $_SERVER['HTTP_REFERER']);
					}	
				} else {
					alert('error', L('HAVE NOT PRIVILEGES'), $_SERVER['HTTP_REFERER']);
				}
					
			} else {
				alert('error', L('TASK_NOT_EXIST'), $_SERVER['HTTP_REFERER']);
			}			
		} else {
			alert('error', L('SELECT_TASK_TO_DELETE'),$_SERVER['HTTP_REFERER']);
		}
	}
	
	public function completeDelete(){
		$m_task = M('Task');
		$r_module = array('Log'=>'RLogTask', 'File'=>'RFileTask', 'RBusinessTask', 'RContactsTask', 'RCustomerTask', 'RProductTask', 'RLeadsTask');
		if($this->isPost()){
			$task_ids = is_array($_POST['task_id']) ? implode(',', $_POST['task_id']) : '';
			if ('' == $task_ids) {
				alert('error', L('NOT CHOOSE ANY'),$_SERVER['HTTP_REFERER']);
			} else {
				if(!session('?admin')){
					foreach($_POST['task_id'] as $key => $value){
						if(!$m_task->where('creator_role_id = %d and task_id = %d', session('role_id'), $value) -> find()){
							alert('error', L('YOU_DO_NOT_HAVE_FULL_RIGHTS_TO_OPERATE'), $_SERVER['HTTP_REFERER']);
						}
					}
				}
				if($m_task->where('task_id in (%s)', $task_ids)->delete()){	
					foreach ($_POST['task_id'] as $value) {
						foreach ($r_module as $key2=>$value2) {
							$module_ids = M($value2)->where('task_id = %d', $value)->getField($key2 . '_id', true);
							M($value2)->where('task_id = %d', $value) -> delete();
							if(!is_int($key2)){	
								M($key2)->where($key2 . '_id in (%s)', implode(',', $module_ids))->delete();
							}
						}
					}
					alert('success', L('DELETED SUCCESSFULLY'),U('Task/index','by=deleted'));
				} else {
					alert('error', L('DELETE FAILED CONTACT THE ADMINISTRATOR'), $_SERVER['HTTP_REFERER']);
				}
			}
		} elseif ($_GET['id']) {
			$task = $m_task->where('task_id = %d', $_GET['id'])->find();
			if (is_array($task)) {
				if($task['creator_role_id'] == session('role_id') || session('?admin')){
					if($m_task->where('task_id = %d', $_GET['id'])->delete()){
						foreach ($r_module as $key2=>$value2) {
							$module_ids = M($value2)->where('task_id = %d', $_GET['id'])->getField($key2 . '_id', true);
							M($value2)->where('task_id = %d', $_GET['id']) -> delete();
							if(!is_int($key2)){
								M($key2)->where($key2 . '_id in (%s)', implode(',', $module_ids))->delete();
							}
						}
						if($_GET['redirect']){
							alert('success', L('DELETED SUCCESSFULLY'),$_SERVER['HTTP_REFERER']);
						} else {
							alert('success', L('DELETED SUCCESSFULLY'), $_SERVER['HTTP_REFERER']);
						}
					}else{
						alert('error', L('DELETE FAILED CONTACT THE ADMINISTRATOR'), $_SERVER['HTTP_REFERER']);
					}	
				} else {
					alert('error', L('HAVE NOT PRIVILEGES'), $_SERVER['HTTP_REFERER']);
				}
					
			} else {
				alert('error', L('TASK_NOT_EXIST'), $_SERVER['HTTP_REFERER']);
			}			
		} else {
			alert('error', L('SELECT_TASK_TO_DELETE'),$_SERVER['HTTP_REFERER']);
		}
	}
	
	public function revert(){
		$task_id = isset($_GET['id']) ? intval(trim($_GET['id'])) : 0;
		if ($task_id > 0) {
			$m_task = M('task');
			$task = $m_task->where('task_id = %d', $task_id)->find();
			if (session('?admin') || $task['delete_role_id'] == session('role_id')) {
				if ($m_task->where('task_id = %d', $task_id)->setField('is_deleted', 0)) {
					alert('success', L('RESTORE SUCCESSFUL'), $_SERVER['HTTP_REFERER']);
				} else {
					alert('error', L('RESTORE FAILURE'), $_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error', L('HAVE_NO_RIGHTS_TO_RESET'), $_SERVER['HTTP_REFERER']);
			}
		} else {
			alert('error', L('PARAMETER_ERROR'), $_SERVER['HTTP_REFERER']);
		}
	}
	
	public function index(){
		//更新最后阅读时间
		$m_user = M('user');
		$last_read_time_js = $m_user->where('role_id = %d', session('role_id'))->getField('last_read_time');
		$last_read_time = json_decode($last_read_time_js, true);
		$last_read_time['task'] = time();
		$m_user->where('role_id = %d', session('role_id'))->setField('last_read_time',json_encode($last_read_time));
		
		$by = isset($_GET['by']) ? trim($_GET['by']) : '';
		$p = isset($_GET['p']) ? intval($_GET['p']) : 1 ;
		$m_task = M('Task');
		$below_ids = getSubRoleId(false);
		$all_ids = getSubRoleId();
		$where = array();
		$params = array();
		$order = "create_date desc";
		if($_GET['desc_order']){
			$order = trim($_GET['desc_order']).' desc';
		}elseif($_GET['asc_order']){
			$order = trim($_GET['asc_order']).' asc';
		}
		
		switch ($by) {
			case 'create' : $where['creator_role_id'] = session('role_id');break;
			case 's1' : $where['status'] = L('NOT_START');  break;
			case 's2' : $where['status'] = L('DELAY');  break;
			case 's3' : $where['status'] = L('ONGOING');  break;
			case 's4' : $where['status'] = L('COMPLETE');  break;
			case 'closed' : $where['isclose'] = 1; break;
			case 'deleted' : $where['is_deleted'] = 1; break;
			case 'today' : 
				$where['due_date'] =  array('between',array(strtotime(date('Y-m-d')) -1 ,strtotime(date('Y-m-d')) + 86400)); 
				break;
			case 'week' : 
				$week = (date('w') == 0)?7:date('w');
				$where['due_date'] =  array('between',array(strtotime(date('Y-m-d')) - ($week-1) * 86400 -1 ,strtotime(date('Y-m-d')) + (8-$week) * 86400));
				break;
			case 'month' : 
				$next_year = date('Y')+1;
				$next_month = date('m')+1;
				$month_time = date('m') ==12 ? strtotime($next_year.'-01-01') : strtotime(date('Y').'-'.$next_month.'-01');
				$where['due_date'] = array('between',array(strtotime(date('Y-m-01')) -1 ,$month_time));
				break;
			case 'add' : $order = 'create_date desc';  break;
			case 'update' : $order = 'update_date desc';  break;
			case 'me' : $where['_string'] = 'about_roles like "%,'.session('role_id').',%" OR owner_role_id like "%,'.session('role_id').',%"'; break;
			default :  $where['_string'] = 'creator_role_id in ('.implode(',', $all_ids).')  OR about_roles like "%,'.session('role_id').',%" OR owner_role_id like "%,'.session('role_id').',%"'; break;
		}
		if (!isset($where['isclose'])) {
			$where['isclose'] = 0;
		}
		if (!isset($where['is_deleted'])) {
			$where['is_deleted'] = 0;
		}
		if (!isset($where['status'])) {
			$where['status'] = array('neq','完成');
		}
		if (!isset($where['_string'])  && !isset($where['creator_role_id'])){
			$where['_string'] = ' about_roles like "%,'.session('role_id').',%" OR owner_role_id like "%,'.session('role_id').',%" OR creator_role_id in ('.implode(',', $all_ids).') ';
		}
		if ($_REQUEST["field"]) {
			$field = trim($_REQUEST['field']) == 'all' ? 'subject|status|priority|description|due_date' : $_REQUEST['field'];
			$search = empty($_REQUEST['search']) ? '' : trim($_REQUEST['search']);
			$condition = empty($_REQUEST['condition']) ? 'is' : trim($_REQUEST['condition']);
			if	('due_date' == $field || $field == 'update_date' || $field == 'create_date') {
				$search = is_numeric($search)?$search:strtotime($search);
			}
			switch ($condition) {
				case "is" : if($field == 'owner_role_id'){
								$where[$field] = array('like','%,'.$search.',%');
							}else{
								$where[$field] = array('eq',$search);
							}
							break;
				case "isnot" :  $where[$field] = array('neq',$search);break;
				case "contains" :  $where[$field] = array('like','%'.$search.'%');break;
				case "not_contain" :  $where[$field] = array('notlike','%'.$search.'%');break;
				case "start_with" :  $where[$field] = array('like',$search.'%');break;
				case "end_with" :  $where[$field] = array('like','%'.$search);break;
				case "is_empty" :  $where[$field] = array('eq','');break;
				case "is_not_empty" :  $where[$field] = array('neq','');break;
				case "gt" :  $where[$field] = array('gt',$search);break;
				case "egt" :  $where[$field] = array('egt',$search);break;
				case "lt" :  $where[$field] = array('lt',$search);break;
				case "elt" :  $where[$field] = array('elt',$search);break;
				case "eq" : $where[$field] = array('eq',$search);break;
				case "neq" : $where[$field] = array('neq',$search);break;
				case "between" : $where[$field] = array('between',array($search-1,$search+86400));break;
				case "nbetween" : $where[$field] = array('not between',array($search,$search+86399));break;
				case "tgt" :  $where[$field] = array('gt',$search+86400);break;
				default : $where[$field] = array('eq',$search);
			}
			$params = array('field='.$field, 'condition='.$condition, 'search='.trim($_REQUEST['search']));
		}
		
		$order = empty($order) ? 'due_date asc' : $order;
		if(trim($_GET['act']) == 'excel'){	
			if(vali_permission('task', 'export')){
				$taskList = $m_task->where($where)->order($order)->select();	
	
				$this->excelExport($taskList);
			}else{
				alert('error', L('HAVE NOT PRIVILEGES'), $_SERVER['HTTP_REFERER']);
			}
		}
		$task_list = $m_task->where($where)->order($order)->page($p.',15')->select();
		$count = $m_task->where($where)->count();
		
		import("@.ORG.Page");
		$Page = new Page($count,15);
		if (!empty($_GET['by'])) {
			$params[]=   "by=".trim($_GET['by']);
		}
		
		$this->parameter = implode('&', $params);
		if ($_GET['desc_order']) {
			$params[] = "desc_order=" . trim($_GET['desc_order']);
		} elseif($_GET['asc_order']){
			$params[] = "asc_order=" . trim($_GET['asc_order']);
		}
		
		$Page->parameter = implode('&', $params);
		$this->assign('page', $Page->show());
		
		foreach ($task_list as $key=>$value) {
			$task_list[$key]['owner'] = D('RoleView')->where('role.role_id in (%s)', '0'.$task_list[$key]['owner_role_id'].'0')->select();
			$task_list[$key]['creator'] = getUserByRoleId($value['creator_role_id']);
			$task_list[$key]['deletor'] = getUserByRoleId($value['delete_role_id']);
			//关联模块
			$r_module = array('Business'=>'RBusinessTask', 'Contacts'=>'RContactsTask', 'Customer'=>'RCustomerTask', 'Product'=>'RProductTask','Leads'=>'RLeadsTask');
			foreach ($r_module as $k=>$v) {
				$r_m = M($v);
				if($module_id = $r_m->where('task_id = %d', $value['task_id'])->getField($k . '_id')){			
					
					$name = M($k)->where($k.'_id = %d', $module_id)->getField('name');
					$is_deleted = M($k)->where($k.'_id = %d', $module_id)->getField('is_deleted');
					$name_str = msubstr($name,0,20,'utf-8',false);
					$name_str .= $is_deleted == 1 ? '<font color="red">('.L("DELETED").')</font>' : '';
					switch ($k){
						case 'Product' : $module_name= L('PRODUCT'); 
							$name = '<a href="index.php?m=product&a=view&id='.$module_id.'" title="'.$name.'">'.$name_str.'</a>';
							break;
						case 'Leads' : $module_name= L('LEADS'); 
							$name = '<a href="index.php?m=leads&a=view&id='.$module_id.'" title="'.$name.'">'.$name_str.'</a>';
						break;
						case 'Contacts' : $module_name= L('CONTACTS'); 
							$name = '<a href="index.php?m=contacts&a=view&id='.$module_id.'" title="'.$name.'">'.$name_str.'</a>';
						break;
						case 'Business' : $module_name= L('BUSINESS'); 
							$name = '<a href="index.php?m=business&a=view&id='.$module_id.'" title="'.$name.'">'.$name_str.'</a>';
						break;
						case 'Customer' : $module_name= L('CUSTOMER'); 
							$name = '<a href="index.php?m=customer&a=view&id='.$module_id.'" title="'.$name.'">'.$name_str.'</a>';
						break;
					}
					$task_list[$key]['module']=array('module'=>$k,'module_name'=>$module_name,'name'=>$name,'module_id'=>$module_id);
					break;
				}
			}
			$due_time = $task_list[$key]['due_date'];
			if($due_time){
				$tomorrow_time = strtotime(date('Y-m-d', time()))+86400;
				$diff_days = ($due_time-$tomorrow_time)%86400>0 ? intval(($due_time-$tomorrow_time)/86400)+1 : intval(($due_time-$tomorrow_time)/86400);
				$task_list[$key]['diff_days'] = $diff_days;
			}
		}
		
		$this->task_list = $task_list;
		$this->alert = parseAlert();
		$this->display();
	}
	
	public function view() {
		$task_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		//if($task_id && !check_permission($task_id, 'task')) alert('error',L('HAVE NOT PRIVILEGES'),U('task/index')); 
		if (0 == $task_id) {
			alert('error', L('PARAMETER_ERROR'), U('task/index'));
		} else {
			$m_task = M('Task');
			$task = $m_task->where('task_id = %d and is_deleted = 0',$task_id)->find();
			if(empty($task)){
				alert('error',L('RECORD_NOT_EXIST_OR_HAVE_BEEN_DELETED',array(L('TASK'))),U('task/index'));
			}
			$owner_role_id = in_array(session('role_id'),explode(',',$task['owner_role_id']));
			$about_roles = in_array(session('role_id'),explode(',',$task['about_roles']));
			$res = in_array($task['creator_role_id'],getSubRoleId(false));
			if($owner_role_id || $about_roles || $res || session('?admin')){
				$task['owner'] = D('RoleView')->where('role.role_id in (%s)', '0'.$task['owner_role_id'].'0')->select();
				$task['creator'] = getUserByRoleId($task['creator_role_id']);
				$task['about_roles'] = D('RoleView')->where('role.role_id in (%s)', '0'.$task['about_roles'].'0')->select();
				$r_module = array('Business'=>'RBusinessTask', 'Contacts'=>'RContactsTask', 'Customer'=>'RCustomerTask', 'Product'=>'RProductTask','Leads'=>'RLeadsTask');
				foreach ($r_module as $key=>$value) {
					$r_m = M($value);
					if($module_id = $r_m->where('task_id = %d', $task_id)->getField($key . '_id')){			
						if($key == 'Leads') {
							$leads = M($key)->where($key.'_id = %d', $module_id)->find();
							$name = $leads['first_name'].$leads['last_name'].$leads['saltname'].' ' . $leads['company'];
						} else {
							$name = M($key)->where($key.'_id = %d', $module_id)->getField('name');
						}
						switch ($key){
							case 'Product' : $module_name= L('PRODUCT'); break;
							case 'Leads' : $module_name= L('LEADS'); break;
							case 'Contacts' : $module_name= L('CONTACTS'); break;
							case 'Business' : $module_name= L('BUSINESS'); break;
							case 'Customer' : $module_name= L('CUSTOMER'); break;
						}
						$task['module']=array('module'=>$key,'module_name'=>$module_name,'name'=>$name,'module_id'=>$module_id);
						break;
					}
				}
			
				$log_ids = M('rLogTask')->where('task_id = %d', $task_id)->getField('log_id', true);
				$task['log'] = M('log')->where('log_id in (%s)', implode(',', $log_ids))->select();
				$log_count = 0;
				foreach ($task['log'] as $key=>$value) {
					$task['log'][$key]['owner'] = D('RoleView')->where('role.role_id = %d', $value['role_id'])->find();
					$file_ids = M('rFileLog')->where('log_id = %d', $value['log_id'])->getField('file_id', true);
					$task['log'][$key]['files'] = M('file')->where(array('file_id'=>array('in',$file_ids)))->select();
					foreach($task['log'][$key]['files'] as $fk=>$fv){
						$task['log'][$key]['files'][$fk]['subName'] = mb_substr($fv['name'],0,30,'utf-8');
					}
					if ($key%2==0) $task['log'][$key]['style'] = 'warning';
					else $task['log'][$key]['style'] = 'info';
					$log_count ++;
				}
				$task['log_count'] = $log_count;
				
				if (in_array($task['owner_role_id'], getSubRoleId(false))) {
					if(!($task['comment_role_id'] > 0)){
						$this->comment_role_id = session('role_id');
					}
				}
				
				$this->comment_list = D('CommentView')->where('module = "task" and module_id = %d', $task['task_id'])->order('comment.create_time desc')->select();
				$this->task = $task;
				$this->alert = parseAlert();
				$this->display();
			}else{
				alert('error',L('HAVE NOT PRIVILEGES'),U('task/index')); 
			}
		}
	}
	
	public function close(){
		$id = isset($_GET['id']) ? $_GET['id'] : 0; 
		if ($id >= 0) {
			$m_task = M('task');
			$task = $m_task->where('creator_role_id = %d and task_id = %d', session('role_id'), $id)->find();
			if ((is_array($task) && !empty($task)) || session('?admin')) {
				if($m_task->where('task_id = %d', $id)->setField('isclose', 1)){
					alert('success', L('CLOSED_SUCCESS'), $_SERVER['HTTP_REFERER']);
				} else {
					alert('error', L('FAIL_TO_CLOSE_TASK'), $_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error', L('HAVE_NO_RIGHTS_TO_CLOSE_TASK'), $_SERVER['HTTP_REFERER']);
			}
		}else{
			alert('error', L('PARAMETER_ERROR'), $_SERVER['HTTP_REFERER']);
		}
	}
	
	/**
	*开启任务
	*
	**/
	public function open(){
		$id = isset($_GET['id']) ? $_GET['id'] : 0; 
		if ($id >= 0) {
			$m_task = M('task');
			$task = $m_task->where('task_id = %d and creator_role_id = %d',$id,session('role_id'))->find();
			if ((is_array($task) && !empty($task))|| session('?admin')) {
				if($m_task->where('task_id = %d', $id)->setField('isclose', 0)){
					alert('success', L('OPEN_SUCCESS'), $_SERVER['HTTP_REFERER']);
				} else {
					alert('error', L('OPEN_FAILURE'), $_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error', L('DO NOT HAVE PRIVILEGES'), $_SERVER['HTTP_REFERER']);
			}
		}else{
			alert('error', L('PARAMETER_ERROR'), $_SERVER['HTTP_REFERER']);
		}
	}
	
	public function listDialog(){
		$m_task = M('task');
		$all_ids = getSubRoleId();
		$where['_string'] = 'creator_role_id in ('.implode(',', $all_ids).')  OR about_roles like "%,'.session('role_id').',%" OR owner_role_id like "%,'.session('role_id').',%"';
		$where['is_deleted'] = 0;
		$where['isclose'] = 0;
		$list = $m_task->where($where)->order('due_date desc')->limit('10')->select();
		foreach ($list as $key=>$value) {
			$list[$key]['owner'] = D('RoleView')->where('role.role_id in (%s)', '0'.$value['owner_role_id'].'0')->select();
			$list[$key]['creator'] = getUserByRoleId($value['creator_role_id']);
			$list[$key]['deletor'] = getUserByRoleId($value['delete_role_id']);
			//关联模块
			$r_module = array('Business'=>'RBusinessTask', 'Contacts'=>'RContactsTask', 'Customer'=>'RCustomerTask', 'Product'=>'RProductTask','Leads'=>'RLeadsTask');
			foreach ($r_module as $k=>$v) {
				$r_m = M($v);
				if($module_id = $r_m->where('task_id = %d', $value['task_id'])->getField($k . '_id')){			
					$name = M($k)->where($k.'_id = %d', $module_id)->getField('name');
					$is_deleted = M($k)->where($k.'_id = %d', $module_id)->getField('is_deleted');
					$name_str = msubstr($name,0,20,'utf-8',false);
					$name_str .= $is_deleted == 1 ? '<font color="red">('.L("DELETED").')</font>' : '';
					switch ($k){
						case 'Product' : $module_name= L('PRODUCT'); 
							$name = '<a target="_blank" href="index.php?m=product&a=view&id='.$module_id.'" title="'.$name.'">'.$name_str.'</a>';
							break;
						case 'Leads' : $module_name= L('LEADS'); 
							$name = '<a target="_blank" href="index.php?m=leads&a=view&id='.$module_id.'" title="'.$name.'">'.$name_str.'</a>';
						break;
						case 'Contacts' : $module_name= L('CONTACTS'); 
							$name = '<a target="_blank" href="index.php?m=contacts&a=view&id='.$module_id.'" title="'.$name.'">'.$name_str.'</a>';
						break;
						case 'Business' : $module_name= L('BUSINESS'); 
							$name = '<a target="_blank" href="index.php?m=business&a=view&id='.$module_id.'" title="'.$name.'">'.$name_str.'</a>';
						break;
						case 'Customer' : $module_name= L('CUSTOMER'); 
							$name = '<a target="_blank" href="index.php?m=customer&a=view&id='.$module_id.'" title="'.$name.'">'.$name_str.'</a>';
						break;
					}
					$list[$key]['module']=array('module'=>$k,'module_name'=>$module_name,'name'=>$name,'module_id'=>$module_id);
					break;
				}
			}
		}
		$this->task_list = $list;
		$count = $m_task->where($where)->count();
		$this->total = $count%10 > 0 ? ceil($count/10) : $count/10;
		$this->count_num = $count;
		$this->display();
	}
	
	public function changecontent(){
		$by = isset($_GET['by']) ? trim($_GET['by']) : '';
		$p = isset($_GET['p']) ? intval($_GET['p']) : 1 ;
		$m_task = M('Task');
		$all_ids = getSubRoleId();
		$where = array();
		$params = array();
		$order = "";
		$where['is_deleted'] = 0;
		$where['isclose'] = 0;
		$where['_string'] = 'creator_role_id in ('.implode(',', $all_ids).')  OR about_roles like "%,'.session('role_id').',%" OR owner_role_id like "%,'.session('role_id').',%"';
		
		if ($_REQUEST["field"]) {
			$field = trim($_REQUEST['field']) == 'all' ? 'subject|status|priority|description|due_date' : $_REQUEST['field'];
			$search = empty($_REQUEST['search']) ? '' : trim($_REQUEST['search']);
			$condition = empty($_REQUEST['condition']) ? 'is' : trim($_REQUEST['condition']);
			if	('due_date' == $field || $field == 'update_date' || $field == 'create_date') {
				$search = is_numeric($search)?$search:strtotime($search);
			}
			switch ($condition) {
				case "is" : $where[$field] = array('eq',$search);break;
				case "isnot" :  $where[$field] = array('neq',$search);break;
				case "contains" :  $where[$field] = array('like','%'.$search.'%');break;
				case "not_contain" :  $where[$field] = array('notlike','%'.$search.'%');break;
				case "start_with" :  $where[$field] = array('like',$search.'%');break;
				case "end_with" :  $where[$field] = array('like','%'.$search);break;
				case "is_empty" :  $where[$field] = array('eq','');break;
				case "is_not_empty" :  $where[$field] = array('neq','');break;
				case "gt" :  $where[$field] = array('gt',$search);break;
				case "egt" :  $where[$field] = array('egt',$search);break;
				case "lt" :  $where[$field] = array('lt',$search);break;
				case "elt" :  $where[$field] = array('elt',$search);break;
				case "eq" : $where[$field] = array('eq',$search);break;
				case "neq" : $where[$field] = array('neq',$search);break;
				case "between" : $where[$field] = array('between',array($search-1,$search+86400));break;
				case "nbetween" : $where[$field] = array('not between',array($search,$search+86399));break;
				case "tgt" :  $where[$field] = array('gt',$search+86400);break;
				default :	if($field == 'owner_role_id'){
								$where[$field] = array('like','%,'.$search.',%');
							}else{
								$where[$field] = array('eq',$search);
							}
							break;
			}
			$params = array('field='.$field, 'condition='.$condition, 'search='.trim($_REQUEST['search']));
		}
		$p = !$_REQUEST['p']||$_REQUEST['p']<=0 ? 1 : intval($_REQUEST['p']);
		$order = empty($order) ? 'due_date asc' : $order;
		$task_list = $m_task->where($where)->order($order)->page($p.',15')->select();
		$count = $m_task->where($where)->count();
		
		foreach ($task_list as $key=>$value) {
			$task_list[$key]['owner'] = D('RoleView')->where('role.role_id in (%s)', '0'.$value['owner_role_id'].'0')->select();
			$task_list[$key]['creator'] = getUserByRoleId($value['creator_role_id']);
			$task_list[$key]['deletor'] = getUserByRoleId($value['delete_role_id']);
			//关联模块
			$r_module = array('Business'=>'RBusinessTask', 'Contacts'=>'RContactsTask', 'Customer'=>'RCustomerTask', 'Product'=>'RProductTask','Leads'=>'RLeadsTask');
			foreach ($r_module as $k=>$v) {
				$r_m = M($v);
				if($module_id = $r_m->where('task_id = %d', $value['task_id'])->getField($k . '_id')){			
					
					$name = M($k)->where($k.'_id = %d', $module_id)->getField('name');
					$is_deleted = M($k)->where($k.'_id = %d', $module_id)->getField('is_deleted');
					$name_str = msubstr($name,0,20,'utf-8',false);
					$name_str .= $is_deleted == 1 ? '<font color="red">('.L("DELETED").')</font>' : '';
					switch ($k){
						case 'Product' : $module_name= L('PRODUCT'); 
							$name = '<a href="index.php?m=product&a=view&id='.$module_id.'" title="'.$name.'">'.$name_str.'</a>';
							break;
						case 'Leads' : $module_name= L('LEADS'); 
							$name = '<a href="index.php?m=leads&a=view&id='.$module_id.'" title="'.$name.'">'.$name_str.'</a>';
						break;
						case 'Contacts' : $module_name= L('CONTACTS'); 
							$name = '<a href="index.php?m=contacts&a=view&id='.$module_id.'" title="'.$name.'">'.$name_str.'</a>';
						break;
						case 'Business' : $module_name= L('BUSINESS'); 
							$name = '<a href="index.php?m=business&a=view&id='.$module_id.'" title="'.$name.'">'.$name_str.'</a>';
						break;
						case 'Customer' : $module_name= L('CUSTOMER'); 
							$name = '<a href="index.php?m=customer&a=view&id='.$module_id.'" title="'.$name.'">'.$name_str.'</a>';
						break;
					}
					$task_list[$key]['module']=array('module'=>$k,'module_name'=>$module_name,'name'=>$name,'module_id'=>$module_id);
					break;
				}
			}
			$due_time = $task_list[$key]['due_date'];
			if($due_time){
				$tomorrow_time = strtotime(date('Y-m-d', time()))+86400;
				$diff_days = ($due_time-$tomorrow_time)%86400>0 ? intval(($due_time-$tomorrow_time)/86400)+1 : intval(($due_time-$tomorrow_time)/86400);
				$task_list[$key]['diff_days'] = $diff_days;
			}
		}
		
		$data['list'] = $task_list;
		$data['p'] = $p;
		$data['count'] = $count;
		$data['total'] = $count%10 > 0 ? ceil($count/10) : $count/10;
		$this->ajaxReturn($data,"",1);
	}
	
	public function excelExport($taskList=false){
		import("ORG.PHPExcel.PHPExcel");
		$objPHPExcel = new PHPExcel();    
		$objProps = $objPHPExcel->getProperties();    
		$objProps->setCreator("5kcrm");    
		$objProps->setLastModifiedBy("5kcrm");    
		$objProps->setTitle("5kcrm Task Data");    
		$objProps->setSubject("5kcrm Task Data");    
		$objProps->setDescription("5kcrm Task Data");    
		$objProps->setKeywords("5kcrm Task Data");    
		$objProps->setCategory("Task");
		$objPHPExcel->setActiveSheetIndex(0);     
		$objActSheet = $objPHPExcel->getActiveSheet(); 
		   
		$objActSheet->setTitle('Sheet1');
		$objActSheet->setCellValue('A1', L('THEME'));
		$objActSheet->setCellValue('B1', L('OWNER_ROLE'));
		$objActSheet->setCellValue('C1', L('DEADLINE'));
		$objActSheet->setCellValue('D1', L('STATUS'));
		$objActSheet->setCellValue('E1', L('PRECEDENCE'));
		$objActSheet->setCellValue('F1', L('WHETHER_SEND_EMAIL_NOTIFICATION'));
		$objActSheet->setCellValue('G1', L('DESCRIPTION'));
		$objActSheet->setCellValue('H1', L('CREATOR_ROLE'));
		$objActSheet->setCellValue('I1', L('CREATE_TIME'));
		
		if(is_array($taskList)){
			$list = $taskList;
		}else{
			$where['owner_role_id'] = array('in',implode(',', getSubRoleId()));
			$where['is_deleted'] = 0;
			$list = M('task')->where($where)->select();
		}
		
		$i = 1;
		foreach ($list as $k => $v) {
			$i++;
			$owner = D('RoleView')->where('role.role_id = %d', $v['owner_role_id'])->find();
			$creator = D('RoleView')->where('role.role_id = %d', $v['creator_role_id'])->find();
			$objActSheet->setCellValue('A'.$i, $v['subject']);
			$objActSheet->setCellValue('B'.$i, $owner['user_name'].'['.$owner['department_name'].'-'.$owner['role_name']).']';
			$v['due_date'] == 0 || strlen($v['due_date']) != 10 ? $objActSheet->setCellValue('C'.$i, '') : $objActSheet->setCellValue('C'.$i, date("Y-m-d H:i:s", $v['due_date']));
			$objActSheet->setCellValue('D'.$i, $v['status']);
			$objActSheet->setCellValue('E'.$i, $v['priority']);
			$v['send_email'] == 0 ? $objActSheet->setCellValue('F'.$i, L('NO')) : $objActSheet->setCellValue('F'.$i, L('YES'));
			$objActSheet->setCellValue('G'.$i, $v['description']);
			$objActSheet->setCellValue('H'.$i, $creator['user_name'].'['.$creator['department_name'].'-'.$creator['role_name'].']');
			$objActSheet->setCellValue('I'.$i, date("Y-m-d H:i:s", $v['create_date']));
		}
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		header("Content-Type: application/vnd.ms-excel;");
        header("Content-Disposition:attachment;filename=5kcrm_task_".date('Y-m-d',mktime()).".xls");
        header("Pragma:no-cache");
        header("Expires:0");
        $objWriter->save('php://output'); 
	}

	public function excelImport(){
		C('TOKEN_ON',false);
		$m_task = M('task');
		if($_POST['submit']){
			if (isset($_FILES['excel']['size']) && $_FILES['excel']['size'] != null) {
				import('@.ORG.UploadFile');
				$upload = new UploadFile();
				$upload->maxSize = 20000000;
				$upload->allowExts  = array('xls');
				$dirname = UPLOAD_PATH . date('Ym', time()).'/'.date('d', time()).'/';
				if (!is_dir($dirname) && !mkdir($dirname, 0777, true)) {
					alert('error', L('ATTACHMENTS TO UPLOAD DIRECTORY CANNOT WRITE'), U('task/index'));
				}
				$upload->savePath = $dirname;
				if(!$upload->upload()) {
					alert('error', $upload->getErrorMsg(), U('task/index'));
				}else{
					$info =  $upload->getUploadFileInfo();
				}
			}
			if(is_array($info[0]) && !empty($info[0])){
				$savePath = $dirname . $info[0]['savename'];
			}else{
				alert('error', L('UPLOAD FAILED'), U('task/index'));
			};
			import("ORG.PHPExcel.PHPExcel");
			$PHPExcel = new PHPExcel();
			$PHPReader = new PHPExcel_Reader_Excel2007();
			if(!$PHPReader->canRead($savePath)){
				$PHPReader = new PHPExcel_Reader_Excel5();
			}
			$PHPExcel = $PHPReader->load($savePath);
			$currentSheet = $PHPExcel->getSheet(0);
			$allRow = $currentSheet->getHighestRow();
			for ($currentRow = 2;$currentRow <= $allRow;$currentRow++) {
				$data['subject'] = $currentSheet->getCell('B'.$currentRow)->getValue();
				$data['owner_role_id'] = $currentSheet->getCell('E'.$currentRow)->getValue();
				$data['due_date'] = strtotime($currentSheet->getCell('G'.$currentRow)->getValue());
				$data['status'] = $currentSheet->getCell('H'.$currentRow)->getValue();
				$data['priority'] = $currentSheet->getCell('I'.$currentRow)->getValue();
				$data['send_email'] = $currentSheet->getCell('J'.$currentRow)->getValue();
				$data['description'] = $currentSheet->getCell('K'.$currentRow)->getValue();
				$data['creator_role_id'] = $currentSheet->getCell('N'.$currentRow)->getValue();
				$data['create_time'] = strtotime($currentSheet->getCell('P'.$currentRow)->getValue());
				$data['update_time'] = strtotime($currentSheet->getCell('Q'.$currentRow)->getValue());
				if(!$m_task->add($data)) {
					if($this->_post('error_handing','intval',0) == 0){
							alert('error', L('ERROR INTRODUCED INTO THE LINE', array($currentRow, $m_task->getError())), U('task/index'));
						}else{
							$error_message .= L('LINE ERROR' ,array($currentRow , $m_task->getError()));
							$m_task->clearError();
						}
					break;
				}
			}
			alert('success', $error_message .L('IMPORT SUCCESS'), U('task/index'));
		}else{
			$this->display();
		}
	}

	public function analytics(){
		$m_task = M('Task');
		if($_GET['role']) {
			$role_id = intval($_GET['role']);
		}else{
			$role_id = 'all';
		}
		if($_GET['department'] && $_GET['department'] != 'all'){
			$department_id = intval($_GET['department']);
		}else{
			$department_id = D('RoleView')->where('role.role_id = %d', session('role_id'))->getField('department_id');
		}
		if($_GET['start_time']) $start_time = strtotime($_GET['start_time']);
		$end_time = $_GET['end_time'] ?  strtotime($_GET['end_time']) : time();
		if($role_id == "all") {
			$roleList = getRoleByDepartmentId($department_id);
			$role_id_array = array();
			foreach($roleList as $v){
				$role_id_array[] = '%,'.$v['role_id'].',%';
			}
			$where_completion['owner_role_id'] = array('like',$role_id_array,'or');
		}else{
			$where_completion['owner_role_id'] = array('like','%,'.$role_id.',%');
		}
		if($start_time){
			$where_create_time = array(array('lt',$end_time),array('gt',$start_time), 'and');
			$where_completion['create_time'] = $where_create_time;
		}else{
			$where_completion['create_time'] = array('lt',$end_time);
		}
		
		$completion_count_array = array();
		$statusList = array(L('NOT_START'), L('DELAY'), L('ONGOING'), L('COMPLETE'));
		$where_completion['is_deleted'] = 0;
		$where_completion['isclose'] = 0;
		foreach($statusList as $v){
			$where_completion['status'] = $v;
			$target_count = $m_task ->where($where_completion)->count();
			$completion_count_array[] = '['.'"'.$v.'",'.$target_count.']';
		}
		$this->completion_count = implode(',', $completion_count_array);
		
		$role_id_array = array();
		if($role_id == "all"){
			if($department_id != "all"){
				$roleList = getRoleByDepartmentId($department_id);
				foreach($roleList as $v){
					$role_id_array[] = $v['role_id'];
				}
			}else{
				$role_id_array = getSubRoleId();
			}
		}else{
			$role_id_array[] = $role_id;
		}
		if($start_time){
			$create_time= array(array('lt',$end_time),array('gt',$start_time), 'and');
		}else{
			$create_time = array('lt',$end_time);
		}
		
		$own_count_total = 0;
		$new_count_total = 0;
		$late_count_total = 0;
		$deal_count_total = 0;
		$success_count_total = 0;
		$busi_customer_array = M('Business')->getField('customer_id', true);
		$busi_customer_id=implode(',', $busi_customer_array);
		foreach($role_id_array as $v){
			$user = getUserByRoleId($v);
			$owner_role_id = array('like', '%,'.$v.',%');
			$own_count = $m_task->where(array('is_deleted'=>0,'isclose'=>0, 'owner_role_id'=>$owner_role_id, 'create_date'=>$create_time))->count();
			$new_count = $m_task->where(array('is_deleted'=>0,'isclose'=>0,'status'=>L('NOT_START'), 'owner_role_id'=>$owner_role_id, 'create_date'=>$create_time))->count();
			$late_count = $m_task->where(array('is_deleted'=>0,'isclose'=>0,'status'=>L('DELAY'), 'owner_role_id'=>$owner_role_id, 'create_date'=>$create_time))->count();
			$deal_count = $m_task->where(array('is_deleted'=>0,'isclose'=>0,'status'=>L('ONGOING'), 'owner_role_id'=>$owner_role_id, 'create_date'=>$create_time))->count();
			$success_count =  $m_task->where(array('is_deleted'=>0,'isclose'=>0,'status'=>L('COMPLETE'), 'owner_role_id'=>$owner_role_id, 'create_date'=>$create_time))->count();
			
			$reportList[] = array("user"=>$user,"new_count"=>$new_count,"late_count"=>$late_count,"own_count"=>$own_count,"success_count"=>$success_count,"deal_count"=>$deal_count);
			$late_count_total += $late_count;
			$own_count_total += $own_count;
			$success_count_total += $success_count;
			$deal_count_total += $deal_count;
			$new_count_total += $new_count;
		}
		$this->total_report = array("new_count"=>$new_count_total,"late_count"=>$late_count_total, "own_count"=>$own_count_total, "success_count"=>$success_count_total, "deal_count"=>$deal_count_total);
		$this->reportList = $reportList;
		
		$idArray = getSubRoleId();
		$roleList = array();
		foreach($idArray as $roleId){				
			$roleList[$roleId] = getUserByRoleId($roleId);
		}
		$this->roleList = $roleList;
		
		$departments = M('roleDepartment')->select();
		$department_id = D('RoleView')->where('role.role_id = %d', session('role_id'))->getField('department_id');
		$departmentList[] = M('roleDepartment')->where('department_id = %d', $department_id)->find();$departmentList = array_merge($departmentList, getSubDepartment($department_id,$departments,''));
		$this->assign('departmentList', $departmentList);
		$this->display();
	}
	
	public function tips(){
		$m_task = M('Task');
		$num = $m_task->where('owner_role_id = %d and isclose = 0 and status <> "'.L('COMPLETE').'" and is_deleted <> 1', session('role_id'))->count();
		$this->ajaxReturn($num,"",1);
	}
}