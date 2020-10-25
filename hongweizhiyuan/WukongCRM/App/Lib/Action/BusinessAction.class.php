<?php
/**
*商机模块
*
**/
class BusinessAction extends Action{
	/**
	*用于判断权限
	*@permission 无限制
	*@allow 登录用户可访问
	*@other 其他根据系统设置
	**/
	public function _initialize(){
		$action = array(
			'permission'=>array(''),
			'allow'=>array('close','analytics','getaddchartbyroleid','changecontent','getownchartbyroleid','advance','validate','check','revert','getsalesfunnel')
		);
		B('Authenticate', $action);
	}

	/**
	*Ajax检测商机名称
	*
	**/
	public function check() {	
		import("@.ORG.SplitWord");
		$sp = new SplitWord();
		$m_business = M('Business');
		$useless_words = array(L('COMPANY'),L('LIMITED'),L('DI'),L('LIMITED_COMPANY'));
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
			$name_list = $m_business->getField('name', true);
			$seach_array = array();
			foreach($name_list as $k=>$v){
				$search = 0;
				foreach($result_array as $k2=>$v2){
					if(strpos($v, $v2) > -1){
						$v = str_replace("$v2","<span style='color:red;'>$v2</span>", $v, $count);
						$search += $count;
					}
				}
				if($search > 2) $seach_array[$k] = array('value'=>$v,'search'=>$search);
			}
			$seach_sort_result = array_sort($seach_array,'search','desc');
			if(empty($seach_sort_result)){
				$this->ajaxReturn(0,L('ABLE_ADD'),0);
			}else{
				$this->ajaxReturn($seach_sort_result,L('CUSTOMER_IS_CREATED'),1);
			}
		}		
	}
	
	/**
	*Ajax检测商机唯一字段
	*
	**/
	public function validate() {
		if($this->isAjax()){
            if(!$this->_request('clientid','trim') || !$this->_request($this->_request('clientid','trim'),'trim')){
				$this->ajaxReturn("","",3);
			}
            $field = M('Fields')->where('model = "Business" and field = "'.$this->_request('clientid','trim').'"')->find();
            $m_business = $field['is_main'] ? D('Business') : D('BusinessData');
            $where[$this->_request('clientid','trim')] = array('eq',$this->_request($this->_request('clientid','trim'),'trim'));
            if($this->_request('id','intval',0)){
                $where[$m_business->getpk()] = array('neq',$this->_request('id','intval',0));
            }
			if($this->_request('clientid','trim')) {
				if ($m_business->where($where)->find()) {
					$this->ajaxReturn("","",1);
				} else {
					$this->ajaxReturn("","",0);
				}
			}else{
				$this->ajaxReturn("","",0);
			}
		}
	}
	
	/**
	*添加商机
	*
	**/
	public function add(){
		if($this->isPost()){
			$m_business = D('Business');
			$m_business_data = D('BusinessData');
			$field_list = M('Fields')->where('model = "business" and in_add = 1')->order('order_id')->select();
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
							$a =array_filter($_POST[$v['field']]);
							$_POST[$v['field']] = !empty($a) ? implode(chr(10),$a) : '';
						}
					break;
				}
			}
			if(empty($_POST['customer_id'])){
				$this -> error(L('THE_CUSTOMER_CANNOT_BE_EMPTY'));
			}
			if($m_business->create()){
				if($m_business_data->create()!==false){
					$m_business->create_time = $m_business->update_time = time();
					$m_business->creator_role_id = $m_business->update_role_id = session('role_id');
					if($business_id = $m_business->add()){
						$m_business_data->business_id = $business_id;
						if($m_business_data->add()){
							$m_rbusinessProduct = M('RBusinessProduct');
							if(is_array($_POST['product'])){
								foreach($_POST['product'] as $val){
									$data['product_id'] = $val['product_id'];
									$data['unit_price'] = $val['unit_price'];
									$data['amount'] = $val['amount'];
									$data['discount_rate'] = $val['discount_rate'];
									$data['tax_rate'] = $val['tax_rate'];
									$data['subtotal'] = $val['subtotal'];
									$data['description'] = $val['description'];
									$data['subtotal_val'] = $_POST['subtotal_val'];
									$data['discount_price'] = $_POST['discount_price'];
									$data['sales_price'] = $_POST['sales_price'];
									$data['business_id'] = $business_id;
									$m_rbusinessProduct->add($data);	
								}
							}
							if(intval($_POST['status_id']) == 100){
								M('Customer')->where('customer_id = %d', intval($_POST['customer_id']))->setField('is_locked',1);
							}
							actionLog($business_id);
							if($_POST['submit'] == L('SAVE')) {
							    if($_POST['refer_url'])
								{
								   alert('success', L('ADD_BUSINESS_SUCCESS'), $_POST['refer_url']);
								}
								else{
								   alert('success', L('ADD_BUSINESS_SUCCESS'), U('business/index'));
								}
							} else {
								alert('success', L('ADD_BUSINESS_SUCCESS'), U('business/add'));
							}
						}else{
							$m_business->where(array('business_id'=>$business_id))->delete();
							alert('error', L('ADD_BUSINESS_FAILURE'), U('business/add'));
						}
					} else {
						alert('error', L('ADD_BUSINESS_FAILURE'), U('business/add'));
					}
				}else{			
					$this->error($m_business_data->getError());
				}
			}else{
				$this->error($m_business->getError());
			}
		}else{
		    $this->refer_url=$_SERVER['HTTP_REFERER'];
			$alert = parseAlert();
			$this->alert = $alert;
			$this->field_list = field_list_html('add','business');
			$this->display();
		}
	}
	
	/**
	*修改商机
	*
	**/
	public function edit(){		
		$v_business = D('BusinessView');
		$business = $v_business ->where('business.business_id = %d',$this->_request('id'))->find();		
		if (!$business) {
            alert('error', L('THERE_IS_NO_BUSINESS_OPPORTUNITIES'),$_SERVER['HTTP_REFERER']);
        }
        $field_list = M('Fields')->where('model = "business"')->order('order_id')->select();
		$business_id=$_POST['business_id'] ? intval($_POST['business_id']) : intval($_GET['id']);
		if(!check_permission($business_id, 'business')) $this->error(L('HAVE NOT PRIVILEGES'));
		if($this->isPost()){
			$m_business = D('business');
			$m_business_data = D('BusinessData');
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
			if(empty($_POST['customer_id'])){
				$this -> error(L('THE_CUSTOMER_CANNOT_BE_EMPTY'));
			}
			
			if($m_business->create()){
				if($m_business_data->create()!==false){
					$m_business->update_time = time();
					$r = M('rBusinessProduct');
					foreach($_POST['product'] as $val){
						$data = array();
						$data['product_id'] = $val['product_id'];
						$data['unit_price'] = $val['unit_price'];
						$data['amount'] = $val['amount'];
						$data['discount_rate'] = $val['discount_rate'];
						$data['tax_rate'] = $val['tax_rate'];
						$data['subtotal'] = $val['subtotal'];
						$data['subtotal_val'] = $_POST['subtotal_val'];
						$data['discount_price'] = $_POST['discount_price'];
						$data['sales_price'] = $_POST['sales_price'];
						$data['description'] = $val['description'];
						$data['business_id'] = $business_id;
						//在编辑时，如果又添加商品，根据是否存在sales_product_id来进行编辑或添加
						if(empty($val['r_id'])){
							//添加
							$result_product= $r->add($data);
							if(empty($result_product)){
								$res = false;
								break;
							}
						}else{
							//编辑
							$result_product = $r->where('id = %d', $val['r_id'])->save($data);
							if($result_product === false){
								$res = false;
								break;
							}
						}
						//在编辑时，如果从原来的商品中去除一条信息，则删除该产品
						if($val['r_id'] && empty($val['product_id'])){
							$result_product = $r->where('id = %d', $val['r_id'])->delete();
							if($result_product == 0 || $result_product === false){
								$res = false;
							}
						}
					}
					$a = $m_business->where('business_id=' . $business['business_id'])->save();
					$b = $m_business_data->where('business_id=' . $business['business_id'])->save();
					if($a && $b!==false) {
						if(intval($_POST['status_id']) == 100){
							M('Customer')->where('customer_id = %d', intval($_POST['customer_id']))->setField('is_locked',1);
						}
						actionLog($business['business_id']);
						alert('success', L('MODIFY_BUSINESS_INFORMATION_SUCCESSFULLY'), U('business/index'));
					} else {
						alert('error', L('MODIFY_THE_BUSINESS_INFORMATION_FAILURE'),$_SERVER['HTTP_REFERER']);
					}
				}else{
					$this->error($m_business_data->getError());
				}
			}else{
				$this->error($m_business->getError());
			}
		}else{
			$business['owner'] = getUserByRoleId($business['owner_role_id']);
			$business['product'] = M('rBusinessProduct')->where('business_id = %d', $business_id)->select();
			$product_count =  M('rBusinessProduct')->where('business_id = %d', $business_id)->count();
			$business['product_count'] = empty($product_count)? 0 : $product_count;
			$product_category = M('product_category');
			foreach ($business['product'] as $k => $v) {
				$info = M('product')->where('product_id = %d', $v['product_id'])->find();
				$business['product'][$k]['info'] = $info;
				$total_amount += $v['amount'];
			}
			$this->business = $business;
			$this->total_amount = $total_amount; 
			$alert = parseAlert();
			$this->alert = $alert;
			$this->field_list = field_list_html('edit','business',$business);
			$this->display();
		}
	}
	
	/**
	*查看商机详情
	*
	**/
	public function view(){
		
		if (intval($this->_request('id')) <= 0) {
			alert('error', L('PARAMETER_ERROR'), U('business/index'));
		}
		$business_id = $this->_request('id');
		if(!check_permission($business_id, 'business')) $this->error(L('HAVE NOT PRIVILEGES'));
		$v_business = D('BusinessView');
		$business = $v_business ->where('business.business_id = %d',$this->_request('id'))->find();
		if (!$business) {
            alert('error', L('THERE_IS_NO_BUSINESS_OPPORTUNITIES'),$_SERVER['HTTP_REFERER']);
        }
        $field_list = M('Fields')->where('model = "business"')->order('order_id')->select();

		$business['customer'] = M('Customer')->where('customer_id = %d', $business['customer_id'])->find();
		$business['contacts'] = M('contacts')->where('contacts_id = %d and is_deleted=0', $business['contacts_id'])->find();
		$business['owner'] = getUserByRoleId($business['owner_role_id']);
		$business['status_id'] = M('BusinessStatus')->where('status_id = %d', $business['status_id'])->getField('name');
		$bsList = M('RBusinessStatus')->where('business_id = %d', $business_id)->select();
		foreach($bsList as $key => $value) {
			$bsList[$key]['status_name'] = M('BusinessStatus')->where('status_id = %d', $value['status_id'])->getField('name');
			$bsList[$key]['owner'] = D('RoleView')->where('role.role_id = %d', $value['owner_role_id'])->find();
			$bsList[$key]['update'] = D('RoleView')->where('role.role_id = %d', $value['update_role_id'])->find();
		}
		$business['bsList'] = $bsList;
		$log_ids = M('rBusinessLog')->where('business_id = %d', $business_id)->getField('log_id', true);
		$business['log'] = M('log')->where('log_id in (%s)', implode(',', $log_ids))->select();
		$log_count = M('log')->where('log_id in (%s)', implode(',', $log_ids))->count();
		$business['log_count'] = empty($log_count)? 0 : $log_count;
		foreach ($business['log'] as $key=>$value) {
			$business['log'][$key]['owner'] = D('RoleView')->where('role.role_id = %d', $value['role_id'])->find();
		}
		
		$file_ids = M('rBusinessFile')->where('business_id = %d', $business_id)->getField('file_id', true);
		$business['file'] = M('file')->where('file_id in (%s)', implode(',', $file_ids))->select();
		$file_count= M('file')->where('file_id in (%s)', implode(',', $file_ids))->count();
		$business['file_count'] = empty($file_count)? 0 : $file_count;
		foreach ($business['file'] as $key=>$value) {
			$business['file'][$key]['owner'] = D('RoleView')->where('role.role_id = %d', $value['role_id'])->find();
			$business['file'][$key]['file_path'] = U('file/filedownload','path='.urlencode($value['file_path']).'&name='.urlencode($value['name']));
		}
		
		$task_ids = M('rBusinessTask')->where('business_id = %d', $business_id)->getField('task_id', true);
		$business['task'] = M('task')->where('task_id in (%s) and is_deleted=0', implode(',', $task_ids))->select();
		$task_count = M('task')->where('task_id in (%s) and is_deleted=0', implode(',', $task_ids))->count();
		$business['task_count'] = empty($task_count)? 0 : $task_count;
		foreach ($business['task'] as $key=>$value) {
			$business['task'][$key]['owner'] = D('RoleView')->where('role.role_id in (%s)', '0'.$value['owner_role_id'].'0')->select();
			$business['task'][$key]['about_roles'] = D('RoleView')->where('role.role_id in (%s)', '0'.$value['about_roles'].'0')->select();
		}
		
		$contract_ids = M('rBusinessContract')->where('business_id = %d', $business_id)->getField('contract_id', true);
		$business['contract'] = M('contract')->where('contract_id in (%s) and is_deleted=0', implode(',', $contract_ids))->select();
		$contract_count = M('contract')->where('contract_id in (%s) and is_deleted=0', implode(',', $contract_ids))->count();
		$business['contract_count'] = empty($contract_count) ? 0 : $contract_count;
		foreach ($business['contract'] as $key=>$value) {
			$business['contract'][$key]['owner'] = D('RoleView')->where('role.role_id = %d', $value['owner_role_id'])->find();
			$payables = D('PayablesView')->where(array('payables.contract_id'=>$value['contract_id'],'payables.is_deleted'=>0))->select();
			if(empty($payables) || empty($business['payables'])){
				$business['payables'] = $business['payables']?$business['payables']:$payables;
			}else{
				$business['payables'] = array_merge($payables,$business['payables']);
			}
			$receivables = D('ReceivablesView')->where(array('receivables.contract_id'=>$value['contract_id'],'receivables.is_deleted'=>0))->select();
			if(empty($receivables) || empty($business['receivables'])){
				$business['receivables'] = $business['receivables']?$business['receivables']:$receivables;
			}else{
				$business['receivables'] = array_merge($receivables,$business['receivables']);
			}
		}
		foreach ($business['payables'] as $key=>$value) {
			$business['payables'][$key]['owner'] = D('RoleView')->where('role.role_id = %d', $value['owner_role_id'])->find();
		}
		foreach ($business['receivables'] as $key=>$value) {
			$business['receivables'][$key]['owner'] = D('RoleView')->where('role.role_id = %d', $value['owner_role_id'])->find();
		}
		$business['payables_count'] = count($business['payables']);
		$business['receivables_count'] = count($business['receivables']);
		
		$event_ids = M('rBusinessEvent')->where('business_id = %d', $business_id)->getField('event_id', true);
		$business['event'] = M('event')->where('event_id in (%s)', implode(',', $event_ids))->select();
		$event_count = M('event')->where('event_id in (%s)', implode(',', $event_ids))->count();
		$business['event_count'] = empty($event_count)? 0 : $event_count;
		foreach ($business['event'] as $key=>$value) {
			$business['event'][$key]['owner'] = D('RoleView')->where('role.role_id = %d and is_deleted=0', $value['owner_role_id'])->find();
		}
		
		$business['product'] = M('rBusinessProduct')->where('business_id = %d', $business_id)->select();
		$product_count =  M('rBusinessProduct')->where('business_id = %d', $business_id)->count();
		$business['product_count'] = empty($product_count)? 0 : $product_count;
		$product_category = M('product_category');
		
		$total_amount = 0;
		$total_money = 0;
		foreach ($business['product'] as $k => $v) {
			$m_product_category = M('productCategory');
			$info = M('product')->where('product_id = %d', $v['product_id'])->find();
			$business['product'][$k]['info'] = $info;
			$business['product'][$k]['category_name'] = $m_product_category->where('category_id = %d',$info['category_id'])->getField('name'); 
			$total_amount += $v['amount'];
		}
		
		$alert = parseAlert();
		$this->alert = $alert;
		$this->business = $business;
		$this->total_amount = $total_amount; 
		$this->field_list = $field_list;
		actionLog($business['business_id']);
		$this->display();
	}
	
	/**
	*从回收站彻底删除商机
	*
	**/
	public function completeDelete(){
		$m_business = M('business');
		$m_business_data = M('BusinessData');
		$r_module = array('RBusinessCustomer', 'Event'=>'RBusinessEvent', 'File'=>'RBusinessFile', 'Log'=>'RBusinessLog', 'RBusinessProduct', 'Task'=>'RBusinessTask');
		if (!session('?admin')) {
			alert('error', L('THE_ADMINISTRATOR_CAN_NOT_DELETE_THE_CONTENTS_OF_THE_RECYCLE_BIN'), $_SERVER['HTTP_REFERER']);
		}
		if ($this->isPost()) {
			$business_ids = is_array($_POST['business_id']) ? implode(',', $_POST['business_id']) : '';
			if ('' == $business_ids) {
				alert('error', L('YOU_DO_NOT_CHOOSE_ANY_CONTENT'), $_SERVER['HTTP_REFERER']);
			} else {
				if($m_business->where('business_id in (%s)', $business_ids)->delete() && $m_business_data->where('business_id in (%s)', $business_ids)->delete()){
					foreach ($_POST['business_id'] as $value) {
						actionLog($value);
						foreach ($r_module as $key2=>$value2) {
							$module_ids = M($value2)->where('business_id = %d', $value)->getField($key2 . '_id',true);
							M($value2)->where('business_id = %d', $value) -> delete();
							if(!is_int($key2)){
								M($key2)->where($key2 . '_id in (%s)', implode(',', $module_ids))->delete();
							}
						}
					}
					alert('success', L('DELETE_THE_SUCCESS'),U('business/index','by=deleted'));
				} else {
					alert('error', L('DELETE_FAILED'), $_SERVER['HTTP_REFERER']);
				}
			}
		} elseif($_GET['id']) {
			$business_id = intval($_GET['id']);
			$business = $m_business->where('business_id = %d', $business_id)->find();
			if (is_array($business)) {
				if($m_business->where('business_id = %d', $business_id)->delete()){
					actionLog($_GET['id']);
					foreach ($r_module as $key2=>$value2) {
						if(!is_int($key2)){
							$module_ids = M($value2)->where('business_id = %d', $business_id)->getField($key2 . '_id',true);
							$m_key = M($key2);
							$m_key->where($key2 . '_id in (%s)', implode(',', $module_ids))->delete();
							M($value2)->where('business_id = %d', $business_id)->delete();
						}
					}
					alert('success', L('DELETE_THE_SUCCESS'),U('business/index','by=deleted'));
				} else {
					alert('error', L('DELETE_FAILED'),$_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error', L('YOU_WANT_TO_DELETE_THE_RECORD_DOES_NOT_EXIST'), $_SERVER['HTTP_REFERER']);
			}
		} else {
			alert('error', L('PLEASE_SELECT_ITEMS_TO_DELETE'),$_SERVER['HTTP_REFERER']);
		}
	}
	
	/**
	*商机放入回收站
	*
	**/
	public function delete(){
		$m_business = M('business');
		
		$business_ids = is_array($_REQUEST['business_id']) ? implode(',', $_REQUEST['business_id']) : $_REQUEST['id'];
		if ('' == $business_ids) {
			alert('error', L('YOU_DO_NOT_CHOOSE_ANY_CONTENT'), U('business/index'));
		} else {
			foreach($_REQUEST['business_id'] as $v){
				actionLog($v);
			}
			$data = array('is_deleted'=>1, 'delete_role_id'=>session('role_id'), 'delete_time'=>time());
			$where['business_id'] = is_array($business_ids) ? array('in',$business_ids): array('in', explode(',',$business_ids));
			if($m_business->where($where)->setField($data)){	
				alert('success', L('DELETE_THE_SUCCESS'),U('business/index'));
			} else {
				alert('error', L('DELETE_FAILED_PLEASE_CONTACT_YOUR_ADMINISTRATOR'), U('business/index'));
			}
		}
	}
	
	/**
	*从回收站还原商机
	*
	**/
	public function revert(){
		$business_id = isset($_GET['id']) ? intval(trim($_GET['id'])) : 0;
		if ($business_id > 0) {
			$m_business = M('business');
			$business = $m_business->where('business_id = %d', $business_id)->find();
			if (session('?admin') || $business['delete_role_id'] == session('role_id')) {
				if ($m_business->where('business_id = %d', $business_id)->setField('is_deleted', 0)) {
					alert('success', L('REDUCTION_OF_SUCCESS'), $_SERVER['HTTP_REFERER']);
				} else {
					alert('error', L('RESTORE_FAILURE'), $_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error', L('YOU_HAVE_NO_PERMISSION_TO_RESTORE'), $_SERVER['HTTP_REFERER']);
			}
		} else {
			alert('error', L('PARAMETER_ERROR'), $_SERVER['HTTP_REFERER']);
		}
	}
	
	/**
	*商机列表页（默认页面）
	*
	**/
	public function index(){
		$d_v_business = D('BusinessView');
		$below_ids = getSubRoleId(false);
		$p = isset($_GET['p']) ? intval($_GET['p']) : 1 ;
		$by = isset($_GET['by']) ? trim($_GET['by']) : '';
		$where = array();
		$params = array();
		$order = "create_time desc";
		
		if($_GET['desc_order']){
			$order = trim($_GET['desc_order']).' desc';
		}elseif($_GET['asc_order']){
			$order = trim($_GET['asc_order']).' asc';
		}
		
		switch ($by) {
			case 'create' : $where['creator_role_id'] = session('role_id'); break;
			case 'sub' : $where['owner_role_id'] = array('in',implode(',', $below_ids)); break;
			case 'subcreate' : $where['creator_role_id'] = array('in',implode(',', $below_ids)); break;
			case 'today' : 
				$where['nextstep_time'] =  array(array('lt',strtotime(date('Y-m-d', time()))+86400), array('gt',0), 'and'); 
				break;
			case 'week' : 
				$where['nextstep_time'] =  array(array('lt',strtotime(date('Y-m-d', time())) + (8-date('N', time())) * 86400), array('gt', 0),'and');
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
			case 'deleted' : $where['is_deleted'] = 1; break;
			case 'add' : $order = 'create_time desc'; break;
			case 'update' : $order = 'update_time desc'; break;
			case 'me' : $where['business.owner_role_id'] = session('role_id'); break;
			default : $where['business.owner_role_id'] = array('in',implode(',', getSubRoleId())); break;
		}
		
		// if($by){
			// if($by != 'deleted') {
				// if(!$_REQUEST["field"] || ($_REQUEST["field"] != 'status_id' && $_REQUEST["field"])) $where['business.status_id'] = array(array('neq', 99), array('neq', 100), 'and');
			// }
		// }else{
			// if(!$_REQUEST["field"] || ($_REQUEST["field"] != 'status_id' && $_REQUEST["field"])) $where['business.status_id'] = array(array('neq', 99), array('neq', 100), 'and');
		// }
		if (!isset($where['is_deleted'])) {
			$where['business.is_deleted'] = 0;
		}
		if (!isset($where['business.owner_role_id'])) {
			$where['business.owner_role_id'] = array('in',implode(',', getSubRoleId())); 
		}
		if ($_REQUEST["field"]) {
			if (trim($_REQUEST['field']) == "all") {
				$field = is_numeric(trim($_REQUEST['search'])) ? 'name|origin|type|description|estimate_price|gain_rate|gain_cycle|sales_price|product_amount|total_price|estimate_income' : 'name|origin|type|description';
			} else {
				$field = trim($_REQUEST['field']);
			}
			$search = empty($_REQUEST['search']) ? '' : trim($_REQUEST['search']);
			$condition = empty($_REQUEST['condition']) ? 'is' : trim($_REQUEST['condition']);
			$field_date = M('Fields')->where('(is_main=1 and model="" and form_type="datetime") or (is_main=1 and model="business" and form_type="datetime")')->select();
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

			if ($this->_request('state') || $this->_request('city')) {
				$params = array('field='.trim($_REQUEST['field']), 'state='.trim($_REQUEST['field']), 'city='.trim($_REQUEST['field']), 'condition='.$condition, 'search='.$where);
			}else{
				$params = array('field='.trim($_REQUEST['field']), 'condition='.$condition, 'search='.$search );
			}
		}		
		$order = empty($order) ? 'business.update_time desc' : $order;
		if(trim($_GET['act']) == 'excel'){	
			if(vali_permission('business', 'export')){
				$businessList = $d_v_business->where($where)->order($order)->select();			
				$this->excelExport($businessList);
			}else{
				alert('error', L('HAVE NOT PRIVILEGES'), $_SERVER['HTTP_REFERER']);
			}
		}
		$list = $d_v_business->where($where)->order($order)->page($p.',15')->select();
		$count =  $d_v_business->where($where)->count();
		import("@.ORG.Page");
		$Page = new Page($count,15);
		if (!empty($_GET['by'])) {
			$params[] = "by=".trim($_GET['by']);
		}
		
		$this->parameter = implode('&', $params);

		if ($_GET['desc_order']) {
			$params[] = "desc_order=" . trim($_GET['desc_order']);
		} elseif($_GET['asc_order']){
			$params[] = "asc_order=" . trim($_GET['asc_order']);
		}
		
		$Page->parameter = implode('&', $params);
		$this->assign('page', $Page->show());
		foreach($list as $key => $value){
			$list[$key]['owner'] = D('RoleView')->where('role.role_id = %d', $value['owner_role_id'])->find();
			$list[$key]['creator'] = D('RoleView')->where('role.role_id = %d', $value['creator_role_id'])->find();
			$list[$key]['customer_name'] = M('customer')->where('customer_id = %s',$value['customer_id'])->getField('name');
			$list[$key]['status_name'] = M('BusinessStatus')->where('status_id = %d', $value['status_id'])->getField('name');
			if($by == 'deleted') {
				$list[$key]["delete_role"] = D('RoleView')->where('role.role_id = %d', $value['delete_role_id'])->find();
			}
		}
		$d_role_view = D('RoleView');
		$this->role_list = $d_role_view->where('role.role_id in (%s)', implode(',', $below_ids))->select();
		$this->customer_list = M('customer')->where('owner_role_id in (%s)', implode(',', getSubRoleId()))->select();
		$this->assign('list',$list);
	
		$this->search_field_array = getMainFields('business');
		$this->field_array = getIndexFields('business');
		$this->alert = parseAlert();
	    $this->display();
	}
	
	/**
	*商机弹出框列表页
	*
	**/
	public function listDialog(){
		$d_business = D('BusinessView');
		$where['business.status_id'] = array(array('neq', 99), array('neq', 100), 'and');
		$where['owner_role_id'] = array('in',implode(',', getSubRoleId()));
		$where['is_deleted'] = 0;
		$list = $d_business->order('business.create_time desc')->where($where)->limit(10)->select();
		foreach($list as $k=>$v){
			$list[$k]['customer_name'] = M('Customer')->where('customer_id = %d', $v['customer_id'])->getField('name');
		}
		$count = $d_business->where($where)->count();
		$this->total = $count%10 > 0 ? ceil($count/10) : $count/10;
		$this->count_num = $count;
		$this->assign('businessList',$list);
		$this->display();
	}
	
	/**
	*商机弹出框Ajax翻页
	*
	**/
	public function changeContent(){
		if($this->isAjax()){
			$m_business = D('BusinessView');
			$p = isset($_GET['p']) ? intval($_GET['p']) : 1 ;
			$where = array();
			$order = "";
			if($_REQUEST["field"] != 'business.status') $where['business.status_id'] = array(array('neq', 99), array('neq', 100), 'and');
			
			$where['is_deleted'] = 0;
			$where['owner_role_id'] = array('in',implode(',', getSubRoleId()));
			
			if ($_REQUEST["field"]) {
				if (trim($_REQUEST['field']) == "all") {
					$field = is_numeric(trim($_REQUEST['search'])) ? 'business.name|business.origin|business.description|business.estimate_price|business.gain_rate|business.gain_cycle|business.sales_price|business.product_amount|business.total_price|business.estimate_income' : 'business.name|business.origin|business.description';
				} else {
					$field = trim($_REQUEST['field']);
				}
				$search = empty($_REQUEST['search']) ? '' : trim($_REQUEST['search']);
				$condition = empty($_REQUEST['condition']) ? 'is' : trim($_REQUEST['condition']);

				if	('business.create_time' == $field || 'business.update_time' == $field || 'business.due_date' == $field) {
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
			$list = $m_business->where($where)->order('business.create_time desc')->page($p.',10')->select();		
			foreach($list as $key => $value){
				$list[$key]['owner'] = D('RoleView')->where('role.role_id = %d', $value['owner_role_id'])->find();
				$list[$key]['creator'] = D('RoleView')->where('role.role_id = %d', $value['creator_role_id'])->find();
				$list[$key]['customer_name'] = M('customer')->where('customer_id = %s',$value['customer_id'])->getField('name');
				$list[$key]['status_name'] = M('BusinessStatus')->where('status_id = %d', $value['status_id'])->getField('name');
				if($by == 'deleted') {
					$list[$key]["delete_role"] = D('RoleView')->where('role.role_id = %d', $value['delete_role_id'])->find();
				}
				if(!$list[$key]['customer_name']) $list[$key]['customer_name'] = '';
				if(!$list[$key]['customer_id']) $list[$key]['customer_id'] = '';
				if($list[$key]['estimate_price'] == 0) $list[$key]['estimate_price'] = '';
				if(!$list[$key]['status_name']) $list[$key]['status_name'] = '';
				$list[$key]['customer_name'] = M('Customer')->where('customer_id = %d', $value['customer_id'])->getField('name');
			}
	
			$count = $m_business->where($where)->count();
			$data['list'] = $list;
			$data['p'] = $p;
			$data['count'] = $count;
			$data['total'] = $count%10 > 0 ? ceil($count/10) : $count/10;
			$this->ajaxReturn($data,"",1);
		}
	}
	
	/**
	*导出商机到excel表格
	*
	**/
	public function excelExport($businessList=false){
		C('OUTPUT_ENCODE', false);
		set_time_limit(0);
		import("ORG.PHPExcel.PHPExcel");
		$objPHPExcel = new PHPExcel();    
		$objProps = $objPHPExcel->getProperties();    
		$objProps->setCreator("5kcrm");    
		$objProps->setLastModifiedBy("5kcrm");    
		$objProps->setTitle("5kcrm Business Data");    
		$objProps->setSubject("5kcrm Business Data");    
		$objProps->setDescription("5kcrm Business Data");    
		$objProps->setKeywords("5kcrm Business Data");    
		$objProps->setCategory("5kcrm");
		$objPHPExcel->setActiveSheetIndex(0);     
		$objActSheet = $objPHPExcel->getActiveSheet(); 
		   
		$objActSheet->setTitle('Sheet1');
        $ascii = 65;
        $cv = '';
		$field_role = M('Fields')->where('form_type = "user" and (field="owner_role_id" or field="creator_role_id")')->select();
        $field_list = M('Fields')->where('model = \'business\'')->order('order_id')->select();
		$field_info = array_merge($field_list, $field_role);
        foreach($field_info as $field){
            $objActSheet->setCellValue($cv.chr($ascii).'1', $field['name']);
            $ascii++;
            if($ascii == 91){
                $ascii = 65;
                $cv .= chr(strlen($cv)+65);
            }
        }
		if(is_array($businessList)){
			$list = $businessList;
		}else{
			$where['owner_role_id'] = array('in',implode(',', getSubRoleId()));
			$where['is_deleted'] = 0;
			$list = M('business')->where($where)->select();
		}
		
		$i = 1;
		foreach ($list as $k => $v) {
            $data = M('BusinessData')->where("business_id = $v[business_id]")->find();
            if(!empty($data)){
                $v = $v+$data;
            }
			$i++;
            $ascii = 65;
            $cv = '';
            foreach($field_info as $field){
                if($field['form_type'] == 'datetime'){
					if($v[$field['field']] == 0 || strlen($v[$field['field']]) != 10){
						$objActSheet->setCellValue($cv.chr($ascii).$i, '');
					}else{
						$objActSheet->setCellValue($cv.chr($ascii).$i, date('Y-m-d',$v[$field['field']]));
					} 
                }elseif($field['form_type'] == 'number' || $field['form_type'] == 'floatnumber' || $field['form_type'] == 'phone' || $field['form_type'] == 'mobile' || ($field['form_type'] == 'text' && is_numeric($v[$field['field']]))){
					//防止使用科学计数法，在数据前加空格
					$objActSheet->setCellValue($cv.chr($ascii).$i, ' '.$v[$field['field']]);
				}elseif($field['field'] == 'customer_id'){
					$m_customer = M('Customer');
					$customer = $m_customer->where('customer_id = %d',$v['customer_id'])->find();
					$objActSheet->setCellValue($cv.chr($ascii).$i, $customer['name']);
				}elseif($field['field'] == 'contacts_id'){
					$m_contacts = M('Contacts');
					$contacts = $m_contacts->where('contacts_id = %d',$v['contacts_id'])->find();
					$objActSheet->setCellValue($cv.chr($ascii).$i, $contacts['name']);
				}elseif($field['field'] == 'status_id'){
					$m_business_status = M('BusinessStatus');
					$business_status = $m_business_status->where('status_id = %d',$v['status_id'])->find();
					$objActSheet->setCellValue($cv.chr($ascii).$i, $business_status['name']);
				}elseif($field['field'] == 'owner_role_id'){
					$m_user = M('user');
					$user_name = $m_user->where('role_id = %d',$v['owner_role_id'])->getField('name');
					$objActSheet->setCellValue($cv.chr($ascii).$i, $user_name);
				}elseif($field['field'] == 'creator_role_id'){
					$m_user = M('user');
					$user_name = $m_user->where('role_id = %d',$v['creator_role_id'])->getField('name');
					$objActSheet->setCellValue($cv.chr($ascii).$i, $user_name);
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
        header("Content-Disposition:attachment;filename=5kcrm_business_".date('Y-m-d',mktime()).".xls");
        header("Pragma:no-cache");
        header("Expires:0");
        $objWriter->save('php://output'); 
	}
	
	/**
	*商机推进
	*
	**/
	public function advance(){
		if($this->isPost()){
			$id = $_POST['business_id'];
			
			$is_updated = false;
			
			$m_r_bs = D('RBusinessStatus');
			$business = D('BusinessView')->where('business.business_id = %d', $_POST['business_id'])->find();	

			$data['business_id'] = $business['business_id'];
			if($business['gain_rate'])      $data['gain_rate'] = $business['gain_rate'];
			$data['status_id'] = $business['status_id'];
			if($business['description'])	$data['description'] = $business['description'];
			$data['owner_role_id'] = $business['owner_role_id'];
			$data['update_time'] = $business['update_time'];
			$data['update_role_id'] = $business['update_role_id'];
			$m_r_bs->add($data);
			
			$m_business = M('business');
			$m_business_data = M('businessData');
			$data2['update_time'] = time();
			$data2['status_id'] = $_POST['status_id'];
			$data2['nextstep_time'] = strtotime($_POST['nextstep_time']);
			$data2['nextstep'] = $_POST['nextstep'];
			$data3['description'] = $_POST['description'];
			$data2['update_role_id'] = session('role_id');
			
			if(intval($_POST['status_id']) == 100){
				M('Customer')->where('customer_id = %d', $business['customer_id'])->setField('is_locked',1);
			}
			
			if($m_business->where('business_id = %d', $id)->save($data2)){
				$m_business_data->where('business_id = %d', $id)->save($data3);
				M('customer')->where('customer_id = %d',$business['customer_id'])->setField('update_time',time());
				alert('success', L('TO_PROMOTE_SUCCESS'), $_SERVER['HTTP_REFERER']);
			}else{
				alert('error', L('PROMOTE_FAILURE_DATA_NO_CHANGE'),$_SERVER['HTTP_REFERER']);
			}
			
		}elseif($this->isGet()){
			$id = intval(trim($_GET['id']));
			if($id > 0){
				$status_id = M('Business')->where('business_id = %d', $id)->getField('status_id');
				$order_id = M('BusinessStatus')->where('status_id = %d', $status_id)->getField('order_id');
				if(!$order_id) $order_id = 0;
				$statusList =  M('BusinessStatus')->where('order_id >= %d', $order_id)->order('order_id')->select();
				$this->statusList = $statusList;
				$this->business_id = $id;
				$this->display();
			}else{
				alert('error',  L('PARAMETER_ERROR'),$_SERVER['HTTP_REFERER']);
			}
		}
	}
	
	/**
	*商机统计
	*
	**/
	public function analytics(){
		$m_business = M('Business');
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
			$where_money['owner_role_id'] = array('in', implode(',', $role_id_array));
			$where_day_create['creator_role_id'] = array('in', implode(',', $role_id_array));
			$where_day_success['owner_role_id'] = array('in', implode(',', $role_id_array));
		}else{
			$where_source['creator_role_id'] = $role_id;
			$where_status['owner_role_id'] = $role_id;
			$where_money['owner_role_id'] = $role_id;
			$where_day_create['creator_role_id'] = array('in', implode(',', $role_id_array));
			$where_day_success['owner_role_id'] = array('in', implode(',', $role_id_array));
		}
		if($start_time){
			$where_source['create_time'] = array(array('lt',$end_time),array('gt',$start_time), 'and');
			$where_status['create_time'] = array(array('lt',$end_time),array('gt',$start_time), 'and');
			$where_money['create_time'] = array(array('lt',$end_time),array('gt',$start_time), 'and');
		}else{
			$where_source['create_time'] = array('lt',$end_time);
			$where_status['create_time'] = array('lt',$end_time);
			$where_money['create_time'] = array('lt',$end_time);
		}
		
		//统计表内容
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
		$add_count_total = 0;
		$own_count_total = 0;
		$success_count_total = 0;
		$deal_count_total = 0;
		foreach($role_id_array as $v){
			$user = getUserByRoleId($v);
			$add_count = $m_business->where(array('is_deleted'=>0, 'creator_role_id'=>$v, 'create_time'=>$create_time))->count();
			$own_count = $m_business->where(array('is_deleted'=>0, 'owner_role_id'=>$v, 'create_time'=>$create_time))->count();
			$success_count = $m_business->where(array('is_deleted'=>0, 'status_id'=>100,'owner_role_id'=>$v, 'create_time'=>$create_time))->count();
			$deal_count = $m_business->where('is_deleted = 0 and status_id not in(99,100) and owner_role_id = %d and update_time>create_time', $v)->count();
			$reportList[] = array("user"=>$user,"add_count"=>$add_count,"own_count"=>$own_count,"success_count"=>$success_count,"deal_count"=>$deal_count);
			$add_count_total += $add_count;
			$own_count_total += $own_count;
			$success_count_total += $success_count;
			$deal_count_total += $deal_count;
		}
		//商机来源统计图
		$source_count_array = array();
		$setting = M('Fields')->where("model = 'business' and field = 'origin'")->getField('setting');
		$setting_str = '$sourceList='.$setting.';';
		eval($setting_str);
		$where_source['is_deleted'] = 0;
		$source_total_count = 0;
		foreach($sourceList['data'] as $v){
			unset($where_source['origin']);
			$where_source['origin'] = $v;
			$target_count = $m_business ->where($where_source)->count();
			$source_count_array[] = '['.'"'.$v.'",'.$target_count.']';
			$source_total_count += $target_count;
		}
		$source_count_array[] = '["'.L('OTHER').'",'.($add_count_total-$source_total_count).']';
		$this->source_count = implode(',', $source_count_array);
		//商机阶段统计图
		$status_count_array = array();
		$statusList = M('BusinessStatus')->order('order_id desc')->where('status_id <> 99')->select();
		$where_status['is_deleted'] = 0;
		$temp_count = 0;
		foreach($statusList as $v){
			unset($where_status['status_id']);
			$where_status['status_id'] = $v['status_id'];
			$target_count = $m_business ->where($where_status)->count();
			$status_count_array[] = '['.'"'.$v['name'].'",'.($target_count+$temp_count).']';
			$temp_count += $target_count;
		}
		$this->status_count = implode(',', array_reverse($status_count_array));
		/*时间序列图(按日)*/
		if ($end_time - 86400*30 > $start_time) {
			$this_time = $end_time - 86400*30;
		} else {
			$this_time = $start_time;
		}
		while(date('Y-m-d', $this_time) <= date('Y-m-d', $end_time)) {
			$day_count_array[] = "'".date('Y/m/d', $this_time)."'";
			$time1 = strtotime(date('Y-m-d', $this_time));
			$time2 = $time1 + 86400;
			
			$where_day_create['create_time'] = array(array('lt',$time2),array('gt',$time1), 'and');
			$day_create_count_array[] = $m_business->where($where_day_create)->count();

			$where_day_success['update_time'] = array(array('lt',$time2),array('gt',$time1), 'and');
			$where_day_success['status_id'] = 100;
			$day_success_count_array[] = $m_business->where($where_day_success)->count();	
			$this_time += 86400;
		}
		$this->day_count = implode(',', $day_count_array);
		$this->day_create_count = implode(',', $day_create_count_array);
		$this->day_success_count = implode(',', $day_success_count_array);
        /*时间序列图(按周)*/
		if ($end_time - 86400*365 > $start_time) {
			$this_time = $end_time - 86400*365 - 86400 * date('w');
		} else {
			$this_time = $start_time - 86400 * date('w');
		}
		while(date('Y-m-d', $this_time) <= date('Y-m-d', $end_time)) {
			$week_count_array[] = "'".date('Y', $this_time).' s'.date('W',$this_time)."'";
			$time1 = strtotime(date('Y-m-d', $this_time));
			$time2 = $time1 + 86400*7;
			
			$where_week_create['create_time'] = array(array('lt',$time2),array('gt',$time1), 'and');
			$week_create_count_array[] = $m_business->where($where_week_create)->count();

			$where_week_success['update_time'] = array(array('lt',$time2),array('gt',$time1), 'and');
			$where_week_success['status_id'] = 100;
			$week_success_count_array[] = $m_business->where($where_week_success)->count();	
			$this_time += 86400*7;
		}
		$this->week_count = implode(',', $week_count_array);
		$this->week_create_count = implode(',', $week_create_count_array);
		$this->week_success_count = implode(',', $week_success_count_array);
        /*时间序列图(按月)*/
		if ($end_time - 86400*365 > $start_time) {
			$this_time = $end_time - 86400*365;
		} else {
			$this_time = $start_time;
		}
		while(date('Y-m-d', $this_time) <= date('Y-m-d', $end_time)) {
			$month_count_array[] = "'".date('Y/m', $this_time)."'";
			$time1 = strtotime(date('Y-m', $this_time));
			$time2 = mktime(0,0,0,date('m', $this_time)+1,1,date('Y', $this_time));
			
			$where_month_create['create_time'] = array(array('lt',$time2),array('gt',$time1), 'and');
			$month_create_count_array[] = $m_business->where($where_month_create)->count();

			$where_month_success['update_time'] = array(array('lt',$time2),array('gt',$time1), 'and');
			$where_month_success['status_id'] = 100;
			$month_success_count_array[] = $m_business->where($where_month_success)->count();	
			$this_time = mktime(date('H', $this_time),date('i', $this_time),date('s', $this_time),date('m', $this_time)+1,date('d', $this_time),date('Y', $this_time));
		}
		$this->month_count = implode(',', $month_count_array);
		$this->month_create_count = implode(',', $month_create_count_array);
		$this->month_success_count = implode(',', $month_success_count_array);

		$max_money = $m_business->where($where_money)->Max('total_price');
		$min_money = $m_business->where($where_money)->Min('total_price');
		if($max_money == $min_money){
			$target_count = $m_business ->where($where_money)->count();
			$money_count_array[] = '["'.$max_money.L('YUAN').'",'.$target_count.']';
		}else{
			$rank1 = round($min_money,2);
			$rank2 = round($min_money + ($max_money - $min_money) * 0.25,2);
			$rank3 = round($min_money + ($max_money - $min_money) * 0.5,2);
			$rank4 = round($min_money + ($max_money - $min_money) * 0.75,2);
			$rank5 = round($max_money,2);
			$money_where = array(
				array('name'=>$rank1.'~'.$rank2.L('YUAN'),'where_money'=>array(array('elt',$rank2),array('egt',$rank1), 'and')),
				array('name'=>$rank2.'~'.$rank3.L('YUAN'),'where_money'=>array(array('elt',$rank3),array('gt',$rank2), 'and')),
				array('name'=>$rank3.'~'.$rank4.L('YUAN'),'where_money'=>array(array('elt',$rank4),array('gt',$rank3), 'and')),
				array('name'=>$rank4.'~'.$rank5.L('YUAN'),'where_money'=>array(array('elt',$rank5),array('egt',$rank4), 'and'))
			);

			$money_count_array = array();
			foreach($money_where as $v){
				$where_money['total_price'] = $v['where_money'];
				$target_count = $m_business ->where($where_money)->count();
				$money_count_array[] = '['.'"'.$v['name'].'",'.$target_count.']';
			}
		}
		$this->money_count = implode(',', $money_count_array);

		$this->total_report = array("add_count"=>$add_count_total, "own_count"=>$own_count_total, "success_count"=>$success_count_total, "deal_count"=>$deal_count_total);
		$this->reportList = $reportList;
		
		$idArray = getSubRoleId();
		$roleList = array();
		foreach($idArray as $roleId){				
			$roleList[$roleId] = getUserByRoleId($roleId);
		}
		$this->roleList = $roleList;
		
		$departments = M('roleDepartment')->select();
		$departmentList[] = M('roleDepartment')->where('department_id = %d', session('department_id'))->find();
		$departmentList = array_merge($departmentList, getSubDepartment(session('department_id'),$departments,''));
		$this->assign('departmentList', $departmentList);
		$this->alert = parseAlert();
		$this->display();
	}
	
	/**
	 * 首页销售漏斗统计
	 **/
	public function getSalesFunnel(){
		$dashboard = M('user')->where('user_id = %d', session('user_id'))->getField('dashboard');
		$widget = unserialize($dashboard);
		$where['owner_role_id'] = array('in',getSubRoleId());

		$m_business = M('Business');
		$status_count_array = array();
		$status= M('BusinessStatus')->order('order_id desc')->where('status_id <> 99')->order('order_id asc')->getField('status_id,name',true);
		$statusList = array();
		$where['is_deleted'] = array('eq',0);
		foreach($status as $k=>$v){
			$where['status_id'] = array('eq',$k);
			$status_count = $m_business ->where($where)->count();
			$statusList[] = array($v, intval($status_count));
		}
		$this->ajaxReturn($statusList,'success',1);
	}
}