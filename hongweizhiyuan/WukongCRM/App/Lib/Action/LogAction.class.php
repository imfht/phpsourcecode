<?php
class LogAction extends Action{
	public function _initialize(){
		$action = array(
			'permission'=>array('wxadd'),
			'allow'=>array('add', 'view', 'delete', 'edit', 'index', 'mylog_view', 'mylog_edit', 'log_delete', 'mylog_add', 'anly','tasklog','notepad','getnotepad')
		);
		B('Authenticate', $action);
	}
	
	public function add(){
		if($_POST['submit']){
			if($_POST['r']){
				$r = $_POST['r'];
				$module = $_POST['module'];
				$model_id = $_POST['id'];
				$m_r = M($r);
				$m_log = M('Log');
				$m_log->create();
				$m_log->category_id = 1;
				$m_log->create_date = time();
				$m_log->update_date = time();
				if($log_id = $m_log->add()){
					$m_id = $module . '_id';
					$data['log_id'] = $log_id;
					$data[$m_id] = $model_id;
					if($m_r -> add($data)){
						if($_POST['nextstep_time']){
							$nextstep_time = strtotime($_POST['nextstep_time']);
							if($module == 'leads' || $module == 'business'){	
								$save_array['nextstep_time'] = $nextstep_time;
								$save_array['nextstep'] = $_POST['nextstep'];
								M($module)->where($module.'_id = %d', $model_id)->save($save_array);
							}
						}
						alert('success',L('ADD SUCCESS', array(L('LOG'))),$_SERVER['HTTP_REFERER']);
					}else{
						alert('error', L('ADD_LOG_FAILED'),$_SERVER['HTTP_REFERER']);
					}
				}else{
					alert('error', L('ADD_LOG_FAILED'),$_SERVER['HTTP_REFERER']);
				}
			}else{
				$m_log = M('Log');
				$m_log->create();
				$m_log->category_id = 1;
				$m_log->create_date = time();
				$m_log->update_date = time();
				if($log_id = $m_log->add()){
					$data['business_id'] = intval($_POST['business_id']);
					$data['task_id'] = intval($_POST['task_id']);
					$data['product_id'] = intval($_POST['product_id']);
					$data['customer_id'] = intval($_POST['customer_id']);
					$data['log_id'] = $log_id;
					
					if ($data['business_id']) {
						M('RBusinessLog')->add($data);
					}
					if ($data['task_id']) {
						M('RLogTask')->add($data);
					}
					if ($data['product_id']) {
						M('RLogProduct')->add($data);
					}
					if ($data['customer_id']) {
						M('RCustomerLog')->add($data);
					}
					
					alert('success',L('ADD SUCCESS', array(L('LOG'))),$_SERVER['HTTP_REFERER']);
				}else{
					alert('error', L('ADD_LOG_FAILED'),$_SERVER['HTTP_REFERER']);
				}
			}
			
		} elseif ($_GET['r'] && $_GET['module'] && $_GET['id']) {
			$this -> r = $_GET['r'];
			$this -> module = $_GET['module'];
			$this -> model_id = $_GET['id'];
			$this->display();
				
		} else {
			alert('error', L('PARAMETER_ERROR'),$_SERVER['HTTP_REFERER']);
		}
	}
	//WeChat page
	public function wxadd(){
		if($_POST['subject']){
			$log = M('Log');
			$log->create();
			$log->create_date = time();
			$log->update_date = time();
			
			$log->role_id = $_GET['id'];
			if($log->add()){
				$this->success(L('ADD SUCCESS', array(L('LOG'))));
			}else{
				$this->error(L('ADD_LOG_FAILED'));
			}
		}else{
			$this->display();
		}
	}
	public function view(){
		if($_GET['id']){
			$log_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
			$m_log = M('Log');
			$log = $m_log->where('log_id = %d', $log_id)->find();
			$file_ids = M('rFileLog')->where('log_id = %d', $log_id)->getField('file_id', true);
			$log['file'] = M('file')->where('file_id in (%s)', implode(',', $file_ids))->select();
			$file_count = 0;
			foreach ($log['file'] as $key=>$value) {
				$log['file'][$key]['owner'] = D('RoleView')->where('role.role_id = %d', $value['role_id'])->find();
				$file_count ++;
			}
			$log['file_count'] = $file_count;
			$log['creator'] = getUserByRoleId($log['role_id']);
			
			$this->log =  $log;
			$this->alert = parseAlert();
			$this->display();
		}else{
			alert('error', L('PARAMETER_ERROR'),$_SERVER['HTTP_REFERER']);
		}
	}

	public function delete(){
		$log_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		if (0 == $log_id){
			alert('error',L('PARAMETER_ERROR'),$_SERVER['HTTP_REFERER']);
		} else {
			if (isset($_GET['r']) && isset($_GET['id'])) {
				$m_r = M($_GET['r']);
				$m_log = M('log');
				
				if ($m_r->where('log_id = %d',$_GET['id'])->delete()) {
					if ($m_log->where('log_id = %d',$_GET['id'])->delete()) {
						alert('success',L('DELETE_LOG_SUCCESS'),$_SERVER['HTTP_REFERER']);
					} else {
						alert('error',L('DELETE FAILED CONTACT THE ADMINISTRATOR'),$_SERVER['HTTP_REFERER']);
					}
				} else {
					alert('error',L('DELETE FAILED CONTACT THE ADMINISTRATOR'),$_SERVER['HTTP_REFERER']);
				}
			} elseif (empty($_GET['r']) && isset($_GET['id'])){
				$m_log = M('Log');
				if ($m_log->where('log_id = %d',$_GET['id'])->delete()){
					alert('success',L('DELETE_LOG_SUCCESS'),U('log/index'));
				} else {
					alert('error',L('DELETE FAILED CONTACT THE ADMINISTRATOR'),$_SERVER['HTTP_REFERER']);
				}
			}
		}
	}
	
	public function anly(){
		$m_log = M('Log');
		$m_comment = M('Comment');
		$by = isset($_GET['by']) ? trim($_GET['by']) : '';
		$where = array();
		$params = array();
		$order = "";
		$below_ids = getSubRoleId(false);
		$all_ids = getSubRoleId();
		$module = isset($_GET['module']) ? trim($_GET['module']) : '';
		switch ($by) {
			case 'today' : $where['create_date'] =  array('gt',strtotime(date('Y-m-d', time()))); break;
			case 'week' : $where['create_date'] =  array('gt',(strtotime(date('Y-m-d', time())) - (date('N', time()) - 1) * 86400)); break;
			case 'month' : $where['create_date'] = array('gt',strtotime(date('Y-m-01', time()))); break;
			case 'add' : $order = 'create_date desc';  break;
			case 'update' : $order = 'update_date desc';  break;
			case 'sub' : $where['role_id'] = array('in',implode(',', $below_ids)); break;
			case 'me' : $where['role_id'] = session('role_id'); break;
			default :  $where['role_id'] = array('in',implode(',', $all_ids)); break;
		}
		if ($_GET['r'] && $_GET['module']){
			$m_r = M($_GET['r']);
			$log_ids = $m_r->getField('log_id', true);
			$where['log_id'] = array('in', implode(',', $log_ids));
		}
		
		$end_time = $_GET['end_time'] ?  strtotime($_GET['end_time']) : time();
		$start_time = $_GET['start_time'] ?  strtotime($_GET['start_time']) : 0;
		if($_GET['end_time'] || $_GET['start_time']){
			$where['create_date'] = array(array('lt',$end_time),array('gt',$start_time), 'and');
		}

		if (!$_GET['role'] || $_GET['role']=='all') {
			$where['role_id'] = array('in',implode(',', getSubRoleId())); 
		}else{
			$where['role_id'] = intval($_GET['role']);
		}
		if(intval($_GET['type'])){
			$where['category_id'] = intval($_GET['type']);
		}else{
			$where['category_id'] = array('eq',1);
		}
		if ($_REQUEST["field"]) {
			$field = trim($_REQUEST['field']);
			$search = empty($_REQUEST['search']) ? '' : trim($_REQUEST['search']);

			$condition = empty($_REQUEST['condition']) ? 'eq' : trim($_REQUEST['condition']);
			if	('create_date' == $field || 'update_date' == $field) {
				$search = strtotime($search);
			} elseif ('role_id' == $field) {
				$condtion = "is";
			}
			$params = array('field='.$_REQUEST['field'], 'condition='.$condition, 'search='.trim($_REQUEST["search"]));
			$field = trim($_REQUEST['field']) == 'all' ? 'subject|content' : $_REQUEST['field'];
			switch ($_REQUEST['condition']) {
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
				default : $where[$field] = array('eq',$search);
			}
		}
		
		if ($order) {
			$list = $m_log->where($where)->order($order)->limit(15)->select();
		} else {

			$p = isset($_GET['p']) ? intval($_GET['p']) : 1 ;
			$list = $m_log->where($where)->page($p.',10')->order('create_date desc')->select();
			$count = $m_log->where($where)->count();
			import("@.ORG.Page");
			$Page = new Page($count,10);
			if (!empty($_REQUEST['by'])){
				$params['by'] = 'by=' . trim($_REQUEST['by']);
				
			}
			$params['start_time'] = 'start_time='.trim($_REQUEST['start_time']);
			$params['end_time'] = 'end_time='.trim($_REQUEST['end_time']);
			
			if (!empty($_REQUEST['r']) && !empty($_REQUEST['module'])) {
				$params['r'] = 'r=' . trim($_REQUEST['r']);
				$params['module'] = 'module=' . trim($_REQUEST['module']);
			}
			if (!empty($_REQUEST['type'])) {
				$params['type'] = 'type=' . trim($_REQUEST['type']);
			}
			$Page->parameter = implode('&', $params);
			$show = $Page->show();		
			$this->assign('page',$show);
		}

		foreach($list as $key=>$value){
			$list[$key]['creator'] = getUserByRoleId($value['role_id']);
			if($m_comment->where("module='log' and module_id=%d", $value['log_id'])->select()){
				$list[$key]['is_comment'] = 1;
			}
		}
		$this->category_list = M('LogCategory')->order('order_id')->select();
		//获取下级和自己的岗位列表,搜索用
		$d_role_view = D('RoleView');
		$this->role_list = $d_role_view->where('role.role_id in (%s)', implode(',', $all_ids))->select();
		//沟通日志  (日志类型中 type = 1 为沟通日志)
		
			$m_customer = M('Customer');
			$m_r_customer_log = M('RCustomerLog');
			$m_contacts = M('Contacts');
			$m_r_contacts_log = M('RContactsLog');
			$m_business = M('Business');
			$m_r_business_log = M('RBusinessLog');
			$m_task = M('Task');
			$m_r_task_log = M('RLogTask');
			$m_event = M('Event');
			$m_r_event_log = M('REventLog');
			$m_leads = M('Leads');
			$m_r_leads_log = M('RLeadsLog');
			foreach($list as $k=>$v){
				$r_customer_log = $m_r_customer_log->where('log_id = %d',$v)->find();
				if(!empty($r_customer_log)){
					$customer = $m_customer->where('customer_id = %d',$r_customer_log['customer_id'])->find();
					$list[$k]['customer_id'] = $customer['customer_id'];
					$list[$k]['customer_name'] = $customer['name'];
				}
				$r_contacts_log = $m_r_contacts_log->where('log_id = %d',$v)->find();
				if(!empty($r_contacts_log)){
					$contacts = $m_contacts->where('contacts_id = %d',$r_contacts_log['contacts_id'])->find();
					$list[$k]['contacts_id'] = $contacts['contacts_id'];
					$list[$k]['contacts_name'] = $contacts['name'];
				}
				$r_business_log = $m_r_business_log->where('log_id = %d',$v)->find();
				if(!empty($r_business_log)){
					$business = $m_business->where('business_id = %d',$r_business_log['business_id'])->find();
					$list[$k]['business_id'] = $business['business_id'];
					$list[$k]['business_name'] = $business['name'];
					$list[$k]['nextstep_time'] = $business['nextstep_time'];
					$list[$k]['nextstep'] = $business['nextstep'];
				}
				$r_task_log = $m_r_task_log->where('log_id = %d',$v)->find();
				if(!empty($r_task_log)){
					$task = $m_task->where('task_id = %d',$r_task_log['task_id'])->find();
					$list[$k]['task_id'] = $task['task_id'];
					$list[$k]['task_name'] = $task['subject'];
				}
				$r_event_log = $m_r_event_log->where('log_id = %d',$v)->find();
				if(!empty($r_event_log)){
					$event = $m_event->where('event_id = %d',$r_event_log['event_id'])->find();
					$list[$k]['event_id'] = $event['event_id'];
					$list[$k]['event_name'] = $event['subject'];
				}
				$r_leads_log = $m_r_leads_log->where('log_id = %d',$v)->find();
				if(!empty($r_leads_log)){
					$leads = $m_leads->where('leads_id = %d',$r_leads_log['leads_id'])->find();
					$list[$k]['leads_id'] = $leads['leads_id'];
					$list[$k]['leads_name'] = $leads['name'];
					$list[$k]['nextstep_time'] = $leads['nextstep_time'];
					$list[$k]['nextstep'] = $leads['nextstep'];
				}
			}
		$this->assign('list',$list);
		$this->alert = parseAlert();
		$this->display();
	}
	
	public function index(){
		$m_log = M('Log');
		$m_comment = M('Comment');
		$where = array();
		$params = array();
		
		$order = "create_date desc";
		if($_GET['desc_order']){
			$order = trim($_GET['desc_order']).' desc';
		}elseif($_GET['asc_order']){
			$order = trim($_GET['asc_order']).' asc';
		}
		
		$below_ids = getSubRoleId(false);
		$all_ids = getSubRoleId();
		$module = isset($_GET['module']) ? trim($_GET['module']) : '';
		$by = isset($_GET['by']) ? trim($_GET['by']) : '';
		switch ($by) {
			case 'today' : $where['create_date'] =  array('gt',strtotime(date('Y-m-d', time()))); break;
			case 'week' : $where['create_date'] =  array('gt',(strtotime(date('Y-m-d', time())) - (date('N', time()) - 1) * 86400)); break;
			case 'month' : $where['create_date'] = array('gt',strtotime(date('Y-m-01', time()))); break;
			case 'add' : $order = 'create_date desc';  break;
			case 'update' : $order = 'update_date desc';  break;
			case 'sub' : $where['role_id'] = array('in',implode(',', $below_ids)); break;
			case 'me' : $where['role_id'] = session('role_id'); break;
			default :  $where['role_id'] = array('in',implode(',', $all_ids)); break;
		}
	
		if ($_GET['r'] && $_GET['module']){
			$m_r = M($_GET['r']);
			$log_ids = $m_r->getField('log_id', true);
			$where['log_id'] = array('in', implode(',', $log_ids));
		}
		if (!isset($where['role_id'])) {
			$where['role_id'] = array('in',implode(',', getSubRoleId())); 
		}
		if(intval($_GET['type'])){
			$where['category_id'] = intval($_GET['type']);
		}else{
			$where['category_id'] = array('neq',1);
		}
		if ($_REQUEST["field"]) {
			$field = trim($_REQUEST['field']);
			$search = empty($_REQUEST['search']) ? '' : trim($_REQUEST['search']);

			$condition = empty($_REQUEST['condition']) ? 'eq' : trim($_REQUEST['condition']);
			if	('create_date' == $field || 'update_date' == $field) {
				$search = strtotime($search);
			} elseif ('role_id' == $field) {
				$condtion = "is";
			}
			$params = array('field='.$_REQUEST['field'], 'condition='.$condition, 'search='.trim($_REQUEST["search"]));
			$field = trim($_REQUEST['field']) == 'all' ? 'subject|content' : $_REQUEST['field'];
			switch ($_REQUEST['condition']) {
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
				default : $where[$field] = array('eq',$search);
			}
		}

		$p = isset($_GET['p']) ? intval($_GET['p']) : 1 ;
		$list = $m_log->where($where)->page($p.',10')->order($order)->select();
		$count = $m_log->where($where)->count();
		import("@.ORG.Page");
		$Page = new Page($count,10);
		if (!empty($_REQUEST['by'])){
			$params['by'] = 'by=' . trim($_REQUEST['by']);
			
		}
		if (!empty($_REQUEST['r']) && !empty($_REQUEST['module'])) {
			$params['r'] = 'r=' . trim($_REQUEST['r']);
			$params['module'] = 'module=' . trim($_REQUEST['module']);
		}
		if (!empty($_REQUEST['type'])) {
			$params['type'] = 'type=' . trim($_REQUEST['type']);
		}
		
		$this->parameter = implode('&', $params);
		if ($_GET['desc_order']) {
			$params[] = "desc_order=" . trim($_GET['desc_order']);
		} elseif($_GET['asc_order']){
			$params[] = "asc_order=" . trim($_GET['asc_order']);
		}
		
		$Page->parameter = implode('&', $params);
		$show = $Page->show();		
		$this->assign('page',$show);

		foreach($list as $key=>$value){
			$list[$key]['creator'] = getUserByRoleId($value['role_id']);
			if($m_comment->where("module='log' and module_id=%d", $value['log_id'])->select()){
				$list[$key]['is_comment'] = 1;
			}
		}
		
		$this->category_list = M('LogCategory')->order('order_id')->select();
		//获取下级和自己的岗位列表,搜索用
		$d_role_view = D('RoleView');
		$this->role_list = $d_role_view->where('role.role_id in (%s)', implode(',', $below_ids))->select();
		
		$this->assign('list',$list);
		$this->alert = parseAlert();
		$this->display();
	}
	
	
	public function mylog_view(){
		if($_GET['id']){
			if (in_array($log['role_id'], getSubRoleId())) alert('error', L('HAVE NOT PRIVILEGES'), $_SERVER['HTTP_REFERER']);
			$log_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
			$m_log = M('Log');
			$m_customer = M('Customer');
			$m_r_customer_log = M('RCustomerLog');
			$m_contacts = M('Contacts');
			$m_r_contacts_log = M('RContactsLog');
			$m_business = M('Business');
			$m_r_business_log = M('RBusinessLog');
			$m_task = M('Task');
			$m_r_task_log = M('RLogTask');
			$m_event = M('Event');
			$m_r_event_log = M('REventLog');
			$m_leads = M('Leads');
			$m_r_leads_log = M('RLeadsLog');
			$log = $m_log->where('log_id = %d', $log_id)->find();
			$file_ids = M('rFileLog')->where('log_id = %d', $log_id)->getField('file_id', true);
			$log['file'] = M('file')->where('file_id in (%s)', implode(',', $file_ids))->select();
			$log['creator'] = getUserByRoleId($log['role_id']);
			//Log related module
			$r_customer_log = $m_r_customer_log->where('log_id = %d',$log_id)->find();
			if(!empty($r_customer_log)){
				$customer = $m_customer->where('customer_id = %d',$r_customer_log['customer_id'])->find();
				$log['customer_id'] = $customer['customer_id'];
				$log['customer_name'] = $customer['name'];
			}
			$r_contacts_log = $m_r_contacts_log->where('log_id = %d',$log_id)->find();
			if(!empty($r_contacts_log)){
				$contacts = $m_contacts->where('contacts_id = %d',$r_contacts_log['contacts_id'])->find();
				$log['contacts_id'] = $contacts['contacts_id'];
				$log['contacts_name'] = $contacts['name'];
			}
			$r_business_log = $m_r_business_log->where('log_id = %d',$log_id)->find();
			if(!empty($r_business_log)){
				$business = $m_business->where('business_id = %d',$r_business_log['business_id'])->find();
				$log['business_id'] = $business['business_id'];
				$log['business_name'] = $business['name'];
			}
			$r_task_log = $m_r_task_log->where('log_id = %d',$log_id)->find();
			if(!empty($r_task_log)){
				$task = $m_task->where('task_id = %d',$r_task_log['task_id'])->find();
				$log['task_id'] = $task['task_id'];
				$log['task_name'] = $task['subject'];
			}
			$r_event_log = $m_r_event_log->where('log_id = %d',$log_id)->find();
			if(!empty($r_event_log)){
				$event = $m_event->where('event_id = %d',$r_event_log['event_id'])->find();
				$log['event_id'] = $event['event_id'];
				$log['event_name'] = $event['subject'];
			}
			$r_leads_log = $m_r_leads_log->where('log_id = %d',$log_id)->find();
			if(!empty($r_leads_log)){
				$leads = $m_leads->where('leads_id = %d',$r_leads_log['leads_id'])->find();
				$log['leads_id'] = $leads['leads_id'];
				$log['leads_name'] = $leads['name'];
			}

			if (in_array($log['role_id'], getSubRoleId(false))) {
				if(!($log['comment_role_id'] > 0)){
					$this->comment_role_id = session('role_id');
				}
			}
			
			if(intval($_GET['type'])){
				$condition['category_id'] = intval($_GET['type']);
			}else{
				$log['category_id'] == '1' ? $condition['category_id'] = array('eq',1) : $condition['category_id'] = array('neq',1);
			}
			$below_ids = getSubRoleId(false);
			$by = isset($_GET['by']) ? trim($_GET['by']) : '';
			switch ($by) {
				case 'today' : $condition['create_date'] =  array('gt',strtotime(date('Y-m-d', time()))); break;
				case 'week' : $condition['create_date'] =  array('gt',(strtotime(date('Y-m-d', time())) - (date('N', time()) - 1) * 86400)); break;
				case 'month' : $condition['create_date'] = array('gt',strtotime(date('Y-m-01', time()))); break;
				case 'add' : $order = 'create_date desc';  break;
				case 'update' : $order = 'update_date desc';  break;
				case 'sub' : $condition['role_id'] = array('in',implode(',', $below_ids)); break;
				case 'me' : $condition['role_id'] = session('role_id'); break;
			}
			if ($_REQUEST["field"]) {
				$field = trim($_REQUEST['field']);
				$search = empty($_REQUEST['search']) ? '' : trim($_REQUEST['search']);
				$terms = empty($_REQUEST['condition']) ? 'eq' : trim($_REQUEST['condition']);
				if	('create_date' == $field || 'update_date' == $field) {
					$search = strtotime($search);
				}
				switch ($terms) {
					case "is" : $condition[$field] = array('eq',$search);break;
					case "isnot" :  $condition[$field] = array('neq',$search);break;
					case "contains" :  $condition[$field] = array('like','%'.$search.'%');break;
					case "not_contain" :  $condition[$field] = array('notlike','%'.$search.'%');break;
					case "start_with" :  $condition[$field] = array('like',$search.'%');break;
					case "end_with" :  $condition[$field] = array('like','%'.$search);break;
					case "is_empty" :  $condition[$field] = array('eq','');break;
					case "is_not_empty" :  $condition[$field] = array('neq','');break;
					case "gt" :  $condition[$field] = array('gt',$search);break;
					case "egt" :  $condition[$field] = array('egt',$search);break;
					case "lt" :  $condition[$field] = array('lt',$search);break;
					case "elt" :  $condition[$field] = array('elt',$search);break;
					case "eq" : $condition[$field] = array('eq',$search);break;
					case "neq" : $condition[$field] = array('neq',$search);break;
					case "between" : $condition[$field] = array('between',array($search-1,$search+86400));break;
					case "nbetween" : $condition[$field] = array('not between',array($search,$search+86399));break;
					case "tgt" :  $condition[$field] = array('gt',$search+86400);break;
					default : $condition[$field] = array('eq',$search);
				}
			}
			//上一篇
			$condition['role_id'] = array('in',implode(',', getSubRoleId()));
			$p_condition = $condition;
			$p_condition['log_id'] = array('gt',$log_id);
			$pre = M('log')->where($p_condition)->order('create_date asc')->limit(1)->find();
			if($pre) $this->pre_href = U('log/mylog_view', 'id='.$pre['log_id'].'&type='.$_GET['type'].'&by='.$by.'&field='.$field.'&condition='.$terms.'&search='.$search);
			//下一篇
			$n_condition = $condition;
			$n_condition['log_id'] = array('lt',$log_id);
			
			$next = M('Log')->where($n_condition)->order('create_date desc')->limit(1)->find();
			if($next) $this->next_href = U('log/mylog_view', 'id='.$next['log_id'].'&type='.$_GET['type'].'&by='.$by.'&field='.$field.'&condition='.$terms.'&search='.$search);
			$this->log =  $log; 
			$this->comment_list = D('CommentView')->where('module = "log" and module_id = %d', $log['log_id'])->order('comment.create_time desc')->select();
			$this->alert = parseAlert();
			$this->display();
		}else{
			alert('error', L('PARAMETER_ERROR'), $_SERVER['HTTP_REFERER']);
		}
	}
	
	public function mylog_edit(){
		if($_GET['id']){
			$log_id = $_GET['id'];
			$m_log = M('Log');
			$log = $m_log->where('log_id = %d', $_GET['id'])->find();
			//Log related Module
			$m_customer = M('Customer');
			$m_r_customer_log = M('RCustomerLog');
			$m_contacts = M('Contacts');
			$m_r_contacts_log = M('RContactsLog');
			$m_business = M('Business');
			$m_r_business_log = M('RBusinessLog');
			$m_task = M('Task');
			$m_r_task_log = M('RLogTask');
			$m_event = M('Event');
			$m_r_event_log = M('REventLog');
			$m_leads = M('Leads');
			$m_r_leads_log = M('RLeadsLog');

			
			$r_customer_log = $m_r_customer_log->where('log_id = %d',$log_id)->find();
			if(!empty($r_customer_log)){
				$customer = $m_customer->where('customer_id = %d',$r_customer_log['customer_id'])->find();
				$log['customer_id'] = $customer['customer_id'];
				$log['customer_name'] = $customer['name'];
			}
			$r_contacts_log = $m_r_contacts_log->where('log_id = %d',$log_id)->find();
			if(!empty($r_contacts_log)){
				$contacts = $m_contacts->where('contacts_id = %d',$r_contacts_log['contacts_id'])->find();
				$log['contacts_id'] = $contacts['contacts_id'];
				$log['contacts_name'] = $contacts['name'];
			}
			$r_business_log = $m_r_business_log->where('log_id = %d',$log_id)->find();
			if(!empty($r_business_log)){
				$business = $m_business->where('business_id = %d',$r_business_log['business_id'])->find();
				$log['business_id'] = $business['business_id'];
				$log['business_name'] = $business['name'];
			}
			$r_task_log = $m_r_task_log->where('log_id = %d',$log_id)->find();
			if(!empty($r_task_log)){
				$task = $m_task->where('task_id = %d',$r_task_log['task_id'])->find();
				$log['task_id'] = $task['task_id'];
				$log['task_name'] = $task['subject'];
			}
			$r_event_log = $m_r_event_log->where('log_id = %d',$log_id)->find();
			if(!empty($r_event_log)){
				$event = $m_event->where('event_id = %d',$r_event_log['event_id'])->find();
				$log['event_id'] = $event['event_id'];
				$log['event_name'] = $event['subject'];
			}
			$r_leads_log = $m_r_leads_log->where('log_id = %d',$log_id)->find();
			if(!empty($r_leads_log)){
				$leads = $m_leads->where('leads_id = %d',$r_leads_log['leads_id'])->find();
				$log['leads_id'] = $leads['leads_id'];
				$log['leads_name'] = $leads['name'];
			}

			if (in_array($log['role_id'], getSubRoleId(false))) {
				if(!($log['comment_role_id'] > 0)){
					$this->comment_role_id = session('role_id');
				}
			}
			$this->log =  $log;
			$this->alert = parseAlert();
			$this->display();
		} elseif ($_POST['submit']){
			$log = M('Log');
			$log -> create();
			if($log->save()){
				alert('success', L('EDIT_LOG_SUCCESS'), U('log/mylog_view','id='.$_POST['log_id']));
			}else{
				alert('error', L('EDIT_LOG_FAILED'), $_SERVER['HTTP_REFERER']);
			}
		}
	}
	public function edit(){
		if($_GET['id']){
			$log = M('Log');
			$this->log =  $log->where('log_id = %d', $_GET['id'])->find();
			$this->alert = parseAlert();
			$this->display();
		} elseif ($_POST['submit']){
			$log = M('Log');
			$log -> create();
			$log -> update_date = time();
			if($log->save()){
				alert('success', L('EDIT_LOG_SUCCESS'), $_SERVER['HTTP_REFERER']);
			}else{
				alert('error', L('EDIT_LOG_FAILED'), $_SERVER['HTTP_REFERER']);
			}
		}
	}
	
	public function mylog_add(){
		if($this->isPost()){
			if(!trim($_POST['subject'])) alert('error',L('NEED_LOG_TITLE'),U('log/index'));
			if(!trim($_POST['content'])) alert('error',L('NEED_LOG_CONTENT'),U('log/index'));
			$log = M('Log');
			$log->create();
			$log->create_date = time();
			$log->update_date = time();
			$log->role_id = session('role_id');
			if($log_id = $log->add()){	
				if (intval($_POST['business_id'])) {
					M('RBusinessLog')->add(array('business_id'=>intval($_POST['business_id']),'log_id'=>$log_id));
				}
				if (intval($_POST['task_id'])) {
					M('RLogTask')->add(array('task_id'=>intval($_POST['task_id']),'log_id'=>$log_id));
				}
				if (intval($_POST['product_id'])) {
					M('RLogProduct')->add(array('product_id'=>intval($_POST['product_id']),'log_id'=>$log_id));
				}
				if (intval($_POST['customer_id'])) {
					M('RCustomerLog')->add(array('customer_id'=>intval($_POST['customer_id']),'log_id'=>$log_id));
				}
				if($_POST['submit'] == L('SAVE')){
					$url = intval($_POST['category_id']) == 1 ? U('log/anly') : U('log/index');
					alert('success',L('ADD SUCCESS', array(L('LOG'))),$url);
				}else{
					alert('success',L('ADD SUCCESS', array(L('LOG'))),U('log/mylog_add'));
				}
			}else{
				alert('error',L('ADD_LOG_FAILED'),U('log/index'));
			}
		}else{
			$this->current_time = time();
			$this->alert = parseAlert();
			$this->display();
		}
	}
	
	public function log_delete() {
		$model = array("rLeadsLog","rBusinessLog","rLogProduct","rCustomerLog","rContactsLog","rLogTask","rEventLog","rFinanceLog");
		if ($_GET['id']){
			$i = 0;
			$log_id = intval($_GET['id']);
			foreach ($model as $v){
				if(M($v)->where('log_id = %d',$log_id)->delete()) $i++;
			}
			if($i == 1){				
				if(M('log')->where('log_id = %d',$log_id)->delete()){
					alert('success' , L('DELETE_RELATED_LOG_SUCCESS') , U('log/anly'));
				}else{
					alert('error' , L('DELETE_RELATED_LOG_FAILED') , $_SERVER['HTTP_REFERER']);
				}
			} elseif (M('log')->where('log_id = %d',$log_id)->delete()){
				alert('success', L('DELETED SUCCESSFULLY'), U('Log/index'));
			}
		} elseif (is_array($_POST['log_id'])) {
			$i = 0;
			foreach ($_POST['log_id'] as $v) {
				foreach ($model as $vv){				
					if(M($vv)->where('log_id = %d',$v)->delete()){						
						$i++;
					}
				}
			}			
			if($i >= 1){
				$log_ids = implode(',', $_POST['log_id']);
				if(M('log')->where('log_id in (%s)', $log_ids)->delete()){
					alert('success', L('DELETE_RELATED_LOG_SUCCESS'), U('Log/anly'));
				} else {
					alert('error', L('DELETE_RELATED_LOG_FAILED'),  $_SERVER['HTTP_REFERER']);
				}
			}else {
				$log_ids = implode(',', $_POST['log_id']);				
				if(M('log')->where('log_id in (%s)', $log_ids)->delete()){					
					alert('success', L('DELETE_RELATED_LOG_SUCCESS'), U('Log/index'));
				} else {					
					alert('error', L('DELETE_RELATED_LOG_FAILED'),  $_SERVER['HTTP_REFERER']);
				}
			}
		}
		
	}

	public function category(){
		$m_category = M('LogCategory');
		$this->category_list = $m_category->order('order_id')->select();
		$this->alert=parseAlert();
		$this->display();
	}
	
	public function categoryAdd(){
		if ($this->isPost()) {
			$m_category = M('LogCategory');
			if($m_category->create()){
				if ($m_category->add()) {
					alert('success', L('ADD SUCCESS', array(L('LOG'))), $_SERVER['HTTP_REFERER']);
				} else {
					alert('error', L('ADD_FAILED_CONTACT_ADMINISTRATOR'), $_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error', L('ADD_FAILED_CONTACT_ADMINISTRATOR'), $_SERVER['HTTP_REFERER']);
			}
		} else {
			$this->alert=parseAlert();
			$this->display();
		}
	}
	
	public function categoryEdit(){
		$m_category = M('LogCategory');
		if ($this->isGet()) {
			$category_id = intval(trim($_GET['id']));
			$this->log_category = $m_category->where('category_id = %d', $category_id)->find();
			$this->display();
		} else {
			if ($m_category->create()) {
				if ($m_category->save()) {
					alert('success', L('EDIT_LOG_SUCCESS'), $_SERVER['HTTP_REFERER']);
				} else {
					alert('error', L('DATA_NO_MODIFIED'), $_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error', L('MODIFY_FAILED_CONTACT_ADMINISTRATOR'), $_SERVER['HTTP_REFERER']);
			}
		}
	}
	
	public function categoryDelete(){
		if ($_POST['category_id']) {
			$id_array = $_POST['category_id'];
			if (M('Log')->where('category_id <> 1 and category_id in (%s)', implode(',', $id_array))->select()) {
				alert('error', L('DELETE_FAILED_PLEASE_DELETE_ONE_BY_ONE'), $_SERVER['HTTP_REFERER']);
			} else {
				if (M('LogCategory')->where('category_id in (%s)', implode(',', $id_array))->delete()) {
					alert('success', L('DELETED SUCCESSFULLY'), $_SERVER['HTTP_REFERER']);
				} else {
					alert('error', L('DELETE_RELATED_LOG_FAILED'), $_SERVER['HTTP_REFERER']);
				}
			}
		} elseif($_POST['old_id']){
			$old_id = intval($_POST['old_id']);
			$new_id = intval($_POST['new_id']);
			if($old_id && $new_id){
				if (M('LogCategory')->where('category_id <> 1 category_id = %d', $old_id)->delete()) {
					M('Log')->where('category_id = %d', $old_id)->setField('category_id', $new_id);
					M('LogCategory')->where('category_id = %d', $old_id)->setField('category_id', $new_id);
					alert('success', L('DELETED SUCCESSFULLY'), $_SERVER['HTTP_REFERER']);
				} else {
					alert('error', L('MODULE_LOG_IS_SYSTEM_FIELDS_CAN_NOT_BE_DELETED'), $_SERVER['HTTP_REFERER']);
				}
			}else{
				alert('error', L('DELETE_FAILED_FOR_INVALIDATE_PARAMETER'), $_SERVER['HTTP_REFERER']);
			}
		} else {
			$old_id = intval(trim($_GET['id']));
			$this->old_id = $old_id;
			$this->statusList = M('LogCategory')->where('category_id <> %d', $old_id)->select();
			$this->display();
		}
	}
	
	public function categorySort(){
		if ($this->isGet()) {
			$status = M('LogCategory');
			$a = 0;
			foreach (explode(',', $_GET['postion']) as $v) {
				$a++;
				$status->where('category_id = %d', $v)->setField('order_id',$a);
			}
			$this->ajaxReturn('1', L('SAVE_SUCCESSFUL'), 1);
		} else {
			$this->ajaxReturn('0', L('SAVE_FAILED'), 1);
		}
	}
	
	/**
	*任务日志
	*
	**/
	public function tasklog(){
		
		$value = unserialize(M('config')->where('name = "defaultinfo"')->getField('value'));
		if($this->isPost()){

			$module = $_POST['module'];
			$task_id = $_POST['task_id'];
			$m_log = M('Log');
			$m_file = M('File');
			
			$m_log->create();
			$m_log->category_id = 1;
			$m_log->create_date = time();
			$m_log->update_date = time();
			
			if (array_sum($_FILES['file']['size'])) {
				//如果有文件上传 上传附件
				import('@.ORG.UploadFile');
				//导入上传类
				$upload = new UploadFile();
				//设置上传文件大小
				$upload->maxSize = 20000000;
				//设置附件上传目录
				$dirname = './Uploads/' . date('Ym', time()).'/'.date('d', time()).'/';
				$upload->allowExts  = explode(',', $value['allow_file_type']);// 设置附件上传类型
				
				if (!is_dir($dirname) && !mkdir($dirname, 0777, true)) {
					$this->error(L('ATTACHMENTS TO UPLOAD DIRECTORY CANNOT WRITE'));
				}
				$upload->savePath = $dirname;
				
				if(!$upload->upload()) {// 上传错误提示错误信息
					alert('error', $upload->getErrorMsg(), $_SERVER['HTTP_REFERER']);
				}else{// 上传成功 获取上传文件信息
					$info =  $upload->getUploadFileInfo();
				}
			}
			if(empty($_POST['content'])){
				alert('error','内容描述不能为空！',$_SERVER['HTTP_REFERER']);
			}
			if($log_id = $m_log->add()){
				$taskList = M('Task')->where('task_id = %d', $task_id)->find();
				M('Task')->where('task_id = %d', $task_id)->setField('about_roles', ($taskList['about_roles']).$_POST['about_roles']);
				M('Task')->where('task_id = %d', $task_id)->setField('status', $_POST['status']);
				$send_email_array = ($taskList['about_roles']).($taskList['owner_role_id']);
				$data['log_id'] = $log_id;
				$data['task_id'] = $task_id;
				$send_email_str = explode(',',$send_email_array);
				$creator = getUserByRoleId(session('role_id'));
				$email_content = ("发件人：".$creator['user_name']."<br>".
									"部门：".$creator['department_name']."<br>".
									'岗位：'.$creator['role_name']."<br>".
									'内容：'."<pre>".$_POST['content']."</pre>"."<br>".
									'发件时间：'.date('Y-m-d H:i:s',time()));
				if($send_email_str){
					foreach($send_email_str as $k => $v){
						if($v !="" && $v != session('role_id')) {
							if(intval($_POST['email_alert']) == 1){
								sysSendEmail($v,$taskList['subject'],$email_content);
							}
							if(intval($_POST['message_alert']) == 1) {
								sendMessage($v,$email_content,1);
							}
						}
					}	
				}
				if(M('RLogTask') -> add($data)){
					foreach($info as $key=>$value){
						$data['name'] = $value['name'];
						$data['file_path'] = $value['savepath'].$value['savename'];
						$data['role_id'] = $_POST['role_id'];
						$data['size'] = $value['size'];
						$data['create_date'] = time(); 
						if($file_id = $m_file->add($data)){
							$temp = array();
							$temp['file_id'] = $file_id;
							$temp['log_id'] = $log_id;
							if(!M('RFileLog')->add($temp)){
								alert('error', '部分文件上传失败，请联系管理员！', $_SERVER['HTTP_REFERER']);
							}
						}else{
							alert('error', L('ADD_ATTACHMENTS_FAIL'), $_SERVER['HTTP_REFERER']);
						};
					}
					alert('success',L('ADD SUCCESS', array(L('LOG'))),$_SERVER['HTTP_REFERER']);
				}else{
					alert('error', L('ADD_LOG_FAILED'),$_SERVER['HTTP_REFERER']);
				}
				
		
			}else{
				alert('error', L('ADD_LOG_FAILED'),$_SERVER['HTTP_REFERER']);
			}
		} elseif ($_GET['id']) {
			$this->allowExts  = $value['allow_file_type'];
			$this -> model_id = $_GET['id'];
			$status = M('Task')->where('task_id = %d', $_GET['id'])->getField('status');
			$this->status = $status;
			$this->display();
				
		} else {
			alert('error', L('PARAMETER_ERROR'),$_SERVER['HTTP_REFERER']);
		}
	}
	
	/**
	 * 获取便笺
	 **/
	public function getNotepad(){
		$m_note = M('note');
		$note = $m_note->where('role_id = %d', session('role_id'))->order('note_id asc')->getField('content');
		$this->ajaxReturn($note,'success',1);
	}
	
	/**
	 * 写入便笺
	 **/
	public function notepad(){
		$content = empty($_POST['content']) ? '' : $_POST['content'];
		$m_note = M('note');
		$note = $m_note->where('role_id = %d', session('role_id'))->find();
		if($note){
			$result = $m_note->where('role_id = %d', session('role_id'))->save(array('content'=>$content, 'update_time'=>time()));
		}else{
			$result = $m_note->add(array('role_id'=>session('role_id'),'content'=>$content, 'update_time'=>time()));
		}
		if($result){
			$this->ajaxReturn('','success',1);
		}else{
			$this->ajaxReturn('','error',0);
		}
	}
}
