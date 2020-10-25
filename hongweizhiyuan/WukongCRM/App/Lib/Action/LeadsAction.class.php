<?php  
class LeadsAction extends Action{

	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('analytics','transform','changecontent','getaddchartbyroleid','getownchartbyroleid','check','receive','remove','fenpei','batchreceive', 'assigndialog', 'batchassign', 'revert', 'validate','excelimportdownload')
		);
		B('Authenticate', $action);
	}
	/* elseif (!empty($_POST['mobile'])&&!preg_match("/^13[0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|189[0-9]{8}$/", $_POST['mobile'])){   
		alert('error', '手机格式错误!',$_SERVER['HTTP_REFERER']);
	} */
	public function check(){
		import("@.ORG.SplitWord");
		$sp = new SplitWord();
		$m_leads = M('Leads');
		$m_customer = M('Customer');
		//ignore words
		$useless_words = array(L('COMPANY'),L('LIMITED'),L('OF'),L('COMPANY_LIMITED'));
		if ($this->isAjax()) {
			$split_result = $sp->SplitRMM($_POST['name']);
			if(!is_utf8($split_result)) $split_result = iconv("GB2312//IGNORE", "UTF-8", $split_result) ;
			$result_array = explode(' ',trim($split_result));
            if(count($result_array) < 2){
                $this->ajaxReturn(0,'',0);
                die;
            }
			foreach($result_array as $k=>$v){
				if(in_array($v,$useless_words)) unset($result_array[$k]);
			}
			$leads_commpany_list = $m_leads->getField('name', true);
			$customer_commpany_list = $m_customer->getField('name', true);
			
			$search_array = array();
			foreach($leads_commpany_list as $k=>$v){
				$search = 0;
				foreach($result_array as $k2=>$v2){
					if(strpos($v, $v2) > -1){
						$v = str_replace("$v2","<span style='color:red;'>$v2</span>", $v, $count);
						$search += $count;
					}
				}
				if($search > 2) $search_array[$k] = array('value'=>$v,'search'=>$search);
			}
			$seach_sort_result['leads'] = array_sort($search_array,'search','desc');	
			
			$customer_search_array = array();
			foreach($customer_commpany_list as $k=>$v){
				$search = 0;
				foreach($result_array as $k2=>$v2){
					if(strpos($v, $v2) > -1){
						$v = str_replace("$v2","<span style='color:red;'>$v2</span>", $v, $count);
						$search += $count;
					}
				}
				if($search > 2) $customer_search_array[$k] = array('value'=>$v,'search'=>$search);
			}
			$seach_sort_result['customer'] = array_sort($customer_search_array,'search','desc');
			
			$leads_search = $seach_sort_result['leads'];
			$customer_search = $seach_sort_result['customer'];
			
			if(empty($leads_search) && empty($customer_search)){
				$this->ajaxReturn(0,L('YOU_CAN_ADD'),0);
			}else{
				$this->ajaxReturn($seach_sort_result,L('EXIST_SAME_LEADS_OR_COMPANY'),1);
			}
		}
	}
	public function validate() {
		if($this->isAjax()){
            if(!$this->_request('clientid','trim') || !$this->_request($this->_request('clientid','trim'),'trim')) $this->ajaxReturn("","",3);
            $field = M('Fields')->where('model = "leads" and field = "'.$this->_request('clientid','trim').'"')->find();
            $m_leads = $field['is_main'] ? D('Leads') : D('LeadsData');
            $where[$this->_request('clientid','trim')] = array('eq',$this->_request($this->_request('clientid','trim'),'trim'));
            if($this->_request('id','intval',0)){
                $where[$m_leads->getpk()] = array('neq',$this->_request('id','intval',0));
            }
			if($this->_request('clientid','trim')) {
				if ($m_leads->where($where)->find()) {
					$this->ajaxReturn("","",1);
				} else {
					$this->ajaxReturn("","",0);
				}
			}else{
				$this->ajaxReturn("","",0);
			}
           
		}
	}
	
	
	public function add(){
		if($this->isPost()){
			$m_leads = D('Leads');
			$m_leads_data = D('LeadsData');
			$field_list = M('Fields')->where('model = "leads"  and in_add = 1')->order('order_id')->select();
			foreach ($field_list as $v){
				switch($v['form_type']) {
					case 'address':
						$a = array_filter($_POST[$v['field']]);
						$_POST[$v['field']] = !empty($a) ? implode(chr(10),$a) : '';
					break;
					case 'datetime':
						$_POST[$v['field']] = strtotime($_POST[$v['field']]);
					break;
					case 'box':
						eval('$field_type = '.$v['setting'].';');
						if($field_type['type'] == 'checkbox'){
							$b = array_filter($_POST[$v['field']]);
							$_POST[$v['field']] = !empty($b) ? implode(chr(10),$b) : '';
						}
					break;
				}
			}
			if($m_leads->create()){
				if($m_leads_data->create()!==false){
					if($_POST['nextstep_time']) $m_leads->nextstep_time = $_POST['nextstep_time'];
					$m_leads->create_time = time();
					$m_leads->update_time = time();
					$m_leads->have_time = time();
					if ($leads_id = $m_leads->add()) {
						$m_leads_data->leads_id = $leads_id;
						$m_leads_data->add();
						actionLog($leads_id);
						if($_POST['submit'] == L('SAVE')) {
							alert('success', L('LEADS_ADD_SUCCESS'), U('leads/index'));
						} else {
							alert('success', L('LEADS_ADD_SUCCESS'), U('leads/add'));
						}
					} else {
						alert('error', L('INVALIDATE_PARAM_ADD_LEADS_FAILED'),$_SERVER['HTTP_REFERER']);
					}
				}else{
					$this->error($m_leads_data->getError());
				}
			}else{
				$this->error($m_leads->getError());
			}
			
		}else{
			$field_list = field_list_html("add","leads");
		 	$this->field_list = $field_list;
			$this->alert = parseAlert();		
			$this->display();
		}
	}
	
	public function edit(){
		$leads_id = $_POST['leads_id'] ? intval($_POST['leads_id']) : intval($_REQUEST['id']);
		if(!check_permission($leads_id, 'leads')) $this->error(L('HAVE NOT PRIVILEGES'));
		$field_list = M('Fields')->where('model = "leads"')->order('order_id')->select();
		if($this->isPost()){
			$m_leads = M('Leads');
			$m_leads_data = M('LeadsData');
			foreach ($field_list as $v){
				switch($v['form_type']) {
					case 'address':
						$_POST[$v['field']] = implode(chr(10),$_POST[$v['field']]);
					break;
					case 'datetime':
						$_POST[$v['field']] = strtotime($_POST[$v['field']]);
					break;
					case 'box':
						eval('$field_type = '.$v['setting'].';');
						if($field_type['type'] == 'checkbox'){
							$_POST[$v['field']] = implode(chr(10),$_POST[$v['field']]);
						}
					break;
				}
			}
			if($m_leads->create()){
				if($m_leads_data->create()!==false){
					$m_leads->update_time = time();
					$a = $m_leads->where('leads_id= %d',$_REQUEST['leads_id'])->save();
					$b = $m_leads_data->where('leads_id=%d',$_REQUEST['leads_id'])->save();
					if($a && $b!==false) {
						actionLog($_REQUEST['leads_id']);
						alert('success', L('LEADS_MODIFIED_SUCCESSFULLY'), U('leads/index'));
					} else {
						alert('error', L('LEADS_MODIFIED_FAILED'), $_SERVER['HTTP_REFERER']);
					}
				}else{
					$this->error($m_leads_data->getError());
				}
			}else{
				$this->error($m_leads->getError());
			}
		}elseif($_REQUEST['id']){
			$d_v_leads = D('LeadsView')->where('leads.leads_id = %d',$this->_request('id'))->find();
			$d_v_leads['owner'] = D('RoleView')->where('role.role_id = %d', $d_v_leads['owner_role_id'])->find();
			if (!$d_v_leads) {
				alert('error', L('LEADS_DOES_NOT_EXIST'),$_SERVER['HTTP_REFERER']);
				die;
			}
			$field_list = field_list_html("edit","leads",$d_v_leads);
			$this->field_list = $field_list;
			$this->leads = $d_v_leads;
			$this->alert = parseAlert();
			$this->display();
		}else{
			alert('error', L('INVALIDATE_PARAM'),$_SERVER['HTTP_REFERER']);
		}
	}
	
	public function completeDelete() {
		$m_leads = M('Leads');
		$m_leads_data = M('LeadsData');
		$r_module = array('Log'=>'RLeadsLog', 'File'=>'RFileLeads', 'Event'=>'REventLeads', 'Task'=>'RLeadsTask');
		if($this->isPost()){
			$leads_ids = is_array($_POST['leads_id']) ? implode(',', $_POST['leads_id']) : '';
			if ('' == $leads_ids) {
				alert('error', L('NOT CHOOSE ANY'), $_SERVER['HTTP_REFERER']);
			} else {
				if(!session('?admin')){
					alert('error', L('HAVE NOT PRIVILEGES'), $_SERVER['HTTP_REFERER']);
				}
				if(($m_leads->where('leads_id in (%s)', $leads_ids)->delete()) && ($m_leads_data->where('leads_id in (%s)', $leads_ids)->delete())){	
					foreach ($_POST['leads_id'] as $value) {
						actionLog($value);
						foreach ($r_module as $key2=>$value2) {
							$module_ids = M($value2)->where('leads_id = %d', $value)->getField($key2 . '_id', true);
							M($value2)->where('leads_id = %d', $value) -> delete();
							if(!is_int($key2)){	
								M($key2)->where($key2 . '_id in (%s)', implode(',', $module_ids))->delete();
							}
						}
					}
					alert('success', L('DELETED SUCCESSFULLY'),U('leads/index','by=deleted'));
				} else {
					alert('error', L('DELETE FAILED CONTACT THE ADMINISTRATOR'),$_SERVER['HTTP_REFERER']);
				}
			}
		} elseif($_GET['id']) {
			$leads = $m_leads->where('leads_id = %d', $_GET['id'])->find();
			if (is_array($leads)) {
				if($leads['owner_role_id'] == session('role_id') || session('?admin')){
					if($m_leads->where('leads_id = %d', $_GET['id'])->delete()){
						foreach ($r_module as $key2=>$value2) {
							$module_ids = M($value2)->where('leads_id = %d', $_GET['id'])->getField($key2 . '_id', true);
							M($value2)->where('leads_id = %d', $_GET['id']) -> delete();
							if(!is_int($key2)){
								M($key2)->where($key2 . '_id in (%s)', implode(',', $module_ids))->delete();
							}
						}
						actionLog($_GET['id']);
						alert('success', L('DELETED SUCCESSFULLY'),  U('Leads/index','by=deleted'));
					}else{
						alert('error', L('DELETE FAILED CONTACT THE ADMINISTRATOR'), $_SERVER['HTTP_REFERER']);
					}
				} else {
					alert('error', L('HAVE NOT PRIVILEGES'), $_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error', L('LEADS_DOES_NOT_EXIST'), $_SERVER['HTTP_REFERER']);
			}			
		} else {
			alert('error', L('SELECT_LEADS_TO_DELETE'),$_SERVER['HTTP_REFERER']);
		}
	}
	
	public function delete(){
		$m_leads = M('Leads');
		if($this->isPost()){
			$leads_ids = is_array($_POST['leads_id']) ? implode(',', $_POST['leads_id']) : '';
			if ('' == $leads_ids) {
				alert('error', L('NOT CHOOSE ANY'), $_SERVER['HTTP_REFERER']);
			} else {
				$data = array('is_deleted'=>1, 'delete_role_id'=>session('role_id'), 'delete_time'=>time());
				if($m_leads->where('leads_id in (%s)', $leads_ids)->setField($data)){
					foreach($leads_ids as $value){
						actionLog($value);
					}
					alert('success', L('DELETED SUCCESSFULLY'),$_SERVER['HTTP_REFERER']);
				} else {
					alert('error', L('DELETE FAILED CONTACT THE ADMINISTRATOR'),$_SERVER['HTTP_REFERER']);
				}
			}
		} elseif($this->isGet()) {
			$leads_id = intval(trim($_GET['id']));
			$leads = $m_leads->where('leads_id = %d', $leads_id)->find();
			if (is_array($leads)) {
				if($leads['owner_role_id'] == session('role_id') || session('?admin')){
					$data = array('is_deleted'=>1, 'delete_role_id'=>session('role_id'), 'delete_time'=>time());
					if($m_leads->where('leads_id = %d', $leads_id)->setField($data)){				
						actionLog($leads_id);
						//判断线索是否属于线索池
						$outdays = M('config') -> where('name="leads_outdays"')->getField('value');
						$outdate = empty($outdays) ? time() : time()-86400*$outdays;						
						if($leads['have_time'] < $outdate){						
							alert('success', L('DELETED SUCCESSFULLY'),U('Leads/index','by=public'));
						}else{							
							alert('success', L('DELETED SUCCESSFULLY'),U('Leads/index'));
						}		
					}else{
						alert('error', L('DELETE FAILED CONTACT THE ADMINISTRATOR'), $_SERVER['HTTP_REFERER']);
					}
				} else {
					alert('error', L('HAVE NOT PRIVILEGES'), $_SERVER['HTTP_REFERER']);
				}
					
			} else {
				alert('error', L('LEADS_DOES_NOT_EXIST'), $_SERVER['HTTP_REFERER']);
			}			
		} 
	} 
	
	public function index(){
		$by = isset($_GET['by']) ? trim($_GET['by']) : '';
		$p = isset($_GET['p']) ? intval($_GET['p']) : 1 ;
		$below_ids = getSubRoleId(false);
		$below_ids = empty($below_ids) ? -1 : $below_ids;
		$d_v_leads = D('LeadsView');
		$outdays = M('config') -> where('name="leads_outdays"')->getField('value');
		$outdate = empty($outdays) ? time() : time()-86400*$outdays;
		$where = array();
		$params = array();
		$order = "create_time desc";
		$where['have_time'] = array('egt',$outdate);
		
		if($_GET['desc_order']){
			$order = trim($_GET['desc_order']).' desc';
		}elseif($_GET['asc_order']){
			$order = trim($_GET['asc_order']).' asc';
		}
		
		switch ($by) {
			case 'today' :
				$where['nextstep_time'] =  array(array('lt',strtotime(date('Y-m-d', time()))+86400), array('gt',0), 'and'); 
				break;
			case 'week' : 
				$where['nextstep_time'] =  array(array('lt',strtotime(date('Y-m-d', time())) + (date('N', time()) - 1) * 86400), array('gt', 0),'and'); 
				break;
			case 'month' : 
				$where['nextstep_time'] =  array(array('lt',strtotime(date('Y-m-01', strtotime('+1 month')))), array('gt', 0),'and'); 
				break;
			case 'd7' : 
				$where['update_time'] =  array('lt',strtotime(date('Y-m-d', time()))-86400*6); 
				break;
			case 'd15' : 
				$where['update_time'] =  array('lt',strtotime(date('Y-m-d', time()))-86400*14); 
				break;
			case 'd30' : 
				$where['update_time'] =  array('lt',strtotime(date('Y-m-d', time()))-86400*29); 
				break;
			case 'add' : $order = 'create_time desc';  break;
			case 'update' : $order = 'update_time desc';  break;
			case 'sub' : $where['owner_role_id'] = array('in',implode(',', $below_ids)); break;
			case 'subcreate' : $where['creator_role_id'] = array('in',implode(',', $below_ids)); break;
			case 'public' :
				unset($where['have_time']);
				$where['_string'] = "leads.owner_role_id=0 or leads.have_time < $outdate";
				break;
			case 'deleted' : $where['is_deleted'] = 1;unset($where['have_time']); break;
			case 'transformed' : $where['is_transformed'] = 1; break;
			case 'me' : $where['owner_role_id'] = session('role_id'); break;
			default : $where['owner_role_id'] = array('in',implode(',', getSubRoleId())); break;
		}
		if ($by != 'deleted') {
			$where['is_deleted'] = array('neq',1);
		}
		if ($by != 'transformed') {
			$where['is_transformed'] = array('neq',1);
		}
		if (!isset($where['owner_role_id'])) {
			if(!isset($where['_string'])) $where['owner_role_id'] = array('in', implode(',', getSubRoleId(true)));
			else $where['owner_role_id'] = array('in', '0,'.implode(',', getSubRoleId(true)));
		}
		
		if ($_REQUEST["field"]) {
			if (trim($_REQUEST['field']) == "all") {
				$field = is_numeric(trim($_REQUEST['search'])) ? 'name|owner_role_id|company|position|saltname|phone|mobile|email|qq|fax|website|source|status|industry|state|zip_code|city|state|description|annual_revenue|no_of_employees|' : 'name|owner_role_id|company|position|saltname|phone|mobile|email|qq|fax|website|source|status|industry|state|zip_code|city|state|description';
			} else {
				$field = trim($_REQUEST['field']);
			}
			
			$field_date = M('Fields')->where('is_main=1 and (model="" or model="leads") and form_type="datetime"')->select();
			foreach($field_date as $v){
				if	($field == $v['field']) $search = is_numeric($search)?$search:strtotime($search);
			}
            if ($this->_request('state')){
				$search = $this->_request('state');
				if($this->_request('city')){
					$search .= chr(10) . $this->_request('city');
				}
				if($search){
					$search .= chr(10) .trim($_REQUEST['search']);
				}
			}
			
			$search = empty($_REQUEST['search']) ? '' : trim($_REQUEST['search']);			
			$condition = empty($_REQUEST['condition']) ? 'is' : trim($_REQUEST['condition']);
			if	('create_time' == $field || 'update_time' == $field) {
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
				default : $where[$field] = array('eq',$search);
			}
			$params = array('field='.trim($_REQUEST['field']), 'condition='.$condition, 'search='.$_REQUEST["search"]);
		}
		if(trim($_GET['act'] == 'sms')){
			$customer_list = $d_v_leads->where($where)->select();
			$contacts = array();
			foreach ($customer_list as $k => $v) {
				$contacts[] = array('name'=>$v['contacts_name'], 'customer_name'=>$v['name'], 'telephone'=>trim($v['mobile']));
			}
			$this->contacts = $contacts;
			$this->alert = parseAlert();
			$this->display('Setting:sendsms');
		}elseif(trim($_GET['act']) == 'excel'){
			if(vali_permission('leads', 'export')){
				$order = $order ? $order : 'create_time desc';
				$leadsList = $d_v_leads->where($where)->order($order)->select();			
				$this->excelExport($leadsList);
			}else{
				alert('error', L('HAVE NOT PRIVILEGES'), $_SERVER['HTTP_REFERER']);
			}
		}else{
			$list = $d_v_leads->where($where)->page($p.',15')->order($order)->select();
			$count = $d_v_leads->where($where)->count();
			import("@.ORG.Page");
			$Page = new Page($count,15);
			if (!empty($_GET['by'])) {
				$params[] = 'by='.trim($_GET['by']);
			}
			
			$this->parameter = implode('&', $params);

			if ($_GET['desc_order']) {
				$params[] = "desc_order=" . trim($_GET['desc_order']);
			} elseif($_GET['asc_order']){
				$params[] = "asc_order=" . trim($_GET['asc_order']);
			}
			
			$Page->parameter = implode('&', $params);

			$this->assign('page', $Page->show());

			if($by == 'deleted') {
				foreach ($list as $k => $v) {
					$list[$k]["delete_role"] = getUserByRoleId($v['delete_role_id']);
					$list[$k]["owner"] = getUserByRoleId($v['owner_role_id']);
					$list[$k]["creator"] = getUserByRoleId($v['creator_role_id']);
				}
			}elseif($by == 'transformed'){
				foreach ($list as $k => $v) {
					$list[$k]["owner"] = getUserByRoleId($v['owner_role_id']);
					$list[$k]["creator"] = getUserByRoleId($v['creator_role_id']);				
					$list[$k]["transform_role"] = getUserByRoleId($v['transform_role_id']);
					$list[$k]["business_name"] = M('business')->where('business_id = %d', $v['business_id'])->getField('name');
					$list[$k]["contacts_name"] = M('contacts')->where('contacts_id = %d', $v['contacts_id'])->getField('name');
					$list[$k]["customer_name"] = M('customer')->where('customer_id = %d', $v['customer_id'])->getField('name');
				}
			}else{
				foreach ($list as $k => $v) {
					$days = 0;
					$list[$k]["owner"] = D('RoleView')->where('role.role_id = %d', $v['owner_role_id'])->find();
					$list[$k]["creator"] = D('RoleView')->where('role.role_id = %d', $v['creator_role_id'])->find();
					$days =  M('leads')->where('leads_id = %d', $v['leads_id'])->getField('have_time');
					$list[$k]["days"] = $outdays-floor((time()-$days)/86400);
				}
			}
			//get subordinate's and youself position list
			$d_role_view = D('RoleView');
			$this->role_list = $d_role_view->where('role.role_id in (%s)', implode(',', $below_ids))->select();
			$this->assign('leadslist',$list);
			$this->field_array = getIndexFields('leads');
			$this->field_list = getMainFields('leads');
			$this->alert = parseAlert();
			$this->display();
		}
	}
	
	public function view(){		
		$leads_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		if(!check_permission($leads_id, 'leads')) $this->error(L('HAVE NOT PRIVILEGES'));
		if (0 == $leads_id) {
			alert('error', L('PARAMETER_ERROR'), U('leads/index'));
		} else {
			$leads = D('LeadsView')->where('leads.leads_id = %d', $leads_id)->find();
			$field_list = M('Fields')->where('model = "leads"')->order('order_id')->select();
			$leads['owner'] = D('RoleView')->where('role.role_id = %d', $leads['owner_role_id'])->find();
			$leads['creator'] = D('RoleView')->where('role.role_id = %d', $leads['creator_role_id'])->find();
			$log_ids = M('rLeadsLog')->where('leads_id = %d', $leads_id)->getField('log_id', true);
			$leads['log'] = M('log')->where('log_id in (%s)', implode(',', $log_ids))->select();
			$log_count = 0;
			foreach ($leads['log'] as $key=>$value) {
				$leads['log'][$key]['owner'] = D('RoleView')->where('role.role_id = %d', $value['role_id'])->find();
				$log_count ++;
			}
			$leads['log_count'] = $log_count;
			
			$file_ids = M('rFileLeads')->where('leads_id = %d', $leads_id)->getField('file_id', true);
			$leads['file'] = M('file')->where('file_id in (%s)', implode(',', $file_ids))->select();
			$file_count = 0;
			foreach ($leads['file'] as $key=>$value) {
				$leads['file'][$key]['owner'] = D('RoleView')->where('role.role_id = %d', $value['role_id'])->find();
				$leads['file'][$key]['file_path'] = U('file/filedownload','path='.urlencode($value['file_path']).'&name='.urlencode($value['name']));
				$file_count ++;
			}
			$leads['file_count'] = $file_count;
			
			$task_ids = M('rLeadsTask')->where('leads_id = %d', $leads_id)->getField('task_id', true);
			$leads['task'] = M('task')->where('task_id in (%s) and is_deleted = 0', implode(',', $task_ids))->select();
			$task_count = 0;
			foreach ($leads['task'] as $key=>$value) {
				$leads['task'][$key]['owner'] = D('RoleView')->where('role.role_id in (%s)', '0'.$value['owner_role_id'].'0')->select();
				$leads['task'][$key]['about_roles'] = D('RoleView')->where('role.role_id in (%s)', '0'.$value['about_roles'].'0')->select();
				$task_count ++;
			}
			$leads['task_count'] = $task_count;
			
			$event_ids = M('rEventLeads')->where('leads_id = %d', $leads_id)->getField('event_id', true);
			$leads['event'] = M('event')->where('event_id in (%s)', implode(',', $event_ids))->select();
			$event_count = 0;
			foreach ($leads['event'] as $key=>$value) {
				$leads['event'][$key]['owner'] = D('RoleView')->where('role.role_id = %d', $value['owner_role_id'])->find();
				$event_count++;
			}
			$leads['event_count'] = $event_count;
            $leads['record'] = M('leadsRecord')->where('leads_id = %d', $leads_id)->select();
			$record_count = 0;
			foreach ($leads['record'] as $key=>$value) {
				$leads['record'][$key]['owner'] = D('RoleView')->where('role.role_id = %d', $value['owner_role_id'])->find();
				$record_count ++;
			}
			$leads['record_count'] = $record_count;
			$this->statusList = M('BusinessStatus')->order('order_id')->select();
			$this->leads = $leads;
			$this->field_list = $field_list;
			$this->alert = parseAlert();
			$this->display();
		}
	}
	
	public function transform(){
		if ($this->isPost()) {
			$leads_id = isset($_POST['leads_id']) ? $_POST['leads_id'] : 0;
			if ($leads_id != 0) {
				$m_leads = M('Leads');
				$m_customer = M('Customer');
				$m_contacts = M('Contacts');
				$m_business = M('Business');
				$m_r = M('RContactsCustomer');
				$r_module = array(
					array('key'=>'log_id','r1'=>'RCustomerLog','r2'=>'RLeadsLog'), 
					array('key'=>'file_id','r1'=>'RCustomerFile','r2'=>'RFileLeads'),
					array('key'=>'event_id','r1'=>'RCustomerEvent','r2'=>'REventLeads'),
					array('key'=>'task_id','r1'=>'RCustomerTask','r2'=>'RLeadsTask')
				);
				$leads = $m_leads->where('leads_id = %d',$leads_id)->find();
				if(($leads['owner_role_id'] != session('role_id')) && !session('?admin')){
					alert('error', L('ONLY_OWNER_CAN_CONVERT_LEADS'), $_SERVER['HTTP_REFERER']);
				}
				if($leads['name'] && $leads['company']) {
					if($m_customer->where('name = "%s"', $leads['company'])->find()){
						alert('error', L('CONVERT_LEADS_FAILED_FOR_EXIST_CUSTOMER'), $_SERVER['HTTP_REFERER']);
					}
					!empty($leads['company']) ? $customer['name'] = $leads['company'] : '';	
					!empty($leads['email']) ? $customer['email'] = $leads['email'] : '';
					!empty($leads['phone']) ? $customer['telephone'] = $leads['phone'] : '';
					!empty($leads['source_id']) ? $customer['source_id'] = $leads['source_id'] : '';
					intval($_POST['owner_role_id']) > 0 ? $customer['owner_role_id'] = $_POST['owner_role_id'] : '';
					!empty($leads['website']) ? $customer['website'] = $leads['website'] : '';
					!empty($leads['industry_id']) ? $customer['industry_id'] = $leads['industry_id'] : '';
					!empty($leads['annual_revenue']) ? $customer['annual_revenue'] = $leads['annual_revenue'] : '';
					!empty($leads['no_of_employees']) ? $customer['no_of_employees'] = $leads['no_of_employees'] : '';
					(!empty($leads['state'])&&!empty($leads['city'])&&!empty($leads['street'])) ? $customer['address'] = $leads['state'] . $leads['city'] . $leads['street'] : '';
					!empty($leads['zip_code']) ? $customer['zip_code'] = $leads['zip_code'] : '';
					!empty($leads['rating']) ? $customer['rating'] = $leads['rating'] : '';
					$customer['creator_role_id'] = session('role_id');
					!empty($leads['ownership']) ? $customer['ownership'] = $leads['ownership'] : '';
					$customer['create_time'] = time();
					$customer['update_time'] = time();
					if(!$customer_id = $m_customer->add($customer)){
						alert('error', L('CONVERT_LEADS_FAILED_CONTACTS_ADMINISTRATOR'), $_SERVER['HTTP_REFERER']);
					};
					!empty($leads['name']) ? $contacts['name'] = $leads['name'] : '';
					!empty($leads['saltname']) ? $contacts['saltname'] = $leads['saltname'] : '';
					!empty($leads['position']) ? $contacts['post'] = $leads['position'] : '';
					!empty($leads['mobile']) ? $contacts['telephone'] = $leads['mobile'] : '';
					!empty($leads['email']) ? $contacts['email'] = $leads['email'] : '';
					!empty($leads['qq']) ? $contacts['qq'] = $leads['qq'] : '';
					(!empty($leads['state'])&&!empty($leads['city'])&&!empty($leads['street'])) ? $contacts['address'] = $leads['state'] . $leads['city'] . $leads['street'] : '';
					!empty($leads['zip_code']) ? $contacts['zip_code'] = $leads['zip_code'] : '';
					!empty($leads['name']) ? $contacts['name'] = $leads['name'] : '';
					(intval($_POST['owner_role_id'])>0) ? $contacts['owner_role_id'] = intval($_POST['owner_role_id']):'';	
					$contacts['creator_role_id'] = session('role_id');
					$contacts['customer_id'] = $customer_id;
					$contacts['create_time'] = time();
					$contacts['update_time'] = time();
					if($contacts_id = $m_contacts->add($contacts)){
						$data['customer_id'] = $customer_id;
						$data['contacts_id'] = $contacts_id;
						$data['transform_role_id'] = session('role_id');
						$data['is_transformed'] = 1;
						$data['update_time'] = time();
						$m_leads->where('leads_id = %d', $leads_id)->save($data);
						$m_r->add($data);
						$data['business_id'] = $customer_id;
						$m_leads->where('leads_id = %d',$leads_id)->save($data);
						foreach ($r_module as $key=>$value) {
							$key_id_array = M($value['r2'])->where('leads_id = %d', $leads_id)->getField($value['key'],true);
							$r1 = M($value['r1']);
							$data['customer_id'] = $customer_id;
							foreach($key_id_array as $k=>$v){
								$data[$value['key']] = $v;
								$r1->add($data);
							}
						}
						if($_POST['business_name'] == "" || $_POST['business_name'] == null){
							alert('success', L('CONVERT_LEADS_SUCCESSFULY'), U('leads/index'));
						}				
					}else{
						alert('error', L('CONVERT_LEADS_FAILED_CONTACTS_ADMINISTRATOR'), $_SERVER['HTTP_REFERER']);
					}					
					//if create business successfully and the execute this
					if ($_POST['business_name']) {
						if ($m_business->create()) {
							$m_business->creator_role_id = session('role_id');
							$m_business->origin = $leads['source'];
							$m_business->name = $_POST['business_name'];
							$m_business->contacts_id = $contacts_id;
							if($_POST['due_date']) $m_business->due_date = strtotime($_POST['due_date']);
							$m_business->customer_id = $customer_id;
							$m_business->create_time = time();
							$m_business->update_time = time();
							if ($business_id = $m_business->add()) {
								alert('success', L('CONVERT_LEADS_SUCCESSFULLY'), U('leads/index'));
							} else {
								alert('error', L('CREATE SUCCESS',array(L('BUSINESS'))), $_SERVER['HTTP_REFERER']);
							}
						} else {
							alert('error', L('CREATE FAILED',array(L('BUSINESS'))), $_SERVER['HTTP_REFERER']);
						}
					} else {
						$data['customer_id'] = $customer_id;
						$m_leads->where('leads_id = %d',$leads_id)->save($data);
						foreach ($r_module as $key=>$value) {
							$key_id_array = M($value['r2'])->where('leads_id = %d', $leads_id)->getField($value['key'],true);
							$r1 = M($value['r1']);
							foreach($key_id_array as $k=>$v){
								$data[$value['key']] = $v;
								$r1->add($data);
							}
						}
					
						$m_leads->where('leads_id = %d', $leads_id)->save($data);
						alert('success', L('CONVERT_LEADS_SUCCESSFULLY'), U('leads/index'));
					}
				}else{
					alert('error', L('CONVERT_LEADS_FAILED_FOR_INCOMPLETE_INFO'), $_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error', L('PARAMETER_ERROR'), $_SERVER['HTTP_REFERER']);
			}
		} else {
			alert('error', L('SELECT_LEADS_TO_CONVERT'), $_SERVER['HTTP_REFERER']);
		}
	}
	
	public function excelExport($leadsList=false){
		C('OUTPUT_ENCODE', false);
		import("ORG.PHPExcel.PHPExcel");
		$objPHPExcel = new PHPExcel();    
		$objProps = $objPHPExcel->getProperties();    
		$objProps->setCreator("5kcrm");    
		$objProps->setLastModifiedBy("5kcrm");    
		$objProps->setTitle("5kcrm Leads Data");    
		$objProps->setSubject("5kcrm Leads Data");    
		$objProps->setDescription("5kcrm Leads Data");    
		$objProps->setKeywords("5kcrm Leads Data");    
		$objProps->setCategory("Leads");
		$objPHPExcel->setActiveSheetIndex(0);     
		$objActSheet = $objPHPExcel->getActiveSheet(); 
		   
		$objActSheet->setTitle('Sheet1');
        $ascii = 65;
        $cv = '';
        $field_list = M('Fields')->where('model = \'leads\'')->order('order_id')->select();
        foreach($field_list as $field){
            $objActSheet->setCellValue($cv.chr($ascii).'1', $field['name']);
            $ascii++;
            if($ascii == 91){
                $ascii = 65;
                $cv .= chr(strlen($cv)+65);
            }
        }
		
		if(is_array($leadsList)){
			$list = $leadsList;
		}else{
			$where['owner_role_id'] = array('in',implode(',', getSubRoleId()));
			$where['is_deleted'] = 0;
			$list = M('Leads')->where($where)->select();
		}
		
		$i = 1;
		foreach ($list as $k => $v) {
            $data = M('LeadsData')->where("leads_id = $v[leads_id]")->find();
            if(!empty($data)){
                $v = $v+$data;
            }
			$i++;
            $ascii = 65;
            $cv = '';
            foreach($field_list as $field){
                if($field['form_type'] == 'datetime'){
					if($v[$field['field']] == 0 || strlen($v[$field['field']]) != 10){
						$objActSheet->setCellValue($cv.chr($ascii).$i, '');
					}else{
						$objActSheet->setCellValue($cv.chr($ascii).$i, date('Y-m-d',$v[$field['field']]));
					}
                }elseif($field['form_type'] == 'number' || $field['form_type'] == 'floatnumber' || $field['form_type'] == 'phone' || $field['form_type'] == 'mobile' || ($field['form_type'] == 'text' && is_numeric($v[$field['field']]))){
					//防止使用科学计数法，在数据前加空格
					$objActSheet->setCellValue($cv.chr($ascii).$i, ' '.$v[$field['field']]);
				}else{
                    $objActSheet->setCellValue($cv.chr($ascii).$i, $v[$field['field']]);
                }
                $ascii++;
                if($ascii == 91){
                    $ascii = 65;
                    $cv .= chr(strlen($cv)+65);
                }
            }
		}
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		header("Content-Type: application/vnd.ms-excel;");
        header("Content-Disposition:attachment;filename=5kcrm_leads_".date('Y-m-d',mktime()).".xls");
        header("Pragma:no-cache");
        header("Expires:0");
        $objWriter->save('php://output'); 
	}
 	public function excelImportDownload(){
		C('OUTPUT_ENCODE', false);
        import("ORG.PHPExcel.PHPExcel");
		$objPHPExcel = new PHPExcel();    
		$objProps = $objPHPExcel->getProperties();    
		$objProps->setCreator("5kcrm");
		$objProps->setLastModifiedBy("5kcrm");    
		$objProps->setTitle("5kcrm leads");    
		$objProps->setSubject("5kcrm leads Data");    
		$objProps->setDescription("5kcrm leads Data");    
		$objProps->setKeywords("5kcrm leads Data");    
		$objProps->setCategory("5kcrm");
		$objPHPExcel->setActiveSheetIndex(0);     
		$objActSheet = $objPHPExcel->getActiveSheet(); 
		   
		$objActSheet->setTitle('Sheet1');
        $ascii = 65;
        $cv = '';
        $field_list = M('Fields')->where('model = \'leads\' ')->order('order_id')->select();
        foreach($field_list as $field){
            $objActSheet->setCellValue($cv.chr($ascii).'2', $field['name']);
            $ascii++;
            if($ascii == 91){
                $ascii = 65;
                $cv .= chr(strlen($cv)+65);
            }
        }
		$objActSheet->mergeCells('A1:'.$cv.chr($ascii).'1');
		$objActSheet->getRowDimension('1')->setRowHeight(80);
		$objActSheet->getStyle('A1')->getFont()->getColor()->setARGB('FFFF0000');
		 $objActSheet->getStyle('A1')->getAlignment()->setWrapText(true);
        $content = L('ADRESS');
        $objActSheet->setCellValue('A1', $content);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		header("Content-Type: application/vnd.ms-excel;");
        header("Content-Disposition:attachment;filename=5kcrm_leads.xls");
        header("Pragma:no-cache");
        header("Expires:0");
        $objWriter->save('php://output'); 
    }
	public function excelImport(){
		$m_leads = D('Leads');
		$m_leads_data = D('LeadsData');
		if($_POST['submit']){
			if (isset($_FILES['excel']['size']) && $_FILES['excel']['size'] != null) {
				import('@.ORG.UploadFile');
				$upload = new UploadFile();
				$upload->maxSize = 20000000;
				$upload->allowExts  = array('xls');
				$dirname = UPLOAD_PATH . date('Ym', time()).'/'.date('d', time()).'/';
				if (!is_dir($dirname) && !mkdir($dirname, 0777, true)) {
					alert('error', L('ATTACHMENTS TO UPLOAD DIRECTORY CANNOT WRITE'), $_SERVER['HTTP_REFERER']);
				}
				$upload->savePath = $dirname;
				if(!$upload->upload()) {
					alert('error', $upload->getErrorMsg(), $_SERVER['HTTP_REFERER']);
				}else{
					$info =  $upload->getUploadFileInfo();
				}
			}
			
			if(is_array($info[0]) && !empty($info[0])){
				$savePath = $dirname . $info[0]['savename'];
			}else{
				alert('error', L('UPLOAD FAILED'), $_SERVER['HTTP_REFERER']);
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
			if ($allRow <= 2) {
				alert('error', L('UPLOAD A FILE WITHOUT A VALID DATA'), $_SERVER['HTTP_REFERER']);
			} else {
				$field_list = M('Fields')->where('model = \'leads\'')->order('order_id')->select();
				for($currentRow = 3;$currentRow <= $allRow;$currentRow++){
					$data = array();
					$data['creator_role_id'] = session('role_id');
					$data['owner_role_id'] = intval($_POST['owner_role_id']);
					$data['create_time'] = time();
					$data['update_time'] = time();
					$data['have_time'] = time();
					$ascii = 65;
                    $cv = '';
					foreach($field_list as $field){
                        $info = (String)$currentSheet->getCell($cv.chr($ascii).$currentRow)->getValue();
						
                        if ($field['is_main'] == 1){
                            $data[$field['field']] = ($field['form_type'] == 'datetime' && $info != null) ? intval(PHPExcel_Shared_Date::ExcelToPHP($info))-8*60*60 : $info;
                        }else{
                            $data_date[$field['field']] = ($field['form_type'] == 'datetime' && $info != null) ? intval(PHPExcel_Shared_Date::ExcelToPHP($info))-8*60*60 : $info;;
                        }
                        
                        $ascii++;
                        if($ascii == 91){
                            $ascii = 65;
                            $cv .= chr(strlen($cv)+65);
                        }
                    }
					if($m_leads->create($data) && $m_leads_data->create($data_date)) {
						$leads_id = $m_leads->add();
						$m_leads_data->leads_id=$leads_id;
						$m_leads_data->add();
					}else{
						if($this->_post('error_handing','intval',0) == 0){
							alert('error', L('ERROR INTRODUCED INTO THE LINE',array($currentRow,$m_leads->getError().$m_leads_data->getError())),$_SERVER['HTTP_REFERER']);
						}else{
							$error_message .= L('LINE ERROR',array($currentRow,$m_leads->getError().$m_leads_data->getError()));
							$m_leads->clearError();
							$m_leads_data->clearError();
						}
                    }
				}
				alert('success', $error_message.L('IMPORT SUCCESS'), $_SERVER['HTTP_REFERER']);
			}
		} else {
			$this->display();
		}
	}
	
	public function listDialog(){
		$m_leads = M('Leads');
		$this->leadsList = $m_leads->where('owner_role_id in (%s) and is_deleted = 0 and is_transformed = 0', implode(',', getSubRoleId()))->order('create_time desc')->limit(10)->select();
		$count = $m_leads->where('owner_role_id in (%s) and is_deleted = 0 and is_transformed = 0', implode(',', getSubRoleId()))->order('create_time desc')->count();
		$this->total = $count%10 > 0 ? ceil($count/10) : $count/10;
		$this->count_num = $count;
		$this->display();
	}
	
	public function changeContent(){
		if($this->isAjax()){
			$below_ids = getSubRoleId(false);
			$m_leads = M('Leads');
			$where['is_deleted'] = array('neq',1);
			$where['is_transformed'] = array('neq',1);
			$where['owner_role_id'] = array('in',implode(',', getSubRoleId(true))); 
			
			if ($_REQUEST["field"]) {
				if (trim($_REQUEST['field']) == "all") {
					$field = is_numeric(trim($_REQUEST['search'])) ? 'name|owner_role_id|company|position|saltname|phone|mobile|email|qq|fax|website|source|status|industry|state|zip_code|city|state|description|annual_revenue|no_of_employees|' : 'name|owner_role_id|company|position|saltname|phone|mobile|email|qq|fax|website|source|status|industry|state|zip_code|city|state|description';
				} else {
					$field = trim($_REQUEST['field']);
				}
				
				$search = empty($_REQUEST['search']) ? '' : trim($_REQUEST['search']);			
				$condition = empty($_REQUEST['condition']) ? 'is' : trim($_REQUEST['condition']);
				if	('create_time' == $field || 'update_time' == $field) {
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
					default : $where[$field] = array('eq',$search);
				}
			}
			
			$p = !$_REQUEST['p']||$_REQUEST['p']<=0 ? 1 : intval($_REQUEST['p']);
			$list = $m_leads->where($where)->page($p.',10')->order('create_time desc')->select();
			$count = $m_leads->where($where)->count();
			$data['list'] = $list;
			$data['p'] = $p;
			$data['count'] = $count;
			$data['total'] = $count%10 > 0 ? ceil($count/10) : $count/10;
			$this->ajaxReturn($data,"",1);
		}
	}
	

	public function receive1(){
		$leads_id = isset($_GET['id']) ? intval(trim($_GET['id'])) : 0;
		if ($leads_id > 0) {
			$m_leads = M('Leads');
			$leads = $m_leads->where('leads_id = %d', $leads_id)->find();
			if (isset($leads['owner_role_id']) || $leads['owner_role_id'] <= 0) {
				if ($m_leads->where('leads_id = %d', $leads_id)->setField('owner_role_id', session('role_id'))) {
					alert('success', L('RECEIVE_LEADS_SUCCESSFULLY'), $_SERVER['HTTP_REFERER']);
				} else {
					alert('error', L('RECEIVE_LEADS_FAILED'), $_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error', L('RECEIVED_BY_OTHERS'), $_SERVER['HTTP_REFERER']);
			}
		} else {
			alert('error', L('PARAMETER_ERROR'), $_SERVER['HTTP_REFERER']);
		}
	}
	public function remove(){
		if($this->isPost()){
			$m_leads = M('leads');
			$leads_ids = is_array($_POST['leads_id']) ? implode(',', $_POST['leads_id']) : '';
			if('' == $leads_ids){
				alert('error', L('NOT CHOOSE ANY'), $_SERVER['HTTP_REFERER']);
			}
			if($m_leads->where('leads_id in (%s)', $leads_ids)->setField('owner_role_id',0)){
				alert('success', L('BATCH_LEADS_INTO_THE_POOL_SUCCESSFULLY'), $_SERVER['HTTP_REFERER']);
			}else{
				alert('error', L('BATCH_LEADS_INTO_THE_POOL_FAILED'), $_SERVER['HTTP_REFERER']);
			}
			
		}
	}
	public function receive(){
		$leads_id = isset($_REQUEST['id']) ? intval(trim($_REQUEST['id'])) : 0;
		if($_REQUEST['owner_role_id']) {
			$owner_role_id = intval($_REQUEST['owner_role_id']);
		}else{
			$owner_role_id = session('role_id');
		}
		if ($leads_id > 0) {
			$m_leads = M('Leads');
			$m_config = M('Config');
			$leads = $m_leads->where('leads_id = %d', $leads_id)->find();
			$config = $m_config->where(array('name'=>'leads_outdays'))->find();
			if((time() - $leads['have_time']) < ($config['value'] * 86400) && $leads['owner_role_id'] != 0 ){
				alert('error', L('RECEIVED_BY_SOMEONE',array($leads['name'])), $_SERVER['HTTP_REFERER']);
			}
			$a = $m_leads->where('leads_id = %d', $leads_id)->setField('owner_role_id', $owner_role_id);
			$b = $m_leads->where('leads_id = %d',$leads_id)->setField('have_time',time());
			if ($a || $b) {
				$d = array('leads_id'=>$leads_id,'owner_role_id'=>$owner_role_id,'start_time'=>time());
				M('LeadsRecord')->data($d)->add();
				$title=L('NEW_LEADS_MESSAGE_NOTICE_TITLE');
				$content=L('NEW_LEADS_MESSAGE_NOTICE_CONTENT',array(session('name'),U('Leads/view','id='.$leads_id), $leads['name']));
				
				if(intval($_POST['message_alert']) == 1) {
					sendMessage($owner_role_id,$content,1);
				}
				if(intval($_POST['email_alert']) == 1){
					$email_result = sysSendEmail($owner_role_id,$title,$content);
					if(!$email_result) alert('error', L('MAIL_NOTIFICATION_FAILS_FOR_NOT_SET_EMAIL'),$_SERVER['HTTP_REFERER']);
				}
				if(intval($_POST['sms_alert']) == 1){
					$sms_result = sysSendSms($owner_role_id,$content);
					if(100 == $sms_result){
						alert('error', L('SMS_NOTIFICATION_FAILS_FOR_NOT_VALIDATE_NUMBER'),$_SERVER['HTTP_REFERER']);
					}elseif($sms_result < 0){
						alert('error',L('SMS_NOTIFICATION_FAILS_CODE', array($sms_result)), $_SERVER['HTTP_REFERER']);
					}
				}
				
				if($_REQUEST['owner_role_id']){
					alert('success', L('ASSIGN_LEADS_SUCCESSFULLY'), $_SERVER['HTTP_REFERER']);
				}else{
					alert('success', L('RECEIVE_LEADS_SUCCESSFULLY'), $_SERVER['HTTP_REFERER']);
				}
			} else {
				if($_REQUEST['owner_role_id']){
					alert('success', L('ASSIGN_LEADS_FAILED'), $_SERVER['HTTP_REFERER']);
				}else{
					alert('success', L('RECEIVE_LEADS_FAILED'), $_SERVER['HTTP_REFERER']);
				}
			}
		} else {
			alert('error', L('PARAMETER_ERROR'), $_SERVER['HTTP_REFERER']);
		}
	}
	
	//batchReceive
	public function batchReceive(){
		$leads_ids = $_REQUEST['leads_id'];
		$owner_role_id = session('role_id');
		if(empty($leads_ids)){
			alert('error', L('NOT CHOOSE ANY'), $_SERVER['HTTP_REFERER']);
		}
		$m_leads = M('Leads');
		$m_config = M('Config');
		foreach($leads_ids as $v){
			$leads = $m_leads->where('leads_id = %d',$v)->find();
			$config = $m_config->where(array('name'=>'leads_outdays'))->find();
			if( (time() - $leads['have_time']) > ($config['value'] * 86400) || $leads['owner_role_id'] == 0 ){
				$data['owner_role_id'] = $owner_role_id;
				$data['have_time'] = time();
				if($m_leads->where('leads_id = %d',$v)->save($data)){
					M('LeadsRecord')->add(array('leads_id'=>$v,'owner_role_id'=>$owner_role_id,'start_time'=>time()));
				}else{
					alert('success', L('RECEIVE_LEADS_FAILED'), $_SERVER['HTTP_REFERER']);
				}
			}else{
				alert('error', L('RECEIVED_BY_SOMEONE', array($leads['name'])), $_SERVER['HTTP_REFERER']);
			}
		}
		alert('success', L('RECEIVE_LEADS_SUCCESSFULLY'), $_SERVER['HTTP_REFERER']);
	}
	
	//批量分配
	public function batchAssign(){
		$leads_ids = $_POST['leads_id'];
		$owner_role_id = $_POST['owner_id'];
		$message = empty($_POST['message']) ? 0 :$_POST['message'];
		$sms = empty($_POST['sms']) ? 0 :$_POST['sms'];
		$email = empty($_POST['email']) ? 0 :$_POST['email'];
		if(empty($leads_ids)){
			alert('error', L('NOT CHOOSE ANY'), $_SERVER['HTTP_REFERER']);
		}
		$m_leads = M('Leads');
		$m_config = M('Config');
		$title = L('NEW_LEADS_MESSAGE_NOTICE_TITLE');
		$content = '';
		$success_leads_name='';
		$error_leads_name='';
		foreach($leads_ids as $v){
			$leads = $m_leads->where('leads_id = %d',$v)->find();
			$config = $m_config->where(array('name'=>'leads_outdays'))->find();
			if( (time() - $leads['have_time']) > ($config['value'] * 86400) || $leads['owner_role_id'] == 0 ){
				$a = $m_leads->where('leads_id = %d', $v)->setField('owner_role_id', $owner_role_id);
				$b = $m_leads->where('leads_id = %d',$v)->setField('have_time',time());
				if ($a || $b) {
					$d = array('leads_id'=>$v,'owner_role_id'=>$owner_role_id,'start_time'=>time());
					M('LeadsRecord')->data($d)->add();
					$url=U('leads/view','id='.$v);
					$success_leads_name .='<a href="'.$url.'">' .$leads['name'].'</a>、';
				}else{
					$error_leads_name .= $leads['name'].'、';
				}
			}else{
				alert('error', L('RECEIVED_BY_SOMEONE',array($leads['name'])), $_SERVER['HTTP_REFERER']);
			}
		}
		if($success_leads_name){
			$content = L('ASSIGE_LEADS_MESSAGE_NOTICE_CONTENT' ,array(session('name'), $success_leads_name));
			if($message == 1) {
				sendMessage($owner_role_id,$content,1);
			}
			if($email == 1){
				$email_result = sysSendEmail($owner_role_id,$title,$content);
				if(!$email_result) alert('error', L('MAIL_NOTIFICATION_FAILS_FOR_NOT_SET_EMAIL'),$_SERVER['HTTP_REFERER']);
			}
			if($sms == 1){
				$sms_result = sysSendSms($owner_role_id,$content);
				if(100 == $sms_result){
					alert('error', L('SMS_NOTIFICATION_FAILS_FOR_NOT_VALIDATE_NUMBER'),$_SERVER['HTTP_REFERER']);
				}elseif($sms_result < 0){
					alert('error', L('SMS_NOTIFICATION_FAILS_CODE', array($sms_result)) ,$_SERVER['HTTP_REFERER']);
				}
			}
		}
		if($error_leads_name){
			alert('error', L('BATCH_ASSIGN_LEADS_TO_SOMEONE_FAILED', array($error_leads_name)), $_SERVER['HTTP_REFERER']);
		}else{
			alert('success', L('BATCH_ASSIGN_LEADS_SUCCESSFULLY'), $_SERVER['HTTP_REFERER']);
		}
		
	}
	
	public function assignDialog(){
		$this->display();
	}
	
	public function fenpei(){
		$leads_id = intval($_GET['id']);
		 if ($leads_id > 0) {
			$this->leads_id = $leads_id;
			$this->display();
		} else {
			alert('error', L('PARAMETER_ERROR'), $_SERVER['HTTP_REFERER']);
		}
	}

	public function analytics(){
		$m_leads = M('leads');
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
		if($_GET['start_time']) $start_time = strtotime(date('Y-m-d',strtotime($_GET['start_time'])));
		$end_time = $_GET['end_time'] ?  strtotime(date('Y-m-d 23:59:59',strtotime($_GET['end_time']))) : strtotime(date('Y-m-d 23:59:59',time()));
		if($role_id == "all") {
			$roleList = getRoleByDepartmentId($department_id);
			$role_id_array = array();
			foreach($roleList as $v2){
				$role_id_array[] = $v2['role_id'];
			}
			$where_source['creator_role_id'] = array('in', implode(',', $role_id_array));
			$where_status['owner_role_id'] = array('in', implode(',', $role_id_array));
		}else{
			$where_source['creator_role_id'] = $role_id;
			$where_status['owner_role_id'] = $role_id;
		}
		if($start_time){
			$where_source['create_time'] = array(array('elt',$end_time),array('egt',$start_time), 'and');
			$where_status['create_time'] = array(array('elt',$end_time),array('egt',$start_time), 'and');
		}else{
			$where_source['create_time'] = array('elt',$end_time);
			$where_status['create_time'] = array('elt',$end_time);
		}
		
		//线索来源统计
		$setting = M('Fields')->where("model = 'leads' and field = 'source'")->getField('setting');
		$setting_str = '$revenueList='.$setting.';';
	
		$source_count_array = array();
		$sourceList = M('leads')->field('count(1) as num , source')->group('source')->where($where_source)->select();
		foreach($sourceList as $v){
			$source = $v['source']?$v['source']:L('OTHER');
			$source_count[$source] = $v['num'];
		}
		foreach($revenueList['data'] as $v){
			if($source_count[$v]){
				$source_count_array[] = '["'.$v.'",'.$source_count[$v].']';
			}else{
				$source_count_array[] = '["'.$v.'",0]';
			}
		}
		$this->source_count = implode(',', $source_count_array);
		
		if($start_time){
			$create_time= array(array('elt',$end_time),array('egt',$start_time), 'and');
		}else{
			$create_time = array('elt',$end_time);
		}
		$add_count_total = 0;
		$own_count_total = 0;
		$success_count_total = 0;
		$deal_count_total = 0;
		foreach($role_id_array as $v){
			$user = getUserByRoleId($v);
			$add_count = $m_leads->where(array('is_deleted'=>0, 'creator_role_id'=>$v, 'create_time'=>$create_time))->count();
			$own_count = $m_leads->where(array('is_deleted'=>0, 'owner_role_id'=>$v, 'create_time'=>$create_time))->count();
			$success_count = $m_leads->where(array('is_deleted'=>0, 'is_transformed'=>1,'owner_role_id'=>$v, 'create_time'=>$create_time))->count();
			$deal_count = $m_leads->where('is_deleted = 0 and owner_role_id = %d and is_transformed != 1 and update_time>create_time', $v)->count();
			$reportList[] = array("user"=>$user,"add_count"=>$add_count,"own_count"=>$own_count,"success_count"=>$success_count,"deal_count"=>$deal_count);
			$add_count_total += $add_count;
			$own_count_total += $own_count;
			$success_count_total += $success_count;
			$deal_count_total += $deal_count;
		}
		$this->total_report = array("add_count"=>$add_count_total, "own_count"=>$own_count_total, "success_count"=>$success_count_total, "deal_count"=>$deal_count_total);
		$this->reportList = $reportList;
		
		$idArray = getSubRoleId(true, 1);
		$roleList = array();
		foreach($idArray as $roleId){				
			$roleList[$roleId] = getUserByRoleId($roleId);
		}
		$this->roleList = $roleList;
		$departmentList = M('roleDepartment')->select();
		$this->assign('departmentList', $departmentList);
		$this->alert = parseAlert();
		$this->display();
	}
	
	public function getAddDataByRoleId($id){
		if($id <= 0) $id=session('role_id');
		$moon = date('n');
		$year = date('Y');
		$this_moon_where['creator_role_id'] = $id;
		$this_moon_where['create_time'] = array(array('lt',time()),array('gt',strtotime($year.'-'.$moon.'-'.'1')), 'and');
		if($moon-1 > 0){
			$onemoon = $moon-1;
			$onemoonyear = $year;
		}else{
			$onemoon = $moon+11;
			$onemoonyear = intval($year)-1;
		}
		$onemoon_where['creator_role_id'] = $id;
		$onemoon_where['create_time'] = array(array('lt',strtotime($year.'-'.$moon.'-'.'1')),array('gt',strtotime($onemoonyear.'-'.$onemoon.'-'.'1')), 'and');
		
		if($moon-2 > 0){
			$twomoon = $moon-2;
			$twomoonyear = $year;
		}else{
			$twomoon = $moon+10;
			$twomoonyear = intval($year)-1;
			
		}
		$twomoon_where['creator_role_id'] = $id;
		$twomoon_where['create_time'] = array(array('lt',strtotime($onemoonyear.'-'.$onemoon.'-'.'1')),array('gt',strtotime($twomoonyear.'-'.$twomoon.'-'.'1')), 'and');
		
		if($moon-3 > 0){
			$threemoon = $moon-3;
			$threemoonyear = $year;
		}else{
			$threemoon = $moon+9;
			$threemoonyear = intval($year)-1;
			
		}
		$threemoon_where['creator_role_id'] = $id;
		$threemoon_where['create_time'] = array(array('lt',strtotime($twomoonyear.'-'.$twomoon.'-'.'1')),array('gt',strtotime($threemoonyear.'-'.$threemoon.'-'.'1')), 'and');
		
		if($moon-4 > 0){
			$fourmoon = $moon-4;
			$fourmoonyear = $year;
		}else{
			$fourmoon = $moon+8;
			$fourmoonyear = intval($year)-1;
		}
		$fourmoon_where['creator_role_id'] = $id;
		$fourmoon_where['create_time'] = array(array('lt',strtotime($threemoonyear.'-'.$threemoon.'-'.'1')),array('gt',strtotime($fourmoonyear.'-'.$fourmoon.'-'.'1')), 'and');
		
		if($moon-5 > 0){
			$fivemoon = $moon-5;
			$fivemoonyear = $year;
		}else{
			$fivemoon = $moon+7;
			$fivemoonyear = intval($year)-1;
		}
		$fivemoon_where['creator_role_id'] = $id;
		$fivemoon_where['create_time'] = array(array('lt',strtotime($fourmoonyear.'-'.$fourmoon.'-'.'1')),array('gt',strtotime($fivemoonyear.'-'.$fivemoon.'-'.'1')), 'and');
		
		$role_chart['x_data'] = "'".$fivemoon."月','".$fourmoon."月','".$threemoon."月','".$twomoon."月','".$onemoon."月','本月'";
		
		$m_leads = M('Leads');
		$data_fivemoon = $m_leads->where($fivemoon_where)->count();
		$data_fourmoon = $m_leads->where($fourmoon_where)->count();
		$data_threemoon = $m_leads->where($threemoon_where)->count();
		$data_twomoon = $m_leads->where($twomoon_where)->count();
		$data_onemoon = $m_leads->where($onemoon_where)->count();
		$data_thismoon = $m_leads->where($this_moon_where)->count();
		
		$fivemoon_where['is_transformed'] = 1;
		$fourmoon_where['is_transformed'] = 1;
		$threemoon_where['is_transformed'] = 1;
		$twomoon_where['is_transformed'] = 1;
		$onemoon_where['is_transformed'] = 1;
		$this_moon_where['is_transformed'] = 1;
		$data_fivemoon_t = $m_leads->where($fivemoon_where)->count();
		$data_fourmoon_t = $m_leads->where($fourmoon_where)->count();
		$data_threemoon_t = $m_leads->where($threemoon_where)->count();
		$data_twomoon_t = $m_leads->where($twomoon_where)->count();
		$data_onemoon_t = $m_leads->where($onemoon_where)->count();
		$data_thismoon_t = $m_leads->where($this_moon_where)->count();
		
		$role_chart['y_data']['all'] = $data_fivemoon.','.$data_fourmoon.','.$data_threemoon.','.$data_twomoon.','.$data_onemoon.','.$data_thismoon;
		$role_chart['y_data']['value'] = $data_fivemoon_t.','.$data_fourmoon_t.','.$data_threemoon_t.','.$data_twomoon_t.','.$data_onemoon_t.','.$data_thismoon_t;
		
		return $role_chart;
	}
	
	public function getOwnDataByRoleId($id){
		if($id <= 0) $id=session('role_id');
		$moon = date('n');
		$year = date('Y');
		$this_moon_where['owner_role_id'] = $id;
		$this_moon_where['create_time'] = array(array('lt',time()),array('gt',strtotime($year.'-'.$moon.'-'.'1')), 'and');
		if($moon-1 > 0){
			$onemoon = $moon-1;
			$onemoonyear = $year;
			
		}else{
			$onemoon = $moon+11;
			$onemoonyear = intval($year)-1;
		}
		$onemoon_where['owner_role_id'] = $id;
		$onemoon_where['create_time'] = array(array('lt',strtotime($year.'-'.$moon.'-'.'1')),array('gt',strtotime($onemoonyear.'-'.$onemoon.'-'.'1')), 'and');
		
		if($moon-2 > 0){
			$twomoon = $moon-2;
			$twomoonyear = $year;
		}else{
			$twomoon = $moon+10;
			$twomoonyear = intval($year)-1;
			
		}
		$twomoon_where['owner_role_id'] = $id;
		$twomoon_where['create_time'] = array(array('lt',strtotime($onemoonyear.'-'.$onemoon.'-'.'1')),array('gt',strtotime($twomoonyear.'-'.$twomoon.'-'.'1')), 'and');
		
		if($moon-3 > 0){
			$threemoon = $moon-3;
			$threemoonyear = $year;
		}else{
			$threemoon = $moon+9;
			$threemoonyear = intval($year)-1;
			
		}
		$threemoon_where['owner_role_id'] = $id;
		$threemoon_where['create_time'] = array(array('lt',strtotime($twomoonyear.'-'.$twomoon.'-'.'1')),array('gt',strtotime($threemoonyear.'-'.$threemoon.'-'.'1')), 'and');
		
		if($moon-4 > 0){
			$fourmoon = $moon-4;
			$fourmoonyear = $year;
		}else{
			$fourmoon = $moon+8;
			$fourmoonyear = intval($year)-1;
		}
		$fourmoon_where['owner_role_id'] = $id;
		$fourmoon_where['create_time'] = array(array('lt',strtotime($threemoonyear.'-'.$threemoon.'-'.'1')),array('gt',strtotime($fourmoonyear.'-'.$fourmoon.'-'.'1')), 'and');
		
		if($moon-5 > 0){
			$fivemoon = $moon-5;
			$fivemoonyear = $year;
		}else{
			$fivemoon = $moon+7;
			$fivemoonyear = intval($year)-1;
		}
		$fivemoon_where['owner_role_id'] = $id;
		$fivemoon_where['create_time'] = array(array('lt',strtotime($fourmoonyear.'-'.$fourmoon.'-'.'1')),array('gt',strtotime($fivemoonyear.'-'.$fivemoon.'-'.'1')), 'and');
		
		$role_chart['x_data'] = "'".$fivemoon."月','".$fourmoon."月','".$threemoon."月','".$twomoon."月','".$onemoon."月','本月'";
		$role_chart['x_data'] = L('X_DATA',array($fivemoon, $fourmoon, $threemoon, $twomoon , $onemoon));
		
		$m_leads = M('Leads');
		$data_fivemoon = $m_leads->where($fivemoon_where)->count();
		$data_fourmoon = $m_leads->where($fourmoon_where)->count();
		$data_threemoon = $m_leads->where($threemoon_where)->count();
		$data_twomoon = $m_leads->where($twomoon_where)->count();
		$data_onemoon = $m_leads->where($onemoon_where)->count();
		$data_thismoon = $m_leads->where($this_moon_where)->count();
		
		$fivemoon_where['is_transformed'] = 1;
		$fourmoon_where['is_transformed'] = 1;
		$threemoon_where['is_transformed'] = 1;
		$twomoon_where['is_transformed'] = 1;
		$onemoon_where['is_transformed'] = 1;
		$this_moon_where['is_transformed'] = 1;
		$data_fivemoon_t = $m_leads->where($fivemoon_where)->count();
		$data_fourmoon_t = $m_leads->where($fourmoon_where)->count();
		$data_threemoon_t = $m_leads->where($threemoon_where)->count();
		$data_twomoon_t = $m_leads->where($twomoon_where)->count();
		$data_onemoon_t = $m_leads->where($onemoon_where)->count();
		$data_thismoon_t = $m_leads->where($this_moon_where)->count();
		
		$role_chart['y_data']['all'] = $data_fivemoon.','.$data_fourmoon.','.$data_threemoon.','.$data_twomoon.','.$data_onemoon.','.$data_thismoon;
		$role_chart['y_data']['value'] = $data_fivemoon_t.','.$data_fourmoon_t.','.$data_threemoon_t.','.$data_twomoon_t.','.$data_onemoon_t.','.$data_thismoon_t;
		
		return $role_chart;
	}
	
	
	public function getAddChartByRoleId(){
		if($this->isAjax()){
			$id = $_REQUEST['role_id'];
			$role_chart = $this->getAddDataByRoleId($id);
			$this->ajaxReturn($role_chart, '', 1);
		}
	}
	
	public function revert(){
		$leads_id = isset($_GET['id']) ? intval(trim($_GET['id'])) : 0;
		if ($leads_id > 0) {
			$m_leads = M('Leads');
			$leads = $m_leads->where('leads_id = %d', $leads_id)->find();
			if ($leads['delete_role_id'] == session('role_id') || session('?admin')) {
				if (isset($leads['is_deleted']) || $leads['is_deleted'] == 1) {
					if ($m_leads->where('leads_id = %d', $leads_id)->setField('is_deleted', 0)) {
						alert('success', L('RESTORE SUCCESSFUL'), $_SERVER['HTTP_REFERER']);
					} else {
						alert('error', L('RESTORE FAILURE'), $_SERVER['HTTP_REFERER']);
					}
				} else {
					alert('error', L('ALREADY REDUCTION!'), $_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error', L('HAVE_NO_PERMISSION_TO_RECOVERY'), $_SERVER['HTTP_REFERER']);
			}
		} else {
			alert('error', L('PARAMETER_ERROR'), $_SERVER['HTTP_REFERER']);
		} 
	}
}