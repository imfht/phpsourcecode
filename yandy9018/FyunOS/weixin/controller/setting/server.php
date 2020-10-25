<?php
class ControllerSettingServer extends Controller {
	private $error = array();
 
	public function index() {
		$this->load_language('setting/setting'); 

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			
			$this->model_setting_setting->updateSetting('config', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('setting/server', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['heading_title'] = $this->language->get('tab_server');

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
       		'text'      => $this->language->get('heading_title_server'),
			'href'      => $this->url->link('setting/server', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => $this->language->get('text_breadcrumb_separator')
   		);
		
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$this->data['action'] = $this->url->link('setting/server', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('setting/setting', 'token=' . $this->session->data['token'], 'SSL');

		$post_keys=array(
			'config_use_ssl',
			'config_seo_url',
			'config_debug',
			'config_maintenance',
			'config_encryption',
			'config_compression',
			'config_error_display',
			'config_error_log',
			'config_error_filename',
			'config_google_analytics'
		);
		
		foreach ($post_keys as $value) {
			if (isset($this->request->post[$value])) {
				$this->data[$value] = $this->request->post[$value];
			} else {
				$this->data[$value] = $this->config->get($value);
			}
		}
		
		$error_keys=array(
			'warning' => 'error_warning',
			'error_filename' => 'error_error_filename'
		);
		
		foreach ($error_keys as $key => $value) {
			if (isset($this->error[$key])) {
				$this->data[$value] = $this->error[$key];
			} else {
				$this->data[$value] = '';
			}
		}

		$this->template = 'setting/server.tpl';
		$this->id = 'content';
		$this->layout = 'layout/parameter';
		$this->render();
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'setting/server')) {
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