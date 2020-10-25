<?php
class ControllerSettingWechat extends Controller {
	private $error = array();
 
	public function index() {
		$this->load_language('setting/setting'); 

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			
			$this->model_setting_setting->updateSetting('config', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('setting/wechat', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['heading_title'] = $this->language->get('tab_wechat');

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
       		'text'      => $this->language->get('heading_title_wechat'),
			'href'      => $this->url->link('setting/wechat', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => $this->language->get('text_breadcrumb_separator')
   		);
		
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$this->data['action'] = $this->url->link('setting/wechat', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('setting/setting', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['config_wechat_appid'])) {
			$this->data['config_wechat_appid'] = $this->request->post['config_wechat_appid'];
		} else {
			$this->data['config_wechat_appid'] = $this->config->get('config_wechat_appid');
		}
		
		if (isset($this->request->post['config_wechat_appsecret'])) {
			$this->data['config_wechat_appsecret'] = $this->request->post['config_wechat_appsecret'];
		} else {
			$this->data['config_wechat_appsecret'] = $this->config->get('config_wechat_appsecret');
		}
		
		if (isset($this->request->post['config_wechat_token'])) {
			$this->data['config_wechat_token'] = $this->request->post['config_wechat_token'];
		} else {
			$this->data['config_wechat_token'] = $this->config->get('config_wechat_token');
		}
		
		
		if (isset($this->request->post['config_wechat_attention'])) {
			$this->data['config_wechat_attention'] = $this->request->post['config_wechat_attention'];
		} else {
			$this->data['config_wechat_attention'] = $this->config->get('config_wechat_attention');
		}
		
		$this->data['config_url'] = $this->config->get('config_url');
		$this->data['config_wechat_status'] = $this->config->get('config_wechat_status');
		if (isset($this->request->post['config_wechat_reply'])) {
			$this->data['config_wechat_reply'] = $this->request->post['config_wechat_reply'];
		} else {
			$this->data['config_wechat_reply'] = $this->config->get('config_wechat_reply');
		}
	

		$this->template = 'setting/wechat.tpl';
		$this->id = 'content';
		$this->layout = 'layout/parameter';
		$this->render();
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'setting/wechat')) {
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