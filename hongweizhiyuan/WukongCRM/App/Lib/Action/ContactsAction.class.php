<?php 
class ContactsAction extends Action {

	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('checklistdialog','getContactsList', 'revert', 'mdelete','radiolistdialog','changedialog','add_dialog','qrcode','changetofirstcontact')
		);
		B('Authenticate', $action);
	}
	
	public function add(){
		if ($_GET['r'] && $_GET['module'] && $_GET['id']) {
			$this -> r = $_GET['r'];
			$this -> module = $_GET['module'];
			$this -> id = $_GET['id'];
			$this->display('Contacts:add_dialog');
		}elseif($this->isPost()){
			$name = trim($_POST['name']);
			$customer_id = trim($_POST['customer_id']);
			if ($name == '' || $name == null) {
				$this -> error(L('CONTACT NAME CANNOT BE EMPTY'));
			}
			if ($customer_id == '' || $customer_id == null) {
				$this->error(L('CONTACTS_CUSTOMER_CANNOT_BE_EMPTY'));
			}
			$contacts = M('contacts');
			
			$contacts->create();
			$contacts->create_time = time();
			$contacts->update_time = time();
			$contacts->creator_role_id = session('role_id');
			if($contacts_id = $contacts->add()){
				if($_POST['customer_id']){
					$rContactsCustomer['contacts_id'] =  $contacts_id;
					$rContactsCustomer['customer_id'] =  $_POST['customer_id'];
					M('rContactsCustomer') ->add($rContactsCustomer);
				}
				
				if($_POST['redirect'] == 'customer'){
					//alert('success','添加成功!',U('customer/view','id='.intval($_POST['redirect_id'])));
					alert('success',L('ADD A SUCCESS'),U('contacts/view','id='.$contacts_id));
				}else{
					if($_POST['submit'] == L('SAVE')){
						alert('success',L('ADD A SUCCESS'),U('contacts/index'));
					}else{
						alert('success',L('ADD A SUCCESS'),U('contacts/add'));
					}
					
				}
			}else{
				alert('error',L('ADD FAILURE'),$_SERVER['HTTP_REFERER']);
			}		
		}else{
			if($_GET['redirect']){
				$this->redirect_id = $_GET['redirect_id'];
				$this->redirect = $_GET['redirect'];
			}
			$customer = M('customer');
			$this->customer = $customer->where('customer_id =' . $_GET['redirect_id'])->find();
			$this->alert = parseAlert();
			$this->display();
		}
	}
	
	public function edit(){
		$m_contacts = M('contacts');
		$rContactsCustomer = M('rContactsCustomer');
		$contacts_id = $_GET['id'] ? intval($_GET['id']) : intval($_POST['contacts_id']);
		if(empty($contacts_id)){
			alert('error',L('PARAMETER_ERROR'),$_SERVER['HTTP_REFERER']);
		}
		$contacts = D('ContactsView')->where(array('contacts_id'=>$contacts_id))->find();
		if(empty($contacts)) alert('error', L('RECORD_NOT_EXIST_OR_HAVE_BEEN_DELETED',array(L('CONTACTS'))),U('contacts/index'));
		//检查权限(联系人编辑权限跟随客户，如果可以编辑客户即可编辑联系人)
		$customer_id = $rContactsCustomer->where('contacts_id = %d', $contacts_id)->getField('customer_id');
		if(!vali_permission('customer','edit') || !check_permission($customer_id, 'customer')) $this->error(L('HAVE NOT PRIVILEGES'));
		
		if ($this->isPost()) {
			$m_contacts->create();
			$m_contacts->update_time = time();
			$name = trim($_POST['name']);
			if ($name == '' || $name == null) {
				alert('error',L('CONTACT NAME CANNOT BE EMPTY'),$_SERVER['HTTP_REFERER']);
			}
			if (!empty($_POST['customer_id'])) {
				if (empty($customer_id)) {
					$data['contacts_id'] = $_POST['contacts_id'];
					$data['customer_id'] = $_POST['customer_id'];
					$rContactsCustomer ->where('contacts_id = %d', $_POST['contacts_id'])->delete();
					$rContactsCustomer -> add($data);
				}elseif ($_POST['customer_id'] != $customer_id) {
					$rContactsCustomer -> where('contacts_id = %d' , $_POST['contacts_id']) -> setField('customer_id',$_POST['customer_id']);
				}	
			}else{
				alert('error', L('NOT NULL',array(L('CUSTOMER'))), $_SERVER['HTTP_REFERER']);
			}
			if ($m_contacts->save()) {
				alert('success',L('THE CONTACT INFORMATION OF SUCCESS'),U('contacts/view') . "&id=" . $_POST['contacts_id']);
			} else {
				alert('error',L('THE CONTACT INFORMATION CHANGE FAILED'),$_SERVER['HTTP_REFERER']);
			}
		}else{
			$this->contacts = $contacts;
			$this->alert = parseAlert();
			$this->display();
		}
	}
	
	public function view(){
		$contacts_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		$rContactsCustomer = M('rContactsCustomer');
		$d_contacts = D('ContactsView');
		if (0 == $contacts_id) {
			alert('error', L('PARAMETER_ERROR'), U('contacts/index'));
		} else {
			//检查权限(联系人查看权限跟随客户，如果可以查看客户即可查看联系人)
			$customer_id = $rContactsCustomer->where('contacts_id = %d', $contacts_id)->getField('customer_id');
			if(!vali_permission('customer','view') || !check_permission($customer_id, 'customer')){
				$this->error(L('HAVE NOT PRIVILEGES'));
			}
			$contacts = D('ContactsView')->where('contacts.contacts_id = %d' , $contacts_id)->find();
			if(empty($contacts)){
				alert('error',L('RECORD_NOT_EXIST_OR_HAVE_BEEN_DELETED',array(L('CONTACTS'))),U('contacts/index'));
			}
			$this->contacts = $contacts;		
			$this->alert = parseAlert();
			$this->display();
		}		
	}

	public function index(){
		$d_contacts = D('ContactsView');
		$p = isset($_GET['p']) ? intval($_GET['p']) : 1 ;
		$by = isset($_GET['by']) ? trim($_GET['by']) : '';
		$below_ids = getSubRoleId(false);
		$all_ids = getSubRoleId();
		$where = array();
		$params = array();
		$order = "create_time desc";
		
		if($_GET['desc_order']){
			$order = trim($_GET['desc_order']).' desc';
		}elseif($_GET['asc_order']){
			$order = trim($_GET['asc_order']).' asc';
		}
		
		switch ($by) {
			case 'today' : $where['create_time'] =  array('gt',strtotime(date('Y-m-d', time()))); break;
			case 'week' : $where['create_time'] =  array('gt',(strtotime(date('Y-m-d', time())) - (date('N', time()) - 1) * 86400)); break;
			case 'month' : $where['create_time'] = array('gt',strtotime(date('Y-m-01', time()))); break;
			case 'add' : $order = 'create_time desc'; break;
			case 'update' : $order = 'update_time desc'; break;
			case 'deleted' : $where['is_deleted'] = 1; break;
			default : $where['owner_role_id'] = array('in',$all_ids); break;
		}
		if (!isset($where['owner_role_id'])) {
			$where['owner_role_id'] = array('in', $all_ids);
		}
		if (!isset($where['is_deleted'])) {
			$where['is_deleted'] = 0;
		}
		if ($_REQUEST["field"]) {
			$field = trim($_REQUEST['field']) == 'all' ? 'name|telephone|email|address|post|department|description' : $_REQUEST['field'];
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
			$params = array('field='.$field, 'condition='.$condition, 'search='.$_REQUEST["search"]);
		}
		if(trim($_GET['act']) == 'excel'){
			if(vali_permission('contacts', 'export')){
				$order = $order ? $order : 'create_time desc';
				$contactsList = $d_contacts->where($where)->order($order)->select();		
				$this->excelExport($contactsList);
			}else{
				alert('error', L('HAVE NOT PRIVILEGES'), $_SERVER['HTTP_REFERER']);
			}
			
		}else{
				$contactsList = $d_contacts->where($where)->order($order)->page($p.',15')->select();
				$count = $d_contacts->where($where)->count();
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
				$this->assign('page',$Page->show());

			if($by == 'deleted') {
				foreach ($contactsList as $k => $v) {
					$contactsList[$k]["delete_role"] = getUserByRoleId($v['delete_role_id']);
					$contactsList[$k]["creator"] = getUserByRoleId($v['creator_role_id']);
				}
			}else{
				foreach ($contactsList as $k => $v) {		
					$contactsList[$k]["creator"] = getUserByRoleId($v['creator_role_id']);
				}
			}
			
			//获取下级和自己的岗位列表,搜索用
			$d_role_view = D('RoleView');
			$this->role_list = $d_role_view->where('role.role_id in (%s)', implode(',', $below_ids))->select();
			$this->assign('contactsList',$contactsList);
			$this->alert = parseAlert();
			$this->display();
		}
	}

	public function completeDelete(){
		$m_contacts = M('contacts');
		$rContactsCustomer = M('rContactsCustomer');
		//检查权限
		$all_ids = getSubRoleId();
		$customer_idArr = M('customer')->where(array('owner_role_id'=>array('in', $all_ids)))->getField('customer_id', true);
		
		$r_module = array('File'=>'RContactsFile', 'Log'=>'RContactsLog', 'RContactsCustomer', 'RContactsTask','RContactsEvent');
		if ($_POST['contacts_id']) {
			if (!session('?admin')) {
				foreach ($_POST['contacts_id'] as $value) {
					$customer_id = $rContactsCustomer->where('contacts_id = %d', $value)->getField('customer_id');
					if(!in_array($customer_id, $customer_idArr)){
						alert('error', L('YOU DO NOT HAVE PERMISSION TO ALL'), $_SERVER['HTTP_REFERER']);
					}
				}
			}
			if ($m_contacts->where('contacts_id in (%s)', join($_POST['contacts_id'],','))->delete()) {
				foreach ($_POST['contacts_list'] as $value) {
					foreach ($r_module as $key2=>$value2) {
						$module_ids = M($value2)->where('contacts_id = %d', $value)->getField($key2 . '_id',true);
						M($value2)->where('contacts_id = %d', $value) -> delete();
						if(!is_int($key2)){
							M($key2)->where($key2 . '_id in (%s)', implode(',', $module_ids))->delete();
						}
					}
				}
				alert('success', L('DELETED SUCCESSFULLY'),U('contacts/index','by=deleted'));
			} else {
				alert('error',L('DELETE FAILED CONTACT THE ADMINISTRATOR'),$_SERVER['HTTP_REFERER']);
			}
		}elseif($_GET['id']){
			$contacts_id = intval($_GET['id']);
			$contacts = $m_contacts->where('contacts_id = %d', $contacts_id)->find();
			if (is_array($contacts)) {
				//检查权限
				$customer_id = $rContactsCustomer->where('contacts_id = %d', $contacts_id)->getField('customer_id');
				if (session('?admin') || in_array($customer_id, $customer_idArr)) {
					if($m_contacts->where('contacts_id = %d', $contacts_id)->delete()){
						foreach ($r_module as $key2=>$value2) {
							if(!is_int($key2)){
								$module_ids = M($value2)->where('contacts_id = %d', $contacts_id)->getField($key2 . '_id',true);
								M($value2)->where('contacts_id = %d', $contacts_id)->delete();
								$m_key = M($key2);
								$m_key->where($key2 . '_id in (%s)', implode(',', $module_ids))->delete();
							}
						}
						alert('success', L('DELETED SUCCESSFULLY'),U('contacts/index','by=deleted'));
					} else {
						alert('error', L('DELETE FAILED'),$_SERVER['HTTP_REFERER']);
					}
				} else {
					alert('error', L('HAVE NOT PRIVILEGES'),$_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error', L('YOU WANT TO DELETE THE RECORD DOES NOT EXIST'), $_SERVER['HTTP_REFERER']);
			}
		}else{
			alert('error',L('PLEASE CHOOSE TO DELETE THE CONTACT'),$_SERVER['HTTP_REFERER']);
		}
	}
	
	public function delete(){
		$m_contacts = M('contacts');
		$rContactsCustomer = M('rContactsCustomer');
		//检查权限
		$all_ids = getSubRoleId();;
		$customer_idArr = M('customer')->where(array('owner_role_id'=>array('in', $all_ids)))->getField('customer_id', true);
		if ($_POST['contacts_id']) {
			if (!session('?admin')) {
				foreach ($_POST['contacts_id'] as $value) {
					//检查权限
					$customer_id = $rContactsCustomer->where('contacts_id = %d', $value)->getField('customer_id');
					if(!in_array($customer_id, $customer_idArr)){
						alert('error', L('YOU DO NOT HAVE PERMISSION TO ALL'), $_SERVER['HTTP_REFERER']);
					}
				}
			}
			$data = array('is_deleted'=>1, 'delete_role_id'=>session('role_id'), 'delete_time'=>time());
			if ($m_contacts->where('contacts_id in (%s)', implode(',', $_POST['contacts_id']))->setField($data)) {
				alert('success', L('DELETED SUCCESSFULLY'),U('contacts/index'));
			} else {
				echo $m_contacts->getLastSql(); die();
				alert('error', L('DELETE FAILED CONTACT THE ADMINISTRATOR'),$_SERVER['HTTP_REFERER']);
			}
		}elseif($_GET['id']){
			$contacts_id = intval($_GET['id']);
			//检查权限
			$customer_id = $rContactsCustomer->where('contacts_id = %d', $contacts_id)->getField('customer_id');
			
			$contacts = $m_contacts->where('contacts_id = %d', $contacts_id)->find();
			if (is_array($contacts)) {
				if (session('?admin') || in_array($customer_id, $customer_idArr)) {
					$data = array('is_deleted'=>1, 'delete_role_id'=>session('role_id'), 'delete_time'=>time());
					if($m_contacts->where('contacts_id = %d', $contacts_id)->setField($data)){
						alert('success', L('DELETED SUCCESSFULLY'),U('contacts/index'));
					} else {
						alert('error', L('DELETE FAILED'),$_SERVER['HTTP_REFERER']);
					}
				} else {
					alert('error', L('HAVE NOT PRIVILEGES'),$_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error',L('YOU WANT TO DELETE THE RECORD DOES NOT EXIST'), $_SERVER['HTTP_REFERER']);
			}
		}else{
			alert('error',L('PLEASE CHOOSE TO DELETE THE CONTACT'),$_SERVER['HTTP_REFERER']);
		}
	}
	
	public function mDelete(){
		if($_GET['r'] && $_GET['id'] && $_GET['module_id']){
			$m_r = M($_GET['r']);
			if($m_r->where("contacts_id = %d and customer_id", $_GET['id'], $_GET['module_id'])->delete()){
				M('Customer')->where("customer_id", $_GET['module_id'])->setField('contacts_id', 0);
				alert('success',L('DELETED SUCCESSFULLY'),$_SERVER['HTTP_REFERER']);
			} else {
				alert('error',L('DELETE FAILED'),$_SERVER['HTTP_REFERER']);
			}
		} else {
			alert('error',L('PARAMETER_ERROR'),$_SERVER['HTTP_REFERER']);
		}
	}
	
	public function getContactsList(){
		$d_contacts = D('ContactsView');
		$idArray = getSubRoleId();
		//获取下级和自己的客户列表,搜索
		$contactsList = D('Contacts')->where(array('owner_role_id'=>array('in',$idArray),'is_deleted'=>array('eq', 0)))->select();
		$this->ajaxReturn($contactsList, '', 1);
	}
	public function checkListDialog(){
		if($this->isPost()){
			$r = $_POST['r'];
			$model_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
			$m_r = M($r);
			$m_id = $_POST['module'] . '_id';  //对应模块的id字段
			
			$data[$m_id] = $model_id;
			foreach ($_POST['contacts_id'] as $value) {
				$data['contacts_id'] = $value;
				if ($m_r -> add($data) <= 0) {
					alert('error', L('SELECT THE CONTACT FAILURE'),$_SERVER['HTTP_REFERER']);
				}
			}
			alert('success', L('SELECT THE CONTACT SUCCESS'),$_SERVER['HTTP_REFERER']);
		}elseif ($_GET['r'] && $_GET['module'] && $_GET['id']) {
			$list = M($_GET['r']) -> getField('contacts_id', true);
			$m_contacts = M('Contacts');
			$underling_ids = getSubRoleId();
			$list[] = 0;
			$this->contactsList = $m_contacts->where('contacts_id not in (%s) and creator_role_id in (%s) and is_deleted <> 1', implode(',',$list), implode(',', $underling_ids))->order('create_time desc')->limit(10)->select();
			$count = $m_contacts->where('contacts_id not in (%s) and owner_role_id in (%s) and is_deleted = 0', implode(',',$list), implode(',', $underling_ids))->count();
			$this->total = $count%10 > 0 ? ceil($count/10) : $count/10;
			$this->count_num = $count;
			$this -> r = $_GET['r'];
			$this -> module = $_GET['module'];
			$this -> model_id = $_GET['id'];
			$this->display();
		}else{
			alert('error', L('PARAMETER_ERROR'),$_SERVER['HTTP_REFERER']);
		}
	}

	public function excelExport($contactsList){
		C('OUTPUT_ENCODE', false);
		import("ORG.PHPExcel.PHPExcel");
		$objPHPExcel = new PHPExcel();    
		$objProps = $objPHPExcel->getProperties();    
		$objProps->setCreator("5kcrm");
		$objProps->setLastModifiedBy("5kcrm");    
		$objProps->setTitle("5kcrm Contact");    
		$objProps->setSubject("5kcrm Contact Data");    
		$objProps->setDescription("5kcrm Contact Data");    
		$objProps->setKeywords("5kcrm Contact");    
		$objProps->setCategory("5kcrm");
		$objPHPExcel->setActiveSheetIndex(0);     
		$objActSheet = $objPHPExcel->getActiveSheet(); 
		   
		$objActSheet->setTitle('Sheet1');
		$objActSheet->setCellValue('A1', L('NAME'));
		$objActSheet->setCellValue('B1', L('RESPECTFULLY'));
		$objActSheet->setCellValue('C1', L('DEPARTMENT'));
		$objActSheet->setCellValue('D1', L('POSITION'));
		$objActSheet->setCellValue('E1', 'QQ');
		$objActSheet->setCellValue('F1', L('PHONE'));
		$objActSheet->setCellValue('G1', 'Email');
		$objActSheet->setCellValue('H1', L('ADDRESS'));
		$objActSheet->setCellValue('I1', L('POSTCODE'));
		$objActSheet->setCellValue('J1', L('REMARK'));
		$objActSheet->setCellValue('K1', L('BELONGS TO THE CUSTOMER'));
		$objActSheet->setCellValue('L1', L('OWNER_ROLE'));
		$objActSheet->setCellValue('M1', L('CREATOR_ROLE'));
		$objActSheet->setCellValue('N1', L('CREATE_TIME'));

		
		if(empty($contactsList)){
			$where['owner_role_id'] = array('in',implode(',', getSubRoleId()));
			$where['is_deleted'] = 0;
			$list = M('contacts')->where($where)->select();
		}else{
			$list = $contactsList;
		}
		
		$i = 1;
		foreach ($list as $k => $v) {
			$i++;
			$owner = D('RoleView')->where('role.role_id = %d', $v['owner_role_id'])->find();
			$creator = D('RoleView')->where('role.role_id = %d', $v['creator_role_id'])->find();
			$objActSheet->setCellValue('A'.$i, $v['name']);
			$objActSheet->setCellValue('B'.$i, $v['saltname']);
			$objActSheet->setCellValue('C'.$i, $v['department']);
			$objActSheet->setCellValue('D'.$i, $v['post']);
			$objActSheet->setCellValue('E'.$i, $v['qq']);
			$objActSheet->setCellValue('F'.$i, $v['telephone']);
			$objActSheet->setCellValue('G'.$i, $v['email']);
			$objActSheet->setCellValue('H'.$i, $v['address']);
			$objActSheet->setCellValue('I'.$i, $v['zip_code']);
			$objActSheet->setCellValue('J'.$i, $v['description']);
			$customer_id = M('rContactsCustomer')->where('contacts_id = %d', $v['contacts_id'])->getField('customer_id');
			$customer_name = M('customer')->where('customer_id = %d' ,$customer_id)->getField('name');
			$objActSheet->setCellValue('K'.$i, $customer_name);
			$objActSheet->setCellValue('L'.$i, $owner['user_name'] .'['.$owner['department_name'].'-'.$owner['role_name'].']');
			$objActSheet->setCellValue('M'.$i, $creator['user_name'].'['.$creator['department_name'].'-'.$creator['role_name'].']');
			$objActSheet->setCellValue('N'.$i, date("Y-m-d H:i:s", $v['create_time']));
		}
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		header("Content-Type: application/vnd.ms-excel;");
        header("Content-Disposition:attachment;filename=5kcrm_contacts_".date('Y-m-d',mktime()).".xls");
        header("Pragma:no-cache");
        header("Expires:0");
        $objWriter->save('php://output'); 
	}
	
	public function excelImport(){
		$m_contacts = M('contacts');
		if($_POST['submit']){
			if (isset($_FILES['excel']['size']) && $_FILES['excel']['size'] != null) {
				import('@.ORG.UploadFile');
				$upload = new UploadFile();
				$upload->maxSize = 20000000;
				$upload->allowExts  = array('xls');
				$dirname = UPLOAD_PATH . date('Ym', time()).'/'.date('d', time()).'/';
				if (!is_dir($dirname) && !mkdir($dirname, 0777, true)) {
					alert('error', L('ATTACHMENTS TO UPLOAD DIRECTORY CANNOT WRITE'), U('contacts/index'));
				}
				$upload->savePath = $dirname;
				if(!$upload->upload()) {
					alert('error', $upload->getErrorMsg(), U('contacts/index'));
				}else{
					$info =  $upload->getUploadFileInfo();
				}
			}
			if(is_array($info[0]) && !empty($info[0])){
				$savePath = $dirname . $info[0]['savename'];
			}else{
				alert('error', L('UPLOAD FAILED'), U('contacts/index'));
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
			for ($currentRow = 3;$currentRow <= $allRow;$currentRow++) {
				$data = array();
				$data['creator_role_id'] = session('role_id');
				$data['create_time'] = time();
				$data['update_time'] = time();
				$data['owner_role_id'] = trim($_POST['owner_role_id']);
				$name = (String)$currentSheet->getCell('A'.$currentRow)->getValue();
				$name != '' && $name != null ? $data['name']=$name : ''; 
				
				/* $customer_name = (String)$currentSheet->getCell('C'.$currentRow)->getValue();
				$customer_id = M('Customer')->where('name = "%s"' ,trim($customer_name))->getField('customer_id');
				if($customer_name){
					if($customer_id > 0){
						$r_c_c['customer_id'] = $customer_id;
						$data['customer_id'] = $customer_id;
					} else {
						alert('error', '导入至第' . $currentRow . '行出错, 原因："'.$customer_name.'"客户不存在', U('contacts/index'));
						break;
					}
				} */
				
				$saltname = (String)$currentSheet->getCell('B'.$currentRow)->getValue();
				$saltname != '' && $saltname != null ? $data['saltname'] = $saltname : '';
				$department = (String)$currentSheet->getCell('C'.$currentRow)->getValue();
				$department != '' && $department != null ? $data['department'] = $department : '';
				$post = (String)$currentSheet->getCell('D'.$currentRow)->getValue();
				$post != '' && $post != null ? $data['post'] = $post : '';
				$qq = (String)$currentSheet->getCell('E'.$currentRow)->getValue();
				$qq != '' && $qq != null ? $data['qq'] = $qq : '';
				$telephone = (String)$currentSheet->getCell('F'.$currentRow)->getValue();
				$telephone != '' && $telephone != null ? $data['telephone'] = $telephone : '';				
				$email = (String)$currentSheet->getCell('G'.$currentRow)->getValue();
				$email != '' && $email != null ? $data['email'] = $email : '';
				$address = (String)$currentSheet->getCell('H'.$currentRow)->getValue();
				$address != '' && $address != null ? $data['address'] = $address : '';
				$zip_code = (String)$currentSheet->getCell('I'.$currentRow)->getValue();
				$zip_code != '' && $zip_code != null ? $data['zip_code'] = $zip_code : '';
				$description = (String)$currentSheet->getCell('J'.$currentRow)->getValue();
				$description != '' && $description != null ? $data['description'] = $description : '';
				if(!$contacts_id = $m_contacts->add($data)) {
					if($this->_post('error_handing','intval',0) == 0){
							alert('error',L('ERROR INTRODUCED INTO THE LINE',array($currentRow,$m_contacts->getError())) , U('contacts/index'));
						}else{
							$error_message .= L('LINE ERROR',array($currentRow,$m_contacts->getError()));
							$m_contacts->clearError();
						}
					break;
				}
			}
			alert('success', L('IMPORT SUCCESS',array($error_message)), U('contacts/index'));
		} else {
			$this->display();
		}
	}
	
	public function revert(){
		$contacts_id = isset($_GET['id']) ? intval(trim($_GET['id'])) : 0;
		if ($contacts_id > 0) {
			$m_contacts = M('contacts');
			$contacts = $m_contacts->where('contacts_id = %d', $contacts_id)->find();
			if ($contacts['delete_role_id'] == session('role_id') || session('?admin')) {
				if (isset($contacts['is_deleted']) || $contacts['is_deleted'] == 1) {
					if ($m_contacts->where('contacts_id = %d', $contacts_id)->setField('is_deleted', 0)) {
						alert('success', L('REDUCTION OF SUCCESS'), $_SERVER['HTTP_REFERER']);
					} else {
						alert('error', L('REDUCTION OF FAILED'), $_SERVER['HTTP_REFERER']);
					}
				} else {
					alert('error', L('ALREADY REDUCTION'), $_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error', L('YOU HAVE NO PERMISSION TO RESTORE'), $_SERVER['HTTP_REFERER']);
			}
		} else {
			alert('error', L('PARAMETER_ERROR'), $_SERVER['HTTP_REFERER']);
		}
	}
	
	public function changeToFirstContact(){
		$id = $_GET['id'];
		$customer_id = $_GET['customer_id'];
		if(isset($id) && isset($customer_id)){
			$m_customer = M('Customer');
			$data['contacts_id'] = $id;
			if($m_customer->where('customer_id = %d',$customer_id)->save($data)){
				alert('success', L('SET THE FIRST CONTACT SUCCESS') ,$_SERVER['HTTP_REFERER']);
			}else{
				alert('error', L('NO CHANGE INFORMATION') ,$_SERVER['HTTP_REFERER']);
			}
		}else{
			alert('error', L('PARAMETER_ERROR'),$_SERVER['HTTP_REFERER']);
		}
	}
	
	public function radioListDialog(){
		$rcc =  M('RContactsCustomer');
		$m_contacts = M('contacts');
		$where['owner_role_id'] = array('in', implode(',', getSubRoleId()));
		$where['is_deleted'] = 0;
		if($_GET['customer_id']){
			$contacts_id = $rcc->where('customer_id = %d', $_GET['customer_id'])->getField('contacts_id', true);
			$where['contacts_id'] = array('in', implode(',', $contacts_id));
			$this->customer_id = $_GET['customer_id'];
		}
		$list = $m_contacts->where($where)->order('create_time desc')->limit(10)->select();
		$count = $m_contacts->where($where)->order('create_time desc')->count();
		
		
		foreach ($list as $k=>$value) {
			$customer_id = $rcc->where('contacts_id = %d', $value['contacts_id'])->getField('customer_id');
			$list[$k]['customer'] = M('customer')->where('customer_id = %d', $customer_id)->find();
		}
		
		$this->total = $count%10 > 0 ? ceil($count/10) : $count/10;
		$this->count_num = $count;
		//获取下级和自己的岗位列表,搜索用
		$below_ids = getSubRoleId(false);
		$d_role_view = D('RoleView');
		$this->role_list = $d_role_view->where('role.role_id in (%s)', implode(',', $below_ids))->select();
		$this->contactsList = $list;
		$this->display();
	}

	public function changeDialog(){
		if($this->isAjax()){
			$m_contacts = M('contacts');
			$m_customer = M('customer');
			$p = !$_REQUEST['p']||$_REQUEST['p']<=0 ? 1 : intval($_REQUEST['p']);
			$where = array();
			$params = array();

			$where['owner_role_id'] = array('in',implode(',', getSubRoleId(true)));
			$where['is_deleted'] = array('neq', 1);
			if($_REQUEST['customer_id'] != 0){
				$contacts_id = M('RContactsCustomer')->where('customer_id = %d', $_REQUEST['customer_id'])->getField('contacts_id', true);
				$where['contacts_id'] = array('in', implode(',', $contacts_id));
			}elseif($_REQUEST['is_check']){
				$list = M($_REQUEST['r']) -> getField('contacts_id', true);
				$list[] = 0;
				$where['contacts_id'] = array('not in', implode(',', $list));
			}
			if ($_REQUEST["field"]) {
				$field = trim($_REQUEST['field']) == 'all' ? 'name|telephone|email|address|post|department|description' : $_REQUEST['field'];
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
			$contactsList = $m_contacts->where($where)->order('create_time desc')->page($p.',10')->select();
			$count = $m_contacts->where($where)->count();
			if(!$_REQUEST['is_check']){
				foreach ($contactsList as $k => $v) {
					if($customer_id = M('rContactsCustomer')->where('contacts_id = %d', $v['contacts_id'])->getField('customer_id')){
						$contactsList[$k]['customer'] = $m_customer->where('customer_id = %d' ,$customer_id)->find();
					}
				}
			}
			$data['list'] = $contactsList;
			$data['p'] = $p;
			$data['count'] = $count;
			$data['total'] = $count%10 > 0 ? ceil($count/10) : $count/10;
//echo '<pre>';print_r($data);echo '</pre>'; die();
			$this->ajaxReturn($data,"",1);
		}
	}
	
	public function qrcode(){
		$contacts_id = intval($_GET['contacts_id']);
		if($contacts = M('Contacts')->where('contacts_id = %d', $contacts_id)->find()){
			$customer_id = M('RContactsCustomer')->where('contacts_id = %d',$contacts_id)->getField('customer_id');
			$contacts['customer'] = M('Customer')->where('customer_id = %d', $customer_id)->getField('name');
			$qrOpt = '';
			$qrOpt = "BEGIN:VCARD\nVERSION:3.0\n";
			$qrOpt .= $contacts['name'] ? ("FN:".$contacts['name']."\n") : "";
			$qrOpt .= $contacts['telephone'] ? ("TEL:".$contacts['telephone']."\n") : "";
			$qrOpt .= $contacts['email'] ? ("EMAIL;PREF;INTERNET:".$contacts['email']."\n") : "";
			$qrOpt .= $contacts['customer'] ? ("ORG:".$contacts['customer']."\n") : "";	
			$qrOpt .= $contacts['post'] ? ("TITLE:".$contacts['post']."\n") : "";
			$qrOpt .= $contacts['address'] ? ("ADR;WORK;POSTAL:".$contacts['address']."\n") : "";
			$qrOpt .= "END:VCARD";
			
			$png_temp_dir = UPLOAD_PATH.'/qrpng/';
			$filename = $png_temp_dir.$contacts['contacts_id'].'.png';
			if (!is_dir($png_temp_dir) && !mkdir($png_temp_dir, 0777, true)) { echo 3;$this->error('二维码保存目录不可写'); }

			import("@.ORG.QRCode.qrlib");
			QRcode::png($qrOpt, $filename, 'M', 4, 2);
			header('Content-type: image/png');	
			header("Content-Disposition: attachment; filename=".$contacts['contacts_id'].'.png');
			echo file_get_contents($filename);
			unlink($filename);
		}
	}
}