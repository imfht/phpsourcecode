<?php
class CommentAction extends Action{
	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('add', 'delete', 'edit')
		);
		B('Authenticate', $action);
	}
	
	public function add(){
		if($this->isPost()){
			$module = $_POST['module'];
			$module_id =  intval($_POST['module_id']);
			$m_comment = M('Comment');
			$m_comment->create();
			$m_comment->creator_role_id = session('role_id');
			$m_comment->create_time = time();
			$m_comment->update_time = time();
			if($comment_id = $m_comment->add()){
				$m_id = $module . '_id';
				if(intval($_POST['message_alert']) == 1) {
					sendMessage($_POST['to_role_id'], L('THE MAIN CONTENTS ARE AS FOLLOWS',array(createCommentAlertInfo($module, $module_id),chr(10),$_POST['content'])),1);
				}
				if(intval($_POST['email_alert']) == 1){
					$email_result = sysSendEmail($_POST['to_role_id'],createCommentAlertInfo($module, $module_id),L('THE MAIN CONTENT',array($_POST['content'])));
					if(!$email_result) alert('error', L('EMAIL NOTIFICATION OF FAILURE THE OTHER PARTY IS NOT SET EFFECTIVE EMAIL'),$_SERVER['HTTP_REFERER']);
				}
				if(intval($_POST['sms_alert']) == 1){
					$sms_result = sysSendSms($_POST['to_role_id'],createCommentAlertInfo($module, $module_id));
					if(100 == $sms_result){
						alert('error', L('SMS NOTIFICATION OF FAILURE THE OTHER PARTY IS NOT SET EFFECTIVE PHONE'),$_SERVER['HTTP_REFERER']);
					}elseif($sms_result < 0){
						alert (L('SMS SEND FAILS AN ERROR CODE PLEASE CONTACT THE ADMINISTRATOR CONFIRMATION MESSAGE INTERFACE CONFIGURATION',array(error,$sms_result)),$_SERVER['HTTP_REFERER']);
					}
				}
				alert('success',L('ADD COMMENTS SUCCESS'),$_SERVER['HTTP_REFERER']);
			}else{
				alert('error', L('ADD COMMENTS FAILED'),$_SERVER['HTTP_REFERER']);
			}
		} elseif ($_GET['module'] and $_GET['module_id'] and $_GET['to_role_id']) {
			$this->module = $_GET['module'];
			$this->module_id = intval($_GET['module_id']);
			$this->to_role_id = intval($_GET['to_role_id']);
			$this->display();
		} else {
			alert('error', L('PARAMETER ERROR'),$_SERVER['HTTP_REFERER']);
		}
	}

	public function edit(){
		if($this->isPost()){
			$module = $_POST['module'];
			$module_id = intval($_POST['module_id']);
			$m_comment = M('Comment');
			$m_comment->create();
			$m_comment->update_time = time();
			if($m_comment->save()){
				$m_id = $module . '_id';
				if(intval($_POST['message_alert']) == 1) {
					sendMessage($_POST['to_role_id'],L('THE MAIN CONTENTS ARE AS FOLLOWS',array(createCommentAlertInfo($module, $module_id),chr(10),$_POST['content'])),1);
				}
				if(intval($_POST['email_alert']) == 1){
					$email_result = sysSendEmail($_POST['to_role_id'],createCommentAlertInfo($module, $module_id),L('THE MAIN CONTENT',array($_POST['content'])));
					if(!$email_result) alert('error', L('EMAIL NOTIFICATION OF FAILURE THE OTHER PARTY IS NOT SET EFFECTIVE EMAIL'),$_SERVER['HTTP_REFERER']);
				}
				if(intval($_POST['sms_alert']) == 1){
					$sms_result = sysSendSms($_POST['to_role_id'],createCommentAlertInfo($module, $module_id));
					if(100 == $sms_result){
						alert('error', L('SMS NOTIFICATION OF FAILURE THE OTHER PARTY IS NOT SET EFFECTIVE PHONE'),$_SERVER['HTTP_REFERER']);
					}elseif($sms_result < 0){
						alert (L('SMS SEND FAILS AN ERROR CODE PLEASE CONTACT THE ADMINISTRATOR CONFIRMATION MESSAGE INTERFACE CONFIGURATION'),array(error,$sms_result),$_SERVER['HTTP_REFERER']);
					}
				}
				alert('success',L('MODIFY COMMENTS SUCCESS'),$_SERVER['HTTP_REFERER']);
			}else{
				alert('error', L('MODIFY COMMENTS FAILED'),$_SERVER['HTTP_REFERER']);
			}
		} elseif ($_GET['id']) {
			$this->comment = M('Comment')->where('comment_id =%d', $_GET['id'])->find();
			$this->display();
		} else {
			alert('error', L('PARAMETER ERROR'),$_SERVER['HTTP_REFERER']);
		}
	}

	public function delete(){
		$comment_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		if (0 == $m_comment_id){
			alert('error',L('PARAMETER ERROR'),$_SERVER['HTTP_REFERER']);
		} else {
			if (isset($_GET['r']) && isset($_GET['id'])) {
				$m_r = M($_GET['r']);
				$m_Comment = M('Comment');
				
				if ($m_r->where('Comment_id = %d',$_GET['id'])->delete()) {
					if ($m_Comment->where('Comment_id = %d',$_GET['id'])->delete()) {
						alert('success',L('33 WAS REMOVED SUCCESSFULLY'),$_SERVER['HTTP_REFERER']);
					} else {
						alert('success',L('DELETE FAILED PLEASE CONTACT YOUR ADMINISTRATOR'),$_SERVER['HTTP_REFERER']);
					}
				} else {
					alert('success',L('DELETE FAILED PLEASE CONTACT YOUR ADMINISTRATOR'),$_SERVER['HTTP_REFERER']);
				}
			} elseif (empty($_GET['r']) && isset($_GET['id'])){
				$m_Comment = M('Comment');
				if ($m_Comment->where('Comment_id = %d',$_GET['id'])->delete()){
					alert('success',L('33 WAS REMOVED SUCCESSFULLY'),U('Comment/index'));
				} else {
					alert('success',L('DELETE FAILED PLEASE CONTACT YOUR ADMINISTRATOR'),U('Comment/index'));
				}
			}
		}
	}
	
	
	
}
