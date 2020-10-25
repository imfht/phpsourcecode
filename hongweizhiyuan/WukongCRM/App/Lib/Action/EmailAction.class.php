<?php
	class EmailAction extends Action{
		public function _initialize(){
			$action = array(
				'permission'=>array(''),
				'allow'=>array('setting','smtpadd','smtpedit','smtp','index','add')
			);
			B('Authenticate',$action);
		}
		public function index(){
			$templateList = M('EmailTemplate')->order('order_id')->select();
			$this->templateList = $templateList;
			$this->alert=parseAlert();
			$this->display();
		}
		
		public function add(){
			if($this->isPost()){
				$m_template = M('EmailTemplate');
				if(!$_POST['subject']) alert('error', L('TEMPLATE_NAME_CANNOT_BE_EMPTY'), $_SERVER['HTTP_REFERER']);
				if(!$_POST['content']) alert('error', L('MAIL_CONTENTS_CAN_NOT_BE_EMPTY'), $_SERVER['HTTP_REFERER']);	
				if($m_template->create()){
					if($m_template->add()){
						alert('success', L('ADD_A_SUCCESS'), $_SERVER['HTTP_REFERER']);
					}else{
						alert('error', L('ADD_FAILURE'), $_SERVER['HTTP_REFERER']);
					}
				}else{
					alert('error', L('ADD_FAILURE'), $_SERVER['HTTP_REFERER']);
				}
			}else{
				$this->display();
			}
		}
		
		public function edit(){
			$m_template = M('emailTemplate');
			if($this->isPost()){
				
				if(!$_POST['subject']) alert('error', L('TEMPLATE_SUBJECT_CANNOT_BE_EMPTY'), $_SERVER['HTTP_REFERER']);
				if(!$_POST['content']) alert('error', L('TEMPLATE_CONTENT_CANNOT_BE_EMPTY'), $_SERVER['HTTP_REFERER']);
				if($m_template->create()){
					if($m_template->save()){
						alert('success', L('MODIFY_THE_SUCCESS'), $_SERVER['HTTP_REFERER']);
					}else{
						alert('error', L('MODIFY_THE_FAILURE'), $_SERVER['HTTP_REFERER']);
					}
				}else{
					alert('error', L('MODIFY_THE_FAILURE'), $_SERVER['HTTP_REFERER']);
				}
			}else{
				if($_GET['id']){
					$this->template = $m_template->where('template_id = %d', intval($_GET['id']))->find();
					$this->display();
				}else{
					alert('error', L('PARAMETER_ERROR'), $_SERVER['HTTP_REFERER']);
				}
				
			}
		}
		
		public function delete(){
			if($this->isPost()){
				if(!empty($_POST['template_id'])){
					$m_template = M('EmialTemplate');
					$template_ids = $_POST['template_id'];
					if($m_template->where('template_id in (%s)', implode(',', $template_ids))->delete()){
						alert('success', L('DELETED SUCCESSFULLY'), $_SERVER['HTTP_REFERER']);
					}else{
						alert('error', L('DELETED_FAILURE'), $_SERVER['HTTP_REFERER']);
					}
				}else{
					alert('error', L('PARAMETER_ERROR'), $_SERVER['HTTP_REFERER']);
				}
			}else{
				$this->display();
			}
		}
		
		public function orderSort(){
			if ($this->isGet()) {
				$m_template = M('EmailTemplate');
				$a = 0;
				foreach (explode(',', $_GET['postion']) as $v) {
					$a++;
					$m_template->where('template_id = %d', $v)->setField('order_id',$a);
				}
				$this->ajaxReturn('1', L('SAVE_SUCCESS'), 1);
			} else {
				$this->ajaxReturn('0', L('SAVE_FAILURE'), 1);
			}
		}
		
		public function smtp(){
			$smtplist = M('UserSmtp')->select();
			foreach($smtplist as $k => $v){
				$smtplist[$k]['smtp'] = unserialize($v['settinginfo']);
			}
			$this->smtplist = $smtplist;
			$this->alert=parseAlert();
			$this->display();
		}
		
		public function testsmtp(){
			if ($this->isAjax()) {
				if($_POST['address']){
					if (ereg('^([a-zA-Z0-9]+[_|_|.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|_|.]?)*[a-zA-Z0-9]+.[a-zA-Z]{2,3}$',$_POST['address'])){
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
			}
		}
		
		public function smtpadd(){
			if($this->isPost()) {
				$m_user_smtp = M('UserSmtp');
				if(empty($_POST['address'])){
					alert('error', L('NEED_ADDRESS'), $_SERVER['HTTP_REFERER']);
				}
				if(empty($_POST['smtp'])){
					alert('error', L('NEED_SMTP'), $_SERVER['HTTP_REFERER']);
				}
				if(empty($_POST['port'])){
					alert('error', L('NEED_PORT'), $_SERVER['HTTP_REFERER']);
				}
				if(empty($_POST['loginName'])){
					alert('error', L('NEED_LOGINNAME'), $_SERVER['HTTP_REFERER']);
				}
				if(empty($_POST['password'])){
					alert('error', L('NEED_PASSWORD'), $_SERVER['HTTP_REFERER']);
				}
				if($_POST['address']){
					if(is_email($_POST['address'])){
						$demosmtp = array('MAIL_ADDRESS'=>$_POST['address'],'MAIL_SMTP'=>$_POST['smtp'],'MAIL_PORT'=>$_POST['port'],'MAIL_LOGINNAME'=>$_POST['loginName'],'MAIL_PASSWORD'=>$_POST['password'],'MAIL_SECURE'=>$_POST['secure'],'MAIL_CHARSET'=>'UTF-8','MAIL_AUTH'=>true,'MAIL_HTML'=>true);
						$smtp['settinginfo'] = serialize($demosmtp);
						$smtp['user_id'] = session('user_id');
						$smtp['name'] = trim($_POST['name']);
						
						if($m_user_smtp->add($smtp)){
							alert('success',L('ADD_A_SUCCESS'),$_SERVER['HTTP_REFERER']);
						}else{
							echo $m_user_smtp->_sql();
							die();
							alert('error',L('ADD_FAILURE'),$_SERVER['HTTP_REFERER']);
						}
					}else{
						alert('error',L('EMAIL FORMAT ERROR'),$_SERVER['HTTP_REFERER']);
					}
				}
			} else {
				$smtp = M('UserSmtp')->where('name = "smtp"')->getField('smtpmail');
				$this->smtp = unserialize($demosmtp);
				$this->alert = parseAlert();
				$this->display();			
			}
		}
		
		public function smtpedit(){
			$m_smtp = M('UserSmtp');
			if($this->isPost()){
				$m_user_smtp = M('UserSmtp');
				if(empty($_POST['address'])){
					alert('error', L('NEED_ADDRESS'), $_SERVER['HTTP_REFERER']);
				}
				if(empty($_POST['smtp'])){
					alert('error', L('NEED_SMTP'), $_SERVER['HTTP_REFERER']);
				}
				if(empty($_POST['port'])){
					alert('error', L('NEED_PORT'), $_SERVER['HTTP_REFERER']);
				}
				if(empty($_POST['loginName'])){
					alert('error', L('NEED_LOGINNAME'), $_SERVER['HTTP_REFERER']);
				}
				if(empty($_POST['password'])){
					alert('error', L('NEED_PASSWORD'), $_SERVER['HTTP_REFERER']);
				}
				if($_POST['address']){
					if(is_email($_POST['address'])){
						$demosmtp = array('MAIL_ADDRESS'=>$_POST['address'],'MAIL_SMTP'=>$_POST['smtp'],'MAIL_PORT'=>$_POST['port'],'MAIL_LOGINNAME'=>$_POST['loginName'],'MAIL_PASSWORD'=>$_POST['password'],'MAIL_SECURE'=>$_POST['secure'],'MAIL_CHARSET'=>'UTF-8','MAIL_AUTH'=>true,'MAIL_HTML'=>true);
						$smtp['settinginfo'] = serialize($demosmtp);
						$smtp['user_id'] = session('user_id');
						$smtp['name'] = trim($_POST['name']);
						
						if($m_user_smtp->where('smtp_id = %d', intval($_POST['smtp_id']))->save($smtp)){
							alert('success',L('MODIFY_THE_SUCCESS'),$_SERVER['HTTP_REFERER']);
						}else{
					echo $m_user_smtp->getLastSql(); die();
							alert('error',L('DATA UNCHANGED'),$_SERVER['HTTP_REFERER']);
						}
					}else{
						alert('error',L('EMAIL FORMAT ERROR'),$_SERVER['HTTP_REFERER']);
					}
				}
			}else{
				if($_GET['id']){
					$smtp = $m_smtp->where('smtp_id = %d', intval($_GET['id']))->find();
					$smtp['setting'] = unserialize($smtp['settinginfo']);
					$this->smtp = $smtp;
					$this->display();
				}else{
					alert('error', L('PARAMETER_ERROR'), $_SERVER['HTTP_REFERER']);
				}
				
			}
		}
		
		public function smtpdelete(){
			if($this->isPost()){
				if(!empty($_POST['smtp_id'])){
					$m_smtp = M('UserSmtp');
					$smtp_ids = $_POST['smtp_id'];
					if($m_smtp->where('smtp_id in (%s)', implode(',', $smtp_ids))->delete()){
						alert('success', L('DELETED SUCCESSFULLY'), $_SERVER['HTTP_REFERER']);
					}else{
						alert('error', L('DELETED_FAILURE'), $_SERVER['HTTP_REFERER']);
					}
				}else{
					alert('error', L('PARAMETER_ERROR'), $_SERVER['HTTP_REFERER']);
				}
			}else{
				$this->display();
			}
		}
	}