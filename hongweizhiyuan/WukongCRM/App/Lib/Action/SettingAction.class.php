<?php
class SettingAction extends Action{
	public function _initialize(){
		$action = array(
			'permission'=>array('clearcache'),
			'allow'=>array('close', 'getbusinessstatuslist', 'getleadsstatuslist', 'getindustrylist', 'getsourcelist','boxfield','mapdialog')
		);
		B('Authenticate',$action);
	}
	
	public function index(){
		$this->redirect('setting/defaultInfo');
	}
	public function openDebug(){
		$file_path = CONF_PATH.'app_debug.php';
		$result = file_put_contents($file_path, "<?php \n\r define ('APP_DEBUG',true);");
		if($result){
			$this->ajaxReturn(1,'',1);	
		}else{
			$this->ajaxReturn(1,'',2);
		}
	}
	public function closeDebug(){
		$file_path = CONF_PATH.'app_debug.php';
		$result = file_put_contents($file_path, "<?php \n\r define ('APP_DEBUG',false);");
		if($result){
			$this->ajaxReturn(1,'',1);
		}else{
			$this->ajaxReturn(1,'',2);
		}
	}
	public function clearCache(){
		if($this->clear_Cache()){
			$this->ajaxReturn(1,'',1);
		}else{
			$this->ajaxReturn(1,'',0);
		}
		
	}
	protected function clear_Cache(){
		deldir(RUNTIME_PATH);
		return true;
	}

	public function smtp(){
		if ($this->isAjax()) {
			if($_POST['address']){
				if (ereg('^([a-zA-Z0-9]+[-|_|_|.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[-|_|_|.]?)*[a-zA-Z0-9]+.[a-zA-Z]{2,3}$',$_POST['address'])){
					if (ereg('^([a-zA-Z0-9]+[_|_|.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|_|.]?)*[a-zA-Z0-9]+.[a-zA-Z]{2,3}$',$_POST['test_email'])){
						$smtp = array('MAIL_ADDRESS'=>$_POST['address'],'MAIL_SMTP'=>$_POST['smtp'],'MAIL_LOGINNAME'=>$_POST['loginName'],'MAIL_PASSWORD'=>$_POST['password'],'MAIL_PORT'=>$_POST['port'],'MAIL_SECURE'=>$_POST['secure'],'MAIL_CHARSET'=>'UTF-8','MAIL_AUTH'=>true,'MAIL_HTML'=>true);
						C($smtp,'smtp');
						//C('','smtp');
						import('@.ORG.Mail');
						$content = L('EMALI_CONTENT');
						$message = SendMail($_POST['test_email'],L('EMALI_TITLE'),$content,L('EMALI_AUTOGRAPH'));
						if($message === true){
							$message = L('SENT SUCCESSFULLY');
						} else {
							$message = $message ? $message : L('SENT FAILED');
						}
					} else {
						$message = L('TEST YOUR INBOX MALFORMED');
					}
				} else {
					$message = L('EMAIL FORMAT ERROR');
				}
				$this->ajaxReturn("", $message, 1);
			}else{
				if($_POST['uid'] && $_POST['passwd'] && $_POST['phone']){
					$result = sendtestSMS(trim($_POST['uid']), trim($_POST['passwd']), $_POST['phone']);
					if($result < 1){
						$message = L('ACCOUNT INFORMATION ERROR');
					}elseif($result == 1){
						$message = L('SENT SUCCESSFULLY SMS');
					}
				}else{
					$message = L('SENT FAILED SMS');
				}
				$this->ajaxReturn("", $message, 1);
			}
		} elseif($this->isPost()) {
			$edit = false;
			$m_config = M('Config');
			if(empty($_POST['address'])){
				alert('error', L('NEED_ADDRESS'), U('setting/smtp'));
			}
			if(empty($_POST['smtp'])){
				alert('error', L('NEED_SMTP'), U('setting/smtp'));
			}
			if(empty($_POST['port'])){
				alert('error', L('NEED_PORT'), U('setting/smtp'));
			}
			if(empty($_POST['loginName'])){
				alert('error', L('NEED_LOGINNAME'), U('setting/smtp'));
			}
			if(empty($_POST['password'])){
				alert('error', L('NEED_PASSWORD'), U('setting/smtp'));
			}
			if($_POST['address']){
				if(is_email($_POST['address'])){
					$demosmtp = array('MAIL_ADDRESS'=>$_POST['address'],'MAIL_SMTP'=>$_POST['smtp'],'MAIL_PORT'=>$_POST['port'],'MAIL_LOGINNAME'=>$_POST['loginName'],'MAIL_PASSWORD'=>$_POST['password'],'MAIL_SECURE'=>$_POST['secure'],'MAIL_CHARSET'=>'UTF-8','MAIL_AUTH'=>true,'MAIL_HTML'=>true);
					$smtp['name'] = 'smtp';
					$smtp['value'] =serialize($demosmtp);
					if($m_config->where('name = "smtp"')->find()){
						if($m_config->where('name = "smtp"')->save($smtp)){
							F('smtp',$demosmtp);
							$edit = true;
						}
					} else {
						if($m_config->add($smtp)){
							F('smtp',$demosmtp);
							$edit = true;
						}else{
							alert('error',L('ADD FAILED'),U('setting/smtp'));
						}
					}
				}else{
					alert('error',L('EMAIL FORMAT ERROR'),U('setting/smtp'));
				}
			}
			
			if($_POST['uid']){
				if(strstr(trim($_POST['uid']), 'BST') === false)	$message = L('ACCOUNT NAME IS MALFORMED');
				$sms = array('uid'=>trim($_POST['uid']),'passwd'=>trim($_POST['passwd']),'sign_name'=>trim($_POST['sign_name']),'sign_sysname'=>trim($_POST['sign_sysname']));
				$sms['name'] = 'sms';
				$sms['value'] =serialize($sms);
				
				if($m_config->where('name = "sms"')->find()){
					if($m_config->where('name = "sms"')->save($sms)){
						F('sms',$sms);
						$edit = true;
					} 
				} else {
					if($m_config->add($sms)){
						F('sms',$sms);
						$edit = true;
					}else{
						alert('error',L('EMAIL FORMAT ERROR'),U('setting/smtp'));
					}
				}
			}
			
			if($edit){
				alert('success',L('SUCCESSFULLY SET AND SAVED'),U('setting/smtp'));
			}else{
				alert('error',L('DATA UNCHANGED'),U('setting/smtp'));
			}
		} else {
			$smtp = M('Config')->where('name = "smtp"')->getField('value');
			$sms = M('Config')->where('name = "sms"')->getField('value');
			$this->smtp = unserialize($smtp);
			$this->sms = unserialize($sms);
			$this->alert = parseAlert();
			$this->display();			
		}
	}
	
	public function source(){
		$m_source = M('InfoSource');
		$this->sourceList = $m_source->order('order_id')->select();
		$this->alert=parseAlert();
		$this->display();
	}
	
	public function sourceAdd(){
		if ($this->isPost()) {
			$m_source = M('InfoSource');
			if($m_source->create()){
				if ($m_source->add()) {
					alert('success', L('SUCCESSFULLY ADDED'), $_SERVER['HTTP_REFERER']);
				} else {
					alert('error', L('THE STATE NAME ALREADY EXISTS'), $_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error', L('ADD FAILED'), $_SERVER['HTTP_REFERER']);
			}
		} else {
			$this->alert=parseAlert();
			$this->display();
		}
	}
	
	public function sourceEdit(){
		$m_source = M('InfoSource');
		if ($this->isGet()) {
			$source_id = intval(trim($_GET['id']));
			$this->source = $m_source->where('source_id = %d', $source_id)->find();
			$this->display();
		} else {
			if ($m_source->create()) {
				if ($m_source->save()) {
					alert('success', L('SUCCESSFULLY EDIT'), $_SERVER['HTTP_REFERER']);
				} else {
					alert('error', L('DATA UNCHANGED'), $_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error', L('EDIT FAILED'), $_SERVER['HTTP_REFERER']);
			}
		}
	}
	
	public function sourceDelete(){
		if ($_POST['source_id']) {
			$id_array = $_POST['source_id'];
			if (M('customer')->where('source_id in (%s)', implode(',', $id_array))->select() || M('leads')->where('source_id in (%s)', implode(',', $id_array))->select()) {
				alert('error', L('DELETE FAILED STATUS'), $_SERVER['HTTP_REFERER']);
			} else {
				if (M('InfoIndustry')->where('source_id in (%s)', implode(',', $id_array))->delete()) {
					alert('success', L('SUCCESSFULLY DELETE'), $_SERVER['HTTP_REFERER']);
				} else {
					alert('error', L('DELETE FAILED'), $_SERVER['HTTP_REFERER']);
				}
			}
		} elseif($_POST['old_id']) {
			$old_id = intval($_POST['old_id']);
			$new_id = intval($_POST['new_id']);
			if (M('InfoSource')->where('source_id = %d', $old_id)->delete()) {
				M('Business')->where('source_id = %d', $old_id)->setField('source_id', $new_id);
				M('Leads')->where('source_id = %d', $old_id)->setField('source_id', $new_id);
				alert('success', L('SUCCESSFULLY DELETE'), $_SERVER['HTTP_REFERER']);
			} else {
				alert('error', L('DELETE FAILED'), $_SERVER['HTTP_REFERER']);
			}
		} else {
			$old_id = intval(trim($_GET['id']));
			$this->old_id = $old_id;
			$this->sourceList = M('InfoSource')->where('source_id <> %d', $old_id)->select();
			$this->display();
		}
	}
	
	public function sourceSort(){
		if ($this->isGet()) {
			$m_source = M('InfoSource');
			$a = 0;
			foreach (explode(',', $_GET['postion']) as $v) {
				$a++;
				$m_source->where('source_id = %d', $v)->setField('order_id',$a);
			}
			$this->ajaxReturn('1', L('SUCCESSFULLY EDIT'), 1);
		} else {
			$this->ajaxReturn('0', L('EDIT FAILED'), 1);
		}
	}
		
	public function industry(){
		$m_status = M('InfoIndustry');
		$this->industryList = $m_status->order('order_id')->select();
		$this->alert=parseAlert();
		$this->display();
	}
	
	public function industryAdd(){
		if ($this->isPost()) {
			$m_status = M('InfoIndustry');
			if($m_status->create()){
				if ($m_status->add()) {
					alert('success', L('SUCCESSFULLY ADDED'), $_SERVER['HTTP_REFERER']);
				} else {
					alert('error', L('THE STATE NAME ALREADY EXISTS'), $_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error', L('ADD FAILED'), $_SERVER['HTTP_REFERER']);
			}
		} else {
			$this->alert=parseAlert();
			$this->display();
		}
	}
	
	public function industryEdit(){
		$m_industry = M('InfoIndustry');
		if ($this->isGet()) {
			$industry_id = intval(trim($_GET['id']));
			$this->industry = $m_industry->where('industry_id = %d', $industry_id)->find();
			$this->display();
		} else {
			if ($m_industry->create()) {
				if ($m_industry->save()) {
					alert('success', L('SUCCESSFULLY EDIT'), $_SERVER['HTTP_REFERER']);
				} else {
					alert('error', L('DATA UNCHANGED'), $_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error', L('EDIT FAILED'), $_SERVER['HTTP_REFERER']);
			}
		}
	}
	
	public function industryDelete(){
		if ($_POST['industry_id']) {
			$id_array = $_POST['industry_id'];
			if (M('customer')->where('industry_id in (%s)', implode(',', $id_array))->select() || M('leads')->where('industry_id in (%s)', implode(',', $id_array))->select()) {
				alert('error', L('DELETE FAILED STATUS'), $_SERVER['HTTP_REFERER']);
			} else {
				if (M('InfoIndustry')->where('industry_id in (%s)', implode(',', $id_array))->delete()) {
					alert('success', L('SUCCESSFULLY DELETE'), $_SERVER['HTTP_REFERER']);
				} else {
					alert('error', L('EDIT FAILED'), $_SERVER['HTTP_REFERER']);
				}
			}
		} elseif($_POST['old_id']){
			$old_id = intval($_POST['old_id']);
			$new_id = intval($_POST['new_id']);
			if (M('InfoIndustry')->where('industry_id = %d', $old_id)->delete()) {
				M('Leads')->where('industry_id = %d', $old_id)->setField('industry_id', $new_id);
				M('Customer')->where('industry_id = %d', $old_id)->setField('industry_id', $new_id);
				alert('success', L('SUCCESSFULLY DELETE'), $_SERVER['HTTP_REFERER']);
			} else {
				alert('error', L('EDIT FAILED'), $_SERVER['HTTP_REFERER']);
			}
		} else {
			$old_id = intval(trim($_GET['id']));
			$this->old_id = $old_id;
			$this->industryList = M('InfoIndustry')->where('industry_id <> %d', $old_id)->select();
			$this->display();
		}
	}
	
	public function industrySort(){
		if ($this->isGet()) {
			$m_industry = M('InfoIndustry');
			$a = 0;
			foreach (explode(',', $_GET['postion']) as $v) {
				$a++;
				$m_industry->where('industry_id = %d', $v)->setField('order_id',$a);
			}
			$this->ajaxReturn('1', L('SUCCESSFULLY EDIT'), 1);
		} else {
			$this->ajaxReturn('0', L('EDIT FAILED'), 1);
		}
	}
	
	public function businessStatus(){
		$m_status = M('BusinessStatus');
		$this->statusList = $m_status->order('order_id')->select();
		$this->alert=parseAlert();
		$this->display();
	}
	
	public function businessStatusAdd(){
		if ($this->isPost()) {
			$m_status = M('BusinessStatus');
			if($m_status->create()){
				if ($m_status->add()) {
					alert('success', L('SUCCESSFULLY ADDED'), $_SERVER['HTTP_REFERER']);
				} else {
					alert('error', L('THE STATE NAME ALREADY EXISTS'), $_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error', L('ADD FAILED'), $_SERVER['HTTP_REFERER']);
			}
		} else {
			$this->alert=parseAlert();
			$this->display();
		}
	}
	
	public function businessStatusEdit(){
		$m_status = M('BusinessStatus');
		if ($this->isGet()) {
			$status_id = intval(trim($_GET['id']));
			$this->status = $m_status->where('status_id = %d', $status_id)->find();
			$this->display();
		} else {
			if ($m_status->create()) {
				if ($m_status->save()) {
					alert('success', L('SUCCESSFULLY EDIT'), $_SERVER['HTTP_REFERER']);
				} else {
					alert('error', L('DATA UNCHANGED'), $_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error', L('EDIT FAILED'), $_SERVER['HTTP_REFERER']);
			}
		}
	}
	
	public function businessStatusDelete(){
		if ($_POST['status_id']) {
			$id_array = $_POST['status_id'];
			if (M('Business')->where('status_id in (%s)', implode(',', $id_array))->select() || M('RBusinessStatus')->where('status_id in (%s)', implode(',', $id_array))->select()) {
				alert('error', L('DELETE FAILED STATUS'), $_SERVER['HTTP_REFERER']);
			} else {
				if (M('BusinessStatus')->where('status_id in (%s)', implode(',', $id_array))->delete()) {
					alert('success', L('SUCCESSFULLY DELETE'), $_SERVER['HTTP_REFERER']);
				} else {
					alert('error', L('DELETE FAILED'), $_SERVER['HTTP_REFERER']);
				}
			}
		} elseif($_POST['old_id']){
			$old_id = intval($_POST['old_id']);
			$new_id = intval($_POST['new_id']);
			if (M('BusinessStatus')->where('status_id = %d', $old_id)->delete()) {
				M('Business')->where('status_id = %d', $old_id)->setField('status_id', $new_id);
				M('RBusinessStatus')->where('status_id = %d', $old_id)->setField('status_id', $new_id);
				alert('success', L('SUCCESSFULLY DELETE'), $_SERVER['HTTP_REFERER']);
			} else {
				alert('error', L('DELETE FAILED'), $_SERVER['HTTP_REFERER']);
			}
		} else {
			$old_id = intval(trim($_GET['id']));
			$this->old_id = $old_id;
			$this->statusList = M('BusinessStatus')->where('status_id <> %d', $old_id)->select();
			$this->display();
		}
	}
	
	public function businessStatusSort(){
		if ($this->isGet()) {
			$status = M('BusinessStatus');
			$a = 0;
			foreach (explode(',', $_GET['postion']) as $v) {
				$a++;
				$status->where('status_id = %d', $v)->setField('order_id',$a);
			}
			$this->ajaxReturn('1', L('SUCCESSFULLY EDIT'), 1);
		} else {
			$this->ajaxReturn('0', L('EDIT FAILED'), 1);
		}
	}

	public function leadsStatus(){
		$m_status = M('leadsStatus');
		$this->statusList = $m_status->order('order_id')->select();
		$this->alert=parseAlert();
		$this->display();
	}
	
	public function leadsStatusAdd(){
		if ($this->isPost()) {
			$m_status = M('leadsStatus');
			if($m_status->create()){
				if ($m_status->add()) {
					alert('success', L('SUCCESSFULLY ADDED'), $_SERVER['HTTP_REFERER']);
				} else {
					alert('error', L('THE STATE NAME ALREADY EXISTS'), $_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error', L('ADD FAILED'), $_SERVER['HTTP_REFERER']);
			}
		} else {
			$this->alert=parseAlert();
			$this->display();
		}
	}
	
	public function leadsStatusEdit(){
		$m_status = M('leadsStatus');
		if ($this->isGet()) {
			$status_id = intval(trim($_GET['id']));
			$this->status = $m_status->where('status_id = %d', $status_id)->find();
			$this->display();
		} else {
			if ($m_status->create()) {
				if ($m_status->save()) {
					alert('success', L('SUCCESSFULLY EDIT'), $_SERVER['HTTP_REFERER']);
				} else {
					alert('error', L('DATA UNCHANGED'), $_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error', L('EDIT FAILED'), $_SERVER['HTTP_REFERER']);
			}
		}
	}
	
	public function leadsStatusDelete(){
		if ($_POST['status_id']) {
			$id_array = $_POST['status_id'];
			if (M('leads')->where('status_id in (%s)', implode(',', $id_array))->select()) {
				alert('error', L('DELETE FAILED STATUS'), $_SERVER['HTTP_REFERER']);
			} else {
				if (M('leadsStatus')->where('status_id in (%s)', implode(',', $id_array))->delete()) {
					alert('success', L('SUCCESSFULLY DELETE'), $_SERVER['HTTP_REFERER']);
				} else {
					alert('error', L('DELETE FAILED'), $_SERVER['HTTP_REFERER']);
				}
			}
		} elseif($_POST['old_id']){
			$old_id = intval($_POST['old_id']);
			$new_id = intval($_POST['new_id']);
			if (M('leadsStatus')->where('status_id = %d', $old_id)->delete()) {
				M('leads')->where('status_id = %d', $old_id)->setField('status_id', $new_id);
				alert('success', L('SUCCESSFULLY DELETE'), $_SERVER['HTTP_REFERER']);
			} else {
				alert('error', L('DELETE FAILED'), $_SERVER['HTTP_REFERER']);
			}
		} else {
			$old_id = intval(trim($_GET['id']));
			$this->old_id = $old_id;
			$this->statusList = M('leadsStatus')->where('status_id <> %d', $old_id)->select();
			$this->display();
		}
	}
	
	public function leadsStatusSort(){
		if ($this->isGet()) {
			$status = M('leadsStatus');
			$a = 0;
			foreach (explode(',', $_GET['postion']) as $v) {
				$a++;
				$status->where('status_id = %d', $v)->setField('order_id',$a);
			}
			$this->ajaxReturn('1', L('SUCCESSFULLY EDIT'), 1);
		} else {
			$this->ajaxReturn('0', L('EDIT FAILED'), 1);
		}
	}
	
	public function statusflow() {
		$this->flowList = M('BusinessStatusFlow')->select();
		$this->alert=parseAlert();
		$this->display();
	}
	
	public function statusflowAdd() {
		if($this->isPost()){
			foreach($_POST['status'] as $value){
				$a = 0;
				foreach($_POST['status'] as $value2){
					if($value == $value2){
						$a++;
					}
					if($a>1){
						alert('error', L('THE STATE CAN NOT BE REPEATED'), $_SERVER['HTTP_REFERER']);
					}
				}
			}
			$flow = D('BusinessStatusFlow');
			$flow->create();
			$flow->data = serialize($_POST['status']);
			if ($flow->add()) {
				alert('success', L('SUCCESSFULLY ADDED'), $_SERVER['HTTP_REFERER']);
			} else {
				alert('error', L('ADD FAILED'), $_SERVER['HTTP_REFERER']);
			}
		}else{
			$status = M('BusinessStatus');
			$this->statusList = $status->select();
			$this->display(); 
		}
	}
		
	public function statusflowEdit(){
		if ($this->isGet()) {
			$flow = M('BusinessStatusFlow')->where("flow_id =" . $_GET['id'])->find();
			$this->flow = $flow;
			$this->data = unserialize($flow['data']);
			$this->statusList = M('BusinessStatus')->select();
			$this->display(); 
		} elseif($this->isPost()) {
			foreach($_POST['status'] as $value){
				$a = 0;
				foreach($_POST['status'] as $value2){
					if($value == $value2){
						$a++;
					}
					if($a>1){
						alert('error', L('THE STATE CAN NOT BE REPEATED'), $_SERVER['HTTP_REFERER']);
					}
				}
			}
			$flow = M('BusinessStatusFlow');
			$data['name'] = $_POST['name'];
			$data['description'] = $_POST['description'];
			$data['data'] = serialize($_POST['status']);
			if($flow->where('flow_id = %d',$_POST['flow_id'])->save($data)){
				alert('success', L('SUCCESSFULLY EDIT'), $_SERVER['HTTP_REFERER']);
			}else{
				alert('error', L('EDIT FAILED'), $_SERVER['HTTP_REFERER']);
			}
		}
	}
	
	public function use_flow(){
		if($this->isGet()){
			$flow_id = intval(trim($_GET['flow_id']));
			M('BusinessStatusFlow')->where('in_user = 1')->setField('in_use', 0);
			if (M('BusinessStatusFlow')->where('flow_id = %d', $flow_id)->setField('in_use', 1)) {
				alert('success', L('SUCCESSFULLY EDIT'), $_SERVER['HTTP_REFERER']);
			}else{
				alert('error', L('EDIT FAILED'), $_SERVER['HTTP_REFERER']);
			}
			
		}
	}
	
	public function statusflowDelete(){
		if ($_POST['flow_id']) {
			$id_array = $_POST['flow_id'];
			if (M('BusinessStatusFlow')->where('flow_id in (%s)', implode(',', $id_array))->delete()) {
				alert('success', L('SUCCESSFULLY DELETE'), $_SERVER['HTTP_REFERER']);
			} else {
				alert('error', L('DELETE FAILED'), $_SERVER['HTTP_REFERER']);
			}
		} else {
			alert('error', L('PARAMETER_ERROR'), $_SERVER['HTTP_REFERER']);
		}
	}
	
	
	public function weixin(){
		if($_POST['submit']){
			$data = array();
			if (isset($_FILES['WEIXIN_IMAGE']['size']) && $_FILES['WEIXIN_IMAGE']['size'] > 0) {
				import('@.ORG.UploadFile');
				$upload = new UploadFile();
				$upload->maxSize = 20000000;
				$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');
				$dirname = UPLOAD_PATH . date('Ym', time()).'/'.date('d', time()).'/';
				if (!is_dir($dirname) && !mkdir($dirname, 0777, true)) {
					alert('error',L("ATTACHMENTS TO UPLOAD DIRECTORY CANNOT WRITE"),U('setting/weixin'));
				}
				$upload->savePath = $dirname;
				if(!$upload->upload()) {
					alert('error',$upload->getErrorMsg(),U('setting/weixin'));
				}else{
					$info =  $upload->getUploadFileInfo();
				}
				if(is_array($info[0]) && !empty($info[0])){
					$data['WEIXIN_IMAGE'] = $dirname . $info[0]['savename'];;
				}else{
					alert('error',L('FAILED TO SAVE THE TWO-DIMENSIONAL CODE IMAGE'),U('setting/weixin'));
				}
			}
			$data['WEIXIN_TOKEN'] = trim($_POST['WEIXIN_TOKEN']);
			if ($data['WEIXIN_TOKEN'] == "") {
				alert('error',L('NOT NULL',array('Token')),U('setting/weixin'));
			} 
			
			$m_config = M('Config');
			$weixin = $m_config->where('name = "weixin"')->find();
			if($weixin){
				$default = unserialize($weixin['value']);					
				if (!isset($data['WEIXIN_IMAGE']) || $data['WEIXIN_IMAGE'] == "") {
					$data['WEIXIN_IMAGE'] = $default['WEIXIN_IMAGE'];
				}				
				if($m_config->where('name = "weixin"')->save(array('value'=>serialize($data)))){
					alert('success',L('SUCCESSFULLY SET AND SAVED'),U('setting/weixin'));
				} else {
					alert('error',L('DATA UNCHANGED'),U('setting/weixin'));
				}
			} else {					
				if($m_config->add(array('value'=>serialize($data), 'name'=>'weixin'))){
					alert('success',L('SUCCESSFULLY SET AND SAVED'),U('setting/weixin'));
				}else{
					alert('error',L('EDIT FAILED'),U('setting/weixin'));
				}
			}
		}else{
			$weixin = M('Config')->where('name = "weixin"')->getField('value');
			$this->weixin = unserialize($weixin);
			$this->alert = parseAlert();
			$this->display();
		}
	}
	
	public function defaultinfo(){
		if($this->isGet()){
			$defaultinfo = M('Config')->where('name = "defaultinfo"')->getField('value');
			$this->defaultinfo = unserialize($defaultinfo);
			$leads_outdays = M('config') -> where('name="leads_outdays"')->getField('value');
			$this->assign('leads_outdays', $leads_outdays);
			$contract_custom = M('config') -> where('name="contract_custom"')->getField('value');
			$this->assign('contract_custom', $contract_custom);
			$customer_outdays = M('config') -> where('name="customer_outdays"')->getField('value');
			$this->assign('customer_outdays', $customer_outdays);
			$customer_limit_condition = M('config') -> where('name="customer_limit_condition"')->getField('value');
			$this->assign('customer_limit_condition', $customer_limit_condition);
			$customer_limit_counts = M('config') -> where('name="customer_limit_counts"')->getField('value');
			$this->assign('customer_limit_counts', $customer_limit_counts);
			$this->alert = parseAlert();
			$this->display();
		}elseif($this->isPost()){
			$m_config = M('Config');
			if (isset($_FILES['logo']['size']) && $_FILES['logo']['size'] > 0) {
				import('@.ORG.UploadFile');
				$upload = new UploadFile();
				$upload->maxSize = 20000000;
				$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');
				$dirname = UPLOAD_PATH . date('Ym', time()).'/'.date('d', time()).'/';
				if (!is_dir($dirname) && !mkdir($dirname, 0777, true)) {
					alert('error',L("ATTACHMENTS TO UPLOAD DIRECTORY CANNOT WRITE"),U('setting/defaultinfo'));
				}
				$upload->savePath = $dirname;
				if(!$upload->upload()) {
					alert('error',$upload->getErrorMsg(),U('setting/defaultinfo'));
				}else{
					$info =  $upload->getUploadFileInfo();
				}
				if(is_array($info[0]) && !empty($info[0])){
					$data['logo'] = $dirname . $info[0]['savename'];;
				}else{
					alert('error',L('LOGO EDIT FAILED'),U('setting/defaultinfo'));
				}
			}
			
			
			$data['name'] = trim($_POST['name']);
			if ($data['name'] == "") {
				alert('error',L('THE SYSTEM NAME CAN NOT BE EMPTY'),U('setting/defaultinfo'));
			}
			$data['description'] = trim($_POST['description']);
			$data['state'] = trim($_POST['state']);
			$data['city'] = trim($_POST['city']);
			$data['allow_file_type'] = !empty($_POST['allow_file_type']) ? trim($_POST['allow_file_type']) : 'pdf,doc,jpg,jpeg,png,gif,txt,doc,xls,zip,docx';
			$data['contract_alert_time'] = intval(trim($_POST['contract_alert_time']));
			$data['task_model'] = trim($_POST['task_model']);
			
			$m_config = M('Config');
			$defaultinfo = $m_config->where('name = "defaultinfo"')->find();
			if($defaultinfo){
				$default = unserialize($defaultinfo['value']);					
				if (!isset($data['logo']) || $data['logo'] == "") {
					$data['logo'] = $default['logo'];
				}				
				if($m_config->where('name = "defaultinfo"')->save(array('value'=>serialize($data)))){
					F('defaultinfo',$data);
					$result_defaultinfo = true;
				} else {
					$result_defaultinfo = false;
				}
			} else {
				if($m_config->add(array('value'=>serialize($data), 'name'=>'defaultinfo'))){
					F('defaultinfo',$data);
					$result_defaultinfo = true;
				}else{
					$result_defaultinfo = false;
				}
			}
			//改变合同前缀名
			if(!$m_config-> where('name="contract_custom"')->find()){
				$contract_custom['name'] = 'contract_custom';
				$contract_custom['contract_custom'] = $_POST['contract_custom'];
				$contract_custom = $m_config -> add($contract_custom);
			}else {
				$contract_custom = $m_config -> where('name="contract_custom"') -> setField('value',$_POST['contract_custom']);
			}
			$leads_outdays = M('config') -> where('name="leads_outdays"') -> setField('value',$_POST['leads_outdays']);
			$result_customer_outdays = $m_config->where('name = "customer_outdays"')->setField('value', $_POST['customer_outdays']);
			$result_customer_limit_condition = $m_config->where('name = "customer_limit_condition"')->setField('value', $_POST['customer_limit_condition']);
			$result_customer_limit_counts = $m_config->where('name = "customer_limit_counts"')->setField('value', $_POST['customer_limit_counts']);
			if($result_defaultinfo || $contract_custom  || $leads_outdays || $result_customer_outdays || $result_customer_limit_condition || $result_customer_limit_counts){
				alert('success',L('SUCCESSFULLY SET AND SAVED'),U('setting/defaultinfo'));
			} else {
				alert('error',L('DATA UNCHANGED'),U('setting/defaultinfo'));
			}
		}
	}
	
	public function getBusinessStatusList(){
		$statusList = M('BusinessStatus')->order('order_id')->select();
		$this->ajaxReturn($statusList, '', 1);
	}
	
	public function getLeadsStatusList(){
		$statusList = M('LeadsStatus')->order('order_id')->select();
		$this->ajaxReturn($statusList, '', 1);
	}
	public function getSourceList(){
		$statusList = M('InfoSource')->order('order_id')->select();
		$this->ajaxReturn($statusList, '', 1);
	}
	public function getIndustryList(){
		$statusList = M('InfoIndustry')->order('order_id')->select();
		$this->ajaxReturn($statusList, '', 1);
	}
	
	public function fields(){
		$model = $this->_get('model','trim','customer');
		$fields = M('fields')->where(array('model'=>$model))->order('order_id ASC')->select();
		$this->assign('model',$model);
		$this->assign('fields',$fields);
		$this->alert=parseAlert();
		$this->display();
	}
	
	public function indexShow(){
		$field = M('fields');
		$field_id = $this->_request('field_id','intval',0);
		if($field_id == 0) alert('error',L('PARAMETER_ERROR'),$_SERVER['HTTP_REFERER']);
		$field_info = $field->where(array('field_id'=>$field_id))->find();
		if($field_info['in_index']) {
			if($field ->where('field_id = %d', $field_id)->setField('in_index', 0)){
				alert('success', L('SUCCESSFULLY EDIT'), $_SERVER['HTTP_REFERER']);
			}else{
				alert('error', L('EDIT FAILED'), $_SERVER['HTTP_REFERER']);
			}
		}else{
			if($field ->where('field_id = %d', $field_id)->setField('in_index', 1)){
				alert('success', L('SUCCESSFULLY EDIT'), $_SERVER['HTTP_REFERER']);
			}else{
				alert('error', L('EDIT FAILED'), $_SERVER['HTTP_REFERER']);
			}
		}
	}
	
	public function fieldAdd(){
		$field = M('fields');
		if($this->isPost()){
			$field_model = D('Field');
			$data['model']         = $this->_post('model'); //模块名称
			$data['field']         = $this->_post('field'); //字段名称
			$data['form_type']     = $this->_post('form_type'); //字段类型
			$data['default_value'] = $this->_post('default_value');  //默认值
			$data['max_length']    = $this->_post('max_length');
			$data['is_main']       = $this->_post('is_main');
			if($field->where(array('field'=>$data['field'],'model'=>array(array('eq',$data['model']),array('eq',''),'OR')))->find()){
				alert('error',L('THE FIELD NAME ALREADY EXISTS'),$_SERVER['HTTP_REFERER']);
			}
			if($field_model->add($data) !== false){
				$field->create();
				if($this->_post('form_type') == 'box'){
					$setting = $this->_post('setting');
					$field->setting = 'array(';
					$field->setting .= "'type'=>'$setting[boxtype]','data'=>array(";
					$i = 0;
					$options = explode(chr(10),$setting['options']);
					$s = array();
					foreach($options as $v){
						$v = trim(str_replace(chr(13),'',$v));
						if($v != '' && !in_array($v ,$s)){
							$i++;
							$field->setting .= "$i=>'$v',";
							$s[] = $v;
						}
					}
					
					$field->setting = substr($field->setting,0,strlen($field->setting) -1 ) .'))';
				}
				$field->add();
				$this->clear_Cache();
				alert('success',L('ADD CUSTOM FIELD SUCCESS'),$_SERVER['HTTP_REFERER']);
			}else{
				if($error = $field_model->getError()){
					alert('error',$error,$_SERVER['HTTP_REFERER']);
				}else{
					alert('error',L('ADDING CUSTOM FIELDS TO FAIL'),$_SERVER['HTTP_REFERER']);
				}
			}
		}else{
			$this->assign('model',$this->_get('model','trim','customer'));
			$this->alert = parseAlert();
			$this->display();
		}
	}
	public function fieldEdit(){
		$field = M('fields');
		$field_id = $this->_request('field_id','intval',0);
		if($field_id == 0) alert('error',L('PARAMETER_ERROR'),$_SERVER['HTTP_REFERER']);
		$field_info = $field->where(array('field_id'=>$field_id))->find();
		if($field_info['operating'] == 2)  alert('error',L('SYSTEM FIXED FIELD PROHIBIT MODIFICATION'),$_SERVER['HTTP_REFERER']);;
		if($this->isPost()){
			$field_model = D('Field');
			$data['model']         = $field_info['model']; //模块名称
			$data['field']         = $field_info['operating'] == 0 ? $this->_post('field') : $field_info['field']; //字段名称
			$data['field_old']     = $field_info['field']; //字段名称
			$data['form_type']     = $field_info['form_type']; //字段类型
			$data['default_value'] = $this->_post('default_value');  //默认值
			$data['max_length']    = $this->_post('max_length');
			$data['is_main']       = $field_info['is_main'];
			
			
			
			if($field->where(array('field'=>$data['field'],'model'=>array(array('eq',$data['model']),array('eq',''),'OR'),'field_id'=>array('neq',$field_id)))->find()){
				alert('error',L('THE FIELD NAME ALREADY EXISTS'),$_SERVER['HTTP_REFERER']);
			}
			if($field_model->save($data) !== false){
				$field->create();
				if($field_info['form_type'] == 'box'){
					eval('$field_info["setting"] = '.$field_info["setting"].';');
					$boxtype = $field_info['setting']['type'];
					$setting = $this->_post('setting');
					$field->setting = 'array(';
					$field->setting .= "'type'=>'$boxtype','data'=>array(";
					$i = 0;
					$options = explode(chr(10),$setting['options']);
					$s = array();
					foreach($options as $v){
						$v = trim(str_replace(chr(13),'',$v));
						if($v != '' && !in_array($v ,$s)){
							$i++;
							$field->setting .= "$i=>'$v',";
							$s[] = $v;
						}
					}
					
					$field->setting = substr($field->setting,0,strlen($field->setting) -1 ) .'))';
				}
				$field->save();
				$this->clear_Cache();
				alert('success',L('MODIFY CUSTOM FIELD SUCCESS'), $_SERVER['HTTP_REFERER']);
			}else{
				if($error = $field_model->getError()){
					alert('error',$error,$_SERVER['HTTP_REFERER']);
				}else{
					alert('error',L('FAILED TO MODIFY CUSTOM FIELDS'),$_SERVER['HTTP_REFERER']);
				}
			}
		}else{

			if($field_info['form_type'] == 'box'){
				eval('$field_info["setting"] = '.$field_info["setting"].';');
				$field_info['form_type_name'] = L('OPTIONS');
				$field_info["setting"]['options'] = implode(chr(10),$field_info["setting"]['data']);
			}else if($field_info['form_type'] == 'editor'){
				$field_info['form_type_name'] = L('EDITOR');
			}else if($field_info['form_type'] == 'text'){
				$field_info['form_type_name'] = L('TEXT');
			}else if($field_info['form_type'] == 'textarea'){
				$field_info['form_type_name'] = L('TEXTAREA');
			}else if($field_info['form_type'] == 'datetime'){
				$field_info['form_type_name'] = L('DATETIME');
			}else if($field_info['form_type'] == 'number'){
				$field_info['form_type_name'] = L('NUMBER');
			}else if($field_info['form_type'] == 'floatnumber'){
				$field_info['form_type_name'] = L('FLOATNUMBER');
			}else if($field_info['form_type'] == 'address'){
				$field_info['form_type_name'] = L('ADDRESS');
			}else if($field_info['form_type'] == 'phone'){
				$field_info['form_type_name'] = L('PHONE');
			}else if($field_info['form_type'] == 'mobile'){
				$field_info['form_type_name'] = L('MOBILE');
			}else if($field_info['form_type'] == 'email'){
				$field_info['form_type_name'] = L('EMAIL');
			}
			$this->assign('fields',$field_info);
			$this->assign('models',array('customer'=>L('CUSTOMER'),'business'=>L('BUSINESS'),'contacts'=>L('CONTACTS')));
			$this->alert = parseAlert();
			$this->display();
		}
	}
	public function fieldDelete(){
		$field = M('fields');
		if($this->isPost()){
			$field_id = is_array($_POST['field_id']) ? implode(',', $_POST['field_id']) : '';
			if ('' == $field_id) {
				alert('error', L('NOT CHOOSE ANY'), $_SERVER['HTTP_REFERER']);
				die;
			} else {
				$where['field_id'] = array('in',$field_id);
				$where['operating'] = array('not in', array(3,0));
				
				$field_info = $field->where($where)->select();
				if($field_info){
					alert('error', L('SYSTEM FIXED FIELDS DELETE PROHIBITED'), $_SERVER['HTTP_REFERER']);
				}else{
					$field_infos = $field->where(array('field_id'=>array('in',$field_id)))->select();
					foreach($field_infos as $field_info){
						$field_model = D('Field');
						$data['model']         = $field_info['model']; //模块名称
						$data['field']         = $field_info['field']; //字段名称
						$data['is_main']       = $field_info['is_main'];
						$field_model->delete($data);
						$field->where(array('field_id'=>$field_info['field_id']))->delete();
					}
					$this->clear_Cache();
					alert('success',L('DELETE CUSTOM FIELD SUCCESS'),$_SERVER['HTTP_REFERER']);
				}
			}
		}else{
			$field_id = $this->_get('field_id','intval',0);
			if($field_id == 0) alert('error',L('PARAMETER_ERROR'),$_SERVER['HTTP_REFERER']);
			$field_info = $field->where(array('field_id'=>$field_id))->find();
			if($field_info['operating'] != 0) alert('error',L('SYSTEM FIXED FIELDS DELETE PROHIBITED'),$_SERVER['HTTP_REFERER']);
			$field_model = D('Field');
			$data['model']         = $field_info['model']; //模块名称
			$data['field']         = $field_info['field']; //字段名称
			$data['is_main']       = $field_info['is_main'];
			if($field_model->delete($data) !== false){
				$field->where(array('field_id'=>$field_id))->delete();
				$this->clear_Cache();
				alert('success',L('DELETE CUSTOM FIELD SUCCESS'),$_SERVER['HTTP_REFERER']);
			}else{
				alert('error',L('FAILED TO DELETE CUSTOM FIELDS'),$_SERVER['HTTP_REFERER']);
			}
		}
		
	}
	public function fieldsort(){	
		if(isset($_GET['postion'])){
			$fields = M('fields');
			foreach(explode(',', $_GET['postion']) AS $k=>$v) {
				$data = array('field_id'=> $v, 'order_id'=>$k);
				$fields->save($data);
			}
			$this->ajaxReturn('1', L('SUCCESSFULLY EDIT'), 1);
		} else {
			$this->ajaxReturn('0', L('EDIT FAILED'), 1);
		}
	}
	public function boxField(){
		$field_list = M('Fields')->where(array('model'=>$this->_get('model'),'field'=>$this->_get('field')))->getField('setting');
		eval('$field_list = '.$field_list .';');
		$this->ajaxReturn($field_list['data'], $field_list['type'], 1);
	}
	
	public function sendSms(){
		if($this->isPost()){
			$phoneNum = trim($_POST['phoneNum']);
			$message = trim($_POST['smsContent']);
			if($_POST['settime']){
				$send_time = strtotime(trim($_POST['sendtime']));
				if($send_time > time()){
					$sendtime = date('YmdHis',$send_time);
				}
			}
			$current_sms_num = getSmsNum();
			if(!F('sms')) alert('success',L('SEND_SMS_FAILED'),$_SERVER['HTTP_REFERER']);
			$phoneNum = str_replace(" ","",$phoneNum);
			$phone_array = explode(chr(10),$phoneNum);
			if(sizeof($phone_array) > 0){
				//if(sizeof($phone_array) > $current_sms_num) alert('error','短信余额不足，请联系管理员，及时充值!',$_SERVER['HTTP_REFERER']);
			}
			$fail_array = array();
			$success_array = array();
			if($phoneNum && $message){		
				if(strpos($message,'{$name}',0) === false){
					foreach($phone_array as $k=>$v){
						if($v){
							$phone = substr($v,0,11);
							if(is_phone($phone)){
								$success_array[] = $phone;
							}else{
								$fail_array[] = $v;
							}
						}
					}
					if(!empty($fail_array)){
						$fail_message = L('PART_OF_NUMBER_SEND_FAILED').implode(',', $fail_array);
					}
					//echo '发送成功!';die();
					$result = sendGroupSMS(implode(',', $success_array),$message,'sign_name', $sendtime);
					if($result == 1){
					    $m_sms_record=M('smsRecord');
						$data['role_id'] = session('role_id');
						$data['telephone'] = implode(',', $success_array);
						$data['content'] = $message;
						$data['sendtime'] = time();
						$m_sms_record->add($data);
						alert('success', L('SEND_SUCCESS_MAY_DELAY_BY_BAD_NETWORK').$fail_message,$_SERVER['HTTP_REFERER']);
					}else{
						alert('error',L('SMS_NOTIFICATION_FAILS_CODE', array($result)),$_SERVER['HTTP_REFERER']);
					}
				}else{
					foreach($phone_array as $k=>$v){
						$real_message = $message;
						$name = ''; 
						if($v){
							$no = str_replace(" ","",$v);
							$phone = substr($no,0,11);
							if(is_phone($phone)){
								if(strpos($v,',',0) === false){
									$info_array = explode('，', $v);
								}else{
									$info_array = explode(',', $v);
								}
								$real_message = str_replace('{$name}',$info_array[1],$real_message);
								$result =sendSMS($phone, $real_message, 'sign_name', $sendtime);
								$m_sms_record=M('smsRecord');
								$data['role_id']=session('role_id');
								$data['telephone']=$phone;
								$data['content']=$real_message;
								$data['sendtime']=time();
								$m_sms_record->add($data);
								
								if($result<0 && $k==0){
									alert('error', L('SMS_NOTIFICATION_FAILS_CODE', array($result)),$_SERVER['HTTP_REFERER']); 
								}
							}else{
								$fail_array[] = $v;
							}
						}
					}
					
					if(!empty($fail_array)){
						$fail_message = L('PART_OF_NUMBER_SEND_FAILED').implode(',', $fail_array);
					}
					
					alert('success',L('SEND_SUCCESS_MAY_DELAY_BY_BAD_NETWORK').$fail_message,U('setting/sendsms'));
					
				}
			}else{
				alert('error',L('INCOMPLETE_INFORMATION'),$_SERVER['HTTP_REFERER']);
			}
		}else{
			$current_sms_num = getSmsNum();
			
			$model = trim($_GET['model']);
			if($model == 'customer'){
				$customer_ids = trim($_GET['customer_ids']);
				if($customer_ids){
					$contacts_ids = M('RContactsCustomer')->where('customer_id in (%s)', $customer_ids)->getField('contacts_id', true);
					$contacts_ids = implode(',', $contacts_ids);
					$contacts = D('ContactsView')->where('contacts.contacts_id in (%s)', $contacts_ids)->select();
					$this->contacts = $contacts;
				}else{
					alert('error',L('SELECT_CUSTOMER_TO_SEND'),$_SERVER['HTTP_REFERER']);
				}
			}elseif($model == 'contacts'){
				$contacts_ids = trim($_GET['contacts_ids']);
				if(!$contacts_ids) alert('error',L('SELECT_CONTACTS_TO_SEND'),$_SERVER['HTTP_REFERER']);
				$contacts = D('ContactsView')->where('contacts.contacts_id in (%s)', $contacts_ids)->select();
				$this->contacts = $contacts;
			}elseif($model == 'leads'){
				$d_v_leads = D('LeadsView');
				$leads_ids = trim($_GET['leads_ids']);
				$where['leads_id'] = array('in',$leads_ids);
				$customer_list = $d_v_leads->where($where)->select();
				$contacts = array();
				foreach ($customer_list as $k => $v) {
					$contacts[] = array('name'=>$v['contacts_name'], 'customer_name'=>$v['name'], 'telephone'=>trim($v['mobile']));
				}
				$this->contacts = $contacts;
			}
			$this->templateList = M('SmsTemplate')->order('order_id')->select();
			$this->alert = parseAlert();
			$this->current_sms_num = $current_sms_num;
			$this->display();
		}
	}
	
	//短信发件箱
	public function smsRecord(){	
	    $m_sms_record=M('smsRecord');
		$where = array();
		$params = array();
		
		if ($_REQUEST["field"]) {
			$field = trim($_REQUEST['field']) == 'all' ? 'title|content' : $_REQUEST['field'];
			$search = empty($_REQUEST['search']) ? '' : trim($_REQUEST['search']);
			$condition = empty($_REQUEST['condition']) ? 'is' : trim($_REQUEST['condition']);
			if	('sendtime' == $field) $search = is_numeric($search)?$search:strtotime($search);
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
			$params = array('field='.$field, 'condition='.$condition, 'search='.trim($_REQUEST["search"]));
		}
	    $p = isset($_GET['p']) ? intval($_GET['p']) : 1 ;
		if(!session('?admin')){
			$where['role_id'] = session('role_id');
		}
		$list = $m_sms_record->where($where)->page($p.',10')->select();
		foreach($list as $k=>$v){
			//查发送人名字
			$list[$k]['send_user'] = M('user')->where('role_id = %d', $v['role_id'])->getField('name');
			//截取手机号
			if(strstr($v['telephone'],',')){
				$list[$k]['subtelephone'] = substr($v['telephone'],0,strpos($v['telephone'],',')).'...';
			}else{
				$list[$k]['subtelephone'] = $v['telephone'];
			}
			//截取内容
			if(mb_strlen($v['content'],'utf-8') >= 30){
				$list[$k]['subcontent'] = mb_substr($v['content'],0,30,'utf8').'...';
			}else{
				$list[$k]['subcontent'] = $v['content'];
			}
		}
		$count =$m_sms_record->where($where)->count();
		import("@.ORG.Page");
		$Page = new Page($count,10);
		$Page->parameter = implode('&', $params);
		$this->assign('page',$Page->show());
		$this->assign('data',$list);
		$this->alert=parseAlert();
		$this->display();
	}
	/**
	删除已发信息
	**/
	public function delete(){
		$m_sms_record = M('smsRecord');
		$r_module = array('Log'=>'REventLog', 'File'=>'REventFile', 'REventLeads', 'RBusinessEvent', 'REventProduct', 'RCustomerEvent', 'RContactsEvent');
		if($this->isPost()){
			$event_ids = is_array($_POST['record_id']) ? implode(',', $_POST['record_id']) : '';
			if ('' == $event_ids) {
				alert('error', L('NOT CHOOSE ANY'), U('Setting/smsRecord'));
			} else {
				
				if($m_sms_record->where('sms_record_id in (%s)', $event_ids)->delete()){	
					foreach ($_POST['record_id'] as $value) {
						foreach ($r_module as $key2=>$value2) {
							$module_ids = M($value2)->where('record_id = %d', $value)->getField($key2 . '_id', true);
							M($value2)->where('record_id = %d', $value) -> delete();
							if(!is_int($key2)){	
								M($key2)->where($key2 . '_id in (%s)', implode(',', $module_ids))->delete();
							}
						}
					}
					alert('success', L('DELETED SUCCESSFULLY'),U('Setting/smsRecord'));
				} else {
					alert('error', L('DELETE FAILED CONTACT THE ADMINISTRATOR'), U('Setting/smsRecord'));
				}
			}
		} 
	}
	public function sendemail(){
		if($this->isPost()){
			if(!$smtp = M('UserSmtp')->where('smtp_id = %d', intval($_POST['smtp']))->find()){
				alert('error', L('NEED_SET_SMTP'),$_SERVER['HTTP_REFERER']);
			}		
			import('@.ORG.Mail');
			$emails = trim($_POST['emails']);
			$title = trim($_POST['title']);
			$content = trim($_POST['content']);
			$url = $this->_server('HTTP_HOST');
			preg_match_all('/<a(.*?)href="(\/Uploads.+?)">(.*?)<\/a>/i',$content,$str_array);
			foreach($str_array as $v){
				$content = str_replace($str_array[0],'',$content);
			}
			$fail_array = array();
			$success_array = array();
			$emails = str_replace(" ","",$emails);
			$emails_array = explode(chr(10),$emails);
			if($emails && $content && $title){
				foreach($emails_array as $k=>$v){
					$email='';
					$str_content='';
					$email_array = array();
					if($v){
						if(strpos($v,',') !== false || strpos($v,'，')!==false){
							$email_array = strpos($v,',') ? explode(',',$v) : explode('，',$v);
							$email = trim($email_array[0]);
							$str_content = str_replace('{name}',$email_array[1],$content);
						}else{
							$email = trim($v);
							$str_content = $content;
						}
						$str_content =(strpos($content,'{name}') !== false) ? str_replace('{name}',$email_array[1],$content) :$content;
						if(is_email($email)){
							$old_array[$email] = $v;
							$success_array[]=array('email'=>$email,'content'=>$str_content);
						}else{
							$fail_array[] = $v;
						}
					}
				}
				if(!empty($fail_array)){
					$fail_message = L('INVALIDATE_EMAIL').implode(',', $fail_array);
				}
				$i=0;
				foreach($success_array as $value){
					$result = bsendemail($value['email'],$title,$value['content'],$str_array[3],true,intval($_POST['smtp']));
					if($result){
						$i++;
					}else{
						$fail_result .= L('SEND_FAILED_UNKNOWN_REASON', array($old_array[$value['email']]));
					}
				}
				if($i>0)
				alert('success',L('SEND_SUCCESS_MAY_DELAY_BY_BAD_NETWORK').$fail_message.'<br>'.$fail_result,$_SERVER['HTTP_REFERER']);
				else
				alert('error',L('SEND_FAILED_CONTACTS_ADMIN').$fail_message.'<br>'.$fail_result,$_SERVER['HTTP_REFERER']);
			}else{
				alert('error',L('INCOMPLETE_INFO'),$_SERVER['HTTP_REFERER']);
			}
		}else{
			$model = trim($_GET['model']);
			if($model == 'customer'){
				$customer_ids = trim($_GET['customer_ids']);
				if($customer_ids){
					if($customer_ids == 'all'){
						$all_ids = getSubRoleId();
						$where['is_deleted'] = array('neq',1);
						$where['owner_role_id'] = array('in', $all_ids);
						$customer_ids = D('CustomerView')->where($where)->getField('customer_id', true);
						$contacts_ids = M('RContactsCustomer')->where('customer_id in (%s)', implode(',', $customer_ids))->getField('contacts_id', true);
						$contacts_ids = implode(',', $contacts_ids);
						$contacts = D('ContactsView')->where('contacts.contacts_id in (%s)', $contacts_ids)->select();
					}else{
						$contacts_ids = M('RContactsCustomer')->where('customer_id in (%s)', $customer_ids)->getField('contacts_id', true);
						$contacts_ids = implode(',', $contacts_ids);
						$contacts = D('ContactsView')->where('contacts.contacts_id in (%s)', $contacts_ids)->select();
					}
					$this->contacts = $contacts;
				}else{
					alert('error',L('SELECT_CUSTOMER_TO_SEND_EMAIL'),$_SERVER['HTTP_REFERER']);
				}
			}elseif($model == 'contacts'){
				$contacts_ids = trim($_GET['contacts_ids']);
				if(!$contacts_ids) alert('error',L('SELECT_CONTACTS_TO_SEND_EMAIL'),$_SERVER['HTTP_REFERER']);
				$contacts = D('ContactsView')->where('contacts.contacts_id in (%s)', $contacts_ids)->select();
				$this->contacts = $contacts;
			}elseif($model == 'leads'){
				$d_v_leads = D('LeadsView');
				$leads_ids = trim($_GET['leads_ids']);
				if('all' != $leads_ids){$where['leads_id'] = array('in',$leads_ids);}
				$customer_list = $d_v_leads->where($where)->select();
				$contacts = array();
				foreach ($customer_list as $k => $v) {
					$contacts[] = array('name'=>$v['contacts_name'], 'customer_name'=>$v['name'], 'email'=>trim($v['email']));
				}
				$this->contacts = $contacts;
			}
			$this->templateList = M('EmailTemplate')->order('order_id')->select();
			$this->smtpList = M('UserSmtp')->select();
			$this->alert = parseAlert();
			$this->display();
		}
	}

}