<?php
class ActionLogAction extends Action{
	public function _initialize(){
		$action = array(
			'permission'=>array('wxadd'),
			'allow'=>array('index')
		);
		B('Authenticate', $action);
	}
	public function delete(){
		if ($this->isPost()) {			
			$log_ids = implode(',', $_POST['log_id']);
			if(M('ActionLog')->where('log_id in (%s)', $log_ids)->delete()){					
				alert('success', L('DELETE_RELATED_LOG_SUCCESS'),  $_SERVER['HTTP_REFERER']);
			} else {					
				alert('success', L('DELETE_RELATED_LOG_FAILED'),  $_SERVER['HTTP_REFERER']);
			}			
		}else{
			alert('error',L('INVALIDATE_PARAMETER'),$_SERVER['HTTP_REFERER']);
		}		
	}
	
	
	public function index(){
		$m_log = M('ActionLog');
		$by = isset($_GET['by']) ? trim($_GET['by']) : '';
		$where = array();
		$params = array();
		
		$order = "create_time desc";
		if($_GET['desc_order']){
			$order = trim($_GET['desc_order']).' desc';
		}elseif($_GET['asc_order']){
			$order = trim($_GET['asc_order']).' asc';
		}
		
		$all_ids = getSubRoleId();
		switch ($by) {
			case 'today' : $where['create_time'] =  array('gt',strtotime(date('Y-m-d', time()))); break;
			case 'week' : $where['create_time'] =  array('gt',(strtotime(date('Y-m-d', time())) - (date('N', time()) - 1) * 86400)); break;
			case 'month' : $where['create_time'] = array('gt',strtotime(date('Y-m-01', time()))); break;
			case 'me' : $where['role_id'] = session('role_id'); break;
			case 'add' : $order = 'create_time desc';  break;
		}
		if (!isset($where['role_id'])) {
			$where['role_id'] = array('in',implode(',', getSubRoleId())); 
		}

		if(trim($_GET['module'])){
			$where['module_name'] = trim($_GET['module']);
		}
		if(trim($_GET['act'])){
			$where['action_name'] = trim($_GET['act']);
		}
		if ($_REQUEST["field"]) {
			$field = trim($_REQUEST['field']) == 'all' ? 'subject|content' : $_REQUEST['field'];
			$search = empty($_REQUEST['search']) ? '' : trim($_REQUEST['search']);
			$condition = empty($_REQUEST['condition']) ? 'eq' : trim($_REQUEST['condition']);
			if	('create_time' == $field) {
				$search = strtotime($search);
			}
			$params = array('field='.$_REQUEST['field'], 'condition='.$condition, 'search='.trim($_REQUEST["search"]));
			
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
		foreach($list as $k=>$v){
			$param_name = '';
			if(!empty($v['param_name'])){
				$param_name = $v['param_name'];
			}
			if($v['module_name'] == 'finance'){
				$module_name = substr($v['param_name'],2);
			}else{
				$module_name = $v['module_name'];
			}
			$m_module_name = M($module_name);
			$id = $m_module_name->getPk();
			$name = $m_module_name->where("$id = %d", $v['action_id'])->getField('name');
			if(empty($name)){
				$name = $m_module_name->where("$id = %d", $v['action_id'])->getField('subject');
			}
			$list[$k]['content'] = $v['content'].'---<a href="./index.php?m='.$v[module_name].'&a=view&'.$param_name.'&id='.$v[action_id].'">'.$name.'</a>';
		}
		import("@.ORG.Page");
		$Page = new Page($count,10);
		if (!empty($_REQUEST['by'])){
			$params['by'] = 'by=' . trim($_REQUEST['by']);
		}
		if (!empty($_REQUEST['module'])) {
			$params['module'] = 'module=' . trim($_REQUEST['module']);
		}
		if (!empty($_REQUEST['act'])) {
			$params['act'] = 'act=' . trim($_REQUEST['act']);
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
		
		foreach($list as $k => $v){
			$list[$k]['creator'] = getUserByRoleId($v['role_id']);
		}
		$d_role_view = D('RoleView');
		$this->role_list = $d_role_view->where('role.role_id in (%s)', implode(',', $below_ids))->select();
		$this->assign('list',$list);
		$this->alert = parseAlert();
		$this->display();
	}

}
