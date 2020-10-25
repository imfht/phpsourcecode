<?php  
class ControllerCommonTop extends Controller {
	public function index() {
		$this->data['account'] = $this->url->link('account/account');

		$this->template = $this->config->get('config_template') . '/template/common/top.tpl';
	
								
		$this->render();
	}
}
?>