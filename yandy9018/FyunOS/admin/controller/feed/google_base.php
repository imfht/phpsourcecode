<?php 
class ControllerFeedGoogleBase extends Controller {
	private $error = array(); 
	
	public function index() {
		$this->load_language('feed/google_base');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('google_base', $this->request->post);				
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL'));
		}


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
       		'text'      => $this->language->get('text_feed'),
			'href'      => $this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => $this->language->get('text_breadcrumb_separator')
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('feed/google_base', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => $this->language->get('text_breadcrumb_separator')
   		);
				
		$this->data['action'] = $this->url->link('feed/google_base', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL');
		
		if (isset($this->request->post['google_base_status'])) {
			$this->data['google_base_status'] = $this->request->post['google_base_status'];
		} else {
			$this->data['google_base_status'] = $this->config->get('google_base_status');
		}
		
		$this->data['data_feed'] = HTTP_CATALOG . 'index.php?route=feed/google_base';

		$this->template = 'feed/google_base.tpl';
		$this->id = 'content';
		$this->layout = 'layout/default';
		$this->render();
	} 
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'feed/google_base')) {
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