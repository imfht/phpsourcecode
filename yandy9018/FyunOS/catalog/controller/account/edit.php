<?php
class ControllerAccountEdit extends Controller {
	private $error = array();

	public function index() {
	//判断用户来自哪里
		if (!$this->customer->isLogged()) {
			if($this->config->get('config_bind_status')=='1'){
				if($this->config->get('config_phone_login')=='1'){
					$this->redirect($this->url->link('account/login&phone=1', '', 'SSL'));
				}else{
					$this->customer->weixinLogin();
					}
			}elseif($this->config->get('config_bind_status')=='2'){
				if(isset($this->request->get['openId'])){
					$this->load->model('account/customer');
					$data['nickname'] = "微信用户";
					$data['openid'] = $this->request->get['openId'];
					$this->model_account_customer->addWeixinCustomer($data);
					$this->session->data['open_id'] = $this->request->get['openId'];
					$this->redirect($this->url->link('account/account', '', 'SSL'));
					}else{
						$this->redirect($this->url->link('account/login&ubind=1', '', 'SSL'));
						}
				}else{
					$this->redirect($this->url->link('account/login', '', 'SSL'));
					}
		}
		
		
		
		$this->session->data['menu']="account";
		

		$this->load_language('account/edit');
		$this->data['country_id'] = $this->config->get('config_country_id');
		$this->data['zone_id'] = $this->customer->getZoneId();
		$this->data['city_id'] =  $this->customer->getCityId();
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('account/customer');
		
		if(isset($this->request->get['do'])){
			$this->data['text_message'] = sprintf($this->language->get('text_message'), $this->url->link('information/contact'));
		}else{
			$this->data['text_message'] ='';
		}
		 
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			
			$this->model_account_customer->getCustomerByTelephone($this->request->post['telephone']);
			$this->model_account_customer->editCustomer($this->request->post);
		}

      	$this->data['breadcrumbs'] = array();

      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),     	
        	'separator' => false
      	); 

      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_account'),
			'href'      => $this->url->link('account/account', '', 'SSL'),        	
        	'separator' => $this->language->get('text_separator')
      	);

      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_edit'),
			'href'      => $this->url->link('account/edit', '', 'SSL'),       	
        	'separator' => $this->language->get('text_separator')
      	);
		
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->error['firstname'])) {
			$this->data['error_firstname'] = $this->error['firstname'];
		} else {
			$this->data['error_firstname'] = '';
		}

		if (isset($this->error['email'])) {
			$this->data['error_email'] = $this->error['email'];
		} else {
			$this->data['error_email'] = '';
		}	
		
		if (isset($this->error['telephone'])) {
			$this->data['error_telephone'] = $this->error['telephone'];
		} else {
			$this->data['error_telephone'] = '';
		}	

		$this->data['action'] = $this->url->link('account/edit', '', 'SSL');

		if ($this->request->server['REQUEST_METHOD'] != 'POST') {
			$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
		}
		
		if (isset($this->request->post['firstname'])) {
			$this->data['firstname'] = $this->request->post['firstname'];
		} elseif (isset($customer_info)) {
			$this->data['firstname'] = $customer_info['firstname'];
		} else {
			$this->data['firstname'] = '';
		}


		if (isset($this->request->post['telephone'])) {
			$this->data['telephone'] = $this->request->post['telephone'];
		} elseif (isset($customer_info)) {
			$this->data['telephone'] = $customer_info['telephone'];
		} else {
			$this->data['telephone'] = '';
		}

		if (isset($this->request->post['address'])) {
			$this->data['address'] = $this->request->post['address'];
		} elseif (isset($customer_info)) {
			$this->data['address'] = $customer_info['address'];
		} else {
			$this->data['address'] = '';
		}

		$this->data['back'] = $this->url->link('account/account', '', 'SSL');
		
		if (isset($this->session->data['success'])) {
    		$this->data['success'] = $this->session->data['success'];
			
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/edit.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/account/edit.tpl';
		} else {
			$this->template = 'default/template/account/edit.tpl';
		}
		
		$this->children = array(
			'common/nav',
			'common/header'	
		);
						
		$this->response->setOutput($this->render());	
	}

	private function validate() {
		
		if ((strlen(utf8_decode($this->request->post['firstname'])) < 1) || (strlen(utf8_decode($this->request->post['firstname'])) > 32)) {
			$this->error['firstname'] = $this->language->get('error_firstname');
		}

		if ((strlen(utf8_decode($this->request->post['telephone'])) < 1) || (strlen(utf8_decode($this->request->post['telephone'])) > 32)) {
			$this->error['telephone'] = $this->language->get('error_telephone');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
}
?>