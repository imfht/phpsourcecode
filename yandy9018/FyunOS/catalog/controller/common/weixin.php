<?php
/**
  * fyun.mobi weichat
*/
class ControllerCommonWeixin extends Controller {
	public function index() {
		define("TOKEN",$this->config->get('config_wechat_token'));
		$weStatus = $this->config->get('config_wechat_status');
	     if ($weStatus==0){
		$this->wechat->valid();
        }else{
			$this->wechat->responseMsg();
			}
	}
}

?>