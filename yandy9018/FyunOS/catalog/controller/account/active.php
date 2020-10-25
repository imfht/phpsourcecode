<?php 
class ControllerAccountActive extends Controller {
	private $error = array();
	      
  	public function index() {
		if ($this->customer->isLogged()) {
	  		$this->redirect($this->url->link('account/account', '', 'SSL'));
    	}

    	$this->load_language('account/active');
		
		$this->load->model('account/customer');
		
		if(isset($this->request->get['active_code'])){
	    	$this->model_account_customer->activeCustomer($this->request->get['active_code']);
	    	$this->session->data['success']=$this->language->get('success');
	    	$this->redirect($this->url->link('account/login', '', 'SSL'));
		}
  	}
}
?>