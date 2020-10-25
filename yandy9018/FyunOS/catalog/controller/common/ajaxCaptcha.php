<?php
class ControllerCommonAjaxCaptcha extends Controller {
	public function index() {
	  
		$num = 955568;
//		  $sms = new Sms();
//		  $sms->url = $this->config->get('config_sms_url');
//		  $sms->uid = $this->config->get('config_sms_ac');
//		  $sms->mobile = $this->request->get['telephone'];
//		  $sms->pwd = $this->config->get('config_sms_authkey');
//		  //$sms->cgid = $this->config->get('config_sms_cgid');
//		  $sms->content = $num;
//		  //$sms->sgid = $this->config->get('config_sms_sgid');
//		  $q = $sms->sendSMS();
//setcookie("captcha",  md5($num), time()+300); /* 有效期 1个小时 */ 
$this->session->data['captcha'] = md5($num);

		  echo "Y";	
		 
	}
	
	public function captcha() {
		$num="";
		for($i=0; $i<6; $i++){
			$num .= mt_rand(0,9);
			}
		
		return $num;
	
	}
	
	public function verification() {
		$code = $this->request->get['codeRand'];
		
			if(md5($code) == $this->session->data['captcha']){
			echo "A";
			}else{
				echo "N";
				}
		
		
	}
	
}
?>