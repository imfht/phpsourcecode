<?php
class ControllerSettingSms extends Controller {
	private $error = array();
 
	public function index() {
		$this->load_language('setting/setting'); 

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			
			$this->model_setting_setting->updateSetting('config', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('setting/sms', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['heading_title'] = $this->language->get('tab_sms');

		$this->data['token'] = $this->session->data['token'];

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
   		    'text'      => $this->language->get('heading_title_1'),
   			'href'      => $this->url->link('setting/parameter', 'token=' . $this->session->data['token'], 'SSL'),
   		    'separator' => $this->language->get('text_breadcrumb_separator')
   		);
   		
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title_sms'),
			'href'      => $this->url->link('setting/sms', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => $this->language->get('text_breadcrumb_separator')
   		);
		
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$this->data['action'] = $this->url->link('setting/sms', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('setting/setting', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['config_sms_url'])) {
			$this->data['config_sms_url'] = $this->request->post['config_sms_url'];
		} else {
			$this->data['config_sms_url'] = $this->config->get('config_sms_url');
		}
		
		if (isset($this->request->post['config_sms_ac'])) {
			$this->data['config_sms_ac'] = $this->request->post['config_sms_ac'];
		} else {
			$this->data['config_sms_ac'] = $this->config->get('config_sms_ac');
		}
		
		if (isset($this->request->post['config_sms_authkey'])) {
			$this->data['config_sms_authkey'] = $this->request->post['config_sms_authkey'];
		} else {
			$this->data['config_sms_authkey'] = $this->config->get('config_sms_authkey');
		}
		
		
		if (isset($this->request->post['config_sms_cgid'])) {
			$this->data['config_sms_cgid'] = $this->request->post['config_sms_cgid'];
		} else {
			$this->data['config_sms_cgid'] = $this->config->get('config_sms_cgid');
		}
		
		
		if (isset($this->request->post['config_sms_csid'])) {
			$this->data['config_sms_csid'] = $this->request->post['config_sms_csid'];
		} else {
			$this->data['config_sms_csid'] = $this->config->get('config_sms_csid');
		}
	

		$this->template = 'setting/sms.tpl';
		$this->id = 'content';
		$this->layout = 'layout/parameter';
		$this->render();
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'setting/sms')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
	
	
}
?>