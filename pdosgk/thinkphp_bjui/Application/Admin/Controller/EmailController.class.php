<?php
/**
 * 邮件模块
 * @author Lain
 *
 */
namespace Admin\Controller;
use Admin\Controller\AdminController;
class EmailController extends AdminController{
	public function _initialize(){
		$action = array(
				//'permission'=>array('changePassword'),
				//'allow'=>array('index')
		);
		B('Admin\\Behaviors\\Authenticate', '', $action);
	}

    public function index(){
    	if(IS_POST){
	    	$info = I('post.info');
	    	$info['MAIL_AUTH'] = true;
	    	C($info);
	    	//发送邮件
	    	$title = '这是一封测试邮件';
	    	$message = '这是一封测试邮件, 无需回复';
	    	$address = $info['MAIL_TO_ADDRESS'];
	    	
	    	$fromname = '一起PHP';
	    	import('Org.Util.Mail');
	    	$mail= new \PHPMailer(true);
	    	try {
	    		$mail->IsSMTP();
	    		$mail->CharSet = 'UTF-8';
	    		if(is_array($address)){
	    			foreach ($address as $v){
	    				$mail->AddAddress($v);
	    			}
	    		}else{
	    			$mail->AddAddress($address);
	    		}

	    		// var_dump(C('MAIL_PASSWORD'));exit;
	    		$mail->Body=$message;
	    		$mail->From= C('MAIL_ADDRESS');
	    		$mail->FromName=$fromname;
	    		$mail->Subject= $title;
	    		$mail->Host=C('MAIL_SMTP');
	    		$mail->SMTPAuth=C('MAIL_AUTH');
	    		$mail->Port=C('MAIL_PORT');
	    		$mail->SMTPSecure = 'ssl';
	    		$mail->Username=C('MAIL_LOGINNAME');
	    		$mail->Password=C('MAIL_PASSWORD');
	    		$mail->IsHTML(true);
	    		$mail->MsgHTML($message);
	    		$result = $mail->Send();
	    		if($result){
	    			F('email_setting', $info);
	    			$this->ajaxReturn(array('statusCode'=>200, 'message' => '发送成功'));
	    		}
	    	} catch (\phpmailerException $e) {
	    		$this->ajaxReturn(array('statusCode'=>300,'message' => $e->errorMessage()));
	    	} catch (\Exception $e) {
	    		$this->ajaxReturn(array('statusCode'=>300,'message' => $e->getMessage()));
	    	}
    	}else{
    		$setting = F('email_setting');
    		$this->assign('setting', $setting);
	    	$this->display();
    	}
    }
}